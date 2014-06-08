<?php
$html = '';
if (empty($_SESSION['id_user'])) {
	$html.='<p>Vous devez vous connecter pour accéder à cette page. Si vous n\'avez pas encore de compte, cliquez
		<a href="index.php?page=inscription">ici</a></p>';
} else {
	$sections = array('mon_compte', 'mes_groupes', 'mes_pronos');
	foreach ($sections as $section) {
		require_once('app/includes/mon_espace/'.$section.'.php');
	}
}

echo $html;
?>
