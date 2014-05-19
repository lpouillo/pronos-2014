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
			`poule` smallint(6) NOT NULL DEFAULT '0',
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
$content .= "</ul>\n<p>Remplissage des tables</p>\n";
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
(12, '2010-06-17', '0000-00-00', 'graphs', '', '', 0);";
mysql_query($fill_pages, $db_pronos);


?>
