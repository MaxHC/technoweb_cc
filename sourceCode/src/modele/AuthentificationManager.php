<?php

class AuthentificationManager{

	private $accountStorage;

	public function __construct($accountStorage){
		$this->accountStorage = $accountStorage;
	}

	public function stayConnect(){
		if(key_exists("session", $_COOKIE)){
			$login = explode("/", $_COOKIE["session"]);
			if(count($login) > 1){
				$this->connectUser($login[0], $login[1]);
			}
		}
	}

	public function connectUser($username, $password){
		$account = $this->accountStorage->getUser($username, $password);
		if($account != null){
			$_SESSION["user"] = $account;
			return true;
		}

		return false;
	}

	public function isUserConnected(){
		return key_exists("user", $_SESSION);
	}

	public function isAdminConnected(){
		return $_SESSION["user"]->getAdmin();
	}

	public function getUserName(){
		return $_SESSION["user"]->getUsername();
	}

	public function getUserID(){
		return $_SESSION["user"]->getID();
	}

	public function disconnectUser(){
		unset($_SESSION["user"]);
		unset($_COOKIE["session"]);
		setcookie("session", '', 1);
	}

	public function createUser($username, $password){
		return $this->accountStorage->createUser($username, $password);
	}

}

?>