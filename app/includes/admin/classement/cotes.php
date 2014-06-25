<?php
/*
 * Created on 22 juin 2014
 * Ce fichier permet de calculer les cotes des matchs à partir des paris des utilisateurs
 */
$html.=($page == 'admin')?'<h3>Calcul des cotes</h3>':'';
$s_matchs="SELECT id_match, COUNT( id_user ) AS total, SUM(
			CASE WHEN score1 > score2
			THEN 1
			ELSE 0
			END ) AS cote_1, SUM(
			CASE WHEN score1 = score2
			THEN 1
			ELSE 0
			END ) AS cote_N, SUM(
			CASE WHEN score1 < score2
			THEN 1
			ELSE 0
			END ) AS cote_2
			FROM pronos
			GROUP BY id_match";
$r_matchs=mysqli_query($db_pronos,$s_matchs);

$s_cotes = "INSERT INTO matchs (id_match, cote_1, cote_N, cote_2) VALUES ";
while ($d_matchs=mysqli_fetch_array($r_matchs)) {
	$s_cotes.= "(".$d_matchs['id_match'].
		",".round($d_matchs['cote_1']/$d_matchs['total'],2).
		",".round($d_matchs['cote_N']/$d_matchs['total'],2).
		",".round($d_matchs['cote_2']/$d_matchs['total'],2)."),";

}
$s_cotes=rtrim($s_cotes, ",")." ON DUPLICATE KEY UPDATE cote_1=VALUES(cote_1)," .
	"cote_N=VALUES(cote_N), cote_2=VALUES(cote_2)";

mysqli_query($db_pronos, $s_cotes)
	or die(mysqli_error($db_pronos));
$html.=($page == 'admin')?$s_cotes:'';
?>
