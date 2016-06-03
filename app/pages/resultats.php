<?php
$html = '';
if (empty($_GET['section'])) {
	if (time()<$timestamp_poules_fin) {
		require_once('app/includes/resultats/poules.php');
		$html.='<br/>';
		require_once('app/includes/resultats/tableau.php');
	} else {
		require_once('app/includes/resultats/tableau.php');
		$html.='<br/>';
		require_once('app/includes/resultats/poules.php');
	}
} else {
	switch($_GET['section']) {
		case 'match':
			include('app/includes/resultats/match.php');
		break;
	}
}
echo $html;
?>