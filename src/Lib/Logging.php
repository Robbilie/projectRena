<?php

namespace ProjectRena\Lib;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use ProjectRena\RenaApp;

/**
 * Class Logging
 *
 * @package ProjectRena\Lib\Service
 */
class Logging
{
    /**
     * @var RenaApp
     */
    protected $app;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var
     */
    protected $config;

    /**
     * @param $app
     */
    public function __construct(RenaApp $app)
    {
        $this->app = $app;
        $this->logger = new Logger('projectRena');
        $this->logger->pushHandler(new StreamHandler($this->app->baseConfig->getConfig('logFile', 'Logging', __DIR__ . '/../../logs/app.log'), $this->app->baseConfig->getConfig("logLevel", "Logging", 100)));

    }

    /**
     * Logs data into the logfile.
     *
     * @static
     *
     * @param string $logType the type of logging, debug, info, warning, error
     * @param string $logMessage the message for the log
     */
    public function log($logType, $logMessage)
    {
        $this->logger->log($logType, $logMessage);
    }

    /**
     * Inserts data into the slim flasher.
     *
     * @static
     *
     * @param string $logType the type of logging, debug, info, warning, error
     * @param string $logMessage the message for the log
     */
    public function flasher($logType, $logMessage)
    {
        $this->app->flash($logType, $logMessage);
    }
}