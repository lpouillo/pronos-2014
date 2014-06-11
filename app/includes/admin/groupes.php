<?php
$params=array(
		'common' => array(
				'titre' => 'Utilisateurs',
				'element' => 'un utilisateur',
				'icone_titre' => 'group',
				'icone_ajout' => 'ajouter'),
		'liste' => array(
			'message' => 'Voici la liste des utilisateurs qui participent au tournoi de pronostic',
			'sql' => "SELECT G.id_groupe, G.nom, G.description, U.login
				FROM groupes G
				LEFT JOIN users U
					ON U.id_user=G.id_owner ",
			'post_sql' => " ORDER BY U.login, G.nom ",
			'champs' => array(
				'id_groupe' => array('id_groupe','G.id_groupe'),
				'login' => array('login','G.id_owner'),
				'nom' => array('Nom','G.nom'),
				'description' => array('Description','G.description')))
	);
if (isset($_POST['action'])) {
	$action=$_POST['action'];
} else if (isset($_GET['action'])) {
	$action=$_GET['action'];
} else {
	$action='liste';
}


$texte_bouton=ucfirst($action);

if (empty($_POST['filtrage_soumis'])) {
	$html.='<div id="content" class="content_tab">';
}

switch($action) {
	case 'modifier':
	case 'ajouter':
	case 'supprimer':
		$id=$_GET['id'];
	break;
	default:
	$html.=creation_table($params['liste']['sql'],$params['liste']['champs'],$params['liste']['post_sql']);
}
switch($action) {
	case 'modifier':
	// Création de l'entête avec le select sur l'entité considérée
		$html.='<img src="public/images/icons/'.$params['common']['icone_titre'].'.png"/>

				<form id="update" method="post" action="index.php">' .
				'<input type="submit" value="'.$texte_bouton.'"/>
				<input type="hidden" name="requete" value="modifier_groupe">
				<input type="hidden" name="page" value="admin">
				<input type="hidden" name="section" value="groupes">
				<input type="hidden" name="id" value="'.$id.'">
				<input type="hidden" name="action" value="'.$action.'">';
		$s_groupe="SELECT * FROM groupes WHERE id_groupe='".$id."'";

		$r_groupe=mysqli_query($db_pronos, $s_groupe);
		$d_groupe=mysqli_fetch_array($r_groupe);
		$actif=($d_groupe['actif'])?' checked="checked" ':'';
		$html.='<table>
					<tr>
						<th>id_groupe</th><td><input type="text" name="id_groupe" value="'.$d_groupe['id_groupe'].'" readonly/></td>
					</tr>
					<tr>
						<th>Date de création</th><td><input type="text" name="date_in" value="'.$d_groupe['date_in'].'" readonly/></td>
					</tr>
					<tr>
						<th>Date de modification</th><td><input type="text" name="date_modif" value="'.$d_groupe['date_modif'].'" readonly/></td>
					</tr>
					<tr>
						<th>Nom</th><td><input type="text" name="nom" value="'.$d_groupe['nom'].'"/></td>
					</tr>
					<tr>
						<th>Description</th><td><textarea name="description">'.$d_groupe['description'].'</textarea></td>
					</tr>
					<tr>
						<th>Actif</th><td><input type="checkbox" name="actif" value="1" '.$actif.'/></td>
					</tr>
					<tr>
						<th>Propriétaire</th><td></td><select name="id_owner">';
		$s_user="SELECT id_user, login FROM users";
		$r_user=mysqli_query($db_pronos,$s_user);
		while ($d_user=mysqli_fetch_array($r_user)) {
			$sel=($d_user['id_user']==$d_groupe['id_owner'])?' selected="selected" ':'';
			$html.='<option value="'.$d_user['id_user'].'" '.$sel.'>'.$d_user['login'].'</option>';
		}
		$html.='	</select>
					</td>
				</tr>
				</table>
			</form>';
	break;
	case 'supprimer':
	case 'ajouter':
		$html.='<img src="public/images/icons/'.$params['common']['icone_titre'].'.png"/>
				<input type="submit" value="'.$texte_bouton.'" onClick="submitForm(\'update\')"/>
				<form id="update" method="post" action="index.php">
				<input type="hidden" name="requete" value="'.$action.'_groupe">
				<input type="hidden" name="page" value="'.$_POST['page'].'">
				<input type="hidden" name="section" value="'.$_POST['section'].'">
				<input type="hidden" name="id" value="'.$id.'">
				<input type="hidden" name="action" value="'.$action.'">
				<input type="hidden" name="div_target" value="page">
				<table>
					<tr>
						<th>id_groupe</th><td><input type="text" name="id_groupe" value="Automatique" readonly/></td>
					</tr>
					<tr>
						<th>Date de création</th><td><input type="text" name="date_in" value="Automatique" readonly/></td>
					</tr>
					<tr>
						<th>Date de modification</th><td><input type="text" name="date_modif" value="Automatique" readonly/></td>
					</tr>
					<tr>
						<th>Nom</th><td><input type="text" name="nom" /></td>
					</tr>
					<tr>
						<th>Description</th><td><textarea name="description"></textarea></td>
					</tr>
					<tr>
						<th>Actif</th><td><input type="checkbox" name="actif" value="1" /></td>
					</tr>
					<tr>
				</table>
			</form>';
	break;

}
if (empty($_POST['filtrage_soumis'])) {
	$html.'</div>';
}


