<?php
/*
 * Created on 19 mai 2014
 *
 */
$db_pronos=mysql_connect($_POST['db_host'], $_POST['db_user'], $_POST['db_passwd'])
	or die('Echec de connexion au serveur de base de données ('.$_POST['db_host'].') ' .
			'avec l\'utilisateur '. $_POST['db_user'].'.');;
$db_test = mysql_select_db($_POST['db_name'], $db_pronos)
	or die('Impossible d\'utiliser la base '.$_POST['db_name'].'.');
mysql_query("SET NAMES utf8", $db_pronos)
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
	mysql_query($sql, $db_pronos);
	$content .= "<li>".$table."</li>\n";
            }
$content .= "</ul>\n";
$fill_pages = "REPLACE INTO `pages` (`id_page`, `date_in`, `date_modif`, `libelle`, `titre`, `titre_menu`, `position_menu`) VALUES
(1, '2010-03-22', '2010-03-22', 'accueil', 'Derniers résultats, les news du tournoi, les 15 premiers ...', 'Accueil', 1),
(2, '2010-03-22', '2010-03-22', 'resultats', 'Tous les résultats des poules, classements, tournoi', 'Résultats', 3),
(3, '2010-03-22', '2010-03-22', 'concours', 'classement du concours, groupes de parieurs', 'Concours', 4),
(4, '2010-03-22', '2010-03-22', 'reglement', 'Le règlement complet, matchs spéciaux, calcul', 'Règlement', 2),
(5, '2010-03-22', '2010-03-22', 'inscription', 'Formulaire d''inscription au concours', 'Inscription', 5),
(6, '2010-03-22', '2010-03-22', 'mon_espace', 'Mes informations, mes pronostiques, mes groupes', 'Mon espace', 6),
(7, '2010-03-22', '2010-03-22', 'liens', 'Liens utiles', 'Liens', 7),
(8, '2010-03-22', '2010-03-22', 'admin', 'Administration du site', '', 0),
(9, '2010-03-22', '2010-03-22', 'not_found', 'Page non trouvée', '', 0),
(10, '2010-03-22', '2010-03-22', 'forbidden', 'Page non autorisée', '', 0),
(11, '2010-05-25', '2010-05-25', 'deconnexion', 'Déconnexion du site', 'Déconnexion', 0),
(12, '2010-06-17', '2010-06-17', 'graphs', '', '', 0);";
mysql_query($fill_pages, $db_pronos);

