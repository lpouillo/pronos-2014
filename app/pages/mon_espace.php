<?php
$html = '';
if (empty($_SESSION['id_user'])) {
	$html.='<p>Vous devez vous connecter pour accéder à cette page. Si vous n\'avez pas encore de compte, cliquez
		<a href="index.php?page=inscription">ici</a></p>';
} else {
	if (empty($_GET['section'])) {
		$section = 'mon_compte';
	} else {
		$section = $_GET['section'];
	}
	switch ($section) {
			case 'mon_compte':
			case 'mes_pronos':
			case 'mes_groupes':
				require_once('app/includes/mon_espace/'.$section.'.php');
			break;
		default:
			require_once('app/includes/mon_espace/mon_compte.php');
	}
}

echo $html;
?>
