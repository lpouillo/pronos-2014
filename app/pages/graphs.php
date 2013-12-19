<?php
// Standard inclusions   
include('app/classes/pChart/class/pDraw.class.php');
include('app/classes/pChart/class/pImage.class.php');
include('app/classes/pChart/class/pData.class.php'); 

switch($_GET['type']) {
	case 'distribution_generale':
	case 'evolution_points':
		require_once('app/includes/graphs/'.$_GET['type'].'.php');
	break;
}