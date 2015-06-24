<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

class CoreUser extends CoreBase {

	protected $id;
	protected $admin;
	protected $authtoken;

	protected $chars;

	// custom

	public function getChars() {
		if(is_null($this->chars)) {
			$this->chars = array();
			$charsRows = $this->db->query("SELECT * FROM easCharacters WHERE user = :user", array(":user" => $this->id));
			foreach ($charsRows as $charRow)
				array_push($this->chars, new CoreCharacter($this->app, $charRow));
		}
		return $this->chars;
	}

	public function isAdmin() {
		return $this->admin == 1;
	}

	// default

	public function getId () {
		return $this->id;
	}

	public function getAdmin () {
		return $this->admin;
	}

	public function getAuthToken () {
		return $this->authtoken;
	}

}