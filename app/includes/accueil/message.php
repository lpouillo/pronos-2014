<?php

if (time()<$timestamp_poules_debut) {
?>
<p>
Ce jeu est ouvert à toutes et à tous. Son <a href="index.php?page=reglement" title="Connaitre le règlement complet">
règlement</a> a été créé pour le championnat d'Europe des Nations 2004 par Geneviève Moguilny et Alexandre Fournier.
Il se déroule cette année du <?php echo strftime('%A %d %B',$timestamp_poules_debut).' au '.strftime('%A %d %B',$timestamp_tableau_fin);?>,
avec deux phases de paris&nbsp;:
</p>
<ul class="foot">
	<li>pour les matchs de poules : le
	<img height="10px" src="public/images/icons/danger.png" alt="Attention :">
	<?php echo strftime('%d %B &agrave; %H:%M',$timestamp_poules_debut);?>
	<img height="10px" src="public/images/icons/danger.png" alt="Attention :">,
	</li>
	<li>pour le tableau final : le
	<img height="10px" src="public/images/icons/danger.png" alt="Attention :">
	<?php echo strftime('%d %B &agrave; %H:%M',$timestamp_tableau_debut);?>
	<img height="10px" src="public/images/icons/danger.png" alt="Attention :">
	<strong>vous n'aurez que deux jours pour parier</strong></li>
</ul>
<p>
N'hésitez pas à vous <strong><a href="index.php?page=inscription" title="S'inscrire pour pouvoir participer au concours">inscrire sur le site</a></strong>
et à le partager avec vos amis.
</p>
<div style="margin-top:0px;margin-right:100px;text-align:right;">
	Bonne chance à toutes et à tous !
</div>
<?php
} elseif (time()<$timestamp_poules_fin) {
?>
<p> La phase de poule a démarré et les inscriptions sont closes.<br/>
	<img height="10px" src="public/images/icons/danger.png" alt="Attention :">
Après la fin des matchs de poules (le <? echo strftime('%d %B &agrave; %H:%M',$timestamp_poules_fin);?>),
vous devrez parier pour le tableau final avant le
<strong><?php echo strftime('%d %B &agrave; %H:%M',$timestamp_tableau_debut);?></strong>.
<img height="10px" src="public/images/icons/danger.png" alt="Attention :"></p>

<?php
} elseif (time()>$timestamp_poules_fin and time()<$timestamp_tableau_debut) {
?>
<p> La phase de poule est terminée.<br/>
	<img height="10px" src="public/images/icons/danger.png" alt="Attention :">
	vous devez parier pour le tableau final avant le
<strong><?php echo strftime('%d %B &agrave; %H:%M',$timestamp_tableau_debut);?></strong>.
<img height="10px" src="public/images/icons/danger.png" alt="Attention :"></p>


<?php
} else {
?>
<p> Le tournoi final a démarré. Rendez vous <?php echo strftime('%d %B &agrave; %H:%M',$timestamp_tableau_fin);?>
pour la fin du concours</p>
<?php
}
?>