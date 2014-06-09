<?php
$html = '';
if (empty($_SESSION['id_user'])) {
	$html.='<p>Vous devez vous connecter pour accéder à cette page.</p>
									<form method="post" action="index.php" id="form_login">
											<table cellspacing="3" cellpadding="3" border="0" >
												<tr>
													<td>Login</td>
													<td><input type="text" id="login" name="login"/></td>
													<td>Password</td><td><input type="password" name="password"/></td>
													<td><input type="submit" value="OK" class="OK"/>
												</tr>
												<tr>
													<td colspan="4" id="oubli_inscription">';
							$html.=($login_error)?' <em style="color:red"> Mauvais identifiants</em>':'';
							$html.='				<a href="index.php?page=inscription&token=new">Mot de passe oublié</a> -
													<a href="index.php?page=inscription">Inscription</a></td>
												</tr>
											</table>
										</form>';
} else {
	$sections = array('mon_compte', 'mes_groupes', 'mes_pronos');
	foreach ($sections as $section) {
		require_once('app/includes/mon_espace/'.$section.'.php');
	}
}

echo $html;
?>
