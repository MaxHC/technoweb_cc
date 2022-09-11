<?php
/*
 * On indique que les chemins des fichiers qu'on inclut
 * seront relatifs au répertoire src.
 */
set_include_path("./src");

/* Inclusion des classes utilisées dans ce fichier */
require_once("Router.php");
require_once "view/View.php";
require_once "view/PrivateView.php";
require_once "control/Controller.php";
require_once "modele/Car.php";
require_once "modele/CarStorage.php";
require_once "modele/CarStorageMySQL.php";
require_once "modele/CarBuilder.php";
require_once "modele/AuthentificationManager.php";
require_once "modele/Account.php";
require_once "modele/AccountStorageMySQL.php";
require_once "modele/ImageStorageMySQL.php";
require_once "/users/21807030/private/mysql_config.php";

/*
 * Cette page est simplement le point d'arrivée de l'internaute
 * sur notre site. On se contente de créer un routeur
 * et de lancer son main.
 */
$router = new Router();

//setup to connect to DB
$dsn = "mysql:host=$MYSQL_HOST;port=$MYSQL_PORT;dbname=$MYSQL_DB;charset=utf8";

//create PDO instance
$bd = new PDO($dsn, $MYSQL_USER, $MYSQL_PASSWORD);
$bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$carStorageFile = new CarStorageMySQL($bd);
$accountStorage = new AccountStorageMySQL($bd);
$imageStorage = new ImageStorageMySQL($bd);
$auth = new AuthentificationManager($accountStorage);

$router->main($carStorageFile, $auth, $imageStorage);
?>