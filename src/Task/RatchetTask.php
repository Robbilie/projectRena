<?php
namespace ProjectRena\Task;

use Cilex\Command\Command;
use ProjectRena\Lib;
use ProjectRena\RenaApp;
use Ratchet\Wamp\Topic;
use Ratchet\Wamp\WampServerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ZMQ;

/**
 * Starts up a ratchet server that sends killmails to the user
 */
class RatchetTask extends Command
{

    /**
     */
    protected function configure()
    {
        $this->setName('ratchet:run')->setDescription('Starts up a zmq listener, with a websocket server that also passes data to stomp..');
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

        // Setup the react event loop and call up the pusher class
        $loop = \React\EventLoop\Factory::create();
        $pusher = new Pusher();
        $stomper = new stompSend();

        // ZeroMQ server
        $context = new \React\ZMQ\Context($loop);
        $pull = $context->getSocket(ZMQ::SOCKET_PULL);
        $pull->bind("tcp://127.0.0.1:5555");
        $pull->on("message", array($pusher, "onMessage"));
        $pull->on("message", array($stomper, "onMessage"));

        // Websocket server
        $webSock = new \React\Socket\Server($loop);
        $webSock->listen(8800, "0.0.0.0");
        $webServer = new \Ratchet\Server\IoServer(new \Ratchet\Http\HttpServer(new \Ratchet\WebSocket\WsServer(new \Ratchet\Wamp\WampServer($pusher))), $webSock);
        $loop->run();
    }
}

class stompSend
{
    protected $stomp;
    protected $app;
    public function __construct()
    {
        $this->app = RenaApp::getInstance();
        $this->stomp = new \Stomp($this->app->baseConfig->getConfig("server", "stomp"), $this->app->baseConfig->getConfig("username", "stomp"), $this->app->baseConfig->getConfig("password", "stomp"));
    }

    public function onMessage($message)
    {
        $this->app->StatsD->increment("stompSent");
        $this->stomp->send("/topic/kills", $message);
    }
}

/**
 * Class Pusher
 *
 * @package ProjectRena\Task
 */
class Pusher implements WampServerInterface
{
    /**
     * @var \SplObjectStorage
     */
    protected $clients;

    /**
     *
     */
    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
    }

    /**
     * @param \Ratchet\ConnectionInterface $conn
     */
    function onOpen(\Ratchet\ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
    }

    /**
     * @param \Ratchet\ConnectionInterface $conn
     */
    function onClose(\Ratchet\ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        $conn->close();
    }

    /**
     * @param \Ratchet\ConnectionInterface $conn
     * @param \Exception $e
     */
    function onError(\Ratchet\ConnectionInterface $conn, \Exception $e)
    {
        $this->clients->detach($conn);
        $conn->close();
    }

    /**
     * @param $entry
     */
    public function onMessage($entry)
    {
        //$entryData = json_decode($entry, true);
        foreach($this->clients as $client)
            $client->send($entry);
    }

    /**
     * @param \Ratchet\ConnectionInterface $conn
     * @param string $id
     * @param Topic|string $topic
     * @param array $params
     */
    function onCall(\Ratchet\ConnectionInterface $conn, $id, $topic, array $params)
    {
    }

    /**
     * @param \Ratchet\ConnectionInterface $conn
     * @param Topic|string $topic
     */
    function onSubscribe(\Ratchet\ConnectionInterface $conn, $topic)
    {
    }

    /**
     * @param \Ratchet\ConnectionInterface $conn
     * @param Topic|string $topic
     */
    function onUnSubscribe(\Ratchet\ConnectionInterface $conn, $topic)
    {
    }

    /**
     * @param \Ratchet\ConnectionInterface $conn
     * @param Topic|string $topic
     * @param string $event
     * @param array $exclude
     * @param array $eligible
     */
    function onPublish(\Ratchet\ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
    {
    }
}