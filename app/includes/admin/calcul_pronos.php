<?
// Calcul des cotes des matchs
$html.='<h3>Calcul des cotes des matchs</h3>';
$s_cotes="SELECT P.id_match, P.score1, P.score2 FROM pronos P ORDER BY P.id_match";
$r_cotes=mysql_query($s_cotes);
$cotes=array();
while ($d_cotes=mysql_fetch_array($r_cotes)) {
	$cotes[$d_cotes['id_match']]['1']+=($d_cotes['score1']>$d_cotes['score2'])?1:0;
	$cotes[$d_cotes['id_match']]['N']+=($d_cotes['score1']==$d_cotes['score2'])?1:0;
	$cotes[$d_cotes['id_match']]['2']+=($d_cotes['score1']<$d_cotes['score2'])?1:0;
}
foreach($cotes as $id_match => $cote) {
	$n_paris=$cote['1']+$cote['N']+$cote['2'];
	$s_update_cote="UPDATE matchs SET cote_1='".round($cote['1']/$n_paris,2)."', cote_N='".round($cote['N']/$n_paris,2)."', cote_2='".round($cote['2']/$n_paris,2)."' WHERE id_match='".$id_match."'";
	mysql_query($s_update_cote)
		or die(mysql_error());
}

// Selection des tous les pronostics
$s_pronos="SELECT P.id_user, P.id_match, P.score1 AS prono1, P.score2 AS prono2, P.tab1 AS p_tab1, P.tab2 AS p_tab2, M.score1, M.score2, M.special, M.type,
	M.id_equipe1, M.id_equipe2, M.tab1, M.tab2
	FROM pronos P
	INNER JOIN matchs M 
		ON P.id_match=M.id_match
	WHERE M.joue=1
	ORDER BY M.date_match";

$users=array();
$r_pronos=mysql_query($s_pronos)
	or die (mysql_error());
