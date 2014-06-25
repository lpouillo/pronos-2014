<?
$html.=($page == 'admin')?'<h3>Calcul des poules</h3>':'';
// On récupère tous les matchs et on attribue les victoires nuls defaites bp bc à chaque équipe
$s_matchs= "SELECT M.id_equipe1, M.id_equipe2, M.score1, M.score2,
			E.poule
			FROM `matchs` M
			INNER JOIN equipes E
				ON E.id_equipe=M.id_equipe1
		 	WHERE M.type='poule' AND M.joue=1";

$r_matchs= mysqli_query($db_pronos,$s_matchs)
		or die($s_matchs.'<br/>'.mysqli_error($db_pronos));

$poules = array();
while ($match=mysqli_fetch_array($r_matchs)){
	if (!array_key_exists($match['poule'], $poules)) {
		$poules[$match['poule']]=array();
	}
	if (!array_key_exists($match['id_equipe1'], $poules[$match['poule']])) {
		$poules[$match['poule']][$match['id_equipe1']]=array('id_equipe' => $match['id_equipe1'],
				'J' => 0, 'V' => 0, 'N' => 0, 'D' => 0,
				'bp' => 0, 'bc' => 0);
	}
	if (!array_key_exists($match['id_equipe2'], $poules[$match['poule']])) {
		$poules[$match['poule']][$match['id_equipe2']]=array('id_equipe' => $match['id_equipe2'],
				'J' => 0, 'V' => 0, 'N' => 0, 'D' => 0,
				'bp' => 0, 'bc' => 0);
	}

	$poules[$match['poule']][$match['id_equipe1']]['J'] += 1;
	$poules[$match['poule']][$match['id_equipe1']]['V'] += ($match['score1']>$match['score2'])?1:0;
	$poules[$match['poule']][$match['id_equipe1']]['N'] += ($match['score1']==$match['score2'])?1:0;
	$poules[$match['poule']][$match['id_equipe1']]['D'] += ($match['score1']<$match['score2'])?1:0;
	$poules[$match['poule']][$match['id_equipe1']]['bp'] += $match['score1'];
	$poules[$match['poule']][$match['id_equipe1']]['bc'] += $match['score2'];

	$poules[$match['poule']][$match['id_equipe2']]['J'] += 1;
	$poules[$match['poule']][$match['id_equipe2']]['V'] += ($match['score2']>$match['score1'])?1:0;
	$poules[$match['poule']][$match['id_equipe2']]['N'] += ($match['score2']==$match['score1'])?1:0;
	$poules[$match['poule']][$match['id_equipe2']]['D'] += ($match['score2']<$match['score1'])?1:0;
	$poules[$match['poule']][$match['id_equipe2']]['bp'] += $match['score2'];
	$poules[$match['poule']][$match['id_equipe2']]['bc'] += $match['score1'];

}


function compare_teams($a, $b) {
// le plus grand nombre de points obtenus dans tous les matches du groupe ;
	if ($a['pts'] > $b['pts']) {
		$comp = -1;
	} else if ($a['pts'] < $b['pts']) {
		$comp = 1;
	} else {
		// la différence de buts dans tous les matches du groupe ;
		if (($a['bp']-$a['bc']) > ($b['bp']-$b['bc'])) {
			$comp = -1;
		} else if (($a['bp']-$a['bc']) < ($b['bp']-$b['bc'])) {
			$comp = 1;
		} else {
			// le plus grand nombre de buts marqués dans tous les matches du groupe ;
			if ($a['bp'] > $b['bp']) {
				$comp = -1;
			} else if ($a['bp'] < $b['bp']) {
				$comp = 1;
			} else {
// Manque :
// - le plus grand nombre de points obtenus dans les matches de groupe entre les équipes à égalité ;
// - la différence de buts particulière dans les matches de groupe entre les équipes à égalité ;
// - le plus grand nombre de buts marqués dans les matches de groupe entre les équipes à égalité ;
				$comp = 0;
			}
		}
	}
	return $comp;
}

$s_update_equipes="INSERT INTO equipes (`id_equipe`, `joues`, `victoires`, `nuls`, `defaites`," .
		"`but_p`, `but_c`, `pts`, `classement`) VALUES ";
foreach ($poules as $i_poule => &$equipes) {

	foreach($equipes as &$equipe) {
		$equipe['pts'] = 3*$equipe['V']+$equipe['N'];
	}

	usort($equipes, 'compare_teams');
	
	$class=1;
	while (list($key, $value) = each($equipes)) {
		$s_update_equipes .= "(".$value['id_equipe'].",".$value['J'].",".$value['V'].",".$value['N']."" .
				",".$value['D'].",".$value['bp'].",".$value['bc'].",".$value['pts'].",".$class."),";
		$class++;
	}

}

$s_update_equipes=rtrim($s_update_equipes, ",")." ON DUPLICATE KEY UPDATE joues=VALUES(joues)," .
		"victoires=VALUES(victoires),nuls=VALUES(nuls),defaites=VALUES(defaites)," .
		"but_p=VALUES(but_p),but_c=VALUES(but_c), pts=VALUES(pts), classement=VALUES(classement)";


mysqli_query($db_pronos, $s_update_equipes)
	or die(mysqli_error($db_pronos));

$html.=($page == 'admin')?$s_update_equipes:'';

?>
