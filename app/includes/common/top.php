<?php
$html_top='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<title>'.$titre.' - Concours de pronostics pour l\'Euro 2012</title>
		<link rel="shortcut icon" type="image/png" href="public/images/icons/equipe.png" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="keywords" content="Pronostiques de foot, tournoi, Institut de Physique du Globe de Paris, 2012, championnat d\'Europe des Nations, Pologne, Ukraine, UEFA"/>
		<link rel="stylesheet" media="screen" type="text/css" title="Design" href="public/css/design.css"/>
		<link rel="stylesheet" media="screen" type="text/css" title="DatePicker" href="public/css/DatePicker.css"/>
		<script type="text/javascript" src="public/javascript/mootools-1.2.2-core-yc.js"></script>
		<script type="text/javascript" src="public/javascript/mootools-1.2.2.2-more.js"></script>
		<script type="text/javascript" src="public/javascript/main.js"> </script>
		<script type="text/javascript" src="public/javascript/DatePicker.js"> </script>
	</head>';
// Si l'utilisateur n'est pas connecté on lui met le focus dans le formulaire de login
if (empty($_SESSION['id_user'])) {
	$html_top.=' <body onload="document.form_login.login.focus();">';
} else {
	$html_top.='<body>';
}
// début de la structure
$html_top.='
	<div id="conteneur">
		<div id="header">
		<table>
			<tr>
				<td id="photo"><img height="100px" src="public/images/'.$cup_logo.'" alt="Image de la page '.$page.'"/></td>
				<td id="header_titre">
					<h1><a style="text-decoration:none" href="index.php" title="Retourner à l\'accueil du site de pronostics IPGP">
						Concours de pronostics Hekla : <span style="font-variant: small-caps">Euro 2012 
						<p style="text-align:center">
						 <img src="public/images/flags/POL.gif" alt="flag" /> Pologne -
						 Ukraine <img src="public/images/flags/UKR.gif" alt="flag" /></p></span></a></h1>
						<span id="span_titre"><?php echo $titre;?></span>
				</td>
				<td id="td_login">';
// si pas d'utilisateur connecté on met le formulaire de login
if (empty($_SESSION['id_user'])) {
	$html_top.='	<form method="post" action="index.php" id="form_login">
					<table cellspacing="3" cellpadding="3" border="0" >
						<tr>
							<td>Login</td>
							<td style="text-align:right"><input type="text" id="login" name="login" class="input_login"/></td>
						</tr>
						<tr>
							<td>Password ';
// mauvais identification
	$html_top.=($login_error)?' <em style="color:red">Erreur</em>':'';
	$html_top.='				</td>
							<td style="text-align:right"><input type="password" name="password" class="input_login"/></td>
						</tr>
						<tr>
							<td colspan="2" style="text-align:center"><input type="submit" value="Connexion" class="input_submit"/>
							<a href="index.php?page=inscription&amp;token=new">Mot de passe oublié</a>
							<a href="https://hekla.ipgp.fr/"><img style="border:none;height:25px;" src="public/images/hekla.jpg"  alt="Logo de l\'Hekla"/></a>
							</td>	
						</tr>
					</table>
					</form>'; 
} else {
// sinon on lui affiche son nom et le lien vers son espace
	$colspan=($_SESSION['is_admin'])?'':'colspan="2"';
	$html_top.='<table border="0" style="margin:auto;border-collapse:collapse;">
				<tr>
					<td colspan="2" style="text-align:right;padding:4px;"><span style="font-size:13px;color:#00774B;font-weight:bolder;"><em>'.$_SESSION['login'].'</em></span>
					&nbsp;&nbsp;&nbsp;
					'.$_SESSION['points'].' pts &nbsp;&nbsp;&nbsp;	 
					<img  border="0" src="public/images/icons/arrow_right.png" height="10px" alt="evolution"/> <strong>'.$_SESSION['classement'].'</strong>  
					<a href="index.php?page=deconnexion" title="Déconnexion"><img  border="0"src="public/images/icons/door_out.png" alt="logout"/></a></td>
					<td rowspan="3"><p style="margin:auto;margin-top:5px;margin-bottom:5px" onclick="affElement(\'mon_espace\',\'\',\'\',\'\',\'page\')">
						<img  height="75px"  src="public/images/photos/inconnu_small.jpg" alt="photo"/></p></td>
				</tr>
				<tr>
					<td '.$colspan.'><p class="link bouton" onclick="affElement(\'mon_espace\',\'\',\'\',\'\',\'page\')">
								<img  border="0" src="public/images/icons/mon_espace.png" alt="mon_espace"/> Mon compte</p>
					</td>';
	
	$html_top.=($_SESSION['is_admin'])?'<td><p class="link bouton"<a href="#" onclick="affElement(\'admin\',\'\',\'\',\'\',\'page\')">
								<img border="0" src="public/images/icons/tux.png" alt="mon_pronos"/> Administration</a></p></td>':'';
	$html_top.='</tr>
				<tr>
					<td style="width="50%">
								<p class="link bouton" onclick="affElement(\'mon_espace\',\'mes_pronos\',\'\',\'\',\'page\')">
								<img border="0" src="public/images/icons/application_form.png" alt="mon_pronos"/> Pronostiques </p>
					</td>
					<td>
								<p class="link bouton" onclick="affElement(\'mon_espace\',\'mes_groupes\',\'\',\'\',\'page\')">
								<img border="0" src="public/images/icons/group.png" alt="mon_pronos"/> Mes groupes </p>
					</td>
				</tr>
								
					
							</table>';
	$html_top.='</p>';
}

// on ferme le header et on ouvre le menu

$html_top.='
				</td>
			</tr>
		</table>
		</div>
		<div id="menu">
			<ul>';
	
$menu=array();
// titr
foreach($pages as $k_page => $data) {
	if ($data['position']>0) {
		$menu[$data['position']]=array('page' => $k_page,'titre_menu' => $data['titre_menu'], 'title' => $data['titre']);
	}
}
foreach ($menu as $data) {
	$html_top.='
		<li onclick="affElement(\''.$data['page'].'\',\'\',\'\',\'\',\'page\');" title="'.$data['title'].'"><img height="14px" src="public/images/icons/'.$data['page'].'.png" alt="icon"/> '.$data['titre_menu'].'</li>';
}
$html_top.='			</ul>
		</div>
		<div id="page">';
echo $html_top;
