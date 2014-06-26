<?
$html.=($page == 'admin')?'<h3>Calcul du tableau</h3>':'';
// Recuperation des equipes classes premier ou deuxieme
$s_equipes="SELECT id_equipe, nom, poule, classement FROM equipes " .
		"WHERE classement=1 OR classement=2 " .
		"ORDER BY classement";

$r_equipes=mysqli_query($db_pronos,$s_equipes);
$equipes=array();
while ($d_equipes=mysqli_fetch_array($r_equipes)) {
	$equipes[$d_equipes['poule']][$d_equipes['classement']]=array('id_equipe' => $d_equipes['id_equipe'], 'nom' => $d_equipes['nom']);
}


foreach ($regles['Huitieme'] as $i => $poules) {
	$s_update="UPDATE matchs SET id_equipe1='".$equipes[$poules[0]][1]['id_equipe']."', " .
			"id_equipe2='".$equipes[$poules[1]][2]['id_equipe']."'
		WHERE `type`='Huitieme".$i."'";
	mysqli_query($db_pronos,$s_update);
	$html.=($page == 'admin')?$s_update.'<br/>':'';
}


// Mise à jour des quarts
$s_v_huitieme="SELECT type, CASE
					WHEN score1>score2 THEN id_equipe1
					WHEN score1<score2 THEN id_equipe2
					WHEN (score1=score2 AND tab1>tab2) THEN id_equipe1
					WHEN (score1=score2 AND tab1<tab2) THEN id_equipe2
					WHEN (score1=score2 AND tab1=tab2) THEN 0
					END AS id_vainqueur
				FROM matchs WHERE type LIKE 'Huitieme%'";
$r_v_huitieme=mysqli_query($db_pronos,$s_v_huitieme);
$v_huitiemes = array_fill(0, 8, NULL);
while ($d_v_huitieme=mysqli_fetch_array($r_v_huitieme)) {
	$v_huitiemes[substr($d_v_huitieme['type'],-1)] = $d_v_huitieme['id_vainqueur'];
}

foreach ($regles['Quart'] as $i => $huitiemes) {
	$s_update="UPDATE matchs SET id_equipe1='".$v_huitiemes[$huitiemes[0]]."',
			id_equipe2='".$v_huitiemes[$huitiemes[1]]."'
			WHERE type='Quart".$i."'";
	mysqli_query($db_pronos,$s_update);
	$html.=($page == 'admin')?$s_update.'<br/>':'';
}



// Mise à jour des demi
$s_v_quart="SELECT type, CASE
					WHEN score1>score2 THEN id_equipe1
					WHEN score1<score2 THEN id_equipe2
					WHEN (score1=score2 AND tab1>tab2) THEN id_equipe1
					WHEN (score1=score2 AND tab1<tab2) THEN id_equipe2
					WHEN (score1=score2 AND tab1=tab2) THEN 0
					END AS id_vainqueur
				FROM matchs WHERE `type` LIKE 'Quart%'";
$r_v_quart=mysqli_query($db_pronos,$s_v_quart);
$v_quarts = array_fill(0, 4, NULL);
while ($d_v_quart=mysqli_fetch_array($r_v_quart)) {
	$v_quarts[substr($d_v_quart['type'],-1)] = $d_v_quart['id_vainqueur'];
}


foreach ($regles['Demi'] as $i => $quarts) {
	$s_update="UPDATE matchs SET id_equipe1='".$v_quarts[$quarts[0]]."',
			id_equipe2='".$v_quarts[$quarts[1]]."'
			WHERE type='Demi".$i."'";
	mysqli_query($db_pronos,$s_update);
	$html.=($page == 'admin')?$s_update.'<br/>':'';
}


// Mise à jour des finales
$s_v_demies="SELECT type, CASE
					WHEN score1>score2 THEN id_equipe1
					WHEN score1<score2 THEN id_equipe2
					WHEN (score1=score2 AND tab1>tab2) THEN id_equipe1
					WHEN (score1=score2 AND tab1<tab2) THEN id_equipe2
					WHEN (score1=score2 AND tab1=tab2) THEN 0
					END AS id_vainqueur,
					CASE
					WHEN score1>score2 THEN id_equipe2
					WHEN score1<score2 THEN id_equipe1
					WHEN (score1=score2 AND tab1>tab2) THEN id_equipe2
					WHEN (score1=score2 AND tab1<tab2) THEN id_equipe1
					WHEN (score1=score2 AND tab1=tab2) THEN 0
					END AS id_perdant
				FROM matchs WHERE `type` LIKE 'Demi%'";

$r_v_demi=mysqli_query($db_pronos,$s_v_demies)
	or die($s_v_demies.'<br/>'.mysqli_error($db_pronos));
$v_demis=array();
$p_demis=array();
while ($d_v_demi=mysqli_fetch_array($r_v_demi)) {
	$v_demis[substr($d_v_demi['type'],-1)]=$d_v_demi['id_vainqueur'];
	$p_demis[substr($d_v_demi['type'],-1)]=$d_v_demi['id_perdant'];
}

$s_update="UPDATE matchs SET id_equipe1='".$v_demis[1]."', " .
		"id_equipe2='".$v_demis[2]."' WHERE `type`='Finale'";
$html.=($page == 'admin')?$s_update.'<br/>':'';
mysqli_query($db_pronos,$s_update)
	or die(mysqli_error($db_pronos));
$s_update="UPDATE matchs SET id_equipe1='".$p_demis[1]."', " .
		"id_equipe2='".$p_demis[2]."' WHERE `type`='p_finale'";
$html.=($page == 'admin')?$s_update.'<br/>':'';
mysqli_query($db_pronos,$s_update)
	or die(mysqli_error($db_pronos));

?>

