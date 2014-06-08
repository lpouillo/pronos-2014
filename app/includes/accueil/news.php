<?php

$flux = array(
	 array(
	 	'src' => "FIFA",
		'rss' => "http://fr.fifa.com/worldcup/news/rss.xml",
		'filter' => " ",
		'post' => ""),
	array(
		'src' => "CDF",
		'rss' => "http://www.cahiersdufootball.net/rss/rss_article.php",
		'filter' => "",
		'post' => ""),
	array(
		'src' => "EQUIP",
		'rss' => "http://www.lequipe.fr/rss/actu_rss_Football.xml",
		'filter' => "Foot - CM",
		'post' => " "),
	array(
		'src' => "F365",
		'rss' => "http://www.football365.fr/coupe-du-monde-2014/rss.xml",
		'filter' => "CM 2014",
		'post' => "")
);
$news=array();
foreach ($flux as $data) {
	$news = array_merge($news, get_news($data));
}

$dates = array();
foreach ($news as $key => $row)
{
    $dates[$key] = $row['date'];
}
array_multisort($dates, SORT_DESC, $news);
$html='<ul id="news">';
foreach ($news as $info) {
	$link = str_replace('Foot - CM - ', '', $info['txt']);
	$link = str_replace('CM 2014 / ', '', $link);
	$html .= '<li>' .
			'<a href="'.$info['link'].'">'.$link.'</a>'.
			'</li>';
}

$html.='</ul>';

echo $html;
?>
