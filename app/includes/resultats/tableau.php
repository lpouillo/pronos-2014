<?php
/* Récupération des données des matchs */
$s_matchs="SELECT M.id_match, M.date_match, M.heure,
			M.id_equipe1, EQ1.acronym AS ac1, EQ1.nom AS eq1, EQ1.poule AS poule,
			M.id_equipe2, EQ2.acronym AS ac2, EQ2.nom AS eq2,
			M.score1, M.score2, M.tab1, M.tab2,
			M.cote_1, M.cote_N, M.cote_2,
			M.joue, M.special, M.type
			FROM matchs M
			LEFT JOIN equipes EQ1
				ON EQ1.id_equipe=M.id_equipe1
			LEFT JOIN equipes EQ2
				ON EQ2.id_equipe=M.id_equipe2
			WHERE M.type<>'poule'
			ORDER BY M.date_match, M.heure";

$r_matchs=mysqli_query($db_pronos,$s_matchs)
	or die($s_matchs.'<br/>'.mysqli_error($db_pronos));
$mat_par_type=array();

while ($match=mysqli_fetch_array($r_matchs)) {
	$mat_par_type[substr($match['type'], 0, -1)][]=$match;
}

$sections = array(
	'Huitieme' => 'Huitièmes de finales',
	'Quart' => 'Quarts de finales',
	'Demi' => 'Demi-finales',
	'Final' => 'Finale'
	);
$html .= '<div class="box">
			<header><h2>Tableau final</h2></header>
			<div class="row">';
foreach($sections as $nom => $text) {
	$html .= '<section id="'.$nom.'">' .
		'<div class="12u">' .
			'<div class="row">' .
				'<div class="12u" ><h3>'.$text.'</h3></div>';
	$n_u = (sizeof($mat_par_type[$nom])>4)?'3':12/sizeof($mat_par_type[$nom]);
	foreach($mat_par_type[$nom] as $match) {
		$html.='<div class="'.$n_u.'u" style="text-align:center;">'.aff_match($match, 'vertical').'</div>';
	}
	$html .= '</div>' .
		'</div>' .
	'</section>';
}
$html.='</div></div>';


?>
