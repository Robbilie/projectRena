<?php
namespace ProjectRena\Model\Core;

use ProjectRena\RenaApp;

class CoreBase implements \JsonSerializable {

	protected $app;
	protected $db;
	protected $config;

	protected $data;
	protected $location;

	function __construct(RenaApp $app, $data = array()) {
		$this->app = $app;
		$this->db = $this->app->Db;
		$this->config = $this->app->baseConfig;

		$this->data = $data;

		$this->init($this);
	}

	public function jsonSerialize() {
		return $this->data;
	}

	public function init ($cl) {
		if(!is_null($this->data) && is_array($this->data))
			foreach ($this->data as $key => $value)
				if(property_exists($cl, $key) && is_null($this->{$key}))
					$this->{$key} = $value;
	}

	public function RunAsNew () {

	}

	public function getData () {
		return $this->data;
	}

}
