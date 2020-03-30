<?php 
require_once('inc/init.php'); // ---> session et connexion BDD


// 1 : Récupérer et afficher tous les produits (SELECT * FROM produit)
$resultat = $pdo -> query("SELECT * FROM produit");
$produits = $resultat -> fetchAll();

// 2 : Récupérer et afficher toutes les catégories (SELECT DISTINCT categorie FROM produit ORDER BY categorie ASC)

				  // query car pas de données sensibles dans cette requete...
$resultat  = $pdo -> query("SELECT DISTINCT categorie FROM produit ORDER BY categorie ASC");
$categories = $resultat -> fetchAll();

// 3 : Récupérer et afficher les produits en fonction de la catégories (passée en paramètre d'URL)

if(isset($_GET['cat']) && !empty($_GET['cat'])  ){
	
	$resultat = $pdo -> prepare("SELECT * FROM produit WHERE categorie = :cat");
	$resultat -> bindParam(':cat', $_GET['cat'], PDO::PARAM_STR);
	$resultat -> execute();
	
	if($resultat -> rowCount() > 0){
		// Ok on a trouvé des produits correspondant à la catégorie passée en URL
		$produits = $resultat -> fetchAll();
	}
	else{
		header('location:index.php');
	}
}



// 4 : Récupérer et afficher les produits en fonction du/des termes de recherche... 


	//a : Vérifier que l'on a reçu du post
	
	if($_POST){
	//b : requete : 
			$term = '%' . $_POST['term'] . '%';
	
			$resultat = $pdo -> prepare("SELECT * FROM produit 
			WHERE description LIKE :term
			OR titre LIKE :term
			OR taille LIKE  :term
			OR couleur LIKE  :term
			OR categorie LIKE  :term");
			$resultat -> bindParam(':term', $term, PDO::PARAM_STR);
			$resultat -> execute();
			
			if($resultat -> rowCount() > 0){
				$_SESSION['validation'][]  = 'Résultat de votre recherche <b>' . $_POST['term'] . '</b> : ' . $resultat -> rowCOunt() . ' produit(s)';
			}
			else{
				$_SESSION['validation'][]  = 'Aucun résultat pour votre recherche <b>' . $_POST['term'] . '</b>';
			}
			
			$produits = $resultat -> fetchAll();
			
			
			
	}




// debug($produits);
// debug($categories);


require_once('inc/header.php');
?>

<!-- début de la partie contenu --> 
			<div class="row">
				<div class="col-sm-3">
					<div class="list-group">
						<p class="list-group-item text-center">CATEGORIES</p>
						<a href="index.php" class="list-group-item ">Toutes</a>
						
						<?php foreach($categories as $cat) : extract($cat) ?>
							<!-- On parcourt toutes les catégories ($categories) et pôur chacune on l'affiche dans le menu de catégorie -->
							<a href="?cat=<?= $categorie ?>" class="list-group-item "><?= ucfirst($categorie); ?></a>
						<?php endforeach; ?>
						
					</div>
				</div>
				<div class="col-sm-9">
					<div class="jumbotron">
						<h1>Bienvenue sur notre boutique !!</h1>
						<p>De nouveaux articles sont arrivés!</p>
					</div>
					<div class="row">
						
						
						
						
						
						
						
						
						<?php foreach($produits as $p) : extract($p) ?>
						<!-- debut vignette produit -->
						<div class="col-6 col-md-4">
							<div class="card mt-3 border">
								<div class="card-body">
									<div class="card-title text-center">
										<h6 style="height: 80px"><?= $titre ?></h6>
									</div>
									<p>
										<a href="fiche_produit.php?id=<?= $id_produit ?>">
										<img src="img/<?= $photo ?>" alt="" class="card-img img-fluid" >
											 
										</a>
									</p>
									<p class="text-center"><?= $prix ?> €
									</p>
									<p class="text-center">
										<a class="btn btn-primary" href="fiche_produit.php?id=<?= $id_produit ?>">Voir les détails &raquo; </a>
									</p>
								</div>
							</div>
						</div>
						<!-- fin vignette produit -->
						<?php endforeach; ?>
						
						
						
						
						
						
					</div>
				</div>
			</div>	





<?php 
include('inc/footer.php');
?>