<?
// Recuperation des equipes classes premier ou deuxieme
$s_equipes="SELECT id_equipe, poule, classement FROM equipes WHERE classement=1 OR classement=2 ORDER BY classement";
$r_equipes=mysql_query($s_equipes);
$equipes=array();
while ($d_equipes=mysql_fetch_array($r_equipes)) {
	$equipes[$d_equipes['poule']][$d_equipes['classement']]=$d_equipes['id_equipe'];
}
switch ($cup_groups) {
	case 8:
		$j=0;
		for ($i=1;$i<=4;$i++) {
			$s_replace="UPDATE matchs SET id_equipe1='".$equipes[$i+$j][1]."', id_equipe2='".$equipes[$i+$j+1][2]."' WHERE type='Huitieme".$i."'";
			mysql_query($s_replace);
			$s_replace="UPDATE matchs SET id_equipe1='".$equipes[$i+$j][2]."', id_equipe2='".$equipes[$i+$j+1][1]."' WHERE type='Huitieme".($i+4)."'";
			mysql_query($s_replace);
			$j++;
		}
	break;
	case 4:
		$j=0;
		for ($i=1;$i<=2;$i++) {
			$s_replace="UPDATE matchs SET id_equipe1='".$equipes[$i+$j][1]."', id_equipe2='".$equipes[$i+$j+1][2]."' WHERE type='Quart".$i."'";
			mysql_query($s_replace);
			$s_replace="UPDATE matchs SET id_equipe1='".$equipes[$i+$j][2]."', id_equipe2='".$equipes[$i+$j+1][1]."' WHERE type='Quart".($i+2)."'";
			mysql_query($s_replace);
			$j++;
		}
}
if ($cup_groups==8) {
// Mise à jour des huitiemes
	
	
	
}



// Mise à jour des quarts
$s_v_huitieme="SELECT type, CASE 
					WHEN score1>score2 THEN id_equipe1
					WHEN score1<score2 THEN id_equipe2
					WHEN (score1=score2 AND tab1>tab2) THEN id_equipe1
					WHEN (score1=score2 AND tab1<tab2) THEN id_equipe2
					END AS id_vainqueur
				FROM matchs WHERE type LIKE 'Huitieme%'";
$r_v_huitieme=mysql_query($s_v_huitieme);

$i=0;
while ($d_v_huitieme=mysql_fetch_array($r_v_huitieme)) {
	$i++;
	if ($i==2 or $i==4 or $i==6 or $i==8) {
		$s_update="UPDATE matchs SET id_equipe1='".$id_1."', id_equipe2='".$d_v_huitieme['id_vainqueur']."' WHERE type='Quart".($i/2)."'";
		mysql_query($s_update)
			or die(mysql_error());
	}
	$id_1=$d_v_huitieme['id_vainqueur'];
}

// Mise à jour des demi
$s_v_quart="SELECT type, CASE 
					WHEN score1>score2 THEN id_equipe1
					WHEN score1<score2 THEN id_equipe2
					WHEN (score1=score2 AND tab1>tab2) THEN id_equipe1
					WHEN (score1=score2 AND tab1<tab2) THEN id_equipe2
					END AS id_vainqueur
				FROM matchs WHERE type LIKE 'Quart%'";
$r_v_quart=mysql_query($s_v_quart);

$i=0;
while ($d_v_quart=mysql_fetch_array($r_v_quart)) {
	$i++;
	if ($i==2 or $i==4) {
		$s_update="UPDATE matchs SET id_equipe1='".$id_1."', id_equipe2='".$d_v_quart['id_vainqueur']."' WHERE type='Demi".($i/2)."'";
		mysql_query($s_update)
			or die(mysql_error());
	}
	$id_1=$d_v_quart['id_vainqueur'];
}
// Mise à jour des finales
$s_demies="SELECT type, CASE 
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
				FROM matchs WHERE type LIKE 'Demi%'";

$r_demies=mysql_query($s_demies)
	or die($s_demies.'<br/>'.mysql_error());
$i=0;
while ($d_demies=mysql_fetch_array($r_demies)) {
	$i++;
	if ($i==2) {
		$s_update="UPDATE matchs SET id_equipe1='".$id_vainqueur."', id_equipe2='".$d_demies['id_vainqueur']."' WHERE type='Finale'";
		mysql_query($s_update)
			or die(mysql_error());
		$s_update="UPDATE matchs SET id_equipe1='".$id_perdant."', id_equipe2='".$d_demies['id_perdant']."' WHERE type='p_finale'";
		mysql_query($s_update)
			or die(mysql_error());
	}
	$id_vainqueur=$d_demies['id_vainqueur'];
	$id_perdant=$d_demies['id_perdant'];
}

?>

