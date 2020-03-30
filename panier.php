<?php 
require_once('inc/init.php');


//debug($_SESSION);
// Afficher les infos du panier 



// Vider le panier
if(isset($_GET['action']) && $_GET['action'] == 'vider' ){
	unset($_SESSION['panier']);
	$_SESSION['validation'][] = 'Votre panier a bien été vidé';
	header('location:' . PATH . 'panier.php');
	exit; 
}
 

// Supprimer un produit du panier 
if(isset($_GET['action']) && $_GET['action'] == 'supprimer' ){
	if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
		
		if(retirerPanier($_GET['id'])){
			$_SESSION['validation'][] = 'Le produit a bien été supprimé du panier';
		}
		// fonction pour retirer un produit du panier
		header('location:' . PATH . 'panier.php');
		exit;
	}
	else{
		header('location:' . PATH . 'panier.php');
	}
}


// Payer la commande 

if(isset($_POST['payer'])){
	// Si qqun a cliqué sur le bouton payer
	
	// vérifier la disponibilité des produits (boucle)
	for($i = 0; $i < sizeof($_SESSION['panier']['id_produit']); $i++){
		$id = $_SESSION['panier']['id_produit'][$i]; // Je récupere l'id du produit en cours
		$resultat = $pdo -> query("SELECT * FROM produit WHERE id_produit = $id");
		// echo '<p style="color:red; font-size: 40px;">' . $i . '</p>';
		if($resultat -> rowCount() > 0){
			$produit = $resultat -> fetch();	
			if($produit['stock'] < $_SESSION['panier']['quantite'][$i]){
				// Pas OK : Le stock en BDD en inférieur à la quantité commandée dans le panier...
				
				if($produit['stock'] == 0){
					retirerPanier($id);
					
					$error  .= '<div class="alert alert-danger">Malheureusement, le produit <b>' . $produit['titre'] . '</b> n\'est plus disponible. Votre panier a été mis à jour, veuillez le vérifier avant de valider votre commande</div>'; 
		
					$i--; // Au moment où on supprime le produit plus dispo du panier, on ré-indexe tout le panier, du coup le produit suivant ne sera pas checké par notre boucle. En décrémentant $i, on corrige le parcours de la boucle qui repart un cran en arrière...
				}
				else{// Plus de produit du tout... :(
						// Moins de produits disponibles
					$_SESSION['panier']['quantite'][$i] = $produit['stock'];
					
					$error  .= '<div class="alert alert-danger">Malheureusement, la quantité disponible du produit <b>' . $produit['titre'] . '</b> n\'est pas suffisante. Votre panier a été mis à jour, veuillez le vérifier de nouveau avant de valider votre commande</div>';
				}
			}
		}
	}

	if(empty($error)){
		// pas de souci de stock, on peut valider la commande
		// C'est à ce moment-là qu'on déclenche le paiement...
		// Enregistrer les infos 
		$id_user = $_SESSION['membre']['id_membre'];
		$montant = montantTotal();
		
		$resultat = $pdo -> exec("INSERT INTO commande (id_membre, montant, date_enregistrement, etat) VALUES ($id_user, '$montant', NOW(), '1') ");
		// On enregistre La commande dans la table commande. Une commande est liée à un membre
		
		$id_commande = $pdo -> lastInsertId(); 
		// nous retourne l'ID de la derniere ligne enregistrée ou modifié
		
		// Les details de la commande (table details commande - boucle)
		for($i = 0; $i < count($_SESSION['panier']['id_produit']); $i++){
			
			$id_produit = $_SESSION['panier']['id_produit'][$i];
			$prix = $_SESSION['panier']['prix'][$i];
			$quantite = $_SESSION['panier']['quantite'][$i];
			
			$resultat = $pdo -> exec("INSERT INTO details_commande (id_commande, id_produit, prix, quantite) VALUES ( $id_commande, $id_produit, $prix, $quantite)");
			
			// modifions le stock du produit dans la BDD 
			// Modifier le stock des produits (table produit - boucle)
			$resultat = $pdo -> exec("UPDATE produit SET stock = stock - $quantite WHERE id_produit = $id_produit");
		}
		
		// vider le panier 
		unset($_SESSION['panier']);
		$_SESSION['validation'][] = 'Votre commande a bien été prise en compte. Voici le numéro de commande <b>N°'. date('Y'). '-' . time() . '-' . $id_commande . '</b><br/>Un email de confirmation vient de vous être envoyé à l\'adresse <u>' . $_SESSION['membre']['email'] . '</u>'; 			
		header('location:' . PATH . 'panier.php');
		exit; 

		// Envoyer des mails : Membre (cf PHP/post/Formulaire5.php)
		//$header = '';
		//$message = '';
		// mail($_POST['membre']['email'], 'Votre commande N°'. date('Y') . '-' . $id_commande, $message, $header );		
	}
}


    // Incrémentation de produit 
    // On peut incrémenter un produit tant que sa quantité est égal au stock restant... Après on peut plus

    if(isset($_GET['action']) && $_GET['action'] == 'incrementation'){
        if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){

            $position = array_search($_GET['id'], $_SESSION['panier']['id_produit']);

            $resultat = $pdo -> prepare("SELECT stock FROM PRODUIT WHERE id_produit = :id");
            $resultat -> bindParam(':id', $_GET['id'], PDO::PARAM_INT);
            $resultat -> execute();
            $produit = $resultat -> fetch();
            $stock = $produit['stock'];

            if($position !== FALSE){

                if($stock >= $_SESSION['panier']['quantite'][$position] +1){
                    // Le stock en bdd est au moins égal à la quantité actuelle +1
                    $_SESSION['panier']['quantite'][$position] ++;
                    header('location:' . PATH . 'panier.php');
                }
                else{
                    $error .= '<div class="alert alert-danger">Le stock du produit <b>' . $_SESSION['panier']['titre'][$position] . '<b> n\'est pas suffisant pour ajouter un exemplaire dans votre liste d\'achat.</div>';
                }

            }
        }
    }


    // Décrementation de produit 
        // Attention on peut décrémenter un produit tant que sa quantité est supérieur à 1...Après on le retire du panier

        if(isset($_GET['action']) && $_GET['action'] == 'decrementation' ){
            if(isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])){
                
                $position = array_search($_GET['id'], $_SESSION['panier']['id_produit']);
                
                
                if($_SESSION['panier']['quantite'][$position] > 1){
                    $_SESSION['panier']['quantite'][$position] --;
                    header('location:' . PATH . 'panier.php');
                }
                else{
                    // Nous avons 1 exemplaire dans le panier... si on el retire en réalité, c'est le produit qu'il faut retirer du panier. 
                    
                    $error .= '<div class="alert alert-danger">Le produit <b>' . $_SESSION['panier']['titre'][$position] . '</b> a été retiré de votre liste d\'achat.</div>';	
                    retirerPanier($_GET['id']);
                }
            }
        }
            

	
			

