<?php
/*
 * Created on 20 mai 2014
 *
 */
setlocale (LC_TIME, 'fr_FR.utf8', 'fra');
date_default_timezone_set('Europe/Paris');


$s_matchs = "(SELECT `date_match`, `heure`, `type` FROM matchs WHERE `type`='poule' ORDER BY `date_match` ASC LIMIT 1) " .
		"UNION " .
		"(SELECT `date_match`, `heure`, `type` FROM matchs WHERE `type`='poule' ORDER BY `date_match` DESC LIMIT 1) " .
		"UNION" .
		"(SELECT `date_match`, `heure`, `type` FROM matchs WHERE `type`<>'poule' ORDER BY `date_match` ASC LIMIT 1) " .
		"UNION " .
		"(SELECT `date_match`, `heure`, `type` FROM matchs WHERE `type`<>'poule' ORDER BY `date_match` DESC LIMIT 1) ";

$r_matchs = mysqli_query($db_pronos, $s_matchs);


$timestamp_poules_debut = array_date_match_to_ts(mysqli_fetch_row($r_matchs));
$timestamp_poules_fin = array_date_match_to_ts(mysqli_fetch_row($r_matchs)) + 6300;
$timestamp_tableau_debut = array_date_match_to_ts(mysqli_fetch_row($r_matchs));
$timestamp_tableau_fin = array_date_match_to_ts(mysqli_fetch_row($r_matchs)) + 6300;
?>
