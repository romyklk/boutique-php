<?php
require_once('..inc/init.php');

if(userAdmin()){
    header('location:connexion.php');
}
else{
    header('location:gestion_produits.php');
}