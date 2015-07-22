<?php
namespace ProjectRena\Task;

use DateTime;
use ProjectRena\RenaApp;
use Cilex\Command\Command;
use ProjectRena\Lib;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZMQ;
use ZMQContext;

/**
 * Receives data from Squizz stupid queue implementation from hell
 */
class zkillboardReceiveTask extends Command
{

    /**
     */
    protected function configure()
    {
        $this->setName('run:zkb')->setDescription('Receives data from Squizz stupid queue implementation from hell');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Init rena
        $app = RenaApp::getInstance();
        $run = true;
        $oldKillID = 0;
        do
        {
            $p = \RedisQ\Action::listen("redisq.zkillboard.com");
var_dump($p);
            if($p["killID"] > $oldKillID)
            {
                // Get the killmail data.
                $k = $app->killmails->generateFromCREST($p);

                // Poke statsd
                $app->StatsD->increment("zKillboardReceived");

                // Now lets make the json and hash
                $json = json_encode($k);
                $hash = hash("sha256", ":" . $k["killTime"] . ":" . $k["solarSystemID"] . ":" . $k["moonID"] . "::" . $k["victim"]["characterID"] . ":" . $k["victim"]["shipTypeID"] . ":" . $k["victim"]["damageTaken"] . ":");

                // Push it over zmq to the websocket
                $context = new ZMQContext();
                $socket = $context->getSocket(ZMQ::SOCKET_PUSH, "rena");
                $socket->connect("tcp://localhost:5555");
                $socket->send($json);

                // Lets insert the killmail!
                $app->killmails->insertKillmail($p["killID"], 0, $hash, "zkillboardRedisQ", $json);
            }
            $oldKillID = $p["killID"];
        }
        while($run == true);
    }
}
