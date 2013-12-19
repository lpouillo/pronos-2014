		</div>
		<div id="footer">
		<a href="mailto:<? echo $admin_email;?>">Contactez les webmasters</a> - Dernière modification le 
		<?php echo date('d/m/Y à G:i.',$last_mod);?>
		</div>
	</div>
	
	<form method="get" id="form_nav" action="#">
	<p>
		<input type="hidden" value="0" name="id"/>
		<input type="hidden" value="" name="page"/>
		<input type="hidden" value="" name="section"/>
		<input type="hidden" value="" name="action"/>
	</p>
	</form>
	
	<!-- Piwik -->
	<script type="text/javascript">
	var pkBaseURL = (("https:" == document.location.protocol) ? "https://educatix.ipgp.fr/piwik/" : "http://educatix.ipgp.fr/piwik/");
	document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
	</script><script type="text/javascript">
	try {
	var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 18);
	piwikTracker.trackPageView();
	piwikTracker.enableLinkTracking();
	} catch( err ) {}
	</script><noscript><p><img src="http://educatix.ipgp.fr/piwik/piwik.php?idsite=18" style="border:0" alt="" /></p></noscript>
	<!-- End Piwik Tag -->
	</body>
</html>
