<?php
$html='<h3>Informations sur le tournoi</h3>' .
		'<div><h4><a href="http://www.fifa.com/worldcup/">News de la FIFA</a></h4>'
	.lire_rss("http://www.fifa.com/worldcup/news/rss.xml", 6).'
	<h4><a href="http://www.cahiersdufootball.net/">Les Cahiers du Football</a></h4>'
	.lire_rss("http://www.cahiersdufootball.net/rss/rss_article.php",5).'
	<h4><a href="http://www.lequipe.fr/Football/">L\'Équipe</a></h4>'
	.lire_rss("http://www.lequipe.fr/Xml/Football/Titres/actu_rss.xml",5).'
	<h4><a href="http://www.football365.fr/">Football365</a></h4>'
	.lire_rss("http://www.football365.fr/euro-2012/rss.xml",4).'</div>';
echo $html;
?>