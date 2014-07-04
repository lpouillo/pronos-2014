<?

// Selection des tous les pronostics
$s_pronos="SELECT P.id_user, P.id_match, P.score1 AS prono1, P.score2 AS prono2, P.tab1 AS p_tab1, P.tab2 AS p_tab2,
	M.score1, M.score2, M.special, M.type, M.id_equipe1, M.id_equipe2, M.tab1, M.tab2, M.joue
	FROM pronos P
	LEFT JOIN matchs M
		ON P.id_match=M.id_match " .
				"WHERE P.id_user=241
	ORDER BY P.id_user, M.date_match";

$r_pronos=mysqli_query($db_pronos,$s_pronos)
	or die (mysqli_error($db_pronos));

$coeffs = array(
	'poul' => 1,
	'Huitieme' => 2,
	'Quart' => 3,
	'Demi' => 4,
	'p_final' => 4,
	'Final' => 6);

$s_update_pronos = "INSERT INTO pronos (`id_match`, `id_user`, `points`) VALUES ";
$vainqueur_user = array();
while ($d_pronos=mysqli_fetch_array($r_pronos)) {
	if ($d_pronos['joue']==1) {
		// On calcul les points de base
		$points = -5 * delta_1N2($d_pronos['score1'],$d_pronos['score2'],$d_pronos['prono1'],$d_pronos['prono2'])
			+abs($d_pronos['score1']-$d_pronos['prono1'])+abs($d_pronos['score2']-$d_pronos['prono2']);

		// Un match spécial vaut double
		$points = ($d_pronos['special'])?(2*$points):$points;

		// On determine le vainqueur des matchs
		if ($d_pronos['type'] != 'poule') {

			if (substr($d_pronos['type'],0, -1) == 'Huitieme') {
				$vainqueur_user[$d_pronos['id_user']][$d_pronos['type']]=vainqueur_match(array(
					'id_equipe1' => $d_pronos['id_equipe1'], 'id_equipe2' => $d_pronos['id_equipe2'],
					'score1' => $d_pronos['prono1'], 'score2' => $d_pronos['prono2'],
					'tab1' => $d_pronos['p_tab1'], 'tab2' => $d_pronos['p_tab2'] ));
			} else {
				$perdant=false;
				switch (substr($d_pronos['type'], 0, -1)) {
					case 'Quart':
						$prev = 'Huitieme';
					break;
					case 'Demi':
						$prev = 'Quart';
					break;
					case 'p_final':
						$prev = 'Demi';
						$match['type'] .= 1;
						$perdant=true;
					break;
					case 'Final':
						$prev = 'Demi';
						$match['type'] .= 1;
					break;

				}

				$prev1 = $prev.$regles[substr($d_pronos['type'], 0, -1)][substr($d_pronos['type'], -1)][0];
				$prev2 = $prev.$regles[substr($d_pronos['type'], 0, -1)][substr($d_pronos['type'], -1)][1];

				if (!$perdant) {
					$vainqueur_user[$d_pronos['id_user']][$d_pronos['type']] = vainqueur_match(array(
						'id_equipe1' => $vainqueur_user[$d_pronos['id_user']][$prev1],
						'id_equipe2' => $vainqueur_user[$d_pronos['id_user']][$prev2],
						'score1' => $d_pronos['prono1'], 'score2' => $d_pronos['prono2'],
						'tab1' => $d_pronos['p_tab1'], 'tab2' => $d_pronos['p_tab2'] ));
				} else {
					$vainqueur_user[$d_pronos['id_user']][$d_pronos['type']] = perdant_match(array(
						'id_equipe1' => $vainqueur_user[$d_pronos['id_user']][$prev1],
						'id_equipe2' => $vainqueur_user[$d_pronos['id_user']][$prev2],
						'score1' => $d_pronos['prono1'], 'score2' => $d_pronos['prono2'],
						'tab1' => $d_pronos['p_tab1'], 'tab2' => $d_pronos['p_tab2'] ));
				}
				// on determine la penalite pour les matchs du tableau
				$points+=($vainqueur_user[$d_pronos['id_user']][$prev1] != $d_pronos['id_equipe1'])?2:0;
				$points+=($vainqueur_user[$d_pronos['id_user']][$prev2] != $d_pronos['id_equipe2'])?2:0;
			}

		}

		// On applique le coefficient
		$points = $coeffs[substr($d_pronos['type'], 0, -1)] * $points;

	} else {
		$points = 0 ;
	}

	// Mise à jour des points
	$s_update_pronos.="(".$d_pronos['id_match'].",".$d_pronos['id_user'].",".$points."),";

}
$s_update_pronos=rtrim($s_update_pronos, ",")." ON DUPLICATE KEY " .
		"UPDATE points=VALUES(points)";

echo $s_update_pronos;
exit;

$html.= $s_update_pronos;

//mysqli_query($db_pronos,$s_update_pronos)
//		or die(mysqli_error($db_pronos));

?>
