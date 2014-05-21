<?php

// connexion à la base de données
$db_pronos=mysqli_connect($db_host,$db_user,$db_passwd)
	or die('Echec de connexion au serveur de base de données ('.$db_host.') avec l\'utilisateur '.$db_user.'. Contactez les adminitrateurs ... ');

// sélection de la base
$db_test = mysqli_select_db($db_pronos, $db_name)
	or die('Impossible d\'utiliser la base '.$db_name.'. Contactez les adminitrateurs ... ');

// spécification du charset par défaut pour éviter les problèmes d'accent
mysqli_query($db_pronos, "SET NAMES utf8")
	or die('Impssible de sélectionner le charset utf8. Contactez les adminitrateurs ... ');

?>
