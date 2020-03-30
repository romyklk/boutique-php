<?php 

// OUVERTURE DE SESSION 
session_start();



// CONNEXION A LA BDD 
$pdo = new PDO('mysql:host=localhost;dbname=boutique','root','root', array(
	PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,	
	PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
));

// VARIABLES 
$error = ''; 
$page = ''; 

// CHEMIN ABSOLU DU SITE 
define('PATH', '/PHP COURS/Boutique/');  // racine du site, à partir de HTDocs

// FONCTIONS
include('fonctions.php');

// FONCTIONS
createPanier();

