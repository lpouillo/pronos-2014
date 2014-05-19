<?php
/* On va tester selon la date et l'heure ce qu'il faut afficher 
 * en utilisant les variables timestamp */

//echo time().'<br/>'.$timestamp_poules_debut;

if (time()<$timestamp_poules_debut) {
	// On affiche les poules modifiables et le tableau en grisé ainsi la DL
	$en_premier=&$html_poules;
	$en_second=&$html_tableau;
	$poule_edit=1;
	$tableau_edit=0;
	$message='Il vous reste encore '.transforme($timestamp_poules_debut-time()).' secondes pour 
	parier sur la phase de poules. La seconde phase concernant le tableau final débutera le '
	.strftime('%A %d %B à %H:%M',$timestamp_poules_fin).'.<br/> Les matchs encadrés en 
	<span style="color:red">ROUGE</span> compte double.';
} elseif (time()<$timestamp_poules_fin) {
	// On affiche tout en grisé avec la date de la seconde phase de paris
	$en_premier=&$html_poules;
	$en_second=&$html_tableau;
	$poule_edit=0;
	$tableau_edit=0;
	$message='Voici vos pronostics pour les poules et les points qu\'ils vous ont rapportés. Vous pourrez
	parier pour le tableau final à compter du '.strftime('%A %d %B à %H:%M',$timestamp_poules_fin).' et
	jusqu\'au '.strftime('%A %d %B à %H:%M',$timestamp_tableau_debut).'.<br/> Les matchs encadrés en 
	<span style="color:red">ROUGE</span> compte double.';
	
} elseif (time()<$timestamp_tableau_debut) {
	// On affiche les poules en grisé et en second ainsi que le tableau éditable
	$en_premier=&$html_tableau;
	$en_second=&$html_poules;
	$poule_edit=0;
	$tableau_edit=1;
	$message='Il vous reste encore '.transforme($timestamp_tableau_debut-time()).' secondes pour 
	parier sur le tableau final.';
} elseif (time()<$timestamp_tableau_fin) {
	// On affiche tout en grisé avec la date de la finale de l'euro
	
	
	
}


/* On récupère toutes les données des matchs de poules */
// Récupération des matchs
$s_matchs="SELECT M.id_match, M.date_match, M.heure, M.id_equipe1, M.score1 AS res1, M.score2 AS res2, M.id_equipe2, M.joue, M.cote_1, M.cote_N, M.cote_2,
			EQ1.acronym AS ac1, EQ1.nom AS eq1, EQ1.poule AS poule, EQ2.acronym AS ac2, EQ2.nom AS eq2, M.special,
			P.score1, P.score2, P.tab1, P.tab2, P.points
			FROM matchs M
			INNER JOIN equipes EQ1
				ON EQ1.id_equipe=M.id_equipe1
			INNER JOIN equipes EQ2
				ON EQ2.id_equipe=M.id_equipe2
			LEFT JOIN pronos P
				ON P.id_user='".$_SESSION['id_user']."'
				AND P.id_match=M.id_match
			WHERE M.type='poule'
			ORDER BY EQ1.poule, M.date_match, M.heure";

$r_matchs=mysql_query($s_matchs)
	or die($s_matchs.'<br/>'.mysql_error());
$mat_par_poule=array();
while ($d_matchs=mysql_fetch_array($r_matchs)) {
	$mat_par_poule[$d_matchs['poule']][]=$d_matchs;
}

