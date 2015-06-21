<?php

namespace ProjectRena\Lib;

use Closure;
use ProjectRena\RenaApp;
use Redis;

/**
 * Class Cache.
 */
class Cache
{
				/**
				 * @var Redis
				 */
				private $redis;

				/**
				 * @var bool
				 */
				public $persistence = true;

				/**
				 * @param RenaApp $app
				 */
				function __construct(RenaApp $app)
				{
								$this->redis = new Redis();

								if(!$this->persistence)
												$this->redis->connect($app->baseConfig->getConfig('host', 'redis', '127.0.0.1'), $app->baseConfig->getConfig('port', 'redis', 6379));
								else
												$this->redis->pconnect($app->baseConfig->getConfig('host', 'redis', '127.0.0.1'), $app->baseConfig->getConfig('port', 'redis', 6379));
				}

				/**
				 * Sets expiration time for cache key.
				 *
				 * @param string $key The key to uniquely identify the cached item
				 * @param integer $timeout A `strtotime()`-compatible string or a Unix timestamp.
				 *
				 * @return bool
				 */
				protected function expireAt($key, $timeout)
				{
								return $this->redis->expireAt($key, is_int($timeout) ? $timeout : strtotime($timeout));
				}

				/**
				 * Read value from the cache.
				 *
				 * @param string $key The key to uniquely identify the cached item
				 *
				 * @return mixed
				 */
				public function get($key)
				{
								return $this->redis->get($key);
				}

				/**
				 * Write value to the cache.
				 *
				 * @param string $key The key to uniquely identify the cached item
				 * @param mixed $value The value to be cached
				 * @param integer $timeout A strtotime() compatible cache time.
				 *
				 * @return bool
				 */
				public function set($key, $value, $timeout = 0)
				{
								$result = $this->redis->set($key, $value);

								if($timeout > 0)
								{
												return $result ? $this->expireAt($key, $timeout) : $result;
								}

								return $result;
				}

				/**
				 * Override value in the cache.
				 *
				 * @param string $key The key to uniquely identify the cached item
				 * @param mixed $value The value to be cached
				 * @param null|string $timeout A strtotime() compatible cache time.
				 *
				 * @return bool
				 */
				public function replace($key, $value, $timeout)
				{
								return $this->redis->set($key, $value, $timeout);
				}

				/**
				 * Delete value from the cache.
				 *
				 * @param string $key The key to uniquely identify the cached item
				 *
				 * @return bool
				 */
				public function delete($key)
				{
								return (boolean) $this->redis->del($key);
				}

				/**
				 * Performs an atomic increment operation on specified numeric cache item.
				 *
				 * Note that if the value of the specified key is *not* an integer, the increment
				 * operation will have no effect whatsoever. Redis chooses to not typecast values
				 * to integers when performing an atomic increment operation.
				 *
				 * @param string $key Key of numeric cache item to increment
				 * @param int $step
				 * @param int $timeout
				 *
				 * @return callable Function returning item's new value on successful increment, else `false`
				 */
				public function increment($key, $step = 1, $timeout = 0)
				{
								if($timeout)
								{
												self::expireAt($key, $timeout);
								}

								return $this->redis->incr($key, $step);
				}

				/**
				 * Performs an atomic decrement operation on specified numeric cache item.
				 *
				 * Note that if the value of the specified key is *not* an integer, the decrement
				 * operation will have no effect whatsoever. Redis chooses to not typecast values
				 * to integers when performing an atomic decrement operation.
				 *
				 * @param string $key Key of numeric cache item to decrement
				 * @param int $step Offset to decrement - defaults to 1
				 * @param int $timeout A strtotime() compatible cache time.
				 *
				 * @return Closure Function returning item's new value on successful decrement, else `false`
				 */
				public function decrement($key, $step = 1, $timeout = 0)
				{
								if($timeout)
								{
												self::expireAt($key, $timeout);
								}

								return $this->redis->decr($key, $step);
				}

				/**
				 * Clears user-space cache.
				 *
				 * @return bool|null
				 */
				public function flush()
				{
								$this->redis->flushdb();
				}
}