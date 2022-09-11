<?php

require_once "view/View.php";

class Controller{

	private $carStorage, $view, $auth, $imageStorage;
	private const CONNECT_TIME = 7*60*60*24;//time in seconds : 1s*60*60*24 = 1min*60*24 = 1h*24 = 1d.

	function __construct($view, $carStorage, $auth, $imageStorage){
		$this->view = $view;
		$this->carStorage = $carStorage;
		$this->auth = $auth;
		$this->imageStorage = $imageStorage;
	}

	public function showInformation($id){
		if($id == false){
			$this->view->makeHomePage();
			$this->view->render("src/css/");
		}else {
			$car = $this->carStorage->read($id);
			$images = $this->imageStorage->read($id);
			if($car != null){
				$this->view->makeCarPage($car, $id, $images);
			} else {
				$this->view->makeUnknownCarPage();
			}
			$this->view->render();
		}
	}

	public function showList(){
		$this->view->makeListPage($this->carStorage->readAll());
		$this->view->render();	
	}

	public function showInfo(){
		$this->view->makeInfoPage();
		$this->view->render();	
	}

	public function saveNewCar(array $data){
		$carBuilder = new CarBuilder($data);
		$fileError = key_exists("files", $_FILES)?$this->uploadError():"";
		if($carBuilder->isValid() == true){
			
			if($fileError == ""){
				if(key_exists("currentNewCar", $_SESSION) && $fileError == ""){
					unset($_SESSION["currentNewCar"]);
				}

				$car = $carBuilder->createCar($this->auth->getUserID());
				$id = $this->carStorage->create($car);
				if(key_exists("files", $_FILES)){
					$this->uploadImages($id);
				}
				$this->view->displayCarCreationSuccess($id);
			} else {
				$_SESSION["currentNewCar"] = $carBuilder;
				$this->view->displayCarCreationFailure($fileError);
			}

		} else {
			$_SESSION["currentNewCar"] = $carBuilder;
			$this->view->displayCarCreationFailure("champ non valide !");
		}
	}

	public function uploadError(){
		$validExtension = ["jpg", "png", "jpeg", "gif"];
		$error = "";
		$toUpload = array();
		$files = $_FILES["files"];

		for($i = 0; $i<count($files["name"]); $i++){
			$fileName = $files["name"][$i];
			$exploded = explode(".", $fileName);
			$fileExtension = strtolower(end($exploded));

			if($fileName != ""){
				if(in_array($fileExtension, $validExtension)){
					if($files["error"][$i] != 0){
						$error .= $fileName." : une erreur est survenue, code d'erreur :".$files["error"][$i]." ";
					}
				} else {
					$error .= $fileName." : mauvaise extension ";
				}
			}
		}

		return $error;
	}

	public function uploadImages($id){
		$files = $_FILES["files"];
		for($i=0; $i<count($files["name"]); $i++){
			$fileName = $files["name"][$i];
			$exploded = explode(".", $fileName);
			$fileExtension = strtolower(end($exploded));

			if($fileName != ""){
				$uniqueName = $uniqueName = uniqid('', true);
				$newName = $uniqueName.".".$fileExtension;
				move_uploaded_file($files["tmp_name"][$i], '/users/21807030/www-dev/projet-inf5c-2022/upload/'.$newName);
				$this->imageStorage->addImage($newName, $id);
			}
		}
	}

	public function createNewCar(){
		$carBuilder = key_exists("currentNewCar", $_SESSION)?$_SESSION["currentNewCar"]:new CarBuilder(array());
		$this->view->makeCarCreationPage($carBuilder);
		$this->view->render();
	}

	public function askModifyCar($id){
		$car = $this->carStorage->read($id);
		
		if($car != null){
			$this->view->makeCarModificationPage($car, $id, $this->imageStorage->read($id));
			$this->view->render("../../src/css/");
		} else {
			$this->view->makeErrorPage("<h2>id non valide !<h2>");
			$this->view->render();
		}
	}

	public function modifyCar(array $data, $id){
		$carBuilder = new CarBuilder($data);
		$fileError = key_exists("files", $_FILES)?$this->uploadError():"";
		if($carBuilder->isValid() == true){
			if($fileError == ""){
				if(key_exists("files", $_FILES)){
					$this->uploadImages($id);
				}

				$images = $this->imageStorage->read($id);
				
				foreach($images as $image){
					$imageName = str_replace(".", "_", $image["name"]);
					if(key_exists($imageName, $_POST)){
						$this->imageStorage->delete($image["name"]);
						unlink("/users/21807030/www-dev/projet-inf5c-2022/upload/".$image["name"]);//remove from upload
					}
				}

				$car = $carBuilder->createCar($this->auth->getUserID());
				$this->carStorage->modify($car, $id);				
				$this->view->displayCarModificationSuccess($id);
			} else {
				$this->view->displayCarModificationFailure($id);
			}
		} else {
			$this->view->displayCarModificationFailure($id);
		}
	}

	public function askCarDeletion($id){
		$car = $this->carStorage->read($id);

		if($this->carStorage->read($id) != null){
			if($this->auth->isUserConnected() && $this->auth->isAdminConnected() || $this->auth->getUserID() == $car->getOwnerID()){
				$this->carStorage->delete($id);
				
				$images = $this->imageStorage->read($id);
				foreach($images as $image){
					unlink("/users/21807030/www-dev/projet-inf5c-2022/upload/".$image["name"]);
				}

				$this->imageStorage->deleteAll($id);
				$this->view->displayCarDeletionSuccess();
			} else {
				$this->view->makeErrorPage("Vous n'avez pas les droits pour supprimer cette voitures !");
				$this->view->render();
			}			
		} else {
			$this->view->makeErrorPage("<h2>id non valide !<h2>");
			$this->view->render();
		}
	}

	public function confirmDeletion($id){
		$this->view->makeCarDeletionPage($id);
		$this->view->render("../../src/css/");
	}

	public function signup(){
		if(key_exists("connection", $_POST)){
			if($_POST["connection"] == "deconnection"){
				$this->auth->disconnectUser();
				$this->view->displayDisconnectedUser();
			} else if($_POST["connection"] == "connection"){
				if($this->connexion()){
					if(key_exists("stayedConnect", $_POST)){
						setcookie("session", $_SESSION["user"]->getUsername()."/".$_SESSION["user"]->getPassword(), time() + self::CONNECT_TIME);
					}

					$this->view->displayConnectionSuccess();
				} else {
					$this->view->displayConnectionFailure();
				}
			}
		} else {
			$this->view->makeSignupPage();
			$this->view->render();
		}
	}

	public function signin(){
		if($this->auth->createUser($_POST["username"], $_POST["password"])){
			$this->connexion();
			$this->view->displayAccountCreationSuccess();
		} else {
			$this->view->displayAccountCreationFailure();
		}
	}

	public function connexion(){
		return $this->auth->connectUser($_POST["username"], $_POST["password"]);
	}

}

?>