<?php
/*
 * Created on 23 juin 2014
 *
 */

$s_last = "SELECT M.id_match, M.date_match, M.heure,
		E1.nom AS equipe1, E2.nom AS equipe2
		FROM matchs M
		LEFT JOIN equipes E1
			ON E1.id_equipe=M.id_equipe1
		LEFT JOIN equipes E2
			ON E2.id_equipe=M.id_equipe2
		WHERE M.joue=0
		ORDER BY M.date_match, M.heure
		LIMIT 1 ";
$r_last= mysqli_query($db_pronos, $s_last)
		or die($s_last.'<br/>'.mysqli_error($db_pronos));

$d_last = mysqli_fetch_array($r_last);

if (time()>strtotime($d_last['date_match'].' '.$d_last['heure']) + 6300) {
	$matchs = flux_match('http://www.matchendirect.fr/rss/foot-coupe-du-monde-p2019.xml');
	foreach($matchs as $raw_match) {
		 $raw_match = explode('(score final :', $raw_match);
		 $teams = explode(' - ', substr($raw_match[0], strlen('Mondial - Groupe F : '), -1));

		 if ($teams[0] == $d_last['equipe1'] and $teams[1] == $d_last['equipe2']) {
		 	$scores = explode('-', substr(trim($raw_match[1]), 0, 3));
		 	$s_update = "UPDATE matchs SET score1=".$scores[0].", score2=".$scores[1].",
		 		joue=1 WHERE id_match=".$d_last['id_match'];
			$r_update= mysqli_query($db_pronos, $s_update)
				or die($s_update.'<br/>'.mysqli_error($db_pronos));
			include('app/includes/admin/classement.php');
		 }

	}
}

$regles = array(
	'Huitieme' => array(
		1 => array(1, 2),
		2 => array(3, 4),
		3 => array(2, 1),
		4 => array(4, 3),
		5 => array(5, 6),
		6 => array(7, 8),
		7 => array(6, 5),
		8 => array(8, 7)),
	'Quart' => array(
		1 => array(5, 6),
		2 => array(1, 2),
		3 => array(7, 8),
		4 => array(3, 4)),
	'Demi' => array(
		1 => array(2, 1),
		2 => array(3, 4)),
	'p_final' => array(1 => array(1,2)),
	'Final' => array(1 => array(1,2)));
?>
