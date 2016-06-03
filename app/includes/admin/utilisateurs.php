<?php
$html='<div class="box">';
$params=array(
		'common' => array(
				'titre' => 'Utilisateurs',
				'element' => 'un utilisateur',
				'icone_titre' => 'user', 
				'icone_ajout' => 'ajouter'),
		'liste' => array(
			'message' => 'Voici la liste des utilisateurs qui participent au tournoi de pronostic',
			'sql' => "SELECT U.id_user, U.login, U.nom_reel FROM users U",
			'post_sql' => " ORDER BY U.login, U.nom_reel",
			'champs' => array(
				'id_user' => array('id_user','U.id_user'), 
				'login' => array('Login','E.nom_reel'), 
				'nom_reel' => array('Nom réel','U.nom_reel')))
	);
if (isset($_POST['action'])) {
	$action=$_POST['action'];
} else {
	$action='';
}

$texte_bouton=ucfirst($action);

if (empty($_POST['filtrage_soumis'])) {
	$html.='<div id="content" class="content_tab">';
}
$mode="rw";
switch($action) {
	case 'modifier':
	case 'ajouter':
	case 'supprimer':
		$id=$_POST['id'];
	break;
	default:
	$html.=creation_table($params['liste']['sql'],$params['liste']['champs'],$params['liste']['post_sql'],$mode);
}
switch($action) {
	case 'modifier':
	// Création de l'entête avec le select sur l'entité considérée
		$html.='<img src="public/images/icons/'.$params['common']['icone_titre'].'.png"/>
				<input type="submit" value="'.$texte_bouton.'" onClick="submitForm(\'update\')"/>
				<form id="update" method="post" action="index.php">
				<input type="hidden" name="requete" value="modifier_user">
				<input type="hidden" name="page" value="'.$_POST['page'].'">
				<input type="hidden" name="section" value="'.$_POST['section'].'">
				<input type="hidden" name="id" value="'.$id.'">
				<input type="hidden" name="action" value="'.$action.'">
				<input type="hidden" name="div_target" value="page">';
		$s_user="SELECT * FROM users WHERE id_user='".$id."'";
	
		$r_user=mysql_query($s_user);
		$d_user=mysql_fetch_array($r_user);
		
		$actif=($d_user['actif'])?' checked="checked" ':'';
		$html.='<table>
					<tr>
						<th>id_user</th><td><input type="text" name="id_user" value="'.$d_user['id_user'].'" readonly/></td>
					</tr>
					<tr>
						<th>Date de création</th><td><input type="text" name="date_in" value="'.$d_user['date_in'].'" readonly/></td>
					</tr>
					<tr>
						<th>Date de modification</th><td><input type="text" name="date_modif" value="'.$d_user['date_modif'].'" readonly/></td>
					</tr>
					<tr>
						<th>Login</th><td><input type="text" name="login" value="'.$d_user['login'].'"/></td>
					</tr>
					<tr>
						<th>Nom réel</th><td><input type="text" name="nom_reel" value="'.$d_user['nom_reel'].'"/></td>
					</tr>
					<tr>
						<th>Email</th><td><input type="text" name="email" value="'.$d_user['email'].'"/></td>
					</tr>
					<tr>
						<th>Actif</th><td><input type="checkbox" name="actif" value="1" '.$actif.'/></td>
					</tr>
				</table>
			</form>';			
	break;
	case 'supprimer':
	case 'ajouter':
		$html.='<img src="public/images/icons/'.$params['common']['icone_titre'].'.png"/>
				<input type="submit" value="'.$texte_bouton.'" onClick="submitForm(\'update\')"/>
				<form id="update" method="post" action="index.php">
				<input type="hidden" name="requete" value="'.$action.'_user">
				<input type="hidden" name="page" value="'.$_POST['page'].'">
				<input type="hidden" name="section" value="'.$_POST['section'].'">
				<input type="hidden" name="id" value="'.$id.'">
				<input type="hidden" name="action" value="'.$action.'">
				<input type="hidden" name="div_target" value="page">
				<table>
					<tr>
						<th>id_user</th><td><input type="text" name="id_user" value="Automatique" readonly/></td>
					</tr>
					<tr>
						<th>Date de création</th><td><input type="text" name="date_in" value="Automatique" readonly/></td>
					</tr>
					<tr>
						<th>Date de modification</th><td><input type="text" name="date_modif" value="Automatique" readonly/></td>
					</tr>
					<tr>
						<th>Login</th><td><input type="text" name="login" /></td>
					</tr>
					<tr>
						<th>Nom réel</th><td><input type="text" name="nom_reel" /></td>
					</tr>
					<tr>
						<th>Email</th><td><input type="text" name="email"/></td>
					</tr>
					<tr>
						<th>Actif</th><td><input type="checkbox" name="actif" value="1" /></td>
					</tr>				</table>
			</form>';
	break;
	
}
if (empty($_POST['filtrage_soumis'])) {
	$html.'</div>';
}

$html.='</div>';