$fill_equipes = "REPLACE INTO `equipes` (`id_equipe`, `date_in`, `date_modif`, `nom`, `acronym`, `poule`) VALUES
(1, '2014-05-20', '2014-05-20', 'Brésil', 'br'),
(2, '2014-05-20', '2014-05-20', 'Croatie', 'hr'),
(3, '2014-05-20', '2014-05-20', 'Mexique', 'mx'),
(4, '2014-05-20', '2014-05-20', 'Cameroun', 'cm'),
(5, '2014-05-20', '2014-05-20', 'Colombie', 'co'),
(6, '2014-05-20', '2014-05-20', 'Grèce', 'gr'),
(7, '2014-05-20', '2014-05-20', 'Côte d'Ivoire', 'ci'),
(8, '2014-05-20', '2014-05-20', 'Japon', 'jp'),
(9, '2014-05-20', '2014-05-20', 'Espagne', 'es'),
(10, '2014-05-20', '2014-05-20', 'Pays-Bas', 'nl'),
(11, '2014-05-20', '2014-05-20', 'Chili', 'cl'),
(12, '2014-05-20', '2014-05-20', 'Australie', 'au'),
(13, '2014-05-20', '2014-05-20', 'Suisse', 'ch'),
(14, '2014-05-20', '2014-05-20', 'Équateur', 'ec'),
(15, '2014-05-20', '2014-05-20', 'France', 'fr'),
(16, '2014-05-20', '2014-05-20', 'Honduras', 'hn'),
(17, '2014-05-20', '2014-05-20', 'Uruguay', 'uy'),
(18, '2014-05-20', '2014-05-20', 'Costa Rica', 'cr'),
(19, '2014-05-20', '2014-05-20', 'Royaume-Uni', 'gb'),
(20, '2014-05-20', '2014-05-20', 'Italie', 'it'),
(21, '2014-05-20', '2014-05-20', 'Allemagne', 'de'),
(22, '2014-05-20', '2014-05-20', 'Portugal', 'pt'),
(23, '2014-05-20', '2014-05-20', 'Ghana', 'gh'),
(24, '2014-05-20', '2014-05-20', 'États-Unis', 'us'),
(25, '2014-05-20', '2014-05-20', 'Argentine', 'ar'),
(26, '2014-05-20', '2014-05-20', 'Bosnie-Herzégovine', 'ba'),
(27, '2014-05-20', '2014-05-20', 'Iran', 'ir'),
(28, '2014-05-20', '2014-05-20', 'Nigeria', 'ng'),
(29, '2014-05-20', '2014-05-20', 'Belgique', 'be'),
(30, '2014-05-20', '2014-05-20', 'Algérie', 'dz'),
(31, '2014-05-20', '2014-05-20', 'Russie', 'ru'),
(32, '2014-05-20', '2014-05-20', 'Corée', 'kr')";
mysql_query($fill_equipes, $db_pronos);

$fill_matchs = "REPLACE INTO `matchs` (`id_match`, `date_in`, `date_modif`, `id_equipe1`, `id_equipe2`, `date_match`, `heure`) VALUES
(1, '2014-05-20', '2014-05-20', 1, 2, '2014-06-12', '17:00'),
(2, '2014-05-20', '2014-05-20', 3, 4, '2014-06-13', '13:00'),
(17, '2014-05-20', '2014-05-20', 1, 3, '2014-06-17', '16:00'),
(18, '2014-05-20', '2014-05-20', 4, 2, '2014-06-18', '18:00'),
(33, '2014-05-20', '2014-05-20', 4, 1, '2014-06-23', '17:00'),
(34, '2014-05-20', '2014-05-20', 2, 3, '2014-06-23', '17:00'),
(3, '2014-05-20', '2014-05-20', 5, 6, '2014-06-13', '16:00'),
(4, '2014-05-20', '2014-05-20', 7, 8, '2014-06-13', '18:00'),
(19, '2014-05-20', '2014-05-20', 5, 7, '2014-06-18', '16:00'),
(20, '2014-05-20', '2014-05-20', 8, 6, '2014-06-18', '13:00'),
(35, '2014-05-20', '2014-05-20', 8, 5, '2014-06-23', '13:00'),
(36, '2014-05-20', '2014-05-20', 6, 7, '2014-06-23', '13:00'),
(5, '2014-05-20', '2014-05-20', 9, 10, '2014-06-14', '13:00'),
(6, '2014-05-20', '2014-05-20', 11, 12, '2014-06-14', '22:00'),
(21, '2014-05-20', '2014-05-20', 9, 11, '2014-06-19', '13:00'),
(22, '2014-05-20', '2014-05-20', 12, 10, '2014-06-19', '19:00'),
(37, '2014-05-20', '2014-05-20', 12, 9, '2014-06-24', '16:00'),
(38, '2014-05-20', '2014-05-20', 10, 11, '2014-06-24', '17:00'),
(7, '2014-05-20', '2014-05-20', 13, 14, '2014-06-14', '16:00'),
(8, '2014-05-20', '2014-05-20', 15, 16, '2014-06-14', '18:00'),
(23, '2014-05-20', '2014-05-20', 13, 15, '2014-06-19', '16:00'),
(24, '2014-05-20', '2014-05-20', 16, 14, '2014-06-20', '13:00'),
(39, '2014-05-20', '2014-05-20', 16, 13, '2014-06-24', '13:00'),
(40, '2014-05-20', '2014-05-20', 14, 15, '2014-06-24', '13:00'),
(9, '2014-05-20', '2014-05-20', 17, 18, '2014-06-15', '13:00'),
(10, '2014-05-20', '2014-05-20', 19, 20, '2014-06-15', '16:00'),
(25, '2014-05-20', '2014-05-20', 17, 19, '2014-06-20', '16:00'),
(26, '2014-05-20', '2014-05-20', 20, 18, '2014-06-20', '19:00'),
(41, '2014-05-20', '2014-05-20', 20, 17, '2014-06-25', '16:00'),
(42, '2014-05-20', '2014-05-20', 18, 19, '2014-06-25', '17:00'),
(11, '2014-05-20', '2014-05-20', 21, 22, '2014-06-15', '19:00'),
(12, '2014-05-20', '2014-05-20', 23, 24, '2014-06-16', '16:00'),
(27, '2014-05-20', '2014-05-20', 21, 23, '2014-06-21', '13:00'),
(28, '2014-05-20', '2014-05-20', 24, 22, '2014-06-21', '18:00'),
(43, '2014-05-20', '2014-05-20', 24, 21, '2014-06-25', '13:00'),
(44, '2014-05-20', '2014-05-20', 22, 23, '2014-06-25', '13:00'),
(13, '2014-05-20', '2014-05-20', 25, 26, '2014-06-16', '13:00'),
(14, '2014-05-20', '2014-05-20', 27, 28, '2014-06-16', '19:00'),
(29, '2014-05-20', '2014-05-20', 25, 27, '2014-06-21', '16:00'),
(30, '2014-05-20', '2014-05-20', 28, 26, '2014-06-22', '18:00'),
(45, '2014-05-20', '2014-05-20', 28, 25, '2014-06-26', '13:00'),
(46, '2014-05-20', '2014-05-20', 26, 27, '2014-06-26', '13:00'),
(15, '2014-05-20', '2014-05-20', 29, 30, '2014-06-17', '13:00'),
(16, '2014-05-20', '2014-05-20', 31, 32, '2014-06-17', '18:00'),
(31, '2014-05-20', '2014-05-20', 29, 31, '2014-06-22', '13:00'),
(32, '2014-05-20', '2014-05-20', 32, 30, '2014-06-22', '16:00'),
(47, '2014-05-20', '2014-05-20', 32, 29, '2014-06-26', '17:00'),
(48, '2014-05-20', '2014-05-20', 30, 31, '2014-06-26', '17:00')";
mysql_query($fill_matchs, $db_pronos)
	or die (mysql_error());

$content .= "<p>Remplissage des tables effectué</p>\n";

print_r ($_POST);
//mysql_query("REPLACE INTO users ('')");



?>
