<?
// Calcul des cotes des matchs
$html.='<h3>Calcul des cotes des matchs</h3>';
$s_cotes="SELECT P.id_match, P.score1, P.score2 FROM pronos P ORDER BY P.id_match";
$r_cotes=mysqli_query($db_pronos,$s_cotes);
$cotes=array();
while ($d_cotes=mysqli_fetch_array($r_cotes)) {
	$cotes[$d_cotes['id_match']]['1']+=($d_cotes['score1']>$d_cotes['score2'])?1:0;
	$cotes[$d_cotes['id_match']]['N']+=($d_cotes['score1']==$d_cotes['score2'])?1:0;
	$cotes[$d_cotes['id_match']]['2']+=($d_cotes['score1']<$d_cotes['score2'])?1:0;
}
foreach($cotes as $id_match => $cote) {
	$n_paris=$cote['1']+$cote['N']+$cote['2'];
	$s_update_cote="UPDATE matchs SET cote_1='".round($cote['1']/$n_paris,2)."', cote_N='".round($cote['N']/$n_paris,2)."', cote_2='".round($cote['2']/$n_paris,2)."' WHERE id_match='".$id_match."'";
	mysqli_query($db_pronos,$s_update_cote)
		or die(mysql_error());
}

$html.='<h3>Calcul des points pour chaque pronostic</h3>';
// Selection des tous les pronostics
$s_pronos="SELECT P.id_user, P.id_match, P.score1 AS prono1, P.score2 AS prono2, P.tab1 AS p_tab1, P.tab2 AS p_tab2,
	M.score1, M.score2, M.special, M.type, M.id_equipe1, M.id_equipe2, M.tab1, M.tab2, M.joue
	FROM pronos P
	INNER JOIN matchs M
		ON P.id_match=M.id_match
	ORDER BY P.id_user, M.date_match";

$users=array();
$vainqueur_user=array();
$r_pronos=mysqli_query($db_pronos,$s_pronos)
	or die (mysql_error());
$n_pronos=array();
while ($d_pronos=mysqli_fetch_array($r_pronos)) {
	if ($d_pronos['joue']) {
		// On calcul les points de base
		$points=-5*delta_1N2($d_pronos['score1'],$d_pronos['score2'],$d_pronos['prono1'],$d_pronos['prono2'])
			+abs($d_pronos['score1']-$d_pronos['prono1'])+abs($d_pronos['score2']-$d_pronos['prono2']);
		$points=($d_pronos['special'])?(2*$points):$points;

		// On determine le vainqueur des matchs et la penalite pour les matchs du tableau
		switch($d_pronos['type']) {
			case 'Quart1':
			case 'Quart2':
			case 'Quart3':
			case 'Quart4':
				$vainqueur_user[$d_pronos['id_user']][$d_pronos['type']]=vainqueur_match(array(
						'id_equipe1' => $d_pronos['id_equipe1'], 'id_equipe2' => $d_pronos['id_equipe2'],
						'score1' => $d_pronos['prono1'], 'score2' => $d_pronos['prono2'],
						'tab1' => $d_pronos['p_tab1'], 'tab2' => $d_pronos['p_tab2'] ));
			break;
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
	} else {
		$points=0;
	}
	// Mise à jour des points
	$s_update="UPDATE pronos SET points='".$points."' WHERE id_user='".$d_pronos['id_user']."' AND id_match='".$d_pronos['id_match']."'";

	mysqli_query($db_pronos,$s_update)
		or die(mysql_error());
	$users[$d_pronos['id_user']]['points']+=$points;

	$users[$d_pronos['id_user']]['n_pronos']++;
}



$html.='<h3>Mise a jour des points des parieurs</h3><p>';
foreach($users as $id_user => $data) {

	// Ajout du malus selon le nombre de pronos
	switch ($data['n_pronos']) {
		case 7:
			$malus=30;
		break;
		case 24:
			$malus=50;
		break;
		default:
			$malus=0;
	}
	// Ajout de la bonification pour le bon vainqueur

	$bonus=($vainqueur_finale==$vainqueur_user[$id_user]['Finale'] and $vainqueur_finale!=0)?-30:0;
	$s_update="UPDATE users SET points='".($data['points']+$bonus+$malus)."' WHERE id_user='".$id_user."'";
	$html.=$id_user.' => '. ($data['points']+$bonus+$malus).' - ';
	$r_update=mysqli_query($db_pronos,$s_update)
		or die(mysql_error());
}

$html.='</p><h3>Mise a jour du classement</h3><p>';
// mise à jour du classement des parieurs
$s_user="SELECT id_user, login, points FROM users WHERE actif=1 ORDER BY points";
$r_user=mysqli_query($db_pronos,$s_user);
$classement=1;
while($d_user=mysqli_fetch_array($r_user)) {
	if ($users[$d_user['id_user']]['n_pronos']>0) {
		$s_class="UPDATE users SET classement=".$classement.", actif=1 WHERE id_user=".$d_user['id_user'];
		$html.= $classement.' '.$d_user['login'].' ('.$d_user['points'].') -';
		$classement++;
	} else {
		$s_class="UPDATE users SET classement=0, points=0, actif=0 WHERE id_user=".$d_user['id_user'];
	}
	mysqli_query($db_pronos,$s_class);
}

// mise à jour des points de groupes
$s_user_groupes="SELECT G.id_groupe, COUNT(UG.id_user) AS n_user, AVG(U.points) AS moyenne
		FROM groupes G
		INNER JOIN l_users_groupes UG
			ON UG.id_groupe=G.id_groupe
		INNER JOIN users U
			ON UG.id_user=U.id_user
		WHERE G.actif=1 AND U.actif=1 AND G.n_user>1
		GROUP BY G.id_groupe
		ORDER BY moyenne, n_user DESC";
$r_user_groupes=mysqli_query($db_pronos,$s_user_groupes)
	or die(mysql_error());
$classement=1;
while ($d_user_groupes=mysqli_fetch_array($r_user_groupes)) {
	$s_update="UPDATE groupes SET moyenne='".$d_user_groupes['moyenne']."', n_user='".$d_user_groupes['n_user']."', classement='".$classement."'
		WHERE id_groupe='".$d_user_groupes['id_groupe']."'";
	mysqli_query($db_pronos,$s_update);
	$classement++;
}

?>