require_once('inc/header.php');
?>

<h1>Panier</h1>
<?= $error ?>
<table class="table table-dark  table-hover m-2">
	<thead align="center">
		<tr>
			<th colspan="6" >PANIER (<?= quantitePdt() ?>)</th>
		</tr>
		<tr>
			<th>Photo</th>
			<th>Titre</th>
			<th>Prix unitaire</th>
			<th>Quantité</th>
			<th>Prix total</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody align="center">
		<?php if(count($_SESSION['panier']['id_produit']) > 0) : ?>
			<?php for($i=0; $i < count($_SESSION['panier']['id_produit']); $i++) : ?>
			<tr>
				<td><a href="<?= PATH ?>fiche_produit.php?id=<?= $_SESSION['panier']['id_produit'][$i] ?>"><img src="<?= PATH ?>img/<?= $_SESSION['panier']['photo'][$i] ?>" height="40"></a></td>
				<td><?= $_SESSION['panier']['titre'][$i] ?></td>
				<td><?= number_format($_SESSION['panier']['prix'][$i], 2, ',', ' ') ?>€</td>
				
				
				<td>
				
				<a href="?action=decrementation&id=<?= $_SESSION['panier']['id_produit'][$i] ?>"><i class="text-primary fas fa-minus-circle"></i></a>
				&nbsp;
				<?= $_SESSION['panier']['quantite'][$i] ?>
				&nbsp;
				<a href="?action=incrementation&id=<?= $_SESSION['panier']['id_produit'][$i] ?>"><i class="text-primary fas fa-plus-circle"></i></a>
				
				</td>
				
				
				
				<td><?= number_format($_SESSION['panier']['prix'][$i] * $_SESSION['panier']['quantite'][$i], 2, ',', ' ')   ?>€</td>
				<td><a href="?action=supprimer&id=<?= $_SESSION['panier']['id_produit'][$i] ?>"><i class="fas fa-trash-alt text-warning" title="Supprimer le produit du panier"></i></a></td>
			</tr>
			<?php endfor; ?>
			
			<tr>
				<td colspan="4">TOTAL | <?= quantitePdt() ?> produit(s)</td>
				<td colspan="1"><?= number_format(montantTotal(), 2, ',', ' ')  ?>€</td>
				<td colspan="1"><a href="?action=vider"><i class="fas fa-trash-alt text-danger" title="Vider le panier"></i></a></td>
			</tr>
			<tr>
				<td colspan="6">	
					<form method="post" action="">
						<input type="submit" class="btn btn-success btn-lg" name="payer" value="Payer le panier"/>
					</form>
				</td>
			</tr>
		<?php else :  ?>
			<tr>
				<td colspan="6">Votre panier est vide <a href="<?= PATH ?>index.php" class="btn btn-primary">Visiter la boutique</a></td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>
<?php 
require_once('inc/footer.php');
?>