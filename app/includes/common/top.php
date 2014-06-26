<html>
	<head>
		<title><?=$titre?> - Concours de Pronostiques World Cup 2014</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<link rel="shortcut icon" type="image/png" href="public/images/icons/equipe.png" />
		<link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,900,300italic" rel="stylesheet" />
		<script src="public/js/jquery.min.js"></script>
		<script src="public/js/jquery.dropotron.min.js"></script>
		<script src="public/js/config.js"></script>
		<script src="public/js/skel.min.js"></script>
		<script src="public/js/skel-panels.min.js"></script>
		<script type="text/javascript" language="javascript" src="public/js/parser.js"></script>
		<noscript>
			<link rel="stylesheet" href="public/css/skel-noscript.css" />
			<link rel="stylesheet" href="public/css/style.css" />
			<link rel="stylesheet" href="public/css/design.css" />
			<link rel="stylesheet" href="public/css/style-desktop.css" />
		</noscript>
		<!--[if lte IE 8]><script src="public/js/html5shiv.js"></script><link rel="stylesheet" href="public/css/ie8.css" /><![endif]-->
	</head>
	<body class="homepage">

		<!-- Header Wrapper -->
			<div id="header-wrapper">
				<div class="container">
					<div class="row">
						<section id="header">
							<div class="12u">
								<div class="row">
									<div class="2u">
										<img height="80px" src="http://upload.wikimedia.org/wikipedia/en/thumb/e/e8/WC-2014-Brasil.svg/160px-WC-2014-Brasil.svg.png" alt="Image de la page '.$page.'"/>
									</div>
									<div class="7u" id="titre">
										<a class="button" href="index.php">Concours de Pronostiques World Cup 2014</a>
									</div>
									<div class="3u" id="login">
									<?php
									if (empty($_SESSION['id_user'])) {
									?>
										<form method="post" action="<?=$_SERVER['REQUEST_URI']?>" id="form_login">
											<table cellspacing="3" cellpadding="3" border="0" >
												<tr>
													<td>Login</td>
													<td><input type="text" id="login" name="login"/></td>
													<td>Password</td><td><input type="password" name="password"/></td>
													<td><input type="submit" value="OK" class="OK"/>
												</tr>
												<tr>
													<td colspan="4" id="oubli_inscription">
													 <?=($login_error)?' <em style="color:red"> Mauvais identifiants</em>':'';?>
													<a href="index.php?page=inscription&token=new">Mot de passe oublié</a> -
													<a href="index.php?page=inscription">Inscription</a></td>
												</tr>
											</table>
										</form>
									<?php
									} else {
									?>

										<ul id="user_menu">
											<li>
												<a href="index.php?page=mon_espace" title="Voir mes informations">
													<img border="0" src="public/images/icons/mon_espace.png" alt="mon_espace"/><strong><?=$_SESSION['login']?></strong>
												</a>
												<img border="0" src="public/images/icons/arrow_right.png" height="10px" alt="evolution"/>
												<strong><?=$_SESSION['classement']?></strong>
												<a href="index.php?page=deconnexion" title="Déconnexion">
												<img border="0"src="public/images/icons/door_out.png" alt="logout"/>
												</a></li>
											<li>
												<a href="index.php?page=mon_espace&#mes_groupes" title="Déconnexion">
													<img border="0" src="public/images/icons/group.png" alt="mon_pronos"/> Mes groupes
												</a>
											</li>
											<li>
												<a href="index.php?page=mon_espace&#mes_pronos" title="Déconnexion">
													<img border="0" src="public/images/icons/application_form.png" alt="mes_pronos"/> Pronostiques
												</a>
											</li>

									<?php
										if ($_SESSION['is_admin']) {
											echo '<li><a href="index.php?page=admin">
												<img border="0" src="public/images/icons/tux.png" alt="admin"/>
												Administration</a></li>';
										}
									?>
										</ul>

									<?php
									}
									?>
									</div>
								</div>
								<nav id="nav">
									<ul>
										<li<?=($page=='accueil')?' class="current_page_item"':''?>>
											<a href="index.php?page=accueil">
												<img height="14px" alt="icon" src="public/images/icons/accueil.png">
												Accueil</a>
										</li>
										<li<?=($page=='reglement')?' class="current_page_item"':''?>>
											<a href="index.php?page=reglement">
												<img height="14px" src="public/images/icons/reglement.png" alt="icon"/>
												Réglement</a>
										</li>
										<li<?=($page=='resultats')?' class="current_page_item"':''?>>
											<a href="index.php?page=resultats">
												<img height="14px" src="public/images/icons/resultats.png" alt="icon"/>
												Résultats</a>
											<ul>
												<li><a href="index.php?page=resultats#poules">Poules</a></li>
												<li><a href="index.php?page=concours#tableau_final">Tableau final</a></li>
											</ul>
										</li>
										<li<?=($page=='concours')?' class="current_page_item"':''?>>
											<a href="index.php?page=concours">
												<img height="14px" src="public/images/icons/concours.png" alt="icon"/>
												Concours</a>
											<ul>
												<li><a href="index.php?page=concours#general">Classement général</a></li>
												<li><a href="index.php?page=concours&section=relief">Classement en relief</a></li>
												<li><a href="index.php?page=concours#groupes">Classement par groupe</a></li>
											</ul>
										</li>
										<li<?=($page=='mon_espace')?' class="current_page_item"':''?>>
											<a href="index.php?page=mon_espace">
												<img height="14px" src="public/images/icons/mon_espace.png" alt="icon"/>
												Mon espace</a>
										</li>

									</ul>
								</nav>
							</div>
						</div>
					</section>
				</div>
			</div>

			<div id="main-wrapper">
				<div class="container">
					<div class="row">
						<div class="12u">
							<section>
								<header class="major">
									<h2><?=$titre?></h2>
								</header>
