<?php
$s_distrib="SELECT points, COUNT(id_user) AS n_users FROM users 
	WHERE actif=1
	GROUP BY points
	ORDER BY points";
$r_distrib=mysql_query($s_distrib)
	or die(mysql_error());
while ($d_distrib=mysql_fetch_array($r_distrib)) {
	$distrib[$d_distrib['points']]=$d_distrib['n_users'];
}
$s_min_max="SELECT MAX(points) AS max, MIN(points) AS min FROM users";
$r_min_max=mysql_query($s_min_max);
$d_min_max=mysql_fetch_array($r_min_max);


$DataSet = new pData;
for($i=$d_min_max['min'];$i<=$d_min_max['max'];$i++) {
	if (empty($distrib[$i])) {
		$distrib[$i]=0;
	}
	$DataSet->AddPoints($distrib[$i],'n_parieurs');
	if ($i%50==0) {
		$DataSet->AddPoints($i,'points');	 
	} else {
		$DataSet->AddPoints('','points');
	}
}


/* Create a pChart object and associate your dataset */ 
$myPicture = new pImage(700,230,$DataSet);

/* Choose a nice font */
$myPicture->setFontProperties(array("FontName"=>"app/classes/pChart/fonts/verdana.ttf","FontSize"=>11));

 /* Define the boundaries of the graph area */
$myPicture->setGraphArea(60,40,670,190);

 /* Draw the scale, keep everything automatic */ 
$myPicture->drawScale();

 /* Draw the scale, keep everything automatic */ 
$myPicture->drawBarChart();

 /* Render the picture (choose the best way) */
//$myPicture->autoOutput("pictures/example.basic.png");
$myPicture->Stroke();

/*
// Définition des séries
$DataSet->SetSerieName('Nombre de parieurs','n_parieurs'); 
$DataSet->AddSerie('n_parieurs'); 
$DataSet->SetAbsciseLabelSerie('points');

// Cache definition   
$Cache = new pCache();  
$Cache->CacheFolder='public/cache/';
$Cache->GetFromCache("Graph1",$DataSet->GetData());  

$Test = new pChart(510,230);  
$Test->setFontProperties("app/classes/pChart/fonts/tahoma.ttf",7);  
$Test->setGraphArea(35,30,485,200);
$Test->drawRoundedRectangle(5,5,500,225,5,0,119,75);  
$Test->drawGraphArea(255,255,255,TRUE);  
$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,0,0,0,TRUE,0,2,TRUE);     
$Test->drawGrid(4,TRUE,230,230,230,50);  
$Test->drawTreshold(0,143,55,72,TRUE,TRUE);  
$Test->setColorPalette(0,0,119,75);
//$Test->drawBarGraph($DataSet->GetData(),$DataSet->GetDataDescription(),TRUE); 
$Test->drawFilledCubicCurve($DataSet->GetData(),$DataSet->GetDataDescription(),.1,50);  
$Test->setFontProperties("app/classes/pChart/fonts/tahoma.ttf",10);  
$Test->drawTitle(50,22,"Répartition des parieurs",50,50,50,300);

// Finalisation
$Cache->WriteToCache("Graph1",$DataSet->GetData(),$Test);  
$Test->Stroke();*/
