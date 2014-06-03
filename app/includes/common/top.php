<html>
	<head>
		<title><?=$titre?> - Concours de Pronostiques World Cup 2014</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<link href="http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,900,300italic" rel="stylesheet" />
		<script src="public/js/jquery.min.js"></script>
		<script src="public/js/jquery.dropotron.min.js"></script>
		<script src="public/js/config.js"></script>
		<script src="public/js/skel.min.js"></script>
		<script src="public/js/skel-panels.min.js"></script>
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
										<form method="post" action="index.php" id="form_login">
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
													<a href="index.php?page=inscription">Mot de passe oublié</a> -
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
											<li><a href="index.php?page=mon_espace&section=mes_groupes" title="Déconnexion"><img border="0" src="public/images/icons/group.png" alt="mon_pronos"/> Mes groupes</a> </li>
											<li><a href="index.php?page=mon_espace&section=mes_pronos" title="Déconnexion"><img border="0" src="public/images/icons/application_form.png" alt="mes_pronos"/> Pronostiques</li>

									<?php
										if ($_SESSION['is_admin']) {
											echo '<li><img border="0" src="public/images/icons/tux.png" alt="mon_pronos"/> Administration</li>';
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
										<li<?=($page=='accueil')?' class="current_page_item"':''?>><a href="index.php?page=accueil">Accueil</a></li>
										<li<?=($page=='reglement')?' class="current_page_item"':''?>><a href="index.php?page=reglement">Réglement</a></li>
										<li<?=($page=='resultats')?' class="current_page_item"':''?>><a href="index.php?page=resultats">Résultats</a></li>
										<li<?=($page=='concours')?' class="current_page_item"':''?>>
											<a href="index.php?page=concours">Concours</a>
											<ul>
												<li><a href="index.php?page=concours&section=general">Classement général</a></li>
												<li><a href="index.php?page=concours&section=relief">Classement en relief</a></li>
												<li><a href="index.php?page=concours&section=groupes">Classement par groupe</a></li>
												<li><a href="index.php?page=concours&section=groupes">Informations sur les parieurs</a></li>
											</ul>
										</li>
										<li<?=($page=='mon_espace')?' class="current_page_item"':''?>><a href="index.php?page=mon_espace">Mon espace</a></li>

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

<?php /*
					<div class="row">
						<div class="12u">

							<!-- Intro -->
								<section id="intro">

									<div>
										<div class="row">
											<div class="4u">
												<section class="first">
													<span class="pennant"><span class="fa fa-cog"></span></span>
													<header>
														<h2>Ipsum consequat</h2>
													</header>
													<p>Nisl amet dolor sit ipsum veroeros sed blandit consequat veroeros et magna tempus.</p>
												</section>
											</div>
											<div class="4u">
												<section class="middle">
													<span class="pennant pennant-alt"><span class="fa fa-flash"></span></span>
													<header>
														<h2>Magna etiam dolor</h2>
													</header>
													<p>Nisl amet dolor sit ipsum veroeros sed blandit consequat veroeros et magna tempus.</p>
												</section>
											</div>
											<div class="4u">
												<section class="last">
													<span class="pennant pennant-alt2"><span class="fa fa-star"></span></span>
													<header>
														<h2>Tempus adipiscing</h2>
													</header>
													<p>Nisl amet dolor sit ipsum veroeros sed blandit consequat veroeros et magna tempus.</p>
												</section>
											</div>
										</div>
									</div>

									<div class="actions">
										<a href="#" class="button big">Get Started</a>
										<a href="#" class="button alt big">Learn More</a>
									</div>

								</section>

						</div>
					</div>
				</div>
			</div>

		<!-- Main Wrapper -->
			<div id="main-wrapper">
				<div class="container">
					<div class="row">
						<div class="12u">

							<!-- Portfolio -->
								<section>
									<header class="major">
										<h2>My Portfolio</h2>
									</header>
									<div>
										<div class="row">
											<div class="4u">
												<section class="box">
													<a href="http://facebook.com/DreametryDoodle" class="image image-full"><img src="images/pic02.jpg" alt="" /></a>
													<header>
														<h3>Ipsum feugiat et dolor</h3>
													</header>
													<p>Lorem ipsum dolor sit amet sit veroeros sed amet blandit consequat veroeros lorem blandit  adipiscing et feugiat phasellus tempus dolore ipsum lorem dolore.</p>
													<footer>
														<a href="#" class="button alt">Find out more</a>
													</footer>
												</section>
											</div>
											<div class="4u">
												<section class="box">
													<a href="http://facebook.com/DreametryDoodle" class="image image-full"><img src="images/pic03.jpg" alt="" /></a>
													<header>
														<h3>Sed etiam lorem nulla</h3>
													</header>
													<p>Lorem ipsum dolor sit amet sit veroeros sed amet blandit consequat veroeros lorem blandit  adipiscing et feugiat phasellus tempus dolore ipsum lorem dolore.</p>
													<footer>
														<a href="#" class="button alt">Find out more</a>
													</footer>
												</section>
											</div>
											<div class="4u">
												<section class="box">
													<a href="http://facebook.com/DreametryDoodle" class="image image-full"><img src="images/pic04.jpg" alt="" /></a>
													<header>
														<h3>Consequat et tempus</h3>
													</header>
													<p>Lorem ipsum dolor sit amet sit veroeros sed amet blandit consequat veroeros lorem blandit  adipiscing et feugiat phasellus tempus dolore ipsum lorem dolore.</p>
													<footer>
														<a href="#" class="button alt">Find out more</a>
													</footer>
												</section>
											</div>
										</div>
										<div class="row">
											<div class="4u">
												<section class="box">
													<a href="http://facebook.com/DreametryDoodle" class="image image-full"><img src="images/pic05.jpg" alt="" /></a>
													<header>
														<h3>Blandit sed adipiscing</h3>
													</header>
													<p>Lorem ipsum dolor sit amet sit veroeros sed amet blandit consequat veroeros lorem blandit  adipiscing et feugiat phasellus tempus dolore ipsum lorem dolore.</p>
													<footer>
														<a href="#" class="button alt">Find out more</a>
													</footer>
												</section>
											</div>
											<div class="4u">
												<section class="box">
													<a href="http://facebook.com/DreametryDoodle" class="image image-full"><img src="images/pic06.jpg" alt="" /></a>
													<header>
														<h3>Etiam nisl consequat</h3>
													</header>
													<p>Lorem ipsum dolor sit amet sit veroeros sed amet blandit consequat veroeros lorem blandit  adipiscing et feugiat phasellus tempus dolore ipsum lorem dolore.</p>
													<footer>
														<a href="#" class="button alt">Find out more</a>
													</footer>
												</section>
											</div>
											<div class="4u">
												<section class="box">
													<a href="http://facebook.com/DreametryDoodle" class="image image-full"><img src="images/pic07.jpg" alt="" /></a>
													<header>
														<h3>Dolore nisl feugiat</h3>
													</header>
													<p>Lorem ipsum dolor sit amet sit veroeros sed amet blandit consequat veroeros lorem blandit  adipiscing et feugiat phasellus tempus dolore ipsum lorem dolore.</p>
													<footer>
														<a href="#" class="button alt">Find out more</a>
													</footer>
												</section>
											</div>
										</div>
									</div>
								</section>

						</div>
					</div>
					<div class="row">
						<div class="12u">

							<!-- Blog -->
								<section>
									<header class="major">
										<h2>The Blog</h2>
									</header>
									<div>
										<div class="row">
											<div class="6u">
												<section class="box">
													<a href="http://facebook.com/DreametryDoodle" class="image image-full"><img src="images/pic08.jpg" alt="" /></a>
													<header>
														<h3>Magna tempus consequat lorem</h3>
														<span class="byline">Posted 45 minutes ago</span>
													</header>
													<p>Lorem ipsum dolor sit amet sit veroeros sed et blandit consequat sed veroeros lorem et blandit  adipiscing feugiat phasellus tempus hendrerit, tortor vitae mattis tempor, sapien sem feugiat sapien, id suscipit magna felis nec elit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos lorem ipsum dolor sit amet.</p>
													<footer class="actions">
														<a href="#" class="button fa fa-file-text">Continue Reading</a>
														<a href="#" class="button alt fa fa-comment">33 comments</a>
													</footer>
												</section>
											</div>
											<div class="6u">
												<section class="box">
													<a href="http://facebook.com/DreametryDoodle" class="image image-full"><img src="images/pic09.jpg" alt="" /></a>
													<header>
														<h3>Aptent veroeros et aliquam</h3>
														<span class="byline">Posted 45 minutes ago</span>
													</header>
													<p>Lorem ipsum dolor sit amet sit veroeros sed et blandit consequat sed veroeros lorem et blandit  adipiscing feugiat phasellus tempus hendrerit, tortor vitae mattis tempor, sapien sem feugiat sapien, id suscipit magna felis nec elit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos lorem ipsum dolor sit amet.</p>
													<footer class="actions">
														<a href="#" class="button fa fa-file-text">Continue Reading</a>
														<a href="#" class="button alt fa fa-comment">33 comments</a>
													</footer>
												</section>
											</div>
										</div>
									</div>
								</section>

						</div>
					</div>
				</div>
			</div>';*/

/*// Si l'utilisateur n'est pas connecté on lui met le focus dans le formulaire de login
if (empty($_SESSION['id_user'])) {
	$html_top.=' <body onload="document.form_login.login.focus();">';
} else {
	$html_top.='<body>';
}
// début de la structure
$html_top.='
	<div id="conteneur">
		<div id="header">
		<table>
			<tr>
				<td id="photo"><img height="100px" src="http://upload.wikimedia.org/wikipedia/en/thumb/e/e8/WC-2014-Brasil.svg/160px-WC-2014-Brasil.svg.png" alt="Image de la page '.$page.'"/></td>
				<td id="header_titre">
					<h1><a style="text-decoration:none" href="index.php" title="Retourner à l\'accueil du site de pronostics IPGP">
						Concours de pronostics Hekla : <span style="font-variant: small-caps">Euro 2012
						<p style="text-align:center">
						 <img src="public/images/flags/POL.gif" alt="flag" /> Pologne -
						 Ukraine <img src="public/images/flags/UKR.gif" alt="flag" /></p></span></a></h1>
						<span id="span_titre"><?php echo $titre;?></span>
				</td>
				<td id="td_login">';
// si pas d'utilisateur connecté on met le formulaire de login
if (empty($_SESSION['id_user'])) {
	$html_top.='	<form method="post" action="index.php" id="form_login">
					<table cellspacing="3" cellpadding="3" border="0" >
						<tr>
							<td>Login</td>
							<td style="text-align:right"><input type="text" id="login" name="login" class="input_login"/></td>
						</tr>
						<tr>
							<td>Password ';
// mauvais identification
	$html_top.=($login_error)?' <em style="color:red">Erreur</em>':'';
	$html_top.='				</td>
							<td style="text-align:right"><input type="password" name="password" class="input_login"/></td>
						</tr>
						<tr>
							<td colspan="2" style="text-align:center"><input type="submit" value="Connexion" class="input_submit"/>
							<a href="index.php?page=inscription&amp;token=new">Mot de passe oublié</a>
							<a href="https://hekla.ipgp.fr/"><img style="border:none;height:25px;" src="public/images/hekla.jpg"  alt="Logo de l\'Hekla"/></a>
							</td>
						</tr>
					</table>
					</form>';
} else {
// sinon on lui affiche son nom et le lien vers son espace
	$colspan=($_SESSION['is_admin'])?'':'colspan="2"';
	$html_top.='<table border="0" style="margin:auto;border-collapse:collapse;">
				<tr>
					<td colspan="2" style="text-align:right;padding:4px;"><span style="font-size:13px;color:#00774B;font-weight:bolder;"><em>'.$_SESSION['login'].'</em></span>
					&nbsp;&nbsp;&nbsp;
					'.$_SESSION['points'].' pts &nbsp;&nbsp;&nbsp;
					<img  border="0" src="public/images/icons/arrow_right.png" height="10px" alt="evolution"/> <strong>'.$_SESSION['classement'].'</strong>
					<a href="index.php?page=deconnexion" title="Déconnexion"><img  border="0"src="public/images/icons/door_out.png" alt="logout"/></a></td>
					<td rowspan="3"><p style="margin:auto;margin-top:5px;margin-bottom:5px" onclick="affElement(\'mon_espace\',\'\',\'\',\'\',\'page\')">
						<img  height="75px"  src="public/images/photos/inconnu_small.jpg" alt="photo"/></p></td>
				</tr>
				<tr>
					<td '.$colspan.'><p class="link bouton" onclick="affElement(\'mon_espace\',\'\',\'\',\'\',\'page\')">
								<img  border="0" src="public/images/icons/mon_espace.png" alt="mon_espace"/> Mon compte</p>
					</td>';

	$html_top.=($_SESSION['is_admin'])?'<td><p class="link bouton"<a href="#" onclick="affElement(\'admin\',\'\',\'\',\'\',\'page\')">
								<img border="0" src="public/images/icons/tux.png" alt="mon_pronos"/> Administration</a></p></td>':'';
	$html_top.='</tr>
				<tr>
					<td style="width="50%">
								<p class="link bouton" onclick="affElement(\'mon_espace\',\'mes_pronos\',\'\',\'\',\'page\')">
								<img border="0" src="public/images/icons/application_form.png" alt="mon_pronos"/> Pronostiques </p>
					</td>
					<td>
								<p class="link bouton" onclick="affElement(\'mon_espace\',\'mes_groupes\',\'\',\'\',\'page\')">
								<img border="0" src="public/images/icons/group.png" alt="mon_pronos"/> Mes groupes </p>
					</td>
				</tr>


							</table>';
	$html_top.='</p>';
}

// on ferme le header et on ouvre le menu

$html_top.='
				</td>
			</tr>
		</table>
		</div>
		<div id="menu">
			<ul>';

$menu=array();
// titr
foreach($pages as $k_page => $data) {
	if ($data['position']>0) {
		$menu[$data['position']]=array('page' => $k_page,'titre_menu' => $data['titre_menu'], 'title' => $data['titre']);
	}
}
foreach ($menu as $data) {
	$html_top.='
		<li onclick="affElement(\''.$data['page'].'\',\'\',\'\',\'\',\'page\');" title="'.$data['title'].'"><img height="14px" src="public/images/icons/'.$data['page'].'.png" alt="icon"/> '.$data['titre_menu'].'</li>';
}
$html_top.='			</ul>
		</div>
		<div id="page">';*/
/*echo $html_top;*/
