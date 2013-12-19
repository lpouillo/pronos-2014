<table id="accueil_tbl">
	<tr>
		<td id="acc_message" colspan="2">
		<?php 
		include('app/includes/accueil/message.php');
		?>
		</td>
		<td id="acc_classement" rowspan="2">
		<?php 
		include('app/includes/accueil/classement.php');
		?>
		</td>
	</tr>
	<tr>
		<td id="acc_matchs">
		<?php 
		include('app/includes/accueil/matchs.php');
		?>
		</td>
		<td id="acc_rss">
		<h3>Informations sur le tournoi</h3>
		<?php 
		include('app/includes/accueil/news.php');
		?>
		</td>
	</tr>
</table>

