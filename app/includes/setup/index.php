<?php
/*
 * Created on 16 mai 2014
 *
 */

//print_r($_POST);
$html_top = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Installation - Concours de pronostics pour</title>
		<link rel="stylesheet" media="screen" type="text/css" title="Design" href="setup.css"/>
	</head>
	<body>
		<div id="conteneur">';
$html_bottom = '
		</div>
	</body>
</html>';

if (empty($_POST['step'])) {
	$content='
		<h1>Installation du site de pronostics</h1>
		<form method="post" action="index.php" id="form_login">
		<table>
			<th>Administrateur</th>
			<tr>
				<td>Login</td>
				<td class="td_input">
					<input type="text" name="admin_login"/>
				</td>
			</tr>
			<tr>
				<td>Password</td>
				<td class="td_input">
					<input type="password" name="admin_passwd"/>
				</td>
			</tr>
			<tr>
				<td>Confirmer</td>
				<td class="td_input">
					<input type="password" name="admin_passwd2"/>
				</td>
			</tr>
			<tr>
				<td>Nom (utilisé dans les emails)</td>
				<td class="td_input">
					<input type="text" name="admin_name"/>
				</td>
			</tr>
			<tr>
				<td>Email</td>
				<td class="td_input">
					<input type="text" name="admin_email"/>
				</td>
			</tr>
			<th>Base de données</th>
			<tr>
				<td>Hôte</td>
				<td class="td_input">
					<input type="text" name="db_host"/>
				</td>
			</tr>
			<tr>
				<td>Nom de la base</td>
				<td class="td_input">
					<input type="text" name="db_name"/>
				</td>
			</tr>
			<tr>
				<td>Utilisateur</td>
				<td class="td_input">
					<input type="text" name="db_user"/>
				</td>
			</tr>
			<tr>
				<td>Mot de passe</td>
				<td class="td_input">
					<input type="password" name="db_passwd"/>
				</td>
			</tr>
			<tr>
				<th colspan="2"><input type="submit" value="Lancer l\'installation" class="input_submit"/>

				</th>
			</tr>
		</table>
		<input type="hidden" name="step" value="check">
		</form>';
} else {
	$content = '';
	switch ($_POST['step']) {
		case 'check':
		$error = false;
		if ($_POST['admin_passwd'] == '') {
			$content .= 'Mot de passe de l\'administrateur vide<br/>';
			$error = true;
		}
		if ($_POST['admin_passwd'] != $_POST['admin_passwd2']) {
			$content .= 'Mot de passe de l\'administrateur non concordants<br/>';
			$error = true;
		}
		$con = mysqli_connect($_POST['db_host'], $_POST['db_user'], $_POST['db_passwd'],
					$_POST['db_name']);
		if (mysqli_connect_errno()) {
 			$content .=  'Échec de connexion à MySQL: ' . mysqli_connect_error() . '<br/>';
 			$error = true;
		}
		if ($error) {
			$content .= '<a href="javascript:history.back()">Revenir en arrière</a>';
		} else {
			$content .= 'Tout est OK, installation en cours.<br/>';

			include('create_database.php');

		}


		break;

	}
}
//$content = 'coucou';

echo $html_top . $content. $html_bottom;
?>
