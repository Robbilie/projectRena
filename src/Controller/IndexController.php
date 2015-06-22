<?php

namespace ProjectRena\Controller;

use ProjectRena\RenaApp;

/**
 * Class IndexController
 *
 * @package ProjectRena\Controller
 */
class IndexController
{

				/**
				 * @var RenaApp
				 */
				protected $app;

				/**
				 * @param RenaApp $app
				 */
				function __construct(RenaApp $app)
				{
								$this->app = $app;
				}

				/**
				 *
				 */
				public function index()
				{
								$this->app->render('index.twig');
				}
}