$n_pronos=array();
while ($d_pronos=mysql_fetch_array($r_pronos)) {	
	if ($d_pronos['score1']>$d_pronos['score2']) {
		$res_match='1';
	} elseif ($d_pronos['score1']<$d_pronos['score2']) {
		$res_match='2';		
	} else {
		$res_match='N';
	}
	if ($d_pronos['prono1']>$d_pronos['prono2']) {
		$res_prono='1';
	} elseif ($d_pronos['prono1']<$d_pronos['prono2']) {
		$res_prono='2';		
	} else {
		$res_prono='N';
	}	
	$delta=($res_prono==$res_match)?1:0;
	$points=-5*$delta+abs($d_pronos['score1']-$d_pronos['prono1'])+abs($d_pronos['score2']-$d_pronos['prono2']);
	$points=($d_pronos['special'])?(2*$points):$points;
	
	switch($d_pronos['type']) {
		/*case 'Huitieme1':
		case 'Huitieme2':
		case 'Huitieme3':
		case 'Huitieme4':
		case 'Huitieme5':
		case 'Huitieme6':
		case 'Huitieme7':
		case 'Huitieme8':
			$coeff=2;
			switch ($res_prono) {
				case '1':
					$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=$d_pronos['id_equipe1'];
				break;
				case '2':
					$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=$d_pronos['id_equipe2'];
				break;
				case 'N':
					if ($d_pronos['p_tab1']>$d_pronos['p_tab2']) {
						$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=$d_pronos['id_equipe1'];
					} elseif ($d_pronos['p_tab1']<$d_pronos['p_tab2']) {
						$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=$d_pronos['id_equipe2'];
					} else {
						$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=0;
					}
				break;
			}
		break;
		case 'Quart1':
		case 'Quart2':
		case 'Quart3':
		case 'Quart4':
			$i_quart=substr($d_pronos['type'],-1);
		
			$coeff=3;
			if ($d_pronos['id_equipe1']!=$users[$d_pronos['id_user']]['Huitieme'.(2*($i_quart-1)+1)]['id_vainqueur']) {
				$points+=3;
			}
			if ($d_pronos['id_equipe2']!=$users[$d_pronos['id_user']]['Huitieme'.(2*($i_quart-1)+2)]['id_vainqueur']) {
				$points+=3;
			}
			switch ($res_prono) {
				case '1':
					$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=$users[$d_pronos['id_user']]['Huitieme'.(2*($i_quart-1)+1)]['id_vainqueur'];
				break;
				case '2':
					$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=$users[$d_pronos['id_user']]['Huitieme'.(2*($i_quart-1)+2)]['id_vainqueur'];
				break;
				case 'N':
					if ($d_pronos['p_tab1']>$d_pronos['p_tab2']) {
						$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=$users[$d_pronos['id_user']]['Huitieme'.(2*($i_quart-1)+1)]['id_vainqueur'];
					} elseif ($d_pronos['p_tab1']<$d_pronos['p_tab2']) {
						$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=$users[$d_pronos['id_user']]['Huitieme'.(2*($i_quart-1)+2)]['id_vainqueur'];
					} else {
						$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=0;
					}
				break;
			}
		break;
		case 'Demi1':
		case 'Demi2':
			if ($d_pronos['id_user']==31 and $d_pronos['type']=='Demi2') {
				echo $points.'<br/>';
			}
			
			$i_demi=substr($d_pronos['type'],-1);
			$coeff=4;
			if ($d_pronos['id_equipe1']!=$users[$d_pronos['id_user']]['Quart'.(2*($i_demi-1)+1)]['id_vainqueur']) {
				$points+=3;
			}
			if ($d_pronos['id_equipe2']!=$users[$d_pronos['id_user']]['Quart'.(2*($i_demi-1)+2)]['id_vainqueur']) {
				$points+=3;
			}
			switch ($res_prono) {
				case '1':
					$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=$users[$d_pronos['id_user']]['Quart'.(2*($i_demi-1)+1)]['id_vainqueur'];
					$users[$d_pronos['id_user']][$d_pronos['type']]['id_perdant']=$users[$d_pronos['id_user']]['Quart'.(2*($i_demi-1)+2)]['id_vainqueur'];
				break;
				case '2':
					$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=$users[$d_pronos['id_user']]['Quart'.(2*($i_demi-1)+2)]['id_vainqueur'];
					$users[$d_pronos['id_user']][$d_pronos['type']]['id_perdant']=$users[$d_pronos['id_user']]['Quart'.(2*($i_demi-1)+1)]['id_vainqueur'];
				break;
				case 'N':
					if ($d_pronos['p_tab1']>$d_pronos['p_tab2']) {
						$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=$users[$d_pronos['id_user']]['Quart'.(2*($i_demi-1)+1)]['id_vainqueur'];
						$users[$d_pronos['id_user']][$d_pronos['type']]['id_perdant']=$users[$d_pronos['id_user']]['Quart'.(2*($i_demi-1)+2)]['id_vainqueur'];
					} elseif ($d_pronos['p_tab1']<$d_pronos['p_tab2']) {
						$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=$users[$d_pronos['id_user']]['Quart'.(2*($i_demi-1)+2)]['id_vainqueur'];
						$users[$d_pronos['id_user']][$d_pronos['type']]['id_perdant']=$users[$d_pronos['id_user']]['Quart'.(2*($i_demi-1)+1)]['id_vainqueur'];
					} else {
						$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=0;
						$users[$d_pronos['id_user']][$d_pronos['type']]['id_perdant']=0;
					}
				break;
			}
			if ($d_pronos['id_user']==31 and $d_pronos['type']=='Demi2') {
				echo $d_pronos['id_equipe1'].' '.$users[$d_pronos['id_user']]['Quart'.(2*($i_demi-1)+1)]['id_vainqueur'].'<br/>';
				echo $d_pronos['id_equipe2'].' '.$users[$d_pronos['id_user']]['Quart'.(2*($i_demi-1)+2)]['id_vainqueur'].'<br/>';
				echo $points.'<br/>';
			}
		break;
		case 'p_finale':
			$coeff=5;
			if ($d_pronos['id_equipe1']!=$users[$d_pronos['id_user']]['Demi1']['id_perdant']) {
				$points+=3;
			}
			if ($d_pronos['id_equipe2']!=$users[$d_pronos['id_user']]['Demi2']['id_perdant']) {
				$points+=3;
			}
		
		break;
		case 'Finale':
			$coeff=6;
			if ($d_pronos['id_equipe1']!=$users[$d_pronos['id_user']]['Demi1']['id_vainqueur']) {
				$points+=3;
			}
			if ($d_pronos['id_equipe2']!=$users[$d_pronos['id_user']]['Demi2']['id_vainqueur']) {
				$points+=3;
			}
			switch ($res_prono) {
				case '1':
					$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=$users[$d_pronos['id_user']]['Demi1']['id_vainqueur'];
				break;
				case '2':
					$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=$users[$d_pronos['id_user']]['Demi2']['id_vainqueur'];
				break;
				case 'N':
					if ($d_pronos['p_tab1']>$d_pronos['p_tab2']) {
						$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=$users[$d_pronos['id_user']]['Demi1']['id_vainqueur'];
					} elseif ($d_pronos['p_tab1']<$d_pronos['p_tab2']) {
						$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=$users[$d_pronos['id_user']]['Demi2']['id_vainqueur'];
					} else {
						$users[$d_pronos['id_user']][$d_pronos['type']]['id_vainqueur']=0;
					}
				break;
			}
			switch ($res_match) {
				case '1':
					$id_vainqueur=$d_pronos['id_equipe1'];
				break;
				case '2':
					$id_vainqueur=$d_pronos['id_equipe2'];
				break;
				case 'N':
					if ($d_pronos['p_tab1']>$d_pronos['p_tab2']) {
						$id_vainqueur=$d_pronos['id_equipe1'];
					} elseif ($d_pronos['p_tab1']<$d_pronos['p_tab2']) {
						$id_vainqueur=$d_pronos['id_equipe2'];
					} 
				break;
			}
		break;
		*/
		default:
			$coeff=1;
	}
	$points=$points*$coeff;
	// Mise à jour des points
	
	$s_update="UPDATE pronos SET points='".$points."' WHERE id_user='".$d_pronos['id_user']."' AND id_match='".$d_pronos['id_match']."'";
	
	mysql_query($s_update)
		or die(mysql_error()); 
	$users[$d_pronos['id_user']]['points']+=$points;
	$users[$d_pronos['id_user']]['n_pronos']++;
}

	


