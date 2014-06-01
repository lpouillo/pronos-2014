
<div class="row">
	<div class="12u">
		<section class="box" id="acc_message">
			<?php
			include('app/includes/accueil/message.php');
			?>
		</section>
	</div>
</div>

<div>
	<div class="row">
		<div class="4u">
			<section class="box">
				<span class="pennant pennant-alt2"><span class="fa fa-tasks"></span></span>
				<header>
					<h2>Résultats des matchs</h2>
				</header>
				<?php
				include('app/includes/accueil/matchs.php');
				?>
				<footer>
					<a href="index.php?page=resultats" class="button">Tous les matchs</a>
				</footer>
			</section>

		</div>
		<div class="4u">
			<section class="box">
				<span class="pennant"><span class="fa fa-star"></span></span>
				<header>
					<h2>Classement du concours</h2>
				</header>
				<?php
				include('app/includes/accueil/classement.php');
				?>
				<footer>
					<a href="index.php?page=concours" class="button">Le classement complet</a>
				</footer>
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