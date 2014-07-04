<?php
$html = '<div id="w">
  <div id="content">
  	<div id="cdf" class="feedcontainer"></div>
    <div id="horsjeu" class="feedcontainer"></div>
    <div id="lequipe" class="feedcontainer"></div>
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

echo $html;
?>
