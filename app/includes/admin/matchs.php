<?php
$params=array(
		'common' => array(
				'titre' => 'Matchs',
				'element' => 'un match',
				'icone_titre' => 'match', 
				'icone_ajout' => 'ajouter'),
		'liste' => array(
			'message' => 'Voici la liste des matchs à jouer',
			'sql' => "SELECT M.id_match, M.date_match, M.heure, EQ1.nom AS eq1, EQ2.nom AS eq2, M.score1, M.score2, M.joue, M.type
				FROM matchs M
				LEFT JOIN equipes EQ1 
					ON M.id_equipe1=EQ1.id_equipe
				LEFT JOIN equipes EQ2 
					ON M.id_equipe2=EQ2.id_equipe",
			'post_sql' => " ORDER BY M.date_match, M.heure",
			'champs' => array(
				'id_match' => array('id_match','M.id_match'), 
				'date_match' => array('Date','M.date_match'), 
				'heure' => array('Heure','M.heure'), 
				'eq1' => array('Equipe 1','EQ1.nom'),
				'score1' => array('Score 1','M.score1'),
				'score2' => array('Score 2','M.score2'), 
				'eq2' => array('Equipe 2','EQ2.nom'), 
				'poule' => array('Poule','E.poule'),
				'type' => array('Type','M.type'),
				'joue' => array('Joué','M.joue')))
	);
