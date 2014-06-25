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
					'Il y a '.mysqli_num_rows($r_parieurs).' participants au concours.' .
					'<div class="row">' .
					$html_parieurs.
					'</div>'.
				'</div>' .
				'<div class="4u box">' .
				'	<header>' .
				'		<h2>Classement par groupes</h2>' .
				'   </header>' .
				'   Il y a '.$n_groupes.' groupe(s) d\'utilisateurs.' .
				'<div class="8u" style="margin:auto;">'.
				'<a class="button" href="index.php?page=mon_espace&#mes_groupes">Accédez à mes groupes</a>'.
				'</div>'.
					$html_groupe.
				'</div>';

} else {
	switch($_GET['section']) {
		case 'groupe':
			include('app/includes/concours/par_groupe.php');
		break;
	}
}
echo $html;
?>
