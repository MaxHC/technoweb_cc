<?php

//require_once "view/View.php";
//require_once "control/Controller.php";

class Router{

	public function main($storage, $auth, $imageStorage){
		session_start();
		
		$auth->stayConnect();

		$feedback = key_exists("feedback", $_SESSION) ? $_SESSION["feedback"] : "";
		$_SESSION['feedback'] = "";

		$path = key_exists('PATH_INFO', $_SERVER)?substr($_SERVER['PATH_INFO'], 1):null;
		$id = explode("/", $path);
		
		if($auth->isUserConnected()){
			$view = new PrivateView($this, $feedback, $_SESSION["user"]);
		} else {
			$view = new View($this, $feedback);
		}

		$controller = new Controller($view, $storage, $auth, $imageStorage);

		if($id[0] != null){
			if($id[0] == "liste"){
				$controller->showList();
			} else if($id[0] == "nouveau"){
				$controller->createNewCar();
			} else if($id[0] == "sauverNouveau"){
				$controller->saveNewCar($_POST);
			} else if($id[0] == "signup"){
				$controller->signup();
			} else if($id[0] == "signin"){
				$controller->signin();
			} else if($id[0] == "informations"){
				$controller->showInfo();
			} else {
				if(count($id) > 1){
					if($id[1] == "confirmDeletion"){
						$controller->confirmDeletion($id[0]);
					} else if($id[1] == "delete"){
						$controller->askCarDeletion($id[0]);
					} else if($id[1] == "modify"){
						$controller->askModifyCar($id[0]);
					} else if($id[1] == "confirmModification"){
						$controller->modifyCar($_POST, $id[0]);
					}
				} else {
					$controller->showInformation($id[0]);
				}
			}
		} else {
			$controller->showInformation(false);
		}

	}

	public function getMainURL(){
		return "/projet-inf5c-2022/cars.php";
	}

	public function getCarURL($id){
		return $this->getMainURL()."/$id";
	}

	public function getCarCreationURL(){
		return $this->getMainURL()."/nouveau";
	}

	public function getCarSaveURL(){
		return $this->getMainURL()."/sauverNouveau";
	}

	public function getCarAskDeletionURL($id){
		return $this->getMainURL()."/$id/confirmDeletion";
	}

	public function getCarDeletionURL($id){
		return $this->getMainURL()."/$id/delete";
	}

	public function getConnexionURL(){
		return $this->getMainURL()."/signup";
	}

	public function getCreateAccountURL(){
		return $this->getMainURL()."/signin";
	}

	public function getModificationURL($id){
		return $this->getMainURL()."/$id/modify";
	}

	public function getCarConfirmModificationURL($id){
		return $this->getMainURL()."/$id/confirmModification";
	}

	public function POSTredirect($url, $feedback){
		$_SESSION["feedback"] = $feedback;
		header("Location: ".$url, true, 303);
	}

}

?>