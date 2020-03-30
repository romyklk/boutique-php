<?php 
require_once('inc/init.php');

// traitement pour la deconnexion 
if(isset($_GET['action']) && $_GET['action'] == 'deco'){
	unset($_SESSION['membre']);
	header('location:index.php');
}

// accessibilité
if(userConnecte()){
	// si l'utilisateur est connecté, il n'a rien à faire ici, donc on le redirige...
	header('location:index.php');
}


// Traitement pour la connexion
if($_POST){
	// Récupérer les données du formulaire
	//debug($_POST);
	
	// Vérifier que l'utilisateur existe bien 
	$resultat = $pdo -> prepare("SELECT * FROM membre WHERE pseudo = :pseudo ");
	$resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
	$resultat -> execute();
	
	if($resultat -> rowCount() > 0){
		// signifie que le pseudo existe en BDD
		
		$membre = $resultat -> fetch();
		//debug($membre);
		
		$password = md5($_POST['mdp']); // fonction de hashage
		// Vérifier que le MDP saisi est le bon (crypté)
		if($password == $membre['mdp']){
			// Signifie que le membre a bien saisi le bon MDP
			
			// Stocker les infos du membre dans la session 
			$_SESSION['membre'] = $membre; 
			//debug($_SESSION);

			// Redirection vers accueil (index.php) 
			header('location:index.php');
		}
		else{
			$error .= '<div class="alert alert-danger">Mauvais mdp</div>';
		}
	}
	else{
		$error .= '<div class="alert alert-danger">Aucun compte existant</div>';
	}
}
require_once('inc/header.php');
?>
<h1>Connexion</h1>

	<?= $error ?> <!---------->
	<form method="post" action="" class="">
	
		<div class="form-group">
			<label>Pseudo : </label>
			<input type="text" name="pseudo" class="form-control" />
		</div>
		
		<div class="form-group">
			<label>Mot de passe : </label>
			<input type="password" name="mdp" class="form-control" />
		</div>
	
		<div class="form-group">
			<input type="submit" class="btn btn-success" value="Connexion"/>
		</div>
	</form>
<?php 
require_once('inc/footer.php');
?>