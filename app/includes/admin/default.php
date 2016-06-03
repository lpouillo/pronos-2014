<?php
$html.='<div class="box center">
	Veuillez choisir une section :
	<form id="choix_admin" method="post" action="index.php">
		<input type="hidden" name="page" value="admin"/>
		<input type="radio" name="section" value="utilisateurs"/> Utilisateurs<br/>
		<input type="radio" name="section" value="droits"/> Droits<br/>
		<input type="radio" name="section" value="equipes"/> Équipes<br/>
		<input type="radio" name="section" value="matchs"/> Matchs<br/>
		<input type="radio" name="section" value="classement"/> Calcul du classement<br/>
		<input type="radio" name="section" value="email"/> Liste des emails<br/>
		<input type="radio" name="section" value="groupes"/> Groupes<br/>
		<input type="submit" value="OK"/>
	</form>

	</div>';
?>