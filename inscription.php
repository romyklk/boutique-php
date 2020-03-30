<?php 
require_once('inc/init.php');

if($_POST){

	// Récupérer les données 
	debug($_POST);
	 
	// Vérifier l'intégrité des données (150 lignes)

	
	// vérification du pseudo 
	if(empty($_POST['pseudo'])){
		$error .= '<div class="alert alert-danger">Veuillez renseigner un pseudo</div>';
	}
	else{
		//Expressions régulières ou REGEX   :   \^[a-zA-Z0-9_-.]{3,20}$\ 
		$verif = preg_match('#^[a-zA-Z0-9._-]{3,20}$#', $_POST['pseudo'] ); // true / False
		if(!$verif){
			// le pseudo ne correspond à ce que j'attend
			$error .= '<div class="alert alert-danger">Veuillez renseigner un pseudo de 3 à 20 caractères, composé de lettre, de chiffre, de -, _, .</div>';
		}
	}
	
	// vérification du MDP
	if(empty($_POST['mdp'])){
		$error .= '<div class="alert alert-danger">Veuillez renseigner un mot de passe</div>';
	}
	else{
		$verif = preg_match('#^(?=.*[0-9])(?=.*[A-Z]).{8,20}$#', $_POST['mdp'] );
		if(!$verif){
			// le pseudo ne correspond à ce que j'attend
			$error .= '<div class="alert alert-danger">Veuillez renseigner un mot de passe avec un chiffre et une lettre en maj et mini 8 caracteres</div>';
		}
	}
	
	
	// vérification du email
	if(empty($_POST['email'])){
		$error .= '<div class="alert alert-danger">Veuillez renseigner un email</div>';
	}
	else{
		if(  !  filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
			$error .= '<div class="alert alert-danger">Veuillez renseigner un email valide</div>';
		}
	}
	

	// vérification du code postal= (regex :  '#^[0-9]{5,5}$#'   ) 	
	if(empty($_POST['codepostal'])){
		$error .= '<div class="alert alert-danger">Veuillez renseigner un code postal</div>';
	}
	else{
		if(!is_numeric($_POST['codepostal']) || mb_strlen($_POST['codepostal']) != 5){
			$error .= '<div class="alert alert-danger">Veuillez renseigner un code postal valide</div>';
		}
	}

	
	// remettre dans les champs les données déjà saisies
	
	if(empty($error)){
		// TOUT EST OK (en terme de vérification des champs
		
		$resultat = $pdo -> prepare("SELECT * FROM membre where pseudo = :pseudo");
		$resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
		$resultat -> execute();
		
		$resultatEmail = $pdo -> prepare("SELECT * FROM membre where email = :email");
		$resultatEmail -> bindParam(':email', $_POST['email'], PDO::PARAM_STR);
		$resultatEmail -> execute();
		
		if($resultat -> rowCount() > 0){
			// Cela signifie que le pseudo existe déjà en BDD...
			$error .= '<div class="alert alert-danger">Le pseudo <b>' . $_POST['pseudo'] . ' </b>n\'est pas disponible. Veuillez choisir un autre pseudo.</div>';	
		}
		elseif($resultatEmail -> rowCount() > 0){
			// Cela signifie que le email existe déjà en BDD...
			$error .= '<div class="alert alert-danger">Cet email est déjà utilisé. Avez-vous oublié votre MDP : <b><a href="">Mot de passe oublié ?</a></b></div>';
		}
		else{
			// Ok on insert le membre en BDD : 
			
			$resultat = $pdo -> prepare("INSERT INTO membre (pseudo, prenom, nom, mdp, email, civilite, ville, code_postal, adresse, statut) VALUES (:pseudo, :prenom, :nom, :mdp, :email, :civilite, :ville, :code_postal, :adresse, '0')");
			
			//STR
			$resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
			$resultat -> bindParam(':prenom', $_POST['prenom'], PDO::PARAM_STR);
			$resultat -> bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
			
			$mdp_crypte = md5($_POST['mdp']);
			$resultat -> bindParam(':mdp', $mdp_crypte, PDO::PARAM_STR);
			
			$resultat -> bindParam(':email',$_POST['email'] , PDO::PARAM_STR);
			$resultat -> bindParam(':civilite', $_POST['civilite'], PDO::PARAM_STR);
			$resultat -> bindParam(':ville', $_POST['ville'], PDO::PARAM_STR);
			$resultat -> bindParam(':adresse', $_POST['adresse'], PDO::PARAM_STR);
			
			// INT
			$resultat -> bindParam(':code_postal', $_POST['codepostal'], PDO::PARAM_INT);
			
			if($resultat -> execute()){	
				
				$_SESSION['validation'][] = 'Félicitations vous êtes inscris... veuillez vous connecter';
				// redirection vers accueil/connexion
				header('location:connexion.php');
				exit; 
			}
		}
	}
}

require_once('inc/header.php');
?>

<h1>Inscription</h1>


<!-- ATTENTION --><?= $error ?><!-- ATTENTION -->

<form method="post" action="">

	<div class="form-group">
		<label>Pseudo</label>
		<input type="text" name="pseudo" class="form-control" value="<?php if(isset($_POST['pseudo'])){echo $_POST['pseudo'];} ?>"/>
	</div>
	<div class="form-group">
		<label>Mot de passe</label>
		<input type="password" name="mdp"  class="form-control"/>
	</div>
	<div class="form-group">
		<label>Prénom</label>
		<input type="text" name="prenom"  class="form-control" value="<?php if(isset($_POST['prenom'])){echo $_POST['prenom'];} ?>"/>
	</div>
	<div class="form-group">
		<label>Nom</label>
		<input type="text" name="nom"  class="form-control" value="<?php if(isset($_POST['nom'])){echo $_POST['nom'];} ?>"/>
	</div>
	<div class="form-group">
		<label>Email</label>
		<input type="text" name="email"  class="form-control" value="<?php if(isset($_POST['email'])){echo $_POST['email'];} ?>"/>
	</div>
	<div class="form-group">
		<label>Civilite</label>
		<select name="civilite"  class="form-control">
			
			<option 
				<?php 
				if(isset($_POST['civilite']) && $_POST['civilite'] == 'm'){
					echo 'selected';
				} 
				?> 
				value="m">Homme
			</option>

			<option <?php if(isset($_POST['civilite']) && $_POST['civilite'] == 'f'){echo 'selected';} ?> value="f">Femme</option>
			
			
		</select>
	</div>
	<div class="form-group">
		<label>Ville</label>
		<input type="text" name="ville"  class="form-control" value="<?php if(isset($_POST['ville'])){echo $_POST['ville'];} ?>"/>
	</div>
	<div class="form-group">
		<label>Code Postal</label>
		<input type="text" name="codepostal"  class="form-control" value="<?php if(isset($_POST['codepostal'])){echo $_POST['codepostal'];} ?>"/>
	</div>
	<div class="form-group">
		<label>Adresse</label>
		<input type="text" name="adresse"  class="form-control" value="<?php if(isset($_POST['adresse'])){echo $_POST['adresse'];} ?>"/>
	</div>
	<div class="form-group">
		<input type="submit" class="btn btn-secondary" value="Inscription" />
	</div>
</form>




<?php 
require_once('inc/footer.php');
?>