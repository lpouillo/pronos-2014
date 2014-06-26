<?

// Selection des tous les pronostics
$s_pronos="SELECT P.id_user, P.id_match, P.score1 AS prono1, P.score2 AS prono2, P.tab1 AS p_tab1, P.tab2 AS p_tab2,
	M.score1, M.score2, M.special, M.type, M.id_equipe1, M.id_equipe2, M.tab1, M.tab2, M.joue
	FROM pronos P
	LEFT JOIN matchs M
		ON P.id_match=M.id_match
	ORDER BY P.id_user, M.date_match";

$r_pronos=mysqli_query($db_pronos,$s_pronos)
	or die (mysqli_error($db_pronos));

$coeffs = array(
	'Huitieme' => 2,
	'Quart' => 3,
	'Demi' => 4,
	'p_final' => 4,
	'Final' => 6);

$s_update_pronos = "INSERT INTO pronos (`id_match`, `id_user`, `points`) VALUES ";
$vainqueur_user = array();
while ($d_pronos=mysqli_fetch_array($r_pronos)) {

	if (!$d_pronos['joue']) {
		$s_update_pronos .= "(".$d_pronos['id_match'].",".$d_pronos['id_user'].", 0),";
	} else {
		// On calcul les points de base
		$points = -5 * delta_1N2($d_pronos['score1'],$d_pronos['score2'],$d_pronos['prono1'],$d_pronos['prono2'])
			+abs($d_pronos['score1']-$d_pronos['prono1'])+abs($d_pronos['score2']-$d_pronos['prono2']);

		if ($d_pronos['type'] == 'poule') {
			$points = ($d_pronos['special'])?(2*$points):$points;
		} else {
			print $d_pronos['type'].'<hr/>';
			$vainqueur_user[$d_pronos['id_user']][$d_pronos['type']]=vainqueur_match(array(
				'id_equipe1' => $d_pronos['id_equipe1'], 'id_equipe2' => $d_pronos['id_equipe2'],
				'score1' => $d_pronos['prono1'], 'score2' => $d_pronos['prono2'],
				'tab1' => $d_pronos['p_tab1'], 'tab2' => $d_pronos['p_tab2'] ));

			$points = $coeffs[$d_pronos['type']] * $points;
		}
		// On determine le vainqueur des matchs et la penalite pour les matchs du tableau
		switch($d_pronos['type']) {

			case 'Demi1':
				if ($d_pronos['id_equipe1']!=$vainqueur_user[$d_pronos['id_user']]['Quart1']) {
					$points+=3;
				}
				if ($d_pronos['id_equipe2']!=$vainqueur_user[$d_pronos['id_user']]['Quart3']) {
					$points+=3;
				}
				$vainqueur_user[$d_pronos['id_user']][$d_pronos['type']]=vainqueur_match(array(
						'id_equipe1' => $vainqueur_user[$d_pronos['id_user']]['Quart1'],
						'id_equipe2' => $vainqueur_user[$d_pronos['id_user']]['Quart3'],
						'score1' => $d_pronos['prono1'], 'score2' => $d_pronos['prono2'],
						'tab1' => $d_pronos['p_tab1'], 'tab2' => $d_pronos['p_tab2'] ));
			break;
			case 'Demi2':
				if ($d_pronos['id_equipe1']!=$vainqueur_user[$d_pronos['id_user']]['Quart2']) {
					$points+=3;
				}
				if ($d_pronos['id_equipe2']!=$vainqueur_user[$d_pronos['id_user']]['Quart4']) {
					$points+=3;
				}
				$vainqueur_user[$d_pronos['id_user']][$d_pronos['type']]=vainqueur_match(array(
						'id_equipe1' => $vainqueur_user[$d_pronos['id_user']]['Quart2'],
						'id_equipe2' => $vainqueur_user[$d_pronos['id_user']]['Quart4'],
						'score1' => $d_pronos['prono1'], 'score2' => $d_pronos['prono2'],
						'tab1' => $d_pronos['p_tab1'], 'tab2' => $d_pronos['p_tab2'] ));
			break;
			case 'Finale':
				if ($d_pronos['id_equipe1']!=$vainqueur_user[$d_pronos['id_user']]['Demi1']) {
					$points+=3;
				}
				if ($d_pronos['id_equipe2']!=$vainqueur_user[$d_pronos['id_user']]['Demi2']) {
					$points+=3;
				}
				$vainqueur_user[$d_pronos['id_user']][$d_pronos['type']]=vainqueur_match(array(
					'id_equipe1' => $vainqueur_user[$d_pronos['id_user']]['Demi1'],
					'id_equipe2' => $vainqueur_user[$d_pronos['id_user']]['Demi2'],
					'score1' => $d_pronos['prono1'], 'score2' => $d_pronos['prono2'],
					'tab1' => $d_pronos['p_tab1'], 'tab2' => $d_pronos['p_tab2'] ));
				// Determination du vrai vainqueur
				$vainqueur_finale=vainqueur_match(array(
					'id_equipe1' => $d_pronos['id_equipe1'],
					'id_equipe2' => $d_pronos['id_equipe2'],
					'score1' => $d_pronos['score1'], 'score2' => $d_pronos['score2'],
					'tab1' => $d_pronos['tab1'], 'tab2' => $d_pronos['tab2']
					));
			break;
		}

		// On determine le coefficient multiplicateur
		switch($d_pronos['type']) {
			case 'Quart1':
			case 'Quart2':
			case 'Quart3':
			case 'Quart4':
				$coeff=3;
			break;
			case 'Demi1':
			case 'Demi2':
				$coeff=4;
			break;
			case 'Finale':
				$coeff=6;
			break;
			default:
				$coeff=1;
		}

		$points=$points*$coeff;
	}
	// Mise à jour des points
	$s_update_pronos.="(".$d_pronos['id_match'].",".$d_pronos['id_user'].",".$points."),";
}
$s_update_pronos=rtrim($s_update_pronos, ",")." ON DUPLICATE KEY " .
		"UPDATE points=VALUES(points)";

$html.= $s_update_pronos;

echo $s_update_pronos;
mysqli_query($db_pronos,$s_update_pronos)
		or die(mysqli_error($db_pronos));

?>
