<?php 
require_once('../inc/init.php');


//Accessibilite
if(!userAdmin()){
	header('location:' . PATH . 'index.php');
}

// recupérer les infos du formulaire et insérer/modifier le produit
if($_POST){
	debug($_POST);
	debug($_FILES);
	
	
	// Verification des infos saisies dans le formulaire
	// taille - type - ref unique - 
	
	
	
	// Les traitements sur l'image 
	
	if(!empty($_FILES['photo']['name'])){
		// Si une image a été uploadée
		// Renommer 	
		$new_nom = 'photo_' . time() . '_' . $_POST['reference'] . '_' . rand(1, 9999) . $_FILES['photo']['name'];
		// tshirt.jpg  ===> 'photo_1500000000_ref5643_4568_tshirt.jpg
		
		// vérifier le type 
		if( 
			$_FILES['photo']['type'] !== 'image/png' &&  
			$_FILES['photo']['type'] !== 'image/gif' && 
			$_FILES['photo']['type'] !== 'image/jpeg'  
		){
			$error .= '<div class="alert alert-danger">Veuillez choisir un fichier GIF, PNG ou JPG</div>';
		}
		
		// vérifier la taille 
		if($_FILES['photo']['size'] > 1000000){
			$error .= '<div class="alert alert-danger">Veuillez choisir un fichier image de 1Mo maximum</div>';
		}
	}
	elseif(isset($_POST['photo_actuelle'])){
		$new_nom = $_POST['photo_actuelle'];
	}
	else{
		// pas de photo uploadée 
		$new_nom = 'default.jpg';
	}
	// On ressort forcement de ce if/elseif/else avec un new_nom qui contient un nom de photo : La nouvelle photo ou la photo_actuelle ou la photo default
	
	if(empty($error)){
		// modifier le produit dans la BDD
		if(isset($_POST['id_produit'])){
			// Signifie qu'on modifie un produit
			$resultat = $pdo -> prepare("UPDATE produit SET reference = :reference, categorie = :categorie, titre = :titre, description = :description, public = :public, couleur = :couleur, taille = :taille, photo = :photo, prix = :prix, stock = :stock WHERE id_produit = :id");
			
			$resultat -> bindParam(':id', $_POST['id_produit'], PDO::PARAM_INT );
		}
		else{
			// insérer le produit dans la BDD : 
			$resultat = $pdo -> prepare("INSERT INTO produit (reference, categorie, titre, description, public, couleur, taille, photo, prix, stock)  VALUES (:reference, :categorie, :titre, :description, :public, :couleur, :taille, :photo, :prix, :stock) ");
		}
		
		//str 
		$resultat -> bindParam(':reference', $_POST['reference'], PDO::PARAM_STR);
		$resultat -> bindParam(':categorie', $_POST['categorie'], PDO::PARAM_STR);
		$resultat -> bindParam(':description', $_POST['description'], PDO::PARAM_STR);
		$resultat -> bindParam(':titre', $_POST['titre'], PDO::PARAM_STR);
		$resultat -> bindParam(':public', $_POST['public'], PDO::PARAM_STR);
		$resultat -> bindParam(':couleur', $_POST['couleur'], PDO::PARAM_STR);
		$resultat -> bindParam(':taille', $_POST['taille'], PDO::PARAM_STR);
		$resultat -> bindParam(':photo', $new_nom, PDO::PARAM_STR);
		$resultat -> bindParam(':prix', $_POST['prix'], PDO::PARAM_STR);
	
		//INT
		$resultat -> bindParam('stock', $_POST['stock'], PDO::PARAM_INT );
		
		if($resultat -> execute()){
			
			if(!empty($_FILES['photo']['name'])){
				copy($_FILES['photo']['tmp_name'],  __DIR__ . '/../img/' . $new_nom);
				
				if(isset($_POST['photo_actuelle']) && file_exists(__DIR__ . '/../img/' . $_POST['photo_actuelle']) && $_POST['photo_actuelle'] != 'default.jpg'){
					unlink(__DIR__ . '/../img/' . $_POST['photo_actuelle']);
					// On supprime l'ancienne image si elle existe
				}
			}
			
			if(isset($_POST['id_produit'])){
				$_SESSION['validation'][] = 'Le produit a bien été modifié !';
			}
			else{
				$_SESSION['validation'][] = 'Le produit a bien été ajouté !';
			}
			header('location:' . PATH . 'admin/gestion_produits.php');
			exit;
		}	
	}
}
	
	
// Recupérer les infos du produit à modifier (s'il y en a un)

