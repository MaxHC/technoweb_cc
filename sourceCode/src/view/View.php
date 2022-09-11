<?php

class View{

	protected $title, $content, $router, $feedback;

	public function __construct($router, $feedback){
		$this->router = $router;
		$this->feedback = "<p>".$feedback."</p>";
		$this->nav = "<nav style='grid-template-columns: 1fr 1fr 1fr 1fr;'>
			<a href='".$this->router->getMainURL()."'>Accueil</a>
			<a href='".$this->router->getMainURL()."/liste'>Liste de voitures</a>
			<a href='".$this->router->getMainURL()."/signup'>Connexion</a>
			<a href='".$this->router->getMainURL()."/informations'>A propos</a>
		</nav>";
	}

	public function render($cssPath = "../src/css/"){
		echo"<!DOCTYPE html>
		<html lang='fr'>
			<head>
				<title>".$this->title."</title>
				<meta charset='utf-8' />
				<link rel='stylesheet' href='".$cssPath."main.css' type='text/css'/>
			</head>
			<body>".$this->nav.$this->feedback."
				<h1>".$this->title."</h1>
				".$this->content."
			</body>
		</html>";
		$_SESSION["feedback"] = "";
	}

	public function makeInfoPage(){
		$this->title = "A Propos";
		$this->content = 
		"<h2>Groupe 20 : <br> Groupe 3A - 21807030 - MARTIN Maxence <br> Groupe 3A - 21805134 - MEYER Arthur</h2>
		<h3>Complements réalisé </h3>
		<ul>
			<li>Site responsive</li>
			<li>Objet illustré par une ou plusieurs images (sans barre de progression)</li>
			<li>Fonctionnalité resté connecté avec durée de validité modifiable par la constante <b>CONNECT_TIME</b> du controller (par defaut a 7j)</li>
		</ul>
		<h3>Répartition des taches </h3>
		<ul>
			<li>Maxence :<ul>
				<li>Gestion BDD</li>
				<li>Gestion voitures (création, modification, illustration ...)</li>
			</ul></li>
			<li>Arthur :<ul>
				<li>Gestion de l'authentification</li>
				<li>HTML/CSS</li>
				<li>Javascript</li>
			</ul></li>
		</ul>
		<h3>Choix de design</h3>
		<p>Nous avons utilisé le principe de MVCR et donc une implémentation des fonctionnalité qui est celle des TP sur les animaux. <br>
		Nous avons Stocké les images avec un noms unique liée a un objets (voiture) plutot qu'a un compte. De cette manière il est plus simple de recupérer les images lorsque l'on demande la page d'un objet. <br>
		La seule chose qu'il manque est la barre de progression d'upload des fichier car nous n'avons pas réussi a la mettre en place.</p>";
	}

	public function makeCarPage(Car $car, $id=""){
		$this->makeErrorPage("Vous devez être connecté pour voir cette page !");
	}

	public function makeUnknownCarPage(){
		$this->title = "Erreur !";
		$this->content = "Voiture inconnu !";
	}

	public function makeHomePage(){
		$this->title = "Bienvenue";
		$this->content = "Bienvenue sur ce site de voiture";
	}

	public function makeListPage($list){
		$carList = "<div id='list'>";
		foreach($list as $id => $car){
			$carList .= "<a href=".$this->router->getCarURL($id).">".$car->getMark()." ".$car->getModel()."</a>";
		}
		$carList .= "</div>";

		$this->title = "Liste de voiture";
		$this->content = $carList;
	}

	public function makeDebugPage($variable) {
		$this->title = 'Debug';
		$this->content = '<pre>'.htmlspecialchars(var_export($variable, true)).'</pre>';
	}

	public function makeCarCreationPage(CarBuilder $car){
		$this->makeErrorPage("Vous devez être conncté pour voir cette page !");
	}

	public function makeCarModificationPage(Car $car, $id){
		$this->makeErrorPage("Vous devez être conncté et avoir les droits pour voir cette page !");
	}

	public function makeErrorPage($error){
		$this->title = "Erreur";
		$this->content = $error;
	}

	public function makeCarDeletionPage($id){
		$this->makeErrorPage("Vous devez être conncté et avoir les droits pour voir cette page !");
	}

	public function displayCarCreationSuccess($id){
		$this->router->POSTredirect($this->router->getCarURL($id), "Voiture créé avec succès !");
	}

	public function displayCarDeletionSuccess(){
		$this->router->POSTredirect($this->router->getMainURL()."/liste", "Voiture supprimé avec succès !");
	}

	public function displayCarCreationFailure($feedback){
		$this->router->POSTredirect($this->router->getCarCreationURL(), $feedback);
	}

	public function displayConnectionSuccess(){
		$this->router->POSTredirect($this->router->getMainURL(), "Connexion réussi !");
	}

	public function displayConnectionFailure(){
		$this->router->POSTredirect($this->router->getConnexionURL(), "Nom d'utilisateur ou mot de passe incorrect");
	}

	public function displayAccountCreationSuccess(){
		$this->router->POSTredirect($this->router->getMainURL(), "Création du compte réussi !");
	}

	public function displayAccountCreationFailure(){
		$this->router->POSTredirect($this->router->getConnexionURL(), "Nom d'utilisateur indisponible");
	}

	public function makeSignupPage(){
		$this->title = "Connexion";
		$this->content = 
		"<div id='connectionForm'><form id='connection' action='".$this->router->getConnexionURL()."' method='POST'>
			<h2>Connexion</h2>
			<p class='error' id='formError'></p>
			<label> Nom d'utilisateur :<input type='text' name='username' id='username' /></label>
			<label> Mot de passe : <input type='password' name='password' id='password' /></label>
			<label> Rester connecté : <input type='checkbox' name='stayedConnect'></label>
			<button type='submit' name='connection' value='connection'>Connexion</button>
		</form>
		<form id='create' action='".$this->router->getCreateAccountURL()."' method='POST'>
			<h2>Création d'un compte</h2>
			<p class='error' id='createError'></p>
			<label>Nom d'utilisateur :<input type='text' name='username' id='createUsername' /></label>
			<label> Mot de passe : <input type='password' name='password' id='createPassword' /></label>
			<label> Confirmation du mot de passe : <input type='password' name='confirmPassword' id='confirmPassword' /></label>
			<button type='submit'>Créer un compte</button>
		</form></div>
		<script src='../src/js/validation.js'></script>
		<script>validateConnectionForm()</script>"; //TODO : add a double entry password to create account
	}

}

?>