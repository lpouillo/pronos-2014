
<div class="row">
<?php
/*if (isset($_SESSION['id_user'])) {
	echo '<div class="9u">';
} else {
	echo '<div class="12u">';
}*/
?>
	<div class="12u">
		<section class="box" id="acc_message">
	<?php
		include('app/includes/accueil/message.php');
	?>

		</section>
	</div>
<?php
if (isset($_SESSION['id_user'])) {
/*
?>
	<div class="3u box" id="acces_mes_pronos">
		<ul>
			<li>
				<a class="button" href="index.php?page=mon_espace" title="Voir mes informations">
					<img border="0" src="public/images/icons/mon_espace.png" alt="mon_espace"/>
					Mon compte</a>
			</li>
			<li>
			<a class="button" href="index.php?page=mon_espace#mes_pronos">
				<img border="0" src="public/images/icons/application_form.png" alt="mes_pronos"/>
				Mes pronostiques</a>
			</li>
					<li>
			<a class="button" href="index.php?page=mon_espace#mes_groupes">
				<img border="0" src="public/images/icons/group.png" alt="mes_pronos"/>
				Mes groupes</a>
			</li>

	</div>
<?php
*/
}
?>

</div>

<div>
	<div class="row">
		<div class="4u">
			<section class="box">
				<span class="pennant"><span class="fa fa-star"></span></span>
				<?php
				include('app/includes/accueil/classement.php');
				?>
			</section>
		</div>
		<div class="4u">
			<section class="box">
				<span class="pennant pennant-alt2"><span class="fa fa-tasks"></span></span>
				<header>
					<h2>Résultats des matchs</h2>
				</header>
				<?php
				include('app/includes/accueil/matchs.php');
				?>
			</section>

		</div>
		<div class="4u">
			<section class="box">
				<span class="pennant pennant-alt"><span class="fa fa-flash"></span></span>
				<header>
					<h2>Nouvelles du tournoi</h2>
				</header>
				<?php
				include('app/includes/accueil/news.php');
				?>
			</section>
		</div>
	</div>
<div>
	<div class="row">
		<div class="12u">
			<div class="4u">
				<section class="first">

				</section>
			</div>
			</section>
			<div class="4u">

			</div>
			<div class="4u">

			</div>
		</div>
	</div>
</div>