if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
	// S'il y a un id dans l'URL, non vide et bien numéric, alors on peut commencer à envisager de récupérer les infos du produit à modifier. 
	
	$resultat = $pdo -> prepare("SELECT * FROM produit WHERE id_produit = :id");
	$resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
	$resultat -> execute();
	
	if($resultat -> rowCount() > 0){
		$produit_a_modifier = $resultat -> fetch();
	}
	else{
		header('location:' . PATH . 'admin/gestion_produits.php');
	}
}	


$reference = ''; $categorie = ''; $public = ''; $description = ''; $couleur = ''; $taille = ''; $photo = ''; $titre = ''; $prix = ''; $stock =''; 
$action = 'Ajouter';

if($_POST){
	extract($_POST);
	$photo = $_FILES['photo']['name'];
}
elseif(isset($produit_a_modifier)){
	extract($produit_a_modifier);
	$action = 'Modifier';
}
	
	
	
require_once('../inc/header.php');	
?>
<h1><?= $action ?> un produit</h1>
<?= $error ?>
<form method="post" action="" enctype="multipart/form-data">
	
	<div class="row">
		<div class="col-6">
			<div class="form-group">
				<label>Référence</label>
				<input type="text" name="reference" class="form-control" value="<?= $reference ?>" />
			</div>
			
			<div class="form-group">
				<label>Categorie</label>
				<input type="text" name="categorie" class="form-control" value="<?= $categorie ?>"/>
			</div>
			
			<div class="form-group">
				<label>Description</label>
				<textarea rows="8" name="description" class="form-control"><?= $description ?></textarea>
			</div>
		
			<div class="form-group">
				<label>Titre</label>
				<input type="text" name="titre" class="form-control" value="<?= $titre ?>"/>
			</div>
		</div>
		<div class="col-6">
			<div class="form-group">
				<label>Couleur</label>
				<input type="text" name="couleur" class="form-control" value="<?= $couleur ?>"/>
			</div>

			<div class="form-group">
				<label>Taille</label>
				<input type="text" name="taille" class="form-control" value="<?= $taille ?>"/>
			</div>
			
			<?php if(isset($produit_a_modifier)) : ?>
				<img src="<?= PATH ?>img/<?= $photo ?>" width="160px"/><br>
				<input type="hidden" name="photo_actuelle" value="<?= $photo ?>" />
				<input type="hidden" name="id_produit"  value="<?= $produit_a_modifier['id_produit'] ?>"/>
			<?php endif; ?>
			
			<div class="form-group">
				<label>Photo</label>
				<input type="file" name="photo" class="form-control"/>
			</div>
			
			<div class="form-group">
				<label>Public</label>
				<select name="public" class="form-control">
					<option value="m" <?= ($public == 'm') ? 'selected' : '' ?>>Homme</option>
					<option value="f" <?= ($public == 'f') ? 'selected' : '' ?>>Femme</option>
					<option value="mixte" <?= ($public == 'mixte') ? 'selected' : '' ?>>Mixte</option>
					<option value="enfant" <?= ($public == 'enfant') ? 'selected' : '' ?>>Enfant</option>
				</select>
			</div>
			
			<div class="form-group">
				<label>Prix</label>
				<input type="text" name="prix" class="form-control" value="<?= $prix ?>" />
			</div>
			
			<div class="form-group">
				<label>Stock</label>
				<input type="text" name="stock" class="form-control" value="<?= $stock ?>"/>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<input type="submit" class="btn btn-success col-12" value="<?= $action ?>"/>
	</div>
</form>


<?php 
require('../inc/footer.php');
?>