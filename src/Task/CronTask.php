<?php

// Tick once a second..
declare (ticks = 1);

namespace ProjectRena\Task;

use Cilex\Command\Command;
use ProjectRena\RenaApp;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CronCommand.
 */
class CronTask extends Command
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('cron:run')->setDescription('Runs cronjobs');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Enable the garbage collector since this is a long running process
        gc_enable();

        // Get the slim instance
        $app = RenaApp::getInstance();

        $run = true;
        $cnt = 0;
        $cronjobs = scandir(__DIR__ . '/Cronjobs/');
        do
        {
            $cnt++;
            if($cnt > 50)
            {
                gc_collect_cycles();
                $cnt = 0;
            }

            foreach($cronjobs as $key => $cron)
            {
                // Unset anything that isn't .php!
                if(!preg_match('/^(.+)\\.php$/', $cron, $match))
                {
                    unset($cronjobs[$key]);
                    continue;
                }

                if(isset($match[1]))
                {
                    $name = $match[1];
                    $md5 = md5($name);

                    // If the script is currently running, skip to the next script
                    if($app->Cache->get($md5 . '_pid') != false)
                    {
                        $pid = $app->Cache->get($md5 . '_pid');
                        $status = pcntl_waitpid($pid, $status, WNOHANG);
                        if($status == -1) $app->Cache->delete($md5 . '_pid');

                        usleep(500000);
                        //continue;
                    }

                    // Get last time this cronjob ran
                    $lastRan = $app->Cache->get($md5) > 0 ? $app->Cache->get($md5) : 0;

                    // Current Time
                    $currentTime = time();

                    // Load the cronjobs class and get the information needed
                    $import = '\\ProjectRena\\Task\\Cronjobs\\' . $name;
                    $class = new $import();
                    $interval = $class->getRunTimes();

                    // If the current time is larger than the lastRunTime and Interval, then we run it again!
                    if($currentTime > ($lastRan + $interval))
                    {
                        $time = time();
                        $output->writeln("Time: {$time}: Running {$name} (Interval: {$interval})");

                        try
                        {
                            // Time to fork it all!
                            $pid = pcntl_fork();
                            if($pid === 0)
                            {
                                // Get the PID
                                $pid = getmypid();
                                // Tell the cache that we're running the cronjobs will automatically remove it from the cache once they're done
                                $app->Cache->set($md5 . '_pid', $pid);

                                // Init all the stuff needed inside the Cronjob
                                $db = new \ProjectRena\Lib\Db($app);
                                $db->persistence = true; // Turn persistent connections off

                                // Execute the cronjob
                                $class->execute($pid, $md5, $db, $app);
                                exit();
                            }

                            // Tell the cache when we ran last!
                            $app->Cache->set($md5, time());
                        } catch(\Exception $e)
                        {
                            $output->writeln("ERROR!! (pid: " . getmypid() . ") " . $e->getMessage());
                            $run = false;
                            posix_kill(getmygid(), 9);
                            exit();
                        }
                    }
                }

                // Sleep for 500 milliseconds, so we don't go nuts with CPU
                usleep(500000);
            }
        } while($run == true);
    }
}
