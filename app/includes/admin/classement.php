<?php
$html = '';

echo time().'<br/>';
// Calcul du tournoi
require_once ('app/includes/admin/classement/poules.php');
require_once ('app/includes/admin/classement/tableau.php');

// Calcul des cotes des matchs
require_once ('app/includes/admin/classement/cotes.php');

// On calcul les points de chaque pronos
$html.=($page == 'admin')?'<h3>Calcul des pronos</h3>':'';
require_once ('app/includes/admin/classement/pronos.php');
echo time().'<br/>';

// Calcul des points des parieurs
$html.=($page == 'admin')?'<h3>Calcul des points et du classement</h3>':'';
require_once ('app/includes/admin/classement/points.php');
echo time().'<br/>';

?>