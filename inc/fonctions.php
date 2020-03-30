<?php 


// fonctions pour afficher les debug : 

function debug($tab){
	echo '<div style="color: white; padding: 20px; font-weight: bold; background: #' . rand(111111, 999999) . '">';
	$trace = debug_backtrace(); // Un tableau multi avec tous les infos sur l'emplacement du code exécuté.
		echo 'Le debug a été demandé dans le fichier ' . $trace[0]['file'] . ' à  la ligne : ' . $trace[0]['line'] . '<hr/>';
		echo '<pre style="color:white">'; 
			print_r($tab);
		echo '</pre>'; 	
	echo '</div>'; 	
}

// Fonction pour tester si user est connecté 
function userConnecte(){
	if(isset($_SESSION['membre'])){
		return true;
	}
	else{
		return false; 
	}
}


// fonction pour tester si user est Admin 

function userAdmin(){
	
	if(userConnecte() && $_SESSION['membre']['statut'] == 1){
		return true;
	}
	else{
		return false; 
	}
}


// fonction créer le panier 
function createPanier(){
	
	if(!isset($_SESSION['panier'])){	
		$_SESSION['panier'] = array();
		$_SESSION['panier']['quantite'] = array();
		$_SESSION['panier']['id_produit'] = array();
		$_SESSION['panier']['titre'] = array();
		$_SESSION['panier']['prix'] = array();
		$_SESSION['panier']['photo'] = array();
	}
}

// fonction pour ajouter un produit au panier 

function ajoutPanier($quantite, $id_produit, $prix, $titre, $photo){
	createPanier();
	
	$position = array_search($id_produit, $_SESSION['panier']['id_produit']);
	if($position !== FALSE){
		// Si le produit ajouté existe déjà dans le panier, on ne veut pas ajouter une ligne, mais ajouter la nouvelle quantité
		$_SESSION['panier']['quantite'][$position] += $quantite;
	}
	else{
		$_SESSION['panier']['id_produit'][] = $id_produit;
		$_SESSION['panier']['quantite'][] = $quantite;
		$_SESSION['panier']['titre'][] = $titre;
		$_SESSION['panier']['prix'][] = $prix;
		$_SESSION['panier']['photo'][] = $photo;
	}
}




// Fonction pour compter le nombre de produit dans le panier 

function quantitePdt(){

	$quantite = 0;

	foreach($_SESSION['panier']['quantite'] as $q){
		$quantite += $q;
	}
	return $quantite;
}

// fonction pour calculer le montant total du panier 

function montantTotal(){
	$total = 0;

	if(isset($_SESSION['panier'])){
		for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++){
			$total += $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i];

		}
	}
	return $total;
}


// fonction pour retirer un produit du panier 
function retirerPanier($id_produit){

	$position = array_search($id_produit, $_SESSION['panier']['id_produit']);

	if($position !== FALSE){

		array_splice($_SESSION['panier']['id_produit'], $position, 1);
		array_splice($_SESSION['panier']['prix'], $position, 1);
		array_splice($_SESSION['panier']['quantite'], $position, 1);
		array_splice($_SESSION['panier']['titre'], $position, 1);
		array_splice($_SESSION['panier']['photo'], $position, 1);

		// Array_splice supprimer une entrée dans un array et ré-indexe l'ensemble du tableau
		
		return true;
	}
	else{
		return false;
	}
}








