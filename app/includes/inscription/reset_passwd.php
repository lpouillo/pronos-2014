<?php
$show_form=true;
if (isset($_POST['login'])) {
	$s_check_user="SELECT email FROM users WHERE login='".$_POST['login']."' AND email='".$_POST['email']."'";
	$r_check_user=mysqli_query($db_pronos, $s_check_user);
	if (mysqli_num_rows($r_check_user)) {
		$show_form=false;
		$token=md5(date('Y-m-d h:i:s'));
		$s_update="UPDATE users SET token='".$token."' WHERE login='".$_POST['login']."' AND email='".$_POST['email']."'";
		mysqli_query($db_pronos, $s_update)
		or die('Impossible de recréer un token <br/>'.$s_update.'<br/>'.mysqli_error($db_pronos));
		// Envoi du mail de confirmation
		$headers ='From: "Pronos 2014" <'.$admin_email.">\n".
				'Bcc: "Pronos 2014" <'.$admin_email.">\n";
		$headers .='Content-Type: text/html; charset="utf-8"'."\n";
		$headers .='Content-Transfer-Encoding: 8bit';

		$uri=explode('&',$_SERVER['REQUEST_URI']);
		$message='Bonjour '.htmlentities($_POST['login']).'.<br/><br/>

							Quelqu\'un (probablement vous) a utilisé demander à réinitialiser votre mot de passe.<br/><br/>
							Pour choisir un nouveau mot de passe, il vous suffit de cliquer sur le le lien suivant :<br/>
							<a href="http://'.$_SERVER['HTTP_HOST'].$uri[0].'&token='.$token.'">
							http://'.$_SERVER['HTTP_HOST'].'/'.$uri[0].'&token='.$token.'</a>
							<br/><br/>
							Cordialement
							<br/>
							Le webmaster du site de pronostiques ..
								';
		sendmail($_POST['email'],'Nouveau mot de passe sur le site de pronostiques 2014',$message);

		$html.='<p class="box">Un lien pour réinitialiser votre mot de passe vous a été envoyé.</p>';
	} else {
		$aucun='Aucun compte correspondant aux informations soumises n\'a été trouvé.';
	}
}  else {
	$aucun='Veuillez renseignez les informations suivantes';
}

if ($show_form) {
	$html.='
							<div class="box">
							<p>'.$aucun.'</p>
							<form method="post" action="#" id="frm_inscription">
							<p>
							<input type="hidden" name="page" value="inscription"/>
							<input type="hidden" name="token" value="new"/>
							</p>
							<table id="inscription">
								<tr>
									<th style="text-align:left;">login</th><td><input type="text" name="login"/></td>
								</tr>
								<tr>
									<th style="text-align:left;">email utilisé pour l\'inscription</th><td><input type="text" name="email"/></td>
								</tr>
							</table>
							<p><input type="submit" value="Faire une demande pour un nouveau mot de passe"/></p>
							</form>
							</div>';
}
?>
