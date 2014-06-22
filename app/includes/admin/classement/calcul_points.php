<?php
// Total des points gagnés par chaque user.
echo '<h3>Calcul du total de points pour chaque utilisateur</h3>';
// récupération de la somme des points pour chaque utilisateur
$s_pronos="SELECT id_user, SUM(points) as points FROM pronos GROUP BY id_user";
$r_pronos=mysqli_query($db_pronos,$s_pronos);

// construction de la requète de mise à jour des points
$s_points="INSERT INTO users (`id_user`, `points`) VALUES ";
while ($joueur=mysqli_fetch_array($r_pronos)) {
	$s_points.="(".$joueur['id_user'].",".$joueur['points']."),";
}
$s_points=rtrim($s_points, ",")." ON DUPLICATE KEY UPDATE points=VALUES(points)";
mysqli_query($db_pronos, $s_points)
	or die(mysqli_error($db_pronos));

// Calcul du nouveau classement
$s_points="SELECT id_user, login, points FROM users ORDER BY points";
$r_points=mysqli_query($db_pronos, $s_points);

$s_classement="INSERT INTO users (`id_user`, `classement`) VALUES ";
$prev_points=-10000;
$count=1;
$classement=1;
while($joueur=mysqli_fetch_array($r_points)) {
	if ($joueur['points']>$prev_points) {
		$classement = $count;
	}
	$s_classement.="(".$joueur['id_user'].",".$classement."),";

	$prev_points=$joueur['points'];
	$count=$count+1;
}
$s_classement=rtrim($s_classement, ",")." ON DUPLICATE KEY " .
		"UPDATE classement=VALUES(classement)";
mysqli_query($db_pronos, $s_classement)
	or die(mysqli_error($db_pronos));


// Calcul de la moyenne et du nombre d'utilisateur pour les groupes des groupes
$s_groupes = "SELECT LUG.id_groupe, SUM(U.points) AS points, COUNT(LUG.id_user) AS n_user " .
		"FROM l_users_groupes LUG " .
		"INNER JOIN users U" .
		"	ON U.id_user=LUG.id_user " .
		"	AND LUG.actif=1 " .
		"INNER JOIN groupes G" .
		"	ON G.id_groupe=LUG.id_groupe" .
		"	AND G.actif=1 ".
		"GROUP BY LUG.id_groupe";
$r_groupes = mysqli_query($db_pronos, $s_groupes);

$s_points_groupe="INSERT INTO groupes (`id_groupe`, `moyenne`, `n_user`) VALUES ";
while($groupe = mysqli_fetch_array($r_groupes)) {
	$s_points_groupe.="(".$groupe['id_groupe'].",".$groupe['points']/$groupe['n_user']
	.",".$groupe['n_user']."),";
}
$s_points_groupe=rtrim($s_points_groupe, ",")." ON DUPLICATE KEY UPDATE moyenne=VALUES(moyenne)," .
		"n_user=VALUES(n_user)";
mysqli_query($db_pronos, $s_points_groupe)
	or die(mysqli_error($db_pronos));

// Calcul du classement des groupes
$s_groupes="SELECT id_groupe, nom, moyenne FROM groupes ORDER BY moyenne, n_user";
$r_groupes=mysqli_query($db_pronos, $s_groupes);

$s_classement="INSERT INTO groupes (`id_groupe`, `classement`) VALUES ";
$prev_points=-10000;
$count=1;
$classement=1;
while($groupe=mysqli_fetch_array($r_groupes)) {
	if ($groupe['moyenne']>$prev_points) {
		$classement = $count;
	}
	$s_classement.="(".$groupe['id_groupe'].",".$classement."),";

	$prev_points=$groupe['moyenne'];
	$count=$count+1;
}
$s_classement=rtrim($s_classement, ",")." ON DUPLICATE KEY " .
		"UPDATE classement=VALUES(classement)";
echo $s_classement;
mysqli_query($db_pronos, $s_classement)
	or die(mysqli_error($db_pronos));

?>