<?php
// Connexion à la base de données
$db_host='localhost';
$db_name='pronos';
$db_user='pronos';
$db_passwd='+pro-nos';

// Chemin d'accès au site
$site_path='/';

// Admin	
$admin_email='lolo@pouilloux.org';
$admin_name='Laurent Pouilloux';

// Informations sur le tournoi
$cup_name='Euro 2012 // Pologne - Ukraine';
$cup_teamnumber=16;
$cup_groups=4;
$cup_logo='euro2012.jpg';


// Fuseau horaire et françisation
setlocale (LC_TIME, 'fr_FR.utf8','fra'); 
date_default_timezone_set('Europe/Paris');

// Dates des matchs
$timestamp_poules_debut=mktime(17,00,00,06,8,2011);
$timestamp_poules_fin=mktime(23,00,00,06,19,2012);
$timestamp_tableau_debut=mktime(20,45,00,06,21,2012);
$timestamp_tableau_fin=mktime(23,45,00,07,01,2012);


//

?>
