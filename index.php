<?php
$setup = 'app/includes/setup/index.php';
if (file_exists($setup)) {
	echo 'Lancement de l\'installation du site';
	require_once('app/includes/setup/redirect.php');
} else {
	// Configuration
	require_once('app/config.php');
	// Fuseau horaire et françisation
	setlocale (LC_TIME, 'fr_FR.utf8','fra');
	date_default_timezone_set('Europe/Paris');
	session_start();
	// Fonctions du site
	require_once('app/includes/common/fonctions.php');

	// Connexion à la base de données
	require_once('app/includes/common/db_connect.php');

	// Test de connexion d'un utilisateur
	require_once('app/includes/common/user_connect.php');

	// Exécution des requètes
	require_once('app/includes/common/execution_requetes.php');

	// Récupération du nom de la page
	require_once('app/includes/common/data_page.php');

	// Rendu de la page
	require_once('app/includes/common/render_page.php');

	// Déconnexion de la base de données
	require_once('app/includes/common/db_close.php');
}

?>
