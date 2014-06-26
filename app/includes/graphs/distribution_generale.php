<?php
$s_distrib="SELECT points, COUNT(id_user) AS n_users FROM users
	WHERE actif=1
	GROUP BY points
	ORDER BY points";
$r_distrib=mysqli_query($db_pronos, $s_distrib)
	or die(mysqli_error());
while ($d_distrib=mysqli_fetch_array($r_distrib)) {
	$distrib[$d_distrib['points']]=$d_distrib['n_users'];
}
$min_points=min(array_keys($distrib));
$max_points=max(array_keys($distrib));


$DataSet = new pData;
for($i=$min_points;$i<=$max_points;$i++) {
	if (empty($distrib[$i])) {
		$distrib[$i]=0;
	}
	$DataSet->AddPoints($distrib[$i],'n_parieurs');
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
