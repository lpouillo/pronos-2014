<?php
if(isset($_SESSION['id_user']) AND $_SESSION['is_admin']) {
	$html='<ul id="menu_admin">
		<li onClick="affElement(\'admin\',\'matchs\',\'\',\'\',\'page\');">Matchs</li><li onClick="affElement(\'admin\',\'equipes\',\'\',\'\',\'page\');">Equipes</li><li onClick="affElement(\'admin\',\'utilisateurs\',\'\',\'\',\'page\');">Utilisateurs</li><li onClick="affElement(\'admin\',\'groupes\',\'\',\'\',\'page\');">Groupes</li><li onClick="affElement(\'admin\',\'classement\',\'\',\'\',\'page\');">Classement</li><li onClick="affElement(\'admin\',\'email\',\'\',\'\',\'page\');">Email</li>
		</ul>';
	if (empty($_POST['section'])) {
		require_once('app/includes/admin/default.php');
	} else {
		switch ($_POST['section']) {
			//case 'droits':
			case 'utilisateurs':
			case 'groupes':
			case 'matchs':
			case 'classement':
			case 'equipes':
			case 'email':
				require_once('app/includes/admin/'.$_POST['section'].'.php');
			break;
			default:
			require_once('app/includes/admin/default.php');
		}
	}
} else {
	$html.='<p>Vous n\'avez pas le droit d\'accéder à cette partie du site. Ou alors connectez-vous !!</p>';
}

echo $html;
?>
