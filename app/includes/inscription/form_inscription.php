<?php
if (empty($_POST["inscription_soumise"])) {
	
	// Si l'inscription n'a pas été soumise, on affiche le formulaire
	$html.='<div class="box">
			<h3>Veuillez renseigner ce formulaire pour effectuer une demande d\'inscription.</h3>
			<form method="post" action="index.php?page=inscription" id="frm_inscription">
			<p>
			<input type="hidden" name="inscription_soumise" value="oui"/>
			</p>
			<table id="inscription">
				<tr>
					<th style="text-align:left;">login</th><td><input type="text" name="login"/></td>
				</tr>
				<tr>
					<th style="text-align:left;">Nom réel</th><td><input type="text" name="nom"/></td>
				</tr>
				<tr>
					<th style="text-align:left;">email (valide)</th><td><input type="text" name="email"/></td>
				</tr>
				<tr>
					<th style="text-align:left;">confirmer l\'adresse email</th><td><input type="text" name="confirm_email"/></td>
				</tr>
				<tr>
					<th style="text-align:left;">Recevoir les news par email</th><td><input type="checkbox" name="news"/></td>
				</tr>
			</table>
			<p><input type="submit" value="Effectuer une demande d\'inscription"/></p>
			</form>
			</div>';
} else {
	// Si le formulaire a été soumis, on teste le login (existe dans la base ?) et l'adresse email est correcte dans les deux cas
	if (empty($_POST['login'])){
		$error_login=1;
	} else {
		$s_test_login="SELECT login FROM users WHERE login='".secure_mysql($_POST['login'])."'";
		$r_test_login=mysqli_query($db_pronos, $s_test_login);
		$error_login=(mysqli_num_rows($r_test_login))?1:0;
	}
	if (empty($_POST['email'])){
		$error_email=1;
	} else {
		$error_email=($_POST['email']==$_POST['confirm_email'])?0:1;
	}

	if ($error_login or $error_email) {
		// si une ou des erreurs on réaffiche le formulaire d'inscription
		$html.='
						<div class="box">
						<form method="post" action="#" id="frm_inscription">
						<p>
						<input type="hidden" name="page" value="inscription"/>
						<input type="hidden" name="inscription_soumise" value="oui"/>
						</p>
						<p><img src="public/images/icons/danger.png" alt="danger"/> Veuillez vérifier les informations ...</p>
						<table id="inscription">
							<tr>
								<th style="text-align:left;">login</th><td><input type="text" name="login" value="'.$_POST['login'].'"';
		$html.=($error_login)?' style="border:1px solid red"/><br/><em style="color:red">Login vide ou déjà utilisé</em>':'"/>';
		$html.='</td>
							</tr>
							<tr>
								<th style="text-align:left;">Nom réel</th><td><input type="text" name="nom" value="'.$_POST['nom'].'"/></td>
							</tr>
							<tr>
								<th style="text-align:left;">email (valide)</th><td><input type="text" name="email" value="'.$_POST['email'].'"/>
								</td>
							</tr>
							<tr>
								<th style="text-align:left;">confirmer l\'adresse email</th><td><input type="text" name="confirm_email" value="'.$_POST['confirm_email'].'"';
		$html.=($error_email)?' style="border:1px solid red"/><br/><em style="color:red">Email vide ou non concordant</em>':'/>';
		$html.='</td>
							</tr>
							<tr>
								<th style="text-align:left;">Recevoir les news par email</th><td><input type="checkbox" name="news" checked="checked"/></td>
							</tr>
						</table>
						<p><input type="submit" value="Effectuer une demande d\'inscription"/></p>
						</form>
						</div>';
	} else {
		// si on a tout valide on insère une entrée dans la table users, on mail la personne pour qu'il confirme
		// et on balance un mail à l'admin
		$token=md5(date('Y-m-d h:i:s-salted'));
		$s_insert="INSERT INTO users (`date_in`,`login`,`nom_reel`,`email`,`token`,`news`) VALUE (CURDATE(),'"
					.secure_mysql($_POST['login'])."','".secure_mysql($_POST['nom'])."','"
					.secure_mysql($_POST['email'])."','".$token."','".secure_mysql($_POST['news'])."')";
		mysqli_query($db_pronos, $s_insert)
		or die('Impossible de créer l\'utilisateur <br/>'.$s_insert.'<br/>'.mysqli_error($db_pronos));

		// Envoi du mail de confirmation
		$message='Bonjour '.htmlentities($_POST['nom']).'.<br/><br/>

						Quelqu\'un (probablement vous) a utilisé votre adresse email pour s\'inscrire sur le site de pronostics avec
						le login : <br/>
						'.$_POST['login'].'
						<br/><br/>
						Pour confirmer votre inscription, choisir votre mot de passe et soummettre vos pronostics, il vous suffit de cliquer sur le le lien suivant :<br/>
						<a href="http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'&token='.$token.'">
						http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI'].'&token='.$token.'</a>
						<br/><br/>
						Cordialement
						<br/>
						Le webmaster du site de pronostiques ..
							';


		sendmail($_POST['email'],'Activation de votre compte',$message);


		$html.='<p class="box">Un compte a été créé sur le site du concours. Pour l\'activer, veuillez suivre 
				le lien que vous allez recevoir par email d\'ici quelques minutes.
				<br/>
				<img height="10px" alt="Attention :" src="public/images/icons/danger.png"> 
				ATTENTION, il est fort possible qu\'il finisse en SPAM .. <br/>
				Une fois votre mot de passe choisi, vous pourrez soumettre vos pronostiques. Bonne chance !!</p>';
	}
}
?>