<?php
$html='';

$s_matchs="SELECT M.id_match, M.date_match, M.heure, " .
		"M.id_equipe1, EQ1.nom AS eq1, EQ1.acronym AS ac1, " .
		"M.id_equipe2, EQ2.nom AS eq2, EQ2.acronym AS ac2,
		M.score1, M.score2, M.tab1, M.tab2, M.type, M.joue, M.special, " .
		"M.cote_1, M.cote_N, M.cote_2
		FROM matchs M
		INNER JOIN equipes EQ1
		ON M.id_equipe1=EQ1.id_equipe
		INNER JOIN equipes EQ2
		ON M.id_equipe2=EQ2.id_equipe
		WHERE joue=1
		ORDER BY M.date_match DESC, M.heure DESC
		LIMIT 5";


$r_matchs=mysqli_query($db_pronos, $s_matchs);
$count_matchs_joues=0;



if (mysqli_num_rows($r_matchs)>0) {
	$matchs = array();
	while ($match=mysqli_fetch_array($r_matchs)) {
		$matchs[] = $match;
		$count_matchs_joues++;
	}
	$matchs = array_reverse($matchs);
	foreach ($matchs as $match) {
		$html.=aff_match($match);
	}
} else {
	$html.='<p>Aucun match n\'a encore été joué.</p>';
}


$html.='<h3>Prochains matchs</h3>';
$s_matchs="SELECT M.id_match, M.date_match, M.heure, " .
		"M.id_equipe1, EQ1.nom AS eq1, EQ1.acronym AS ac1, " .
		"M.id_equipe2, EQ2.nom AS eq2, EQ2.acronym AS ac2,
		M.score1, M.score2, M.tab1, M.tab2, M.type, M.joue, M.special, " .
		"M.cote_1, M.cote_N, M.cote_2
		FROM matchs M
		INNER JOIN equipes EQ1
		ON M.id_equipe1=EQ1.id_equipe
		INNER JOIN equipes EQ2
		ON M.id_equipe2=EQ2.id_equipe
				WHERE joue<>1
				ORDER BY M.date_match, M.heure
				LIMIT ".(9-$count_matchs_joues);
$r_matchs=mysqli_query($db_pronos, $s_matchs);
while ($match=mysqli_fetch_array($r_matchs)) {
	$html.=aff_match($match);
}

echo $html;
?>