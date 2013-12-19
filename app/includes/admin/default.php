<?php 
$html.='<div style="width:300px;margin:auto;">
	Veuillez choisir une section :
	<form id="choix_admin" method="post" action="index.php">
		<input type="hidden" name="page" value="admin"/>
		<input type="radio" name="section" value="utilisateurs"/> Utilisateurs<br/>
		<input type="radio" name="section" value="droits"/> Droits<br/>
		<input type="radio" name="section" value="equipes"/> Équipes<br/>
		<input type="radio" name="section" value="matchs"/> Matchs<br/>
		<input type="radio" name="section" value="classement"/> Calcul du classement<br/>
		<input type="radio" name="section" value="email"/> Calcul du classement<br/>
	</form>
	<input type="submit" value="OK" onClick="submitForm(\'choix_admin\');"/>
	</div>';
?>