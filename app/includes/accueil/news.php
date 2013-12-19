<?php
$html='<div><h4><a href="http://fr.uefa.com">News de l\'UEFA</a></h4>'
	.lire_rss("http://fr.uefa.com/rssfeed/news/rss.xml",6).'
	<h4><a href="http://www.cahiersdufootball.net/">Les Cahiers du Football</a></h4>'
	.lire_rss("http://www.cahiersdufootball.net/rss/rss_article.php",5).'
	<h4><a href="http://www.lequipe.fr/Football/">L\'Équipe</a></h4>'
	.lire_rss("http://www.lequipe.fr/Xml/Football/Titres/actu_rss.xml",5).'
	<h4><a href="http://www.football365.fr/">Football365</a></h4>'
	.lire_rss("http://www.football365.fr/euro-2012/rss.xml",4).'</div>';
echo $html;
?>