// Création des poules
$poules=array();	
for ($i=1;$i<=$cup_groups;$i++) {
// Pour toutes les poules, on calcule les V, N, défaites de chaque équipe
	foreach($mat_par_poule[$i] as $match) {		
		$poules[$i][$match['id_equipe1']]['pts']=0;
		$poules[$i][$match['id_equipe1']]['diff']=0;
		$poules[$i][$match['id_equipe1']]['but_p']+=$match['score1'];
		$poules[$i][$match['id_equipe1']]['but_c']+=$match['score2']; 
		$poules[$i][$match['id_equipe1']]['nom']=$match['eq1'];
		$poules[$i][$match['id_equipe1']]['acronym']=$match['ac1'];
		$poules[$i][$match['id_equipe1']]['V']+=($match['score1']>$match['score2'])?1:0;
		$poules[$i][$match['id_equipe1']]['N']+=($match['score1']==$match['score2'])?1:0;
		$poules[$i][$match['id_equipe1']]['D']+=($match['score1']<$match['score2'])?1:0;
		
		$poules[$i][$match['id_equipe2']]['pts']=0;
		$poules[$i][$match['id_equipe2']]['diff']=0;
		$poules[$i][$match['id_equipe2']]['but_p']+=$match['score2'];
		$poules[$i][$match['id_equipe2']]['but_c']+=$match['score1'];
		$poules[$i][$match['id_equipe2']]['nom']=$match['eq2']; 
		$poules[$i][$match['id_equipe2']]['acronym']=$match['ac2'];
		$poules[$i][$match['id_equipe2']]['V']+=($match['score1']<$match['score2'])?1:0;
		$poules[$i][$match['id_equipe2']]['N']+=($match['score1']==$match['score2'])?1:0;
		$poules[$i][$match['id_equipe2']]['D']+=($match['score1']>$match['score2'])?1:0;		
	}
// on calcule ensuite ses points
	foreach($poules[$i] as $nom => &$equipe) {
		$equipe['pts']=(3*$equipe['V']+$equipe['N']);
		$equipe['diff']=$equipe['but_p']-$equipe['but_c'];
	}
	rsort($poules[$i]);
}


// Création du html des poules
$html_poules.='<tr>
	<td colspan="4"><h2>Poules</h2></td>
	</tr><tr>';
for($i=1;$i<=4;$i++) {
	$html_poules.='<th>Poule '.$i.'</th>';
}
$html_poules.='</tr><tr>';
for($i=1;$i<=4;$i++) {
	$html_poules.='
		<td class="td_poule">
			<table>';
	foreach($mat_par_poule[$i] as $match) {
		// on définit les différents éléments
		$cote=($poule_edit)?'à venir':$match['cote_1'].' / '.$match['cote_N'].' / '.$match['cote_2'];
		$pari1=($poule_edit)?'<input class="score" type="text" size="1" name="pronos['.$match['id_match'].'][score1]" value="'.$match['score1'].'"/>':
			'<span class="score">'.$match['score1'].'</span>';
		$pari2=($poule_edit)?'<input class="score" type="text" size="1" name="pronos['.$match['id_match'].'][score2]" value="'.$match['score2'].'" />':
			'<span class="score">'.$match['score2'].'</span>';
		$resultat=($match['joue'])?'<td class="score">'.$match['res1'].'</td><td class="score">'.$match['res2'].'</td><td style="color:red;">'.$match['points'].' points</td>':
			'<td colspan="3" style="text-align:center;">à venir</td>';
		$spec=($match['special'])?'special ':'';
		// On constuire le bloc de ligne correspondant au match
		$html_poules.='<tr>
				<td colspan="4"><span class="date">Le '.dateMysqlToFormatted($match['date_match'],$match['heure']).'</span></td>
				</tr>
				<tr>
					<td class="'.$spec.' equipe"><img src="public/images/flags/'.$match['ac1'].'.gif" alt="flag"/></br>'.$match['eq1'].'</td>
					<td>'.$pari1.'</td>
					<td>'.$pari2.'</td>
					<td class="'.$spec.' equipe" style="text-align:right;"><img src="public/images/flags/'.$match['ac2'].'.gif" alt="flag"/></br>'.$match['eq2'].'</td>
			</tr>
			<tr><td>Résultat</td>'.$resultat.'</tr>
			<tr><td style="border-bottom:1px dotted #00774B;">Cote</td>
				<td colspan="3" style=" border-bottom:1px dotted #00774B;text-align:center">'.$cote.'</td></tr>';
	}
	$html_poules.='</table><table class="poule">
				<tr>
					<th>Rang</th><th>Equipe</th><th>Pts</th><th>V</th><th>N</th><th>D</th><th>Diff</th>
				</tr>';
	for ($k=0;$k<=3;$k++) {
		$html_poules.='<tr>
					<td>'.($k+1).'</td>
					<td style="text-align:left;"><img src="public/images/flags/'.$poules[$i][$k]['acronym'].'.gif" alt="flag"/> '.$poules[$i][$k]['nom'].'</td>
					<td>'.$poules[$i][$k]['pts'].'</td>
					<td>'.$poules[$i][$k]['V'].'</td>
					<td>'.$poules[$i][$k]['N'].'</td>
					<td>'.$poules[$i][$k]['D'].'</td>
					<td>'.$poules[$i][$k]['diff'].'</td>
					
				</tr>';
	}
	$html_poules.='</table>';
}
$html_poules.='</td></tr>';


