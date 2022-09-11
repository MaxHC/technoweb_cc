<?php

class AccountStorageMySQL{
	
	private $bd;

	public function __construct($bd){
		$this->bd = $bd;
	}

	public function getUser($username, $password){
		$rq = "SELECT * FROM users WHERE username=:username";
		$stmt = $this->bd->prepare($rq);
		$data = array(
			':username' => $username,
		);

		$stmt->execute($data);
		$result = $stmt->fetch();

		if($result != null && password_verify($password, $result["password"])){
			return new Account($result["id"], $username, $password, $result["admin"]);
		}

		return null;
	}

	public function createUser($username, $password){
		$rq = "SELECT * FROM users WHERE username=:username";
		$stmt = $this->bd->prepare($rq);

		$data = array(
			':username' => $username,
		);

		$stmt->execute($data);
		$result = $stmt->fetch();

		if($result != null){
			return false;
		} else {
			$insert = "INSERT INTO users (username, password, admin) VALUES (:username, :password, false)";
			$stmt = $this->bd->prepare($insert);
			
			$data = array(
				':username' => $username,
				':password' => password_hash($password, PASSWORD_BCRYPT),
			);

			$stmt->execute($data);
		}

		return true;
	}

}

?>