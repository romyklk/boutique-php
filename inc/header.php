<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>Boutique</title>

		<!-- Bootstrap4 CSS + Font Awesome -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
		<link rel="stylesheet" href="<?= PATH ?>css/styles.css">
		<style>body{padding-top:100px !important;}</style>
	</head>
	<body>
		<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
			<a class="navbar-brand" href="<?= PATH ?>index.php"><img src="img/logo.jpg" width="50px">Shop</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
			  <span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarCollapse">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item">
						<a class="nav-link" href="<?= PATH ?>index.php"><i class="fa fa-home"></i>Boutique <span class="sr-only">(current)</span></a>
					</li>				
					<?php if(!userConnecte()): ?>
					<li class="nav-item">
					  <a class="nav-link" href="<?= PATH ?>connexion.php">Connexion</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="<?= PATH ?>inscription.php">Inscription</a>
					</li>
					<?php else :  ?>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown1" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fa fa-user"></i> <?= $_SESSION['membre']['pseudo'] ?>
						</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdown1">
							<a class="dropdown-item" href="<?= PATH ?>profil.php">Profil</a>
							<a class="dropdown-item" href="<?= PATH ?>commandes.php">Commandes</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="<?= PATH ?>connexion.php?action=deco">Déconnexion</a>
						</div>
					</li>
					<?php endif; ?>
					
					<?php if(userAdmin()) : ?>
					<!-- Menu admin -->
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown2" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fa fa-cogs"></i> Gestion de la boutique</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdown2">
							<a class="dropdown-item" href="<?= PATH ?>admin/gestion_produits.php">Gestion des produits</a>
							<a class="dropdown-item" href="<?= PATH ?>admin/gestion_membres.php">Gestion des membres</a>
							<a class="dropdown-item" href="<?= PATH ?>admin/gestion_commandes.php">Gestion des commandes</a>
						</div>
					</li>
					<?php endif; ?>
					
					
					
					
					<li class="nav-item">
						<a class="nav-link" href="panier.php">
							<i class="fa fa-shopping-cart fa-2x"></i> 

							<?php if(isset($_SESSION['panier']) && quantitePdt () >0) : ?>
							<span class="badge badge-danger"><?= quantitePdt() ?></span>
							<?php endif; ?>

						</a>
					</li>
				</ul>
				
				
				<form class="form-inline mt-2 mt-md-0" action="index.php" method="post">
					<input class="form-control mr-sm-2" type="text" placeholder="rechercher" aria-label="rechercher" name="term" value="">
					<input class="btn btn-outline-success my-2 my-sm-0" type="submit" value="Rechercher" name="rechercher">
				</form>
				
				
				
				
				
			</div>
		</nav>
		<div class="container main-container">
		
		<?php 
		 if(isset($_SESSION['validation'])){
			 for($i = 0; $i < count($_SESSION['validation']); $i ++){
				echo '<div class="alert alert-success">' . $_SESSION['validation'][$i] . '</div>';
			 } 
			array_splice($_SESSION['validation'], 0, count($_SESSION['validation']));
		 }
		?>
		
		
		
		<!-- Fin de la partie header du site -->