// On récupère toutes les données des matchs


// Création du html pour le tableau final

$html_tableau.='<tr>
	<td colspan="4"><h2>Tableau final</h2></td></tr>
	<tr>
	<td colspan="4">Il faut attendre la fin des matchs de poules avant de pouvoir parier sur le tableau final
		soit dans '.transforme($timestamp_poules_fin-time()).' !';
/*
switch($cup_teamnumber) {
	case 16:
		$html_tableau.='<table border=0 id="tableau_final">
					<tr>
						<td rowspan="2" class="match">
							'.pronostableau($quarts[1]).'
						</td>
						<td rowspan="17" style="background-color:#00774B;">&nbsp;</td>
						<td rowspan="2">&nbsp;</td>
					</tr>
					
					<tr>
						<td rowspan="3">&nbsp;</td><td rowspan="3"  class="match">
							'.pronostableau($demis[1]).'
						</td>
						<td rowspan="13" style="background-color:#00774B;">&nbsp;</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					
					<tr>
						<td rowspan="2" class="match">
							'.pronostableau($quarts[2]).'
						</td>
						<td rowspan="2">&nbsp;</td>
					</tr>
					
					<tr>
						<td rowspan="3" colspan="2">&nbsp;</td>
					
						<td class="match" rowspan="3">'.pronostableau($finale).'</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td rowspan="2" class="match">
							'.pronostableau($quarts[3]).'		
						</td>
						<td rowspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td rowspan="3">&nbsp;</td>
						<td rowspan="3" class="match">
							'.pronostableau($demis[2]).'
						</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td rowspan="2" class="match">
							'.pronostableau($quarts[4]).'
						</td>
					
					</tr>
					<tr><td rowspan="3">&nbsp;</td></tr>
				</table>';
	break;
	case 32:
		$html_tableau.='<table border=0 id="tableau_final">
					<tr>
						<td rowspan="2" class="match">
							'.pronostableau($huitiemes[1]).'
						</td>
						<td rowspan="23" style="background-color:#00774B;">&nbsp;</td>
						<td colspan="5">&nbsp;</td>
						
					</tr>
					<tr>
						<td rowspan="2" class="match">
							'.pronostableau($quarts[1]).'
						</td>
						<td rowspan="17" style="background-color:#00774B;">&nbsp;</td>
						<td rowspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td rowspan="2" class="match">
							'.pronostableau($huitiemes[2]).'
						</td>
						
					</tr>
					<tr>
						<td rowspan="3">&nbsp;</td><td rowspan="3"  class="match">
							'.pronostableau($demis[1]).'
						</td>
						<td rowspan="13" style="background-color:#00774B;">&nbsp;</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td rowspan="2" class="match">
							'.pronostableau($huitiemes[3]).'
						</td>
					</tr>
					<tr>
						<td rowspan="2" class="match">
							'.pronostableau($quarts[2]).'
						</td>
						<td rowspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td rowspan="2" class="match">
							'.pronostableau($huitiemes[4]).'
						</td>
						
					</tr>
					<tr>
						<td rowspan="3">&nbsp;</td>
						<td class="match" rowspan="3">'.pronostableau($petite_finale).'</td>
						<td class="match" rowspan="3">'.pronostableau($finale).'</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td rowspan="2" class="match">
							'.pronostableau($huitiemes[5]).'
						</td>
						
					</tr>
					<tr>
						<td rowspan="2" class="match">
							'.pronostableau($quarts[3]).'		
						</td>
						<td rowspan="2">&nbsp;</td>
					</tr>
					<tr>
						<td rowspan="2" class="match">
							'.pronostableau($huitiemes[6]).'
						</td>
					</tr>
					<tr>
						<td rowspan="3">&nbsp;</td>
						<td rowspan="3" class="match">
							'.pronostableau($demis[2]).'
						</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td rowspan="2" class="match">
							'.pronostableau($huitiemes[7]).'
						</td>
					</tr>
					<tr>
						<td rowspan="2" class="match">
							'.pronostableau($quarts[4]).'
						</td>
					
					</tr>
					<tr>
						<td rowspan="2" class="match">
							'.pronostableau($huitiemes[8]).'
						</td>
					</tr>
					<tr><td rowspan="3">&nbsp;</td></tr>
				</table>';	
	break;
}
	



$html_tableau.='</tr>';
/*
$eq_huitieme=array();
for ($i=1;$i<=4;$i++) {
	$eq_huitieme[$i]=array('id_equipe1' => $poules[$i+$j][0]['nom'], 'id_equipe2' => $poules[$i+$j+1][1]['nom']);
	$eq_huitieme[$i+4]=array('id_equipe1' => $poules[$i+$j][1]['nom'], 'id_equipe2' => $poules[$i+$j+1][0]['nom']);
	$j++;
}
// Récupération des équipes
$s_equipes="SELECT id_equipe, nom, acronym FROM equipes";
$r_equipes=mysql_query($s_equipes);
$equipes=array();
while ($d_equipes=mysql_fetch_array($r_equipes)) {
	$equipes[$d_equipes['id_equipe']]=array('nom' => $d_equipes['nom'], 'acronym' => $d_equipes['acronym']);
}

// Récupération de tous les matchs du tableau et des paris du joueur
$s_tableau="SELECT M.id_match, M.date_match, M.heure, M.cote_1, M.cote_N, M.cote_2,
					M.score1, M.score2, M.tab1, M.tab2, M.type, M.id_equipe1, M.id_equipe2, 
					P.score1 AS p_score1, P.score2 AS p_score2, P.tab1 AS p_tab1, P.tab2 AS p_tab2, P.points
				FROM matchs M 
				LEFT JOIN pronos P
					ON P.id_user='".$_SESSION['id_user']."'
					AND P.id_match=M.id_match
				WHERE M.type<>'poule'";
$r_tableau=mysql_query($s_tableau)
	or die(mysql_error());
$matchs_tableau=array();
while ($d_tableau=mysql_fetch_array($r_tableau)) {
	$matchs_tableau[$d_tableau['type']]=$d_tableau;
}

// Traitement des huitiemes
$huitiemes=array();
for ($i=1;$i<=8;$i++) {
	$huitiemes[$i]=array(
		'match' => array(
			'id_match' => $matchs_tableau['Huitieme'.$i]['id_match'],
			'date_match' => $matchs_tableau['Huitieme'.$i]['date_match'],
			'heure' => $matchs_tableau['Huitieme'.$i]['heure'],
			'cote_1' => $matchs_tableau['Huitieme'.$i]['cote_1'],
			'cote_N' => $matchs_tableau['Huitieme'.$i]['cote_N'],
			'cote_2' => $matchs_tableau['Huitieme'.$i]['cote_2'],
			'id_equipe1' => $matchs_tableau['Huitieme'.$i]['id_equipe1'],
			'id_equipe2' => $matchs_tableau['Huitieme'.$i]['id_equipe2'],
			'score1' => $matchs_tableau['Huitieme'.$i]['score1'],
			'score2' => $matchs_tableau['Huitieme'.$i]['score2'],
			'tab1' => $matchs_tableau['Huitieme'.$i]['tab1'],
			'tab2' => $matchs_tableau['Huitieme'.$i]['tab2']),
		'pronos' => array(
			'id_equipe1' => $matchs_tableau['Huitieme'.$i]['id_equipe1'],
			'id_equipe2' => $matchs_tableau['Huitieme'.$i]['id_equipe2'],
			'score1' => $matchs_tableau['Huitieme'.$i]['p_score1'],
			'score2' => $matchs_tableau['Huitieme'.$i]['p_score2'],
			'tab1' => $matchs_tableau['Huitieme'.$i]['p_tab1'],
			'tab2' => $matchs_tableau['Huitieme'.$i]['p_tab2'],
			'points' => $matchs_tableau['Huitieme'.$i]['points'])
	);
}

// Traitement des quarts
$quarts=array();
$j=0;
for ($i=1;$i<=4;$i++) {
	$quarts[$i]=array(
		'match' => array(
			'id_match' => $matchs_tableau['Quart'.$i]['id_match'],
			'date_match' => $matchs_tableau['Quart'.$i]['date_match'],
			'heure' => $matchs_tableau['Quart'.$i]['heure'],
			'cote_1' => $matchs_tableau['Quart'.$i]['cote_1'],
			'cote_N' => $matchs_tableau['Quart'.$i]['cote_N'],
			'cote_2' => $matchs_tableau['Quart'.$i]['cote_2'],
			'id_equipe1' => $matchs_tableau['Quart'.$i]['id_equipe1'],
			'id_equipe2' => $matchs_tableau['Quart'.$i]['id_equipe2'],
			'score1' => $matchs_tableau['Quart'.$i]['score1'],
			'score2' => $matchs_tableau['Quart'.$i]['score2'],
			'tab1' => $matchs_tableau['Quart'.$i]['tab1'],
			'tab2' => $matchs_tableau['Quart'.$i]['tab2']),
		'pronos' => array(
			'id_equipe1' => vainqueur_match($huitiemes[$i+$j]['pronos']),
			'id_equipe2' => vainqueur_match($huitiemes[$i+$j+1]['pronos']),
			'score1' => $matchs_tableau['Quart'.$i]['p_score1'],
			'score2' => $matchs_tableau['Quart'.$i]['p_score2'],
			'tab1' => $matchs_tableau['Quart'.$i]['p_tab1'],
			'tab2' => $matchs_tableau['Quart'.$i]['p_tab2'],
			'points' => $matchs_tableau['Quart'.$i]['points'])
	);
	$j++;
}

// Traitement des demi-finales
$demis=array();
$j=0;
for ($i=1;$i<=2;$i++) {
	$demis[$i]=array(
		'match' => array(
			'id_match' => $matchs_tableau['Demi'.$i]['id_match'],
			'date_match' => $matchs_tableau['Demi'.$i]['date_match'],
			'heure' => $matchs_tableau['Demi'.$i]['heure'],
			'cote_1' => $matchs_tableau['Demi'.$i]['cote_1'],
			'cote_N' => $matchs_tableau['Demi'.$i]['cote_N'],
			'cote_2' => $matchs_tableau['Demi'.$i]['cote_2'],
			'id_equipe1' => $matchs_tableau['Demi'.$i]['id_equipe1'],
			'id_equipe2' => $matchs_tableau['Demi'.$i]['id_equipe2'],
			'score1' => $matchs_tableau['Demi'.$i]['score1'],
			'score2' => $matchs_tableau['Demi'.$i]['score2'],
			'tab1' => $matchs_tableau['Demi'.$i]['tab1'],
			'tab2' => $matchs_tableau['Demi'.$i]['tab2']),
		'pronos' => array(
			'id_equipe1' => vainqueur_match($quarts[$i+$j]['pronos']),
			'id_equipe2' => vainqueur_match($quarts[$i+$j+1]['pronos']),
			'score1' => $matchs_tableau['Demi'.$i]['p_score1'],
			'score2' => $matchs_tableau['Demi'.$i]['p_score2'],
			'tab1' => $matchs_tableau['Demi'.$i]['p_tab1'],
			'tab2' => $matchs_tableau['Demi'.$i]['p_tab2'],
			'points' => $matchs_tableau['Demi'.$i]['points'])
	);
	$j++;
}
// Traitement des finales
$petite_finale=array(
		'match' => array(
			'id_match' => $matchs_tableau['p_finale']['id_match'],
			'date_match' => $matchs_tableau['p_finale']['date_match'],
			'heure' => $matchs_tableau['p_finale']['heure'],
			'cote_1' => $matchs_tableau['p_finale']['cote_1'],
			'cote_N' => $matchs_tableau['p_finale']['cote_N'],
			'cote_2' => $matchs_tableau['p_finale']['cote_2'],
			'id_equipe1' => $matchs_tableau['p_finale']['id_equipe1'],
			'id_equipe2' => $matchs_tableau['p_finale']['id_equipe2'],
			'score1' => $matchs_tableau['p_finale']['score1'],
			'score2' => $matchs_tableau['p_finale']['score2'],
			'tab1' => $matchs_tableau['p_finale']['tab1'],
			'tab2' => $matchs_tableau['p_finale']['tab2']),
		'pronos' => array(
			'id_equipe1' => perdant_match($demis[1]['pronos']),
			'id_equipe2' => perdant_match($demis[2]['pronos']),
			'score1' => $matchs_tableau['p_finale']['p_score1'],
			'score2' => $matchs_tableau['p_finale']['p_score2'],
			'tab1' => $matchs_tableau['p_finale']['p_tab1'],
			'tab2' => $matchs_tableau['p_finale']['p_tab2'],
			'points' => $matchs_tableau['p_finale']['points'])
	);
$finale=array(
		'match' => array(
			'id_match' => $matchs_tableau['Finale']['id_match'],
			'date_match' => $matchs_tableau['Finale']['date_match'],
			'heure' => $matchs_tableau['Finale']['heure'],
			'cote_1' => $matchs_tableau['Finale']['cote_1'],
			'cote_N' => $matchs_tableau['Finale']['cote_N'],
			'cote_2' => $matchs_tableau['Finale']['cote_2'],
			'id_equipe1' => $matchs_tableau['Finale']['id_equipe1'],
			'id_equipe2' => $matchs_tableau['Finale']['id_equipe2'],
			'score1' => $matchs_tableau['Finale']['score1'],
			'score2' => $matchs_tableau['Finale']['score2'],
			'tab1' => $matchs_tableau['Finale']['tab1'],
			'tab2' => $matchs_tableau['Finale']['tab2']),
		'pronos' => array(
			'id_equipe1' => vainqueur_match($demis[1]['pronos']),
			'id_equipe2' => vainqueur_match($demis[2]['pronos']),
			'score1' => $matchs_tableau['Finale']['p_score1'],
			'score2' => $matchs_tableau['Finale']['p_score2'],
			'tab1' => $matchs_tableau['Finale']['p_tab1'],
			'tab2' => $matchs_tableau['Finale']['p_tab2'],
			'points' => $matchs_tableau['Finale']['points'])
	);
if ($timestamp_tableau_debut<time()) {
	$dis_tableau=' readonly ';
}


$html_tableau.='<p>La date limite est le samedi 26 juin à 16:00. Précision technique pour les non-footballers : TAB = Tirs aux buts, qui permet de déterminer le 
	vainqueur en cas de match nul.</p>
*/



/* Création de la structure totale */

// message
$html.='<div style="text-align:center;margin-bottom:15px;">'.$message.'</div>';
// début du formulaire
if ($poule_edit or $tableau_edit) {
	$html.='<div style="text-align:center">
		<input type="submit" onclick="submitForm(\'frm_pronos\')" value="Sauvez mes pronos"/>
		</div>
	<form method="post" id="frm_pronos">
	<input type="hidden" name="requete" value="update_pronos"/>
	<input type="hidden" name="page" value="mon_espace"/>
	<input type="hidden" name="section" value="mes_pronos"/>';
}	
// début de la table qui contient tout
$html.='<table id="poules">'
	.$en_premier.$en_second.'
	</table>';
if ($poule_edit or $tableau_edit) {
	$html.='</form><div style="text-align:center">
		<input type="submit" onclick="submitForm(\'frm_pronos\')" value="Sauvez mes pronos"/>
		</div>';
}	
?>