// Mise à jour des totaux

foreach($users as $id_user => $data) {
	echo '<pre>';
	print_r($data);
	echo '</pre>';
	// Ajout de la bonification pour le bon vainqueur
	//$bonus=($id_vainqueur==$data['Finale']['id_vainqueur'])?-30:0;
	$s_update="UPDATE users SET points='".($data['points']+$bonus)."' WHERE id_user='".$id_user."'";
	echo $s_update.'<br/>';
	$r_update=mysql_query($s_update)
		or die(mysql_error());
}

// mise à jour du classement des parieurs
$s_user="SELECT id_user FROM users ORDER BY points";
$r_user=mysql_query($s_user);
$classement=1;
echo 'coucou';
while($d_user=mysql_fetch_array($r_user)) {	
	if ($users[$d_user['id_user']]['n_pronos']>0) {
		$s_class="UPDATE users SET classement=".$classement.", actif=1 WHERE id_user=".$d_user['id_user'];
		$classement++;
		echo '<span>'.$d_user['login'].' '.$d_user['points'].'</span>';
	} else {
		$s_class="UPDATE users SET classement=10000, points=0, actif=0 WHERE id_user=".$d_user['id_user'];
	}
	mysql_query($s_class);
}

// mise à jour des points de groupes
$s_user_groupes="SELECT G.id_groupe, COUNT(UG.id_user) AS n_user, AVG(U.points) AS moyenne
		FROM groupes G
		INNER JOIN l_users_groupes UG
			ON UG.id_groupe=G.id_groupe
		INNER JOIN users U
			ON UG.id_user=U.id_user
		WHERE G.actif=1 AND U.actif=1
		GROUP BY G.id_groupe
		ORDER BY moyenne, n_user DESC";
$r_user_groupes=mysql_query($s_user_groupes)
	or die(mysql_error());
$classement=1;
while ($d_user_groupes=mysql_fetch_array($r_user_groupes)) {
	$s_update="UPDATE groupes SET moyenne='".$d_user_groupes['moyenne']."', n_user='".$d_user_groupes['n_user']."', classement='".$classement."'
		WHERE id_groupe='".$d_user_groupes['id_groupe']."'";
	mysql_query($s_update);
	$classement++;
}

?>