if (isset($_POST['action'])) {
	$action=$_POST['action'];
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
				<input type="hidden" name="requete" value="modifier_match">
				<input type="hidden" name="page" value="'.$_POST['page'].'">
				<input type="hidden" name="section" value="'.$_POST['section'].'">
				<input type="hidden" name="id" value="'.$id.'">
				<input type="hidden" name="action" value="'.$action.'">
				<input type="hidden" name="div_target" value="page">';
		$s_match="SELECT * FROM matchs WHERE id_match='".$id."'";
	
		$r_match=mysql_query($s_match);
		$d_match=mysql_fetch_array($r_match);
		$html.='<table>
					<tr>
						<th>id_match</th><td><input size="2" type="text" name="id_match" value="'.$d_match['id_match'].'" readonly/></td>
						<th>Date de création</th><td><input size="8"  type="text" name="date_in" value="'.$d_match['date_in'].'" readonly/></td>
						<th>Date de modification</th><td><input  size="8" type="text" name="date_modif" value="'.$d_match['date_modif'].'" readonly/></td>
					</tr>
					<tr>
						<th>Date du match</th><td ><input type="text" id="date_match" name="date_match" class="DatePicker" tabindex="1" value="'.$d_match['date_match'].'" ></td>
						<th>Heure</th><td colspan="2"><input size="8" type="text" name="heure" value="'.$d_match['heure'].'" /></td>
					</tr>
					<tr>
						<th>Equipe 1</th><td><select name="id_equipe1">
						<option value="0">Aucune équipe choisie</option>';
		$s_equipes="SELECT id_equipe, nom, poule FROM equipes ORDER BY poule,nom";		
		$r_equipes=mysql_query($s_equipes);
		$eq_par_poules=array();
		while ($d_equipe=mysql_fetch_array($r_equipes)) {
			$eq_par_poules[$d_equipe['poule']][$d_equipe['id_equipe']]=$d_equipe['nom'];
		}	
		foreach($eq_par_poules as $poule => $equipes) {
			$html.='<optgroup label="Poule '.$poule.'">';
			foreach($equipes as $id_equipe => $nom) {
				$sel=($id_equipe==$d_match['id_equipe1'])?' selected="selected"':'';
				$html.='<option value="'.$id_equipe.'" '.$sel.'>'.$nom.'</option>';
			}
			$html.='</optgroup>';
		}
		$html.='</select></td>
						<td><input type="text" size="2" name="score1" value="'.$d_match['score1'].'"/></td>
						<td><input type="text" size="2" name="score2" value="'.$d_match['score2'].'"/></td>
						<th>Equipe 2</th><td><select name="id_equipe2">
						<option value="0">Aucune équipe choisie</option>';
		foreach($eq_par_poules as $poule => $equipes) {
			$html.='<optgroup label="Poule '.$poule.'">';
			foreach($equipes as $id_equipe => $nom) {
				$sel=($id_equipe==$d_match['id_equipe2'])?' selected="selected"':'';
				$html.='<option value="'.$id_equipe.'" '.$sel.'>'.$nom.'</option>';
			}
			$html.='</optgroup>';
		}
		$spec=($d_match['special'])?' checked="checked"':'';
		$joue=($d_match['joue'])?' checked="checked"':'';
		$type=array('Huitieme' => 8, 'Quart' => 4, 'Demi' => 2);
		$html.='</select></td>
					</tr>
					<tr>
						<td colspan="2">&nbsp;</td>
						<td><input type="text" size="2" name="tab1" value="'.$d_match['tab1'].'"/></td>
						<td><input type="text" size="2" name="tab2" value="'.$d_match['tab2'].'"/></td>
					</tr>
					<tr>
						<th>Type</th><td><select name="type">
						<option value="poule">Poule</option>';
		foreach($type as $typ => $n) {
			$html.='<optgroup label="'.$typ.'s">';
			for ($i=1;$i<=$n;$i++) {
				$sel=($typ.$i==$d_match['type'])?' selected="selected" ':'';
				$html.='<option value="'.$typ.$i.'" '.$sel.'>'.$typ.' '.$i.'</option>';
			}
			$html.='</optgroup>';
		}
		$sel_pf=($d_match['type']=='p_finale')?' selected="selected"':'';
		$sel_f=($d_match['type']=='Finale')?' selected="selected"':'';
		$html.='<optgroup label="Finales">
				<option value="p_finale" '.$sel_pf.'>Petite finale</option>
				<option value="Finale" '.$sel_f.'>Finale</option>
			</optgroup></select></td>
						<th>Spécial</th><td><input type="checkbox" value="1" name="special" '.$spec.'/></td>
						<th>Joué</th><td><input type="checkbox" name="joue" value="1" '.$joue.'/></td>
					</tr>
				</table>';
	
		$html.='</form>';			
	break;
	case 'supprimer':
	case 'ajouter':
		$html.='<img src="public/images/icons/'.$params['common']['icone_titre'].'.png"/>
				<input type="submit" value="'.$texte_bouton.'" onClick="submitForm(\'update\')"/>
				<form id="update" method="post" action="index.php">
				<input type="hidden" name="requete" value="'.$action.'_match">
				<input type="hidden" name="page" value="'.$_POST['page'].'">
				<input type="hidden" name="section" value="'.$_POST['section'].'">
				<input type="hidden" name="id" value="'.$id.'">
				<input type="hidden" name="action" value="'.$action.'">
				<input type="hidden" name="div_target" value="page">';
		$html.='<table>
					<tr>
						<th>id_match</th><td><input size="2" type="text" name="id_match" value="'.$d_match['id_match'].'" readonly/></td>
						<th>Date de création</th><td colspan="2"><input size="8" type="text" name="date_in" value="'.$d_match['date_in'].'" readonly/></td>
						<th>Date de modification</th><td colspan="2"><input size="8" type="text" name="date_modif" value="'.$d_match['date_modif'].'" readonly/></td>
					</tr>
					<tr>
						<th>Date du match</th><td ><input type="text" id="date_match" name="date_match" class="DatePicker" tabindex="1" value="'.$d_match['heure'].'" ></td>
						<th>Heure</th><td colspan="2"><input size="8" type="text" name="heure" value="'.$d_match['heure'].'" /></td>
					</tr>
					<tr>
						<th>Equipe 1</th><td><select name="id_equipe1">
						<option value="0">Aucune équipe choisie</option>';
		$s_equipes="SELECT id_equipe, nom, poule FROM equipes ORDER BY poule,nom";		
		$r_equipes=mysql_query($s_equipes);
		$eq_par_poules=array();
		while ($d_equipe=mysql_fetch_array($r_equipes)) {
			$eq_par_poules[$d_equipe['poule']][$d_equipe['id_equipe']]=$d_equipe['nom'];
		}	
		foreach($eq_par_poules as $poule => $equipes) {
			$html.='<optgroup label="Poule '.$poule.'">';
			foreach($equipes as $id_equipe => $nom) {
				$html.='<option value="'.$id_equipe.'">'.$nom.'</option>';
			}
			$html.='</optgroup>';
		}
		$html.='</select></td>
						<td><input type="text" size="2" name="score1"/></td>
						<td><input type="text" size="2" name="score2"/></td>
						<th>Equipe 2</th><td><select name="id_equipe2">
						<option value="0">Aucune équipe choisie</option>';
		foreach($eq_par_poules as $poule => $equipes) {
			$html.='<optgroup label="Poule '.$poule.'">';
			foreach($equipes as $id_equipe => $nom) {
				$html.='<option value="'.$id_equipe.'">'.$nom.'</option>';
			}
			$html.='</optgroup>';
		}
		$html.='</select></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:right;">T.A.B 1</td>
						<td><input type="text" size="2" name="tab1"/></td>
						<td><input type="text" size="2" name="tab2"/></td>
						<td colspan="2">T.A.B 2</td>
					</tr>
					<tr>
						<th>Type</th><td><select name="type">
						<option value="poule">Poule</option>';
		$type=array('Huitieme' => 8, 'Quart' => 4, 'Demi' => 2, 'Finale' => 1);
		
		foreach($type as $typ => $n) {
			$html.='<optgroup label="'.$typ.'s">';
			for ($i=1;$i<=$n;$i++) {
				$html.='<option value="'.$typ.$i.'">'.$typ.' '.$i.'</option>';
			}
			$html.='</optgroup>';
		}
		$html.='</select></td>
						<th>Spécial</th><td><input type="checkbox" value="1" name="special"/></td>
						<th>Joué</th><td><input type="checkbox" name="joue" value="1"/></td>
					</tr>
					<tr>
					</tr>
				</table>';
	
		$html.='</form>';			
;
	break;
	
}
if (empty($_POST['filtrage_soumis'])) {
	$html.'</div>';
}


