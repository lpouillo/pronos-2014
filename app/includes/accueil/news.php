<?php
$html = '<div id="w">
  <div id="content">' .
  	'<div id="cdf" class="feedcontainer"></div>' .
    '<hr>
    <div id="horsjeu" class="feedcontainer"></div>
    <hr>
    <div id="lequipe" class="feedcontainer"></div>
    <hr>
    <div id="fifa" class="feedcontainer"></div>

  </div><!-- @end #content -->
</div><!-- @end #w -->';
$html .= "<script type=\"text/javascript\">
$(function(){
  // running custom RSS functions
  parseRSS('http://horsjeu.net/feed/', '#horsjeu');
  parseRSS('http://www.cahiersdufootball.net/rss/rss_article.php', '#cdf');
  parseRSS('http://www.lequipe.fr/rss/actu_rss_Football.xml', '#lequipe');
  parseRSS('http://fr.fifa.com/worldcup/news/rss.xml', '#fifa');
});
</script>";


/*
$flux = array(
	array(
		'src' => "HorsJeu.net",
		'url' => "http://horsjeu.net/feed/"),
	array(
		'src' => "Cahiers du football",
		'url' => "http://www.cahiersdufootball.net/rss/rss_article.php"),
	array(
		'src' => "L'\équipe",
		'url' => "http://www.lequipe.fr/rss/actu_rss_Football.xml"),
	array(
	 	'src' => "FIFA",
		'url' => "http://fr.fifa.com/worldcup/news/rss.xml"),
	array(
		'src' => "Football 365",
		'url' => "http://www.football365.fr/coupe-du-monde-2014/rss.xml")

);

$html = '';
foreach ($flux as $data) {
	$html.='<h3>'.$data['src'].'</h3>';
	$html.='<script language="JavaScript" ' .
		'src="http://itde.vccs.edu/rss2js/feed2js.php?src='.urlencode($data['src']).'&chan=n&num=3&desc=0&date=n&targ=y" type="text/javascript"></script>

		<noscript>
		<a href="http://itde.vccs.edu/rss2js/feed2js.php?src='.urlencode($data['src']).'&chan=n&num=3&desc=0&date=y&targ=y&html=y">View RSS feed</a>
		</noscript>';
}

/*

$news=array();

	print_r( lit_rss($data['url'], array("title","link","description","pubDate")));
	//$news = array_merge($news, lit_rss($data, array("title","link","description","pubDate")));
}

echo '<pre>';
print_r($news);
echo '</pre>';

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

$html.='</ul>';*/

echo $html;
?>
