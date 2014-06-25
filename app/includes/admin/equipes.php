<?php
$params=array(
		'common' => array(
				'titre' => 'Équipes',
				'element' => 'une équipe',
				'icone_titre' => 'equipe', 
				'icone_ajout' => 'ajouter'),
		'liste' => array(
			'message' => 'Voici la liste des équipes qui participent à la competition',
			'sql' => "SELECT E.id_equipe, E.nom, E.acronym, E.poule FROM equipes E",
			'post_sql' => " ORDER BY E.poule, E.nom",
			'champs' => array(
				'id_equipe' => array('id_equipe','E.id_equipe'), 
				'nom' => array('Nom','E.nom'), 
				'acronym' => array('Acronym','E.acronym'), 
				'poule' => array('Poule','E.poule')))
	);


$section='equipes';

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
				<input type="hidden" name="requete" value="modifier_equipe">
				<input type="hidden" name="page" value="'.$_POST['page'].'">
				<input type="hidden" name="section" value="'.$_POST['section'].'">
				<input type="hidden" name="id" value="'.$id.'">
				<input type="hidden" name="action" value="'.$action.'">
				<input type="hidden" name="div_target" value="page">';
		$s_equipe="SELECT * FROM equipes WHERE id_equipe='".$id."'";
	
		$r_equipe=mysql_query($s_equipe);
		$d_equipe=mysql_fetch_array($r_equipe);
		$html.='<table>
					<tr>
						<th>id_equipe</th><td><input type="text" name="id_equipe" value="'.$d_equipe['id_equipe'].'" readonly/></td>
					</tr>
					<tr>
						<th>Date de création</th><td><input type="text" name="date_in" value="'.$d_equipe['date_in'].'" readonly/></td>
					</tr>
					<tr>
						<th>Date de modification</th><td><input type="text" name="date_modif" value="'.$d_equipe['date_modif'].'" readonly/></td>
					</tr>
					<tr>
						<th>Nom</th><td><input type="text" name="nom" value="'.$d_equipe['nom'].'"/></td>
					</tr>
					<tr>
						<th>Acronyme</th><td><input type="text" name="acronym" value="'.$d_equipe['acronym'].'"/></td>
					</tr>
					<tr>
						<th>Poule</th><td><select name="poule">
						<option value="0">Non défini</option>';
		for ($i=1;$i<=$cup_groups;$i++) {
			$sel=($i==$d_equipe['poule'])?'selected="selected"':'';
			$html.='<option value="'.$i.'" '.$sel.'>Poule '.$i.'</option>';
		}
		$html.='</select></td>
					</tr>
				</table>';
	
		$html.='</form>';			
	break;
	case 'supprimer':
	case 'ajouter':
		$html.='<img src="public/images/icons/'.$params['common']['icone_titre'].'.png"/>
				<input type="submit" value="'.$texte_bouton.'" onClick="submitForm(\'update\')"/>
				<form id="update" method="post" action="index.php">
				<input type="hidden" name="requete" value="'.$action.'_equipe">
				<input type="hidden" name="page" value="'.$_POST['page'].'">
				<input type="hidden" name="section" value="'.$_POST['section'].'">
				<input type="hidden" name="id" value="'.$id.'">
				<input type="hidden" name="action" value="'.$action.'">
				<input type="hidden" name="div_target" value="page">
				<table>
					<tr>
						<th>id_equipe</th><td><input type="text" name="id_equipe" value="Automatique" readonly/></td>
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
						<th>Acronyme</th><td><input type="text" name="acronym" /></td>
					</tr>
					<tr>
						<th>Poule</th><td><select name="poule">
						<option value="0">Non défini</option>';
		for ($i=1;$i<=$cup_groups;$i++) {
			
			$html.='<option value="'.$i.'">Poule '.$i.'</option>';
		}
		$html.='</select></td>
					</tr>
				</table>';
	break;
	
}
if (empty($_POST['filtrage_soumis'])) {
	$html.'</div>';
}


