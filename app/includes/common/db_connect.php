<?php

// connexion à la base de données 
$db_pronos=mysql_connect($db_host,$db_user,$db_passwd) 
	or die('Echec de connexion au serveur de base de données ('.$db_host.') avec l\'utilisateur '.$db_user.'. Contactez les adminitrateurs ... ');
	
// sélection de la base
$db_test = mysql_select_db($db_name,$db_pronos) 
	or die('Impossible d\'utiliser la base '.$db_name.'. Contactez les adminitrateurs ... ');

// spécification du charset par défaut pour éviter les problèmes d'accent
mysql_query("SET NAMES utf8",$db_pronos)
	or die('Impssible de sélectionner le charset utf8. Contactez les adminitrateurs ... ');

?>
