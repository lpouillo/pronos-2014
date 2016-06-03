<?php

$html='<div class="box">';
if(isset($_SESSION['id_user']) AND $_SESSION['is_admin']) {
	$html='';
	if (isset($_POST['section'])) {
		$section=$_POST['section'];
	} else if (isset($_GET['section'])) {
		$section=$_GET['section'];
	} else {
		$section='';
	}
	switch ($section) {
		//case 'droits':
		case 'utilisateurs':
		case 'groupes':
		case 'matchs':
		case 'classement':
		case 'equipes':
		case 'email':
			require_once('app/includes/admin/'.$section.'.php');
		break;
		default:
		require_once('app/includes/admin/default.php');
	}
} else {
	$html='<p>Vous n\'avez pas le droit d\'accéder à cette partie du site. Ou alors connectez-vous !!</p>';
}
$html.="</div>";
echo $html;
?>
