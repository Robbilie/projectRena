<?php
namespace ProjectRena\Lib;

use Domnikl\Statsd\Client;
use Domnikl\Statsd\Connection\UdpSocket;
use ProjectRena\RenaApp;

/**
 * Class StatsD
 *
 * @package ProjectRena\Lib\Service
 */
class StatsD
{
				/**
				 * @var Client
				 */
				private $statsd;
				/**
				 * @var \ProjectRena\Model\Config
				 */
				private $config;

				/**
				 * @param RenaApp $app
				 */
				function __construct(RenaApp $app)
				{
								$this->config = $app->baseConfig;

								$connection = new UdpSocket($this->config->getConfig('server', 'statsd', '127.0.0.1'), $this->config->getConfig('port', 'statsd', 8125));
								$this->statsd = new Client($connection, $this->config->getConfig('namespace', 'statsd', 'rena.namespace'));

								// Global name space
								$this->statsd->setNamespace($this->config->getConfig('globalNamespace', 'statsd', 'rena'));
				}

				/**
				 * Increments a value in statsd.
				 *
					* @static
     *
				 * @param string $name the name of the key
				 * @param int $amount the amount it's incremented
				 */
				public function increment($name, $amount = 1)
				{
								$this->statsd->increment($name, $amount = 1);
				}

				/**
				 * Creates a timing list in statsd.
				 *
				 * @static
				 *
				 * @param string $name the name of the key
				 * @param int $time the time it took for the request/execution to finish
				 */
				public function timing($name, $time)
				{
								$this->statsd->timing($name, $time);
				}

				/**
				 * Creates a gauge in statsd.
				 *
				 * @static
				 *
				 * @param string $name the name of the key
				 * @param string|int $amount the amount the gauge is increased
				 */
				public function gauge($name, $amount)
				{
								$this->statsd->gauge($name, $amount);
				}
}