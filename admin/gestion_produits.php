<?php
require_once('../inc/init.php');

//0 : Accessibilité 
if(!userAdmin()){
	header('location:' . PATH . 'index.php');
}

//traitements pour supprimer un produit : 


if(isset($_GET['action']) && $_GET['action'] == 'supprimer' ){
	// Cela signifie qu'une action de suppression est demandée
	if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
		// cela signifie qu'il y a bien un id à supprimer dans l'URL... 
		$resultat = $pdo -> prepare("SELECT * FROM produit WHERE id_produit = :id");
		$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
		$resultat -> execute();
		$produit = $resultat -> fetch(); // pour récupérer le nom de la ou de(s) photo(s)
		
		if($resultat -> rowCount() > 0){
			// cela signifie que le produit existe bien
			$resultat = $pdo -> exec("DELETE FROM produit WHERE id_produit = $_GET[id]");
			if($resultat){
				// Signifie que tout est ok, la requete a bien fonctionner
				
				// supprimer la ou les photo(s) du produit
				$chemin_photo = __DIR__ . '/../img/' . $produit['photo'];
				if(file_exists($chemin_photo) && $produit['photo'] != 'default.jpg'){
					unlink($chemin_photo); // supprimer un fichier du server
				}
				// message de validation
				// redirection
				$_SESSION['validation'][] = 'Le produit <b>id' . $_GET['id'] . '</b> a boien été supprimé !'; 
				header('location:' . PATH . 'admin/gestion_produits.php');
				exit;
			}
		}
		else{
			$_SESSION['validation'][] = 'Le produit <b>id' . $_GET['id'] . '</b> n\'existe pas !'; 
			header('location:' . PATH . 'admin/gestion_produits.php');
			exit;
		}
	}
	else{
		header('location:' . PATH . 'admin/gestion_produits.php');
	}
}






//1 : Récupérer tous les produits 

$resultat = $pdo -> query("SELECT * FROM produit");
$produits = $resultat -> fetchAll();

//2 : Afficher les produits dans un debug 
//debug($produits);

//3 : Afficher les produits dans un tableau HTML (id/photo/reference/titre/categorie/prix/stock)




require_once('../inc/header.php');
// ../css/styles.css
?>

<h1>Gestion des produits</h1>

<a href="formulaire_produit.php" class="btn btn-primary m-2">AJOUTER UN PRODUIT</a>

<table class="table table-dark table-hover m-2">
	<thead>
		<th>Photo</th>
		<th>Id du Produit</th>
		<th>Référence</th>
		<th>Titre</th>
		<th>Catégorie</th>
		<th>Prix</th>
		<th>Stock</th>
		<th colspan="3">Action</th>
	</thead>
	<tbody>

		<?php foreach($produits as $p) : extract($p)?>
		<tr title="<?= $description ?> - Public : <?= $public ?>">
			<td><img src="<?= PATH ?>img/<?= $photo ?>"  height="60px"/></td>
			<td><?= $id_produit ?></td>
			<td><?= $reference ?></td>
			<td><?= $titre ?></td>
			<td><?= $categorie ?></td>
			<td><?= $prix ?></td>
			<td>
			<?php if($stock == 0) : ?>
				<b class="text-danger">-- RUPTURE --</b>
			<?php elseif($stock < 10) : ?>
				<b class="text-warning"><?= $stock ?></b>
			<?php else :?>
				<b class="text-success"><?= $stock ?></b>
			<?php endif; ?>
			</td>
			
			<td><a href="" title="Voir le produit"> <i class="fas fa-eye text-primary"></i> </a></td>
			
			
			<td><a href="formulaire_produit.php?id=<?= $id_produit ?>" title="Modifier le produit"> <i class="fas fa-edit text-warning"></i> </a></td>
			
			
			<td><a href="?action=supprimer&id=<?= $id_produit ?>" title="Supprimer le produit" onclick="return confirm('Etes-vous certain de vouloir supprimer ce produit ?')"> <i class="fas fa-trash-alt text-danger"></i> </a></td>

			
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>






<?php
require_once('../inc/footer.php');
?>
