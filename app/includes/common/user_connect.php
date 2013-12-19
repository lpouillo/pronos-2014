<?php
/*
 * Created on 2 sept. 2008
 *
 * Script permettant l'authentification et la récupération des données utilisateurs
 **************************************************************************************
 * 2 modes d'authentification :
 *  - table users dans la base de données (gestionnaire, admin, rôles spécifiques)
 *  - vérification sur le ldap de l'institut
 * 
 * Création des données de session
 * Log de la connexion sur le site
 */

$login_error=0;
if (isset($_POST['login']) and isset($_POST['password'])) {
	// test sur la base locale
	$s_user = "SELECT `id_user`,`login`,`nom_reel`,`is_admin`,`email`,`classement`,`points` FROM users WHERE login='".secure_mysql($_POST['login'])."' AND password='".md5($_POST['password'])."'";
	$r_user = mysql_query($s_user) 
		or die ('La requète sur la base locale est mal formulée, tentative d\'injection SQL détectée.');
	
	// si on a trouvé l'user dans la base locale, on récupère son id
	if ($d_user = mysql_fetch_array($r_user)) {
		$_SESSION['id_user']=htmlentities($d_user['id_user']);
		$_SESSION['login']=htmlentities($d_user['login']);
		$_SESSION['nom_reel']=htmlentities($d_user['nom_reel']);
		$_SESSION['email']=htmlentities($d_user['email']);
		$_SESSION['is_admin']=$d_user['is_admin'];
		$_SESSION['classement']=$d_user['classement'];
		$_SESSION['points']=$d_user['points'];
				
		// on log la connexion sur le site 
		$date=date('Y-m-d');
		$heure=date('H:i'); 
		$s_connexion="INSERT INTO connexions (`date_connexion`,`heure_connexion`,`id_user`) VALUES ('".$date."','".$heure."','".$_SESSION['id_user']."')";
		mysql_query($s_connexion) 
			or die('Insertion de la connexion échouée. Contactez les adminitrateurs ... ');		
	} else {
		$login_error=true;
	}		
}
?>
