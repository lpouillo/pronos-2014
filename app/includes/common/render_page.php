<?php
if (empty($_POST['page']) AND $page!='graphs') {
	// Inclusion du haut du template
	require_once('app/includes/common/top.php');
}

// Inclusion de la page demandée
require_once('app/pages/'.$page.'.php');

// Ecriture de l'url et titre pour la mise à jour
if ($page!='graphs') {
	echo '<div id="update_titre">'.$titre.'</div>
	<div id="update_url">'.$url.'</div>';
}

if (empty($_POST['page']) AND $page!='graphs') {
	// Inclusion du bas du template
	require_once('app/includes/common/bottom.php');
}


?>
