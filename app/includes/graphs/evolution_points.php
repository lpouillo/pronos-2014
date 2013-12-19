<?php

// Selection des tous les pronostics des poules
$s_pronos="SELECT P.id_user, P.id_match, P.score1 AS prono1, P.score2 AS prono2, M.score1, M.score2, M.special, M.type, 
	EQ1.acronym AS ac1, EQ2.acronym AS ac2 FROM pronos P	
	INNER JOIN matchs M 
		ON P.id_match=M.id_match
	INNER JOIN equipes EQ1
		ON M.id_equipe1=EQ1.id_equipe
	INNER JOIN equipes EQ2
		ON M.id_equipe2=EQ2.id_equipe
	WHERE M.joue=1
	ORDER BY M.date_match, M.heure";

$users=array();
$r_pronos=mysql_query($s_pronos);
$n_pronos=array();
$points_match_user=array();
while ($d_pronos=mysql_fetch_array($r_pronos)) {
	if ($d_pronos['score1']>$d_pronos['score2']) {
		$res_match='1';
	} elseif ($d_pronos['score1']<$d_pronos['score2']) {
		$res_match='2';		
	} else {
		$res_match='N';
	}
	if ($d_pronos['prono1']>$d_pronos['prono2']) {
		$res_prono='1';
	} elseif ($d_pronos['prono1']<$d_pronos['prono2']) {
		$res_prono='2';		
	} else {
		$res_prono='N';
	}	
	$delta=($res_prono==$res_match)?1:0;
	$points=-5*$delta+abs($d_pronos['score1']-$d_pronos['prono1'])+abs($d_pronos['score2']-$d_pronos['prono2']);
	$points=($d_pronos['special'])?(2*$points):$points;
	switch($d_pronos['type']) {
		case 'Huitieme1':
		case 'Huitieme2':
		case 'Huitieme3':
		case 'Huitieme4':
		case 'Huitieme5':
		case 'Huitieme6':
		case 'Huitieme7':
		case 'Huitieme8':
			$coeff=2;
		break;
		case 'Quart1':
		case 'Quart2':
		case 'Quart3':
		case 'Quart4':
			$coeff=3;
		break;
		case 'Demi1':
		case 'Demi2':
			$coeff=4;
		break;
		case 'p_finale':
			$coeff=5;
		break;
		case 'Finale':
			$coeff=6;
		break;
		default:
			$coeff=1;
	}
	$points=$points*$coeff;
	$points_match_user[$d_pronos['id_match']][$d_pronos['id_user']]=$points;
	$points_match_user[$d_pronos['id_match']]['equipe1']=$d_pronos['ac1'];
	$points_match_user[$d_pronos['id_match']]['equipe2']=$d_pronos['ac2'];
}

$points_evolution_user=array();
$i=1;
foreach($points_match_user as $id_match => $points_user) {
	foreach($points_user as $id_user => $points) {
		$points_evolution_user[$i][$id_user]=$points_evolution_user[$i-1][$id_user]+$points;
	}
	$i++;
}
// Création des séries de données
$DataSet = new pData;

$DataSet->AddPoint(0,'max');
$DataSet->AddPoint(0,'moy');
$DataSet->AddPoint(0,'min');
$DataSet->AddPoint(0,'user');
foreach($points_evolution_user as $i => $points_user) {
	if ($i%2) {
		$DataSet->AddPoint($i,'matchs');
	} else {	
		$DataSet->AddPoint('','matchs');
	}
//	echo $i.' '.max($points_user).' '.array_sum($points_user)/count($points_user).' '.min($points_user).'<br/>';
	$DataSet->AddPoint(max($points_user),'max');
	$DataSet->AddPoint(array_sum($points_user)/count($points_user),'moy');
	$DataSet->AddPoint(min($points_user),'min');
	$DataSet->AddPoint($points_user[$_SESSION['id_user']],'user');
}
$DataSet->SetSerieName('Le dernier','max');
$DataSet->SetSerieName('La moyenne','moy');
$DataSet->SetSerieName('Le premier','min');
$DataSet->SetSerieName('Mon score','user');
$DataSet->AddSerie('user');
$DataSet->AddSerie('min');
$DataSet->AddSerie('moy');
$DataSet->AddSerie('max'); 
$DataSet->SetAbsciseLabelSerie('matchs');

// Cache definition   
$Cache = new pCache();  
$Cache->CacheFolder='public/cache/';
$Cache->GetFromCache("Graph".$_SESSION['id_user'],$DataSet->GetData());

// Création du graphique
$Test = new pChart(1000,300); 
$Test->setFontProperties("app/classes/pChart/Fonts/tahoma.ttf",7);  
$Test->setGraphArea(30,30,970,270);  
$Test->drawGraphArea(255,255,255,TRUE);  
$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,0,0,0,TRUE,0,1,TRUE);     
$Test->drawGrid(2,TRUE,230,230,230,50);  
$Test->drawTreshold(0,143,55,72,TRUE,TRUE);  
$Test->drawFilledLineGraph($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE);
$Test->clearShadow();  
$Test->drawLegend(75,35,$DataSet->GetDataDescription(),255,255,255);    

// Finalisation
$Cache->WriteToCache("Graph".$_SESSION['id_user'],$DataSet->GetData(),$Test);  
$Test->Stroke();

?>
