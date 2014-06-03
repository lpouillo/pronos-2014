<?php
/*
 * Created on 19 mai 2014
 *
 */
$db_pronos=mysqli_connect($_POST['db_host'], $_POST['db_user'], $_POST['db_passwd'])
	or die('Echec de connexion au serveur de base de données ('.$_POST['db_host'].') ' .
			'avec l\'utilisateur '. $_POST['db_user'].'.');;
$db_test = mysqli_select_db($db_pronos, $_POST['db_name'])
	or die('Impossible d\'utiliser la base '.$_POST['db_name'].'.');
mysqli_query($db_pronos, "SET NAMES utf8")
	or die('Impssible de sélectionner le charset utf8.');

$content .= "<p>Création des tables :</p>\n";
$creation_tables = ['connexions' => "CREATE TABLE IF NOT EXISTS `connexions` (
			`id_connexion` int(11) NOT NULL AUTO_INCREMENT,
			`date_connexion` date NOT NULL,
			`heure_connexion` time NOT NULL,
			`id_user` int(11) NOT NULL,
			PRIMARY KEY (`id_connexion`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci" ,
		'equipes' => "CREATE TABLE IF NOT EXISTS `equipes` (
			`id_equipe` int(11) NOT NULL AUTO_INCREMENT,
			`date_in` date NOT NULL DEFAULT '0000-00-00',
			`date_modif` date NOT NULL DEFAULT '0000-00-00',
			`nom` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
			`acronym` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
			`poule` smallint(6) NOT NULL DEFAULT '0',
			`joues` tinyint(4) NOT NULL,
			`victoires` smallint(6) NOT NULL DEFAULT '0',
			`nuls` smallint(6) NOT NULL DEFAULT '0',
			`defaites` smallint(6) NOT NULL DEFAULT '0',
			`but_p` smallint(6) NOT NULL DEFAULT '0',
			`but_c` smallint(6) NOT NULL DEFAULT '0',
			`pts` smallint(6) NOT NULL DEFAULT '0',
			`classement` smallint(6) NOT NULL DEFAULT '0',
			PRIMARY KEY (`id_equipe`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
		'groupes' => "CREATE TABLE IF NOT EXISTS `groupes` (
			`id_groupe` int(11) NOT NULL AUTO_INCREMENT,
			`date_in` date NOT NULL,
			`date_modif` date NOT NULL,
			`nom` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
			`description` text COLLATE utf8_unicode_ci NOT NULL,
			`id_owner` int(11) NOT NULL,
			`id_image` int(11) NOT NULL,
			`actif` tinyint(1) NOT NULL,
			`moyenne` float NOT NULL,
			`n_user` int(11) NOT NULL,
			`classement` int(11) NOT NULL,
			PRIMARY KEY (`id_groupe`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
		'l_users_groupes' => "CREATE TABLE IF NOT EXISTS `l_users_groupes` (
			`id_user` int(11) NOT NULL,
			`id_groupe` int(11) NOT NULL,
			`date_in` date NOT NULL,
			`date_modif` date NOT NULL,
			`actif` tinyint(1) NOT NULL,
			PRIMARY KEY (`id_user`,`id_groupe`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
		'matchs' => "CREATE TABLE IF NOT EXISTS `matchs` (
			`id_match` int(11) NOT NULL AUTO_INCREMENT,
			`date_in` date NOT NULL DEFAULT '0000-00-00',
			`date_modif` date NOT NULL,
			`id_equipe1` mediumint(9) NOT NULL DEFAULT '0',
			`id_equipe2` mediumint(9) NOT NULL DEFAULT '0',
			`date_match` date NOT NULL DEFAULT '0000-00-00',
			`heure` time NOT NULL DEFAULT '00:00:00',
			`score1` smallint(6) NOT NULL DEFAULT '0',
			`score2` smallint(6) NOT NULL DEFAULT '0',
			`tab1` tinyint(4) NOT NULL DEFAULT '0',
			`tab2` tinyint(4) NOT NULL DEFAULT '0',
			`joue` tinyint(1) NOT NULL,
			`type` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
			`special` tinyint(1) NOT NULL,
			`cote_1` float NOT NULL,
			`cote_N` float NOT NULL,
			`cote_2` float NOT NULL,
			PRIMARY KEY (`id_match`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
		'pages' => "CREATE TABLE IF NOT EXISTS `pages` (
			`id_page` int(11) NOT NULL AUTO_INCREMENT,
			`date_in` date NOT NULL,
			`date_modif` date NOT NULL,
			`libelle` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
			`titre` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
			`titre_menu` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
			`position_menu` int(11) NOT NULL,
			PRIMARY KEY (`id_page`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci",
		'pronos' => "CREATE TABLE IF NOT EXISTS `pronos` (
			`date_in` date NOT NULL DEFAULT '0000-00-00',
			`date_modif` date NOT NULL DEFAULT '0000-00-00',
			`id_user` int(11) NOT NULL DEFAULT '0',
			`id_match` mediumint(9) NOT NULL DEFAULT '0',
			`score1` smallint(6) NOT NULL DEFAULT '0',
			`score2` smallint(6) NOT NULL DEFAULT '0',
			`tab1` tinyint(4) NOT NULL,
			`tab2` tinyint(4) NOT NULL,
			`points` float NOT NULL DEFAULT '0',
			PRIMARY KEY (`id_user`,`id_match`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;",
		'users' => "CREATE TABLE IF NOT EXISTS `users` (
			`id_user` int(11) NOT NULL AUTO_INCREMENT,
			`date_in` date NOT NULL DEFAULT '0000-00-00',
			`date_modif` date NOT NULL,
			`login` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
			`password` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
			`nom_reel` text COLLATE utf8_unicode_ci NOT NULL,
			`email` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
			`points` float NOT NULL DEFAULT '0',
			`malus` float NOT NULL,
			`classement` mediumint(9) NOT NULL,
			`classement_precedent` mediumint(9) NOT NULL,
			`is_admin` tinyint(1) NOT NULL,
			`token` varchar(256) COLLATE utf8_unicode_ci NOT NULL,
			`date_recup` date NOT NULL,
			`id_image` int(11) NOT NULL,
			`actif` tinyint(1) NOT NULL,
			`news` tinyint(1) NOT NULL,
			PRIMARY KEY (`id_user`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;"
];
$content .= "<ul>\n";
foreach ($creation_tables as $table => $sql) {
	mysqli_query($db_pronos, $sql);
	$content .= "<li>".$table."</li>\n";
            }
$content .= "</ul>\n";
$fill_pages = "REPLACE INTO `pages` " .
		"(`id_page`, `date_in`, `date_modif`, `libelle`, `titre`, `titre_menu`, `position_menu`) VALUES
(1, NOW(), NOW(), 'accueil', 'Bienvenue à toutes et à tous', 'Accueil', 1),
(2, NOW(), NOW(), 'resultats', 'Tous les résultats des poules et du tournoi', 'Résultats', 3),
(3, NOW(), NOW(), 'concours', 'Classement du concours', 'Concours', 4),
(4, NOW(), NOW(), 'reglement', 'Le règlement complet du concours', 'Règlement', 2),
(5, NOW(), NOW(), 'inscription', 'Formulaire d''inscription au concours', 'Inscription', 5),
(6, NOW(), NOW(), 'mon_espace', 'Gérer mon compte', 'Mon espace', 6),
(7, NOW(), NOW(), 'liens', 'Liens utiles', 'Liens', 7),
(8, NOW(), NOW(), 'admin', 'Administration du site', '', 0),
(9, NOW(), NOW(), 'not_found', 'Page non trouvée', '', 0),
(10, NOW(), NOW(), 'forbidden', 'Page non autorisée', '', 0),
(11, NOW(), NOW(), 'deconnexion', 'Déconnexion du site', 'Déconnexion', 0),
(12, NOW(), NOW(), 'graphs', '', '', 0);";
mysqli_query($db_pronos, $fill_pages)
	or die (mysql_error());

$fill_equipes = "REPLACE INTO `equipes`" .
		" (`id_equipe`, `date_in`, `date_modif`, `nom`, `acronym`, `poule`) VALUES
(1, NOW(), NOW(), 'Brésil', 'br', 1),
(2, NOW(), NOW(), 'Croatie', 'hr', 1),
(3, NOW(), NOW(), 'Mexique', 'mx', 1),
(4, NOW(), NOW(), 'Cameroun', 'cm', 1),
(5, NOW(), NOW(), 'Colombie', 'co', 2),
(6, NOW(), NOW(), 'Grèce', 'gr', 2),
(7, NOW(), NOW(), 'Côte d\'Ivoire', 'ci', 2),
(8, NOW(), NOW(), 'Japon', 'jp', 2),
(9, NOW(), NOW(), 'Espagne', 'es', 3),
(10, NOW(), NOW(), 'Pays-Bas', 'nl', 3),
(11, NOW(), NOW(), 'Chili', 'cl', 3),
(12, NOW(), NOW(), 'Australie', 'au', 3),
(13, NOW(), NOW(), 'Suisse', 'ch', 4),
(14, NOW(), NOW(), 'Équateur', 'ec', 4),
(15, NOW(), NOW(), 'France', 'fr', 4),
(16, NOW(), NOW(), 'Honduras', 'hn', 4),
(17, NOW(), NOW(), 'Uruguay', 'uy', 5),
(18, NOW(), NOW(), 'Costa Rica', 'cr', 5),
(19, NOW(), NOW(), 'Royaume-Uni', 'gb', 5),
(20, NOW(), NOW(), 'Italie', 'it', 5),
(21, NOW(), NOW(), 'Allemagne', 'de', 6),
(22, NOW(), NOW(), 'Portugal', 'pt', 6),
(23, NOW(), NOW(), 'Ghana', 'gh', 6),
(24, NOW(), NOW(), 'États-Unis', 'us', 6),
(25, NOW(), NOW(), 'Argentine', 'ar', 7),
(26, NOW(), NOW(), 'Bosnie', 'ba', 7),
(27, NOW(), NOW(), 'Iran', 'ir', 7),
(28, NOW(), NOW(), 'Nigeria', 'ng', 7),
(29, NOW(), NOW(), 'Belgique', 'be', 8),
(30, NOW(), NOW(), 'Algérie', 'dz', 8),
(31, NOW(), NOW(), 'Russie', 'ru', 8),
(32, NOW(), NOW(), 'Corée', 'kr', 8)";
mysqli_query($db_pronos, $fill_equipes)
	or die (mysql_error());

$fill_matchs = "REPLACE INTO `matchs` " .
		"(`id_match`, `date_in`, `date_modif`, `id_equipe1`, `id_equipe2`, `date_match`, `heure`, `type`, `special`) VALUES
(1, NOW(), NOW(), 1, 2, '2014-06-12', '22:00', 'poule', 0),
(2, NOW(), NOW(), 3, 4, '2014-06-13', '18:00', 'poule', 0),
(17, NOW(), NOW(), 1, 3, '2014-06-17', '21:00', 'poule', 0),
(18, NOW(), NOW(), 4, 2, '2014-06-19', '00:00', 'poule', 0),
(33, NOW(), NOW(), 4, 1, '2014-06-23', '22:00', 'poule', 0),
(34, NOW(), NOW(), 2, 3, '2014-06-23', '22:00', 'poule', 0),
(3, NOW(), NOW(), 5, 6, '2014-06-13', '21:00', 'poule', 0),
(4, NOW(), NOW(), 7, 8, '2014-06-14', '00:00', 'poule', 0),
(19, NOW(), NOW(), 5, 7, '2014-06-18', '21:00', 'poule', 1),
(20, NOW(), NOW(), 8, 6, '2014-06-18', '18:00', 'poule', 0),
(35, NOW(), NOW(), 8, 5, '2014-06-23', '18:00', 'poule', 0),
(36, NOW(), NOW(), 6, 7, '2014-06-23', '18:00', 'poule', 0),
(5, NOW(), NOW(), 9, 10, '2014-06-14', '18:00', 'poule', 1),
(6, NOW(), NOW(), 11, 12, '2014-06-14', '22:00', 'poule', 0),
(21, NOW(), NOW(), 9, 11, '2014-06-19', '18:00', 'poule', 0),
(22, NOW(), NOW(), 12, 10, '2014-06-19', '19:00', 'poule', 0),
(37, NOW(), NOW(), 12, 9, '2014-06-24', '21:00', 'poule', 0),
(38, NOW(), NOW(), 10, 11, '2014-06-24', '22:00', 'poule', 0),
(7, NOW(), NOW(), 13, 14, '2014-06-14', '21:00', 'poule', 0),
(8, NOW(), NOW(), 15, 16, '2014-06-15', '00:00', 'poule', 0),
(23, NOW(), NOW(), 13, 15, '2014-06-19', '21:00', 'poule', 0),
(24, NOW(), NOW(), 16, 14, '2014-06-20', '18:00', 'poule', 0),
(39, NOW(), NOW(), 16, 13, '2014-06-24', '18:00', 'poule', 0),
(40, NOW(), NOW(), 14, 15, '2014-06-24', '18:00', 'poule', 0),
(9, NOW(), NOW(), 17, 18, '2014-06-15', '18:00', 'poule', 0),
(10, NOW(), NOW(), 19, 20, '2014-06-15', '21:00', 'poule', 1),
(25, NOW(), NOW(), 17, 19, '2014-06-20', '21:00', 'poule', 0),
(26, NOW(), NOW(), 20, 18, '2014-06-20', '19:00', 'poule', 0),
(41, NOW(), NOW(), 20, 17, '2014-06-25', '21:00', 'poule', 0),
(42, NOW(), NOW(), 18, 19, '2014-06-25', '22:00', 'poule', 0),
(11, NOW(), NOW(), 21, 22, '2014-06-15', '19:00', 'poule', 0),
(12, NOW(), NOW(), 23, 24, '2014-06-16', '21:00', 'poule', 0),
(27, NOW(), NOW(), 21, 23, '2014-06-21', '18:00', 'poule', 0),
(28, NOW(), NOW(), 24, 22, '2014-06-22', '00:00', 'poule', 0),
(43, NOW(), NOW(), 24, 21, '2014-06-25', '18:00', 'poule', 0),
(44, NOW(), NOW(), 22, 23, '2014-06-25', '18:00', 'poule', 0),
(13, NOW(), NOW(), 25, 26, '2014-06-16', '18:00', 'poule', 0),
(14, NOW(), NOW(), 27, 28, '2014-06-16', '19:00', 'poule', 0),
(29, NOW(), NOW(), 25, 27, '2014-06-21', '21:00', 'poule', 0),
(30, NOW(), NOW(), 28, 26, '2014-06-23', '00:00', 'poule', 0),
(45, NOW(), NOW(), 28, 25, '2014-06-26', '18:00', 'poule', 0),
(46, NOW(), NOW(), 26, 27, '2014-06-26', '18:00', 'poule', 1),
(15, NOW(), NOW(), 29, 30, '2014-06-17', '18:00', 'poule', 0),
(16, NOW(), NOW(), 31, 32, '2014-06-18', '00:00', 'poule', 0),
(31, NOW(), NOW(), 29, 31, '2014-06-22', '18:00', 'poule', 0),
(32, NOW(), NOW(), 32, 30, '2014-06-22', '21:00', 'poule', 0),
(47, NOW(), NOW(), 32, 29, '2014-06-26', '22:00', 'poule', 0),
(48, NOW(), NOW(), 30, 31, '2014-06-26', '22:00', 'poule', 0),
(49, NOW(), NOW(), 0, 0, '2014-06-28', '18:00', 'Huitieme1', 0),
(50, NOW(), NOW(), 0, 0, '2014-06-28', '22:00', 'Huitieme2', 0),
(51, NOW(), NOW(), 0, 0, '2014-06-29', '18:00', 'Huitieme3', 0),
(52, NOW(), NOW(), 0, 0, '2014-06-29', '22:00', 'Huitieme4', 0),
(53, NOW(), NOW(), 0, 0, '2014-06-30', '18:00', 'Huitieme5', 0),
(54, NOW(), NOW(), 0, 0, '2014-06-30', '22:00', 'Huitieme6', 0),
(55, NOW(), NOW(), 0, 0, '2014-07-01', '18:00', 'Huitieme7', 0),
(56, NOW(), NOW(), 0, 0, '2014-07-01', '22:00', 'Huitieme8', 0),
(57, NOW(), NOW(), 0, 0, '2014-07-04', '18:00', 'Quart1', 0),
(58, NOW(), NOW(), 0, 0, '2014-07-04', '22:00', 'Quart2', 0),
(59, NOW(), NOW(), 0, 0, '2014-07-05', '18:00', 'Quart3', 0),
(60, NOW(), NOW(), 0, 0, '2014-07-05', '22:00', 'Quart4', 0),
(61, NOW(), NOW(), 0, 0, '2014-07-08', '22:00', 'Demi1', 0),
(62, NOW(), NOW(), 0, 0, '2014-07-09', '22:00', 'Demi2', 0),
(63, NOW(), NOW(), 0, 0, '2014-07-12', '22:00', 'p_finale', 0),
(64, NOW(), NOW(), 0, 0, '2014-07-13', '21:00', 'Finale', 0);";

mysqli_query($db_pronos, $fill_matchs)
	or die (mysql_error());

$content .= "<p>Remplissage des tables effectué</p>\n";

$s_admin = "REPLACE INTO users " .
		"(`id_user`, `date_in`, `date_modif`, `login`, `password`, `email`, `nom_reel`, `is_admin`, `actif`) VALUES " .
		"(1, NOW(), NOW(), '".$_POST['admin_login']."', '".md5($_POST['admin_passwd'])."', '".$_POST['admin_email']."', " .
				"'".$_POST['admin_name']."', 1, 1)";
mysqli_query($db_pronos, $s_admin)
or die(mysqli_error($db_pronos).'<br/>'.$s_admin);

$content .= "<p>Compte admin créé</p>\n";


?>
