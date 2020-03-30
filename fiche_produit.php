<?php 
require_once('inc/init.php');




// Récupérer et afficher les infos du produit (Si id ok)

if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
	// Il y a bien un id dans url
	$resultat = $pdo -> prepare("SELECT * FROM produit WHERE id_produit = :id");
	$resultat -> execute(array(':id' => $_GET['id']));
	
	if($resultat -> rowCount() > 0){
		// Signifie que le produit existe bien. 
		$produit = $resultat -> fetch();
		extract($produit);
		//debug($produit);
	}
	else{
		header('location:' . PATH . 'index.php');
	}
}
else{
	header('location:' . PATH . 'index.php');
}


// Ajouter au panier 
//debug($_SESSION);

if(isset($_POST['ajout'])){
	// Si qqun a cliqué sur le bouton d'ajout au panier. 
	
	if($_POST['quantite'] != 0){
		ajoutPanier($_POST['quantite'], $id_produit, $prix, $titre, $photo);
		$_SESSION['validation'][] = 'Le produit <b>' . $titre . '</b> a bien été ajouté au panier, en ' . $_POST['quantite'] . ' exemplaire(s)';
		header('location:' . PATH . 'fiche_produit.php?id=' . $id_produit);
		exit;
	}
}








// ajouter les commentaires/note 


require_once('inc/header.php');
?>
<div class="row">
      <div class="col-lg-12">
	  
		<h1 class="my-4"><?=  $titre ?></h1>
		
        <div class="card mt-4">
          <img class="card-img-top img-fluid" src="<?= PATH ?>img/<?= $photo ?>" alt="">
          <div class="card-body">
            <h3 class="card-title"><?=  $titre ?></h3>
			
            <h4><?=  number_format($prix, 2, ',', '&nbsp;') ?>€</h4>
			
            <p class="card-text"><?=  $description ?></p>
            <span class="text-warning">&#9733; &#9733; &#9733; &#9733; &#9734;</span>
            4.0 stars
          </div>
		  <div class="card-body">
          <h3 class="card-title">Acheter</h3>
		  <?php if($stock > 0) : ?>	  
		  <form method="post" action="" class="col-8">		
				<div class="form-group row">
					<label>Quantité</label>
					<select name="quantite" class="col-2 m-1 form-control">
						<?php for($i = 1; $i <= $stock && $i <= 5; $i ++) : ?>
							<option><?= $i ?></option>
						<?php endfor; ?>
					</select>
					<input type="submit" class="col-5 btn btn-success" name="ajout" value="Acheter"/>
				</div>
			</form>
			<?php else :  ?>
			<em>Produit victime de son succès. Indisponible actuellement. </em>
			<?php endif; ?>
		  </div>
		  
        </div>
        <!-- /.card -->
        <div class="card card-outline-secondary my-4">

          <div class="card-header">
            Product Reviews
          </div>
          <div class="card-body">
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Omnis et enim aperiam inventore, similique necessitatibus neque non! Doloribus, modi sapiente laboriosam aperiam fugiat laborum. Sequi mollitia, necessitatibus quae sint natus.</p>
            <small class="text-muted">Posted by Anonymous on 3/1/17</small>
            <hr>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Omnis et enim aperiam inventore, similique necessitatibus neque non! Doloribus, modi sapiente laboriosam aperiam fugiat laborum. Sequi mollitia, necessitatibus quae sint natus.</p>
            <small class="text-muted">Posted by Anonymous on 3/1/17</small>
            <hr>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Omnis et enim aperiam inventore, similique necessitatibus neque non! Doloribus, modi sapiente laboriosam aperiam fugiat laborum. Sequi mollitia, necessitatibus quae sint natus.</p>
            <small class="text-muted">Posted by Anonymous on 3/1/17</small>
            <hr>
          </div>
        </div>
        <!-- /.card -->
		<div class="card card-outline-secondary my-4">
			<div class="card-header">
				Laisser un commentaire
			</div>
			<form method="" action="" class="col-8">	
				
				<div class="form-group">
					<label>Note /5</label>
					<select name="note" class="form-control">
						<option>1</option>
						<option>2</option>
						<option>3</option>
						<option>4</option>
						<option>5</option>
					</select>
				</div>
				<div class="form-group">
					<input type="text" name="comment" class="form-control"/>
				</div>
				<div class="form-group">
				<div class="form-group">
					<input type="submit" class="btn btn-success col-12" value="Laisser un avis"/>
				</div>
			</form>
		</div>
      </div>
    </div>
</div>
<?php 
require_once('inc/footer.php');
?>