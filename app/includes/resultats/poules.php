<?php
$html.='<h2>Poules</h2>
<p style="text-align:center">Les matchs encadrés en <span class="special">ROUGE</span> comptent double. 
	<a href="#" onclick="affElement(\'reglement\',\'\',\'\',\'\',\'page\');">Voir le règlement du concours.</a></p>';
// Récupération des équipes
$s_eq_poules="SELECT * FROM equipes ORDER BY poule, classement, nom";

$r_eq_poules=mysql_query($s_eq_poules);
$poules=array();
while ($d_eq_poules=mysql_fetch_array($r_eq_poules)) {
	$poules[$d_eq_poules['poule']][]=array(
		'nom'=> $d_eq_poules['nom'], 
		'acronym' => $d_eq_poules['acronym'],
		'V' => $d_eq_poules['victoires'],
		'N' => $d_eq_poules['nuls'],
		'D' => $d_eq_poules['defaites'],
		'pts' => $d_eq_poules['pts'],
		'diff' => ($d_eq_poules['but_p']-$d_eq_poules['but_c']));
}

// Recuperation des matchs
$s_matchs="SELECT M.date_match, M.heure, EQ1.acronym AS ac1, EQ1.nom AS eq1, EQ1.poule AS poule, EQ2.acronym AS ac2, EQ2.nom AS eq2,
			M.score1, M.score2, M.cote_1, M.cote_N, M.cote_2, M.joue, M.special
			FROM matchs M
			INNER JOIN equipes EQ1
				ON EQ1.id_equipe=M.id_equipe1
			INNER JOIN equipes EQ2
				ON EQ2.id_equipe=M.id_equipe2
			WHERE M.type='poule'
			ORDER BY M.poule, M.date_match, M.heure";

$r_matchs=mysql_query($s_matchs)
	or die($s_matchs.'<br/>'.mysql_error());
$mat_par_poule=array();
while ($d_matchs=mysql_fetch_array($r_matchs)) {
	$mat_par_poule[$d_matchs['poule']][]=$d_matchs;
}
$html.='<table id="poules" border="0">
		<tr>';

for ($j=1;$j<=4;$j++) {
	$html.='<th>Poule '.$j.'</th>';	
}
$html.='</tr><tr>';
for ($j=1;$j<=4;$j++) {
	$html.='<td class="td_poule">
			<table class="poule">
				<tr>
					<th>Rang</th><th>Equipe</th><th>Pts</th><th>V</th><th>N</th><th>D</th><th>Diff</th>
				</tr>';
	for ($k=0;$k<=3;$k++) {
		$html.='<tr>
					<td>'.($k+1).'</td>
					<td style="text-align:left;"><img src="public/images/flags/'.$poules[$j][$k]['acronym'].'.gif" alt="flag"/> '.$poules[$j][$k]['nom'].'</td>
					<td>'.$poules[$j][$k]['pts'].'</td>
					<td>'.$poules[$j][$k]['V'].'</td>
					<td>'.$poules[$j][$k]['N'].'</td>
					<td>'.$poules[$j][$k]['D'].'</td>
					<td>'.$poules[$j][$k]['diff'].'</td>
					
				</tr>';
	}
	$html.='</table>
	<table style="margin:auto">';
	if (sizeof($mat_par_poule[$j])>0) {
		foreach($mat_par_poule[$j] as $match) {
			$spec=($match['special'])?' class="special" ':'';
			$html.='<tr>
				<td><span class="date">'.dateMysqlToFormatted($match['date_match'],$match['heure'],'%d %B à %H:%M').'</span></td>
			 <td '.$spec.'><span class="equipe" title="'.$match['eq1'].'"><img src="public/images/flags/'.$match['ac1'].'.gif" alt="flag"/></span></td>
			 <td style="text-align:center">';
			$html.=($match['joue'])?$match['score1'].'-'.$match['score2']:$match['cote_1'].'/'.$match['cote_N'].'/'.$match['cote_2'];
			$html.='</td><td '.$spec.'><span class="equipe" title="'.$match['eq2'].'"><img src="public/images/flags/'.$match['ac2'].'.gif" alt="flag"/></span></td></tr>';
		}
	} else {
		$html.='<tr></td>Aucun match dans la base pour l\'instant</td></tr>';
	}
	$html.='</table></td>';	
}
$html.='</tr>';

if (count($poules)==8) {
	$html .='<tr>';
	
	for ($j=5;$j<=8;$j++) {
		$html.='<th>Poule '.$j.'</th>';	
	}
	$html.='<tr>';
	for ($j=5;$j<=8;$j++) {
		$html.='<td class="td_poule">
				<table class="poule">
					<tr>
						<th>Rang</th><th>Equipe</th><th>Pts</th><th>V</th><th>N</th><th>D</th><th>Diff</th>
					</tr>';
	for ($k=0;$k<=3;$k++) {
			$html.='<tr>
						<td>'.($k+1).'</td>
						<td style="text-align:left;"><img src="public/images/flags/'.$poules[$j][$k]['acronym'].'.gif" alt="flag"/> '.$poules[$j][$k]['nom'].'</td>
						<td>'.$poules[$j][$k]['pts'].'</td>
						<td>'.$poules[$j][$k]['V'].'</td>
						<td>'.$poules[$j][$k]['N'].'</td>
						<td>'.$poules[$j][$k]['D'].'</td>
						<td>'.$poules[$j][$k]['diff'].'</td>
						
					</tr>';
		}
		$html.='</table>
		<ul>';
		if (sizeof($mat_par_poule[$j])>0) {
			foreach($mat_par_poule[$j] as $match) {
				$spec=($match['special'])?' class="special" ':'';
				$html.='<li '.$spec.'><span class="date">'.$match['date_match'].' '.substr($match['heure'],0,5).'</span> <span class="equipe" title="'.$match['eq1'].'">'.$match['ac1'].' </span>';
				$html.=($match['joue'])?$match['score1'].'-'.$match['score2']:$match['cote_1'].'/'.$match['cote_N'].'/'.$match['cote_2'];
				$html.=' <span class="equipe" title="'.$match['eq2'].'">'.$match['ac2'].'</span></li>';
			}
		} else {
			$html.='<li>Aucun match dans la base pour l\'instant</li>';
		}
		$html.='</ul></td>';	
	}
	$html.='</tr>';
}
$html.='</table>';

?>
