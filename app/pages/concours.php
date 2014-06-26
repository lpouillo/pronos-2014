<?php
if (empty($_GET['section'])) {
	// Par défaut on affiche le classement des parieurs et des groupes
	// récupération des parieurs
	include('app/includes/concours/general.php');
	include('app/includes/concours/groupes.php');

	$html='<div class="row">
			  	<div class="8u box">
				    <header>' .
				   		'<h2>Classement général du concours</h2>'.
					'</header>' .
					'Il y a '.mysqli_num_rows($r_parieurs).' participants au concours.'.
					' Voir le <a href="index.php?page=concours&section=relief">classement en relief</a>.' .
					$html_parieurs.
				'</div>' .
				'<div class="4u box">' .
				'	<header>' .
				'		<h2>Classement par groupes</h2>' .
				'   </header>' .
				'   Il y a '.$n_groupes.' groupe(s) d\'utilisateurs.' .
					$html_groupe.
				'</div>';

} else {
	switch($_GET['section']) {
		case 'groupe':
			include('app/includes/concours/par_groupe.php');
		break;
		case 'relief':
			include('app/includes/concours/relief.php');
		break;
	}
}
echo $html;
?>
