<?php 
require_once('inc/init.php');
// 1 : Accessibilité
if(!userConnecte()){
	header('location:connexion.php');
}

// 2 : récupérer les infos de l'utilisateur (session) 
extract($_SESSION['membre']);
require_once('inc/header.php');
?>
<div class="row">
	<div class="col-12">
		<h1>Profil de <?= $prenom ?> <?= $nom ?></h1>
	</div>
	<div class="col-6">
		<h4>Informations personnelles</h4>
		<ul>
			<li>Pseudo : <?= $pseudo ?></li>
			<li>Prenom : <?= $prenom ?></li>
			<li>Nom : <?= $nom ?></li>
			<li>Email : <?= $email ?></li>
			<li>Sexe : <?= ($civilite == 'm') ? 'Homme' : 'Femme'  ?></li>
			<?php 
			if($civilite == 'm'){
				echo 'Homme';
			}
			else{
				echo 'Femme'; 
			}
			?>
		</ul>
	</div>
	<div class="col-6">
		<h4>Adresse</h4>
		<?= $adresse ?><br/>
		<?= $code_postal ?> <?= $ville ?>

		<h4>Administrer mon profil</h4>
		<a href="" class="btn btn-success">Modifier mon profil</a>
		<a href="" class="btn btn-warning">Supprimer mon compte</a>
	</div>
</div>
<!-- info de l'utilisateur -->
<!-- Boutons modifier mon profil / supprimer mon profil -->
<?php 
require_once('inc/footer.php');
?>