<?php

class PrivateView extends View{

	private $account;

	public function __construct($router, $feedback, $account){
		parent::__construct($router, $feedback);
		$this->account = $account;
		$this->nav = 
		"<nav style='grid-template-columns: 1fr 1fr 1fr 1fr 1fr;'>
			<a href='".$this->router->getMainURL()."'>Accueil</a>
			<a href='".$this->router->getMainURL()."/liste'>Liste de voitures</a>
			<a href='".$this->router->getMainURL()."/nouveau'>Ajouter une voiture</a>
			<a href='".$this->router->getMainURL()."/signup'>Connexion</a>
			<a href='".$this->router->getMainURL()."/informations'>A propos</a>
		</nav>";
	}

	public function makeSignupPage(){
		$this->title = htmlspecialchars($this->account->getUsername());
		if($this->account->getAdmin()){
			$this->content = "<p>Vous êtes administrateur</p>";
		} else {
			$this->content = "<p>Vous n'êtes pas administrateur</p>";
		}
		$this->content .= 
		"<form action='".$this->router->getMainURL()."/signup' method='POST'>
			<input type='submit' name='connection' value='deconnection'>
		</form>";
	}

	public function displayDisconnectedUser(){
		$this->router->POSTredirect($this->router->getConnexionURL(), "Déconnexion réussi !");
	}

	public function displayCarModificationSuccess($id){
		$this->router->POSTredirect($this->router->getCarURL($id), "Voiture modifié avec succès");
	}

	public function displayCarModificationFailure($id){
		$this->router->POSTredirect($this->router->getModificationURL($id), "Modification impossible !");
	}

	public function makeCarPage(Car $car, $id="", $images = ""){
		$this->title = "Page sur ".htmlspecialchars($car->getMark())." ".htmlspecialchars($car->getModel());
		$this->content = "<p>La ".htmlspecialchars($car->getMark())." ".htmlspecialchars($car->getModel())." est sortie en ".htmlspecialchars($car->getYear()).". Cette voiture est de couleur ".htmlspecialchars($car->getColor())."</p><div id='display'>";

		foreach($images as $image){
			$this->content .= "<img src='/projet-inf5c-2022/upload/".$image["name"]."' alt='".$this->title."'><br>";
		}

		if($this->account->getAdmin() || $this->account->getID() == $car->getOwnerID()){
			$this->content .= "<div><a href=".$this->router->getCarAskDeletionURL($id)."><button>Supprimer</button></a>";
			$this->content .= "<a href=".$this->router->getModificationURL($id)."><button>Modifier</button></a></div>";
		}

		$this->content .= "</div>";
	}

	public function makeCarCreationPage(CarBuilder $car){
		$array = $car->getData();
		$mark = $car->getMarkRef();
		$model = $car->getModelRef();
		$year = $car->getYearRef();
		$color = $car->getColorRef();
		$error = $car->getError();

		$valueMark = "";
		$valueModel = "";
		$valueYear = "";
		$valueColor = "";

		if(key_exists($mark, $array)){
			if($array[$mark] != null){
				$valueMark = htmlspecialchars($array[$mark]);
			}
		}
		if(key_exists($model, $array)){
			if($array[$model] != null){
				$valueModel = htmlspecialchars($array[$model]);
			}
		}
		if(key_exists($year, $array)){
			if($array[$year] != null){
				$valueYear = htmlspecialchars($array[$year]);
			}
		}
		if(key_exists($color, $array)){
			if($array[$color] != null){
				$valueColor = htmlspecialchars($array[$color]);
			}
		}

		$this->title = "Creation d'une nouvelle voiture";
		$this->content = 
		"<form id='form' enctype='multipart/form-data' action='".$this->router->getCarSaveURL()."' method=POST>
			<label>Marque : <m class='error' id='markError'>".$error["mark"]."</m><input type='text' name='$mark' value='$valueMark' id='mark' /></label>
			<label>Modèle : <m class='error' id='modelError'>".$error["model"]."</m><input type='text' name='$model' value='$valueModel' id='model' /></label>
			<label>Année de mise en circulation : <m class='error' id='yearError'>".$error["year"]."</m><input type='text' name='$year' value='$valueYear' id='year' /></label>
			<label>Couleur : <m class='error' id='colorError'>".$error["color"]."</m><input type='text' name='$color' value='$valueColor' id='color' /></label>
			<div id='uploadFile'>
				<label>Ajouter des images (maintenez ctrl ou maj pour en ajouter plusieurs)<input type='file' name='files[]' multiple='multiple' /></label>
			</div>
			<button type='submit'>Envoyer !</button>
		</form>";
	}

	public function makeCarModificationPage(Car $car, $id, $images=""){
		if(!$this->account->getAdmin() && $this->account->getID() != $car->getOwnerID()){
			$this->makeErrorPage("Vous n'avez pas les droit pour modifier cette voiture !");
			return null;
		}

		$mark = "CarBuilder"::MARK_REF;
		$model = "CarBuilder"::MODEL_REF;
		$year = "CarBuilder"::YEAR_REF;
		$color = "CarBuilder"::COLOR_REF;

		$valueMark = htmlspecialchars($car->getMark());
		$valueModel = htmlspecialchars($car->getModel());
		$valueYear = htmlspecialchars($car->getYear());
		$valueColor = htmlspecialchars($car->getColor());

		$this->title = "Modification de la voiture ".$mark." ".$model;
		$this->content = 
		"<form id='form' enctype='multipart/form-data' action='".$this->router->getCarConfirmModificationURL($id)."' method=POST>
			<label>Marque : <input type='text' name='$mark' value='$valueMark' id='mark' \></label>
			<label>Modèle : <input type='text' name='$model' value='$valueModel' id='model' \></label>
			<label>Année de mise en circulation : <input type='text' name='$year' value='$valueYear' id='year' \></label>
			<label>Couleur : <input type='text' name='$color' value='$valueColor' id='color' \></label>";

		foreach($images as $image){
			$this->content .= 
			"<br><label>Supprimer l'image : <img style='width: 50px;' src='/projet-inf5c-2022/upload/".$image["name"]."' alt='".$this->title."'><input type='checkbox' name='".$image["name"]."'></label>";
		}

		$this->content .= 
			"<div id='uploadFile'>
				<label>Ajouter des images (maintenez ctrl ou maj pour en ajouter plusieurs)<input type='file' name='files[]' multiple='multiple' /></label>
			</div>
			<button type='submit'>Envoyer !</button>
		</form>";
	}

	public function makeCarDeletionPage($id){
		if($this->account->getAdmin() || $this->account->getID() == $car->getOwnerID()){
			$this->title = "Suppression";
			$this->content = 
			"<h2>Confirmer la suppression ?</h2>
			<form action='".$this->router->getCarDeletionURL($id)."' method='POST'>
				<button type='submit'>Confirmer</button>
			</form>";
		} else {
			$this->makeErrorPage("Vous n'avez pas les droits pour voir cette page !");
		}
	}

}

?>