<?
$html.='<h3>Calcul du tableau</h3>
	<ul>';
// Recuperation des equipes classes premier ou deuxieme
$s_equipes="SELECT id_equipe, nom, poule, classement FROM equipes WHERE classement=1 OR classement=2 ORDER BY classement";
$r_equipes=mysqli_query($db_pronos,$s_equipes);
$equipes=array();
while ($d_equipes=mysql_fetch_array($r_equipes)) {
	$equipes[$d_equipes['poule']][$d_equipes['classement']]=array('id_equipe' => $d_equipes['id_equipe'], 'nom' => $d_equipes['nom']);
}
switch ($cup_groups) {
	case 8:
		$j=0;
		for ($i=1;$i<=4;$i++) {
			$s_replace="UPDATE matchs SET id_equipe1='".$equipes[$i+$j][1]['id_equipe']."', id_equipe2='".$equipes[$i+$j+1][2]['id_equipe']."'
				WHERE `type`='Huitieme".$i."'";
			mysqli_query($db_pronos,$s_replace);
			$s_replace="UPDATE matchs SET id_equipe1='".$equipes[$i+$j][2]['id_equipe']."', id_equipe2='".$equipes[$i+$j+1][1]['id_equipe']."'
				WHERE `type`='Huitieme".($i+4)."'";
			mysqli_query($db_pronos,$s_replace);
			$j++;
		}
	break;
	case 4:
		$j=0;
		for ($i=1;$i<=2;$i++) {
			$s_replace="UPDATE matchs SET id_equipe1='".$equipes[$i+$j][1]['id_equipe']."', id_equipe2='".$equipes[$i+$j+1][2]['id_equipe']."'
				WHERE `type`='Quart".($i+$j)."'";
			mysqli_query($db_pronos,$s_replace);
			$html.='<li>Quart'.($i+$j).' oppose '.$equipes[$i+$j][1]['nom'].' à '.$equipes[$i+$j+1][2]['nom'].'</li>';
			$s_replace="UPDATE matchs SET id_equipe1='".$equipes[$i+$j+1][1]['id_equipe']."', id_equipe2='".$equipes[$i+$j][2]['id_equipe']."'
				WHERE `type`='Quart".($i+$j+1)."'";
			mysqli_query($db_pronos,$s_replace);
			$html.='<li>Quart'.($i+$j+1).' oppose '.$equipes[$i+$j][2]['nom'].' à '.$equipes[$i+$j+1][1]['nom'].'</li>';
			$j++;
		}
}

/*
if ($cup_groups==8) {
// Mise à jour des huitiemes



}*/



// Mise à jour des quarts
/*$s_v_huitieme="SELECT type, CASE
					WHEN score1>score2 THEN id_equipe1
					WHEN score1<score2 THEN id_equipe2
					WHEN (score1=score2 AND tab1>tab2) THEN id_equipe1
					WHEN (score1=score2 AND tab1<tab2) THEN id_equipe2
					END AS id_vainqueur
				FROM matchs WHERE type LIKE 'Huitieme%'";
$r_v_huitieme=mysqli_query($db_pronos,$s_v_huitieme);

$i=0;
while ($d_v_huitieme=mysql_fetch_array($r_v_huitieme)) {
	$i++;
	if ($i==2 or $i==4 or $i==6 or $i==8) {
		$s_update="UPDATE matchs SET id_equipe1='".$id_1."', id_equipe2='".$d_v_huitieme['id_vainqueur']."' WHERE type='Quart".($i/2)."'";
		mysqli_query($db_pronos,$s_update)
			or die(mysql_error());
	}
	$id_1=$d_v_huitieme['id_vainqueur'];
}*/


// Mise à jour des demi
$s_v_quart="SELECT type, CASE
					WHEN score1>score2 THEN id_equipe1
					WHEN score1<score2 THEN id_equipe2
					WHEN (score1=score2 AND tab1>tab2) THEN id_equipe1
					WHEN (score1=score2 AND tab1<tab2) THEN id_equipe2
					END AS id_vainqueur
				FROM matchs WHERE `type` LIKE 'Quart%'";
$r_v_quart=mysqli_query($db_pronos,$s_v_quart);
$v_quarts=array();
while ($d_v_quart=mysql_fetch_array($r_v_quart)) {
	$v_quarts[$d_v_quart['type']]=$d_v_quart['id_vainqueur'];
}


for ($i=1;$i<=2;$i++) {
	$s_update="UPDATE matchs SET id_equipe1='".$v_quarts['Quart'.$i]."', id_equipe2='".$v_quarts['Quart'.($i+2)]."' WHERE `type`='Demi".$i."'";
	mysqli_query($db_pronos,$s_update)
		or die(mysql_error());
	$html.='<li>Demi'.($i).' oppose '.$v_quarts['Quart'.$i].' à '.$v_quarts['Quart'.($i+2)].'</li>';
}

// Mise à jour de la finale
$s_v_demies="SELECT type, CASE
					WHEN score1>score2 THEN id_equipe1
					WHEN score1<score2 THEN id_equipe2
					WHEN (score1=score2 AND tab1>tab2) THEN id_equipe1
					WHEN (score1=score2 AND tab1<tab2) THEN id_equipe2
					END AS id_vainqueur,
					CASE
					WHEN score1>score2 THEN id_equipe2
					WHEN score1<score2 THEN id_equipe1
					WHEN (score1=score2 AND tab1>tab2) THEN id_equipe2
					WHEN (score1=score2 AND tab1<tab2) THEN id_equipe1
					END AS id_perdant
				FROM matchs WHERE `type` LIKE 'Demi%'";

$r_v_demi=mysqli_query($db_pronos,$s_v_demies)
	or die($s_v_demies.'<br/>'.mysql_error());
$v_demis=array();
while ($d_v_demi=mysql_fetch_array($r_v_demi)) {
	$v_demis[$d_v_demi['type']]=$d_v_demi['id_vainqueur'];
}
$s_update="UPDATE matchs SET id_equipe1='".$v_demis['Demi1']."', id_equipe2='".$v_demis['Demi2']."' WHERE `type`='Finale'";

mysqli_query($db_pronos,$s_update)
	or die(mysql_error());
$html.='<li>Finale oppose '.$v_demis['Demi1'].' a '.$v_demis['Demi2'];

$html.='</ul>';
?>

