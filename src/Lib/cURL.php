<?php

namespace ProjectRena\Lib;

use ProjectRena\RenaApp;

/**
 * Class cURL.
 */
class cURL
{
				/**
				 * @var Cache
					*/
    private $cache;
				private $app;

				function __construct(RenaApp $app)
				{
								$this->app = $app;
								$this->cache = $app->Cache;
				}

				/**
				 * @param $url
				 * @param int $cacheTime
				 *
				 * @return mixed|null
				 */
				public function getData($url, $cacheTime = 3600)
				{
								// Md5 the url
								$md5 = md5($url);

								// Get results if they are available in the cache already
								$result = $cacheTime > 0 ? $this->cache->get($md5) : null;

								// If there was no result, get the data
								if(!$result)
								{
												// Init curl
												$curl = curl_init();
												// Setup curl
												curl_setopt_array($curl, array(
													CURLOPT_USERAGENT      => $this->app->baseConfig->getConfig('userAgent', 'site', 'DataGetter from projectRena (karbowiak@gmail.com)'),
													CURLOPT_TIMEOUT        => 30,
													CURLOPT_POST           => false,
													CURLOPT_FORBID_REUSE   => false,
													CURLOPT_ENCODING       => '',
													CURLOPT_URL            => $url,
													CURLOPT_HTTPHEADER     => array('Connection: keep-alive', 'Keep-Alive: timeout=10, max=1000'),
													CURLOPT_RETURNTRANSFER => true,
													CURLOPT_FAILONERROR    => true,
												));

												// Get the data
												$result = curl_exec($curl);

												// Cache the data
												if($cacheTime > 0)
												{
																$this->cache->set($md5, $result, $cacheTime);
												}
								}

								// Return the data
								return $result;
				}

				/**
				 * @param string $url
				 * @param array $postData
				 * @param array $headers
				 *
				 * @return string
				 */
				public function sendData($url, $postData = array(), $headers = array())
				{
								// Define default headers
								if(empty($headers))
								{
												$headers = array('Connection: keep-alive', 'Keep-Alive: timeout=10, max=1000');
								}

								// Init curl
								$curl = curl_init();

								// Init postLine
								$postLine = '';

								// Populate the $postData
								if(!empty($postData))
								{
												foreach($postData as $key => $value)
												{
																$postLine .= $key . '=' . $value . '&';
												}
								}

								// Trim the last &
								rtrim($postLine, '&');

								curl_setopt($curl, CURLOPT_URL, $url);
								curl_setopt($curl, CURLOPT_USERAGENT, $this->app->baseConfig->getConfig('userAgent', 'site', 'DataGetter from projectRena (karbowiak@gmail.com)'));
								curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
								if(!empty($postData))
								{
												curl_setopt($curl, CURLOPT_POST, count($postData));
												curl_setopt($curl, CURLOPT_POSTFIELDS, $postLine);
								}

								curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
								curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
								curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

								$result = curl_exec($curl);

								curl_close($curl);

								return $result;
				}
}