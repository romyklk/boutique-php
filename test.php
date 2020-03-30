<?php 
session_start();
$_SESSION['validation'][] = 'Félicitations vous êtes inscris... veuillez vous connecter';

header('location:connexion.php');