<?php

class Account{

	private $id, $username, $password, $admin;

	public function __construct($id, $username, $password, $admin){
		$this->id = $id;
		$this->username = $username;
		$this->password = $password;
		$this->admin = $admin;
	}

	public function getID(){
		return $this->id;
	}

	public function getUsername(){
		return $this->username;
	}

	public function getPassword(){
		return $this->password;
	}

	public function getAdmin(){
		return $this->admin;
	}

}

?>