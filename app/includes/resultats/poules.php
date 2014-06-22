<?php

// Récupération des équipes
$s_eq_poules="SELECT * FROM equipes ORDER BY poule, classement, nom";
$r_eq_poules=mysqli_query($db_pronos,$s_eq_poules);
$poules=array();
while ($d_eq_poules=mysqli_fetch_array($r_eq_poules)) {
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
$s_matchs="SELECT M.id_match, M.date_match, M.heure, " .
			"M.id_equipe1, EQ1.acronym AS ac1, EQ1.nom AS eq1, EQ1.poule AS poule, " .
			"M.id_equipe2, EQ2.acronym AS ac2, EQ2.nom AS eq2,
			M.score1, M.score2, M.tab1, M.tab2, M.cote_1, M.cote_N, M.cote_2, M.joue, M.special, M.type
			FROM matchs M
			INNER JOIN equipes EQ1
				ON EQ1.id_equipe=M.id_equipe1
			INNER JOIN equipes EQ2
				ON EQ2.id_equipe=M.id_equipe2
			WHERE M.type='poule'
			ORDER BY EQ1.poule, M.date_match, M.heure";
$r_matchs=mysqli_query($db_pronos,$s_matchs)
	or die($s_matchs.'<br/>'.mysql_error());
$mat_par_poule=array();
while ($match=mysqli_fetch_array($r_matchs)) {
	$mat_par_poule[$match['poule']][]=$match;
}

$html.='<section id="poules">' .
		'<div class="row">' .
		'<header><h2>Poules</h2></header>' .
		'<p style="text-align:center">Les matchs sur <span class="special">fond vert</span> comptent double.</p>' .
		'</div>' .
		'<div class="row">';

$i_poule = 1;
foreach($poules as $poule) {
	$html.='<div class="3u box">' .
			'<header>Poule '.$i_poule.'</header>';
	// Classement de la poule
	$html.=aff_poule($i_poule, $poule);
	// Matchs de la poule
	if (sizeof($mat_par_poule[$i_poule])>0) {
		$html.='<ul>';
		foreach($mat_par_poule[$i_poule] as $match) {
			$html .='<li>';
			$html .= aff_match($match);
			$html.='</li>';
		}
		$html .='</ul>';
	} else {
		$html.='<p>Aucun match dans la base pour l\'instant</p>';
	}
	$html .= '</div>';
	if ($i_poule == 4) {
		$html.='</div><div class="row">';
	}
	$i_poule += 1;
}

$html.='</div></div></section>';
?>
