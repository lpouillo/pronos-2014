<?php
$html='';
$from=$admin_email;


// Les inscriptions sont ouvertes tant que le tournoi n'a pas démarré.
if (time()<$timestamp_poules_debut) {
	// Si aucun utilisateur n'est connecté, on regarde ce qui est passé comme variable dans l'url :
	if (empty($_SESSION['id_user'])) {
		// si pas de token, on affiche le formulaire
		if (empty($_GET['token']) and empty($_POST['token'])) {
			// Si pas de token d'inscription, on affiche le formulaire et on enregistre la demande de compte
			if (empty($_POST['inscription_soumise'])) {
				// Si l'inscription n'a pas été soumise, on affiche le formulaire
				$html.='
					<div style="width:600px;margin:auto;">
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
					<p style="text-align:center"><input type="submit" value="Effectuer une demande d\'inscription"/></p>
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
						<div style="width:500px;margin:auto;">
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
					$token=md5(date('Y-m-d h:i:s'));
					$s_insert="INSERT INTO users (`date_in`,`login`,`nom_reel`,`email`,`token`,`news`) VALUE (CURDATE(),'".$_POST['login']."','".$_POST['nom']."','".$_POST['email']."','".$token."','".$_POST['news']."')";
					mysqli_query($db_pronos, $s_insert)
						or die('Impossible de créer l\'utilisateur <br/>'.$s_insert.'<br/>'.mysql_error());

					// Envoi du mail de confirmation
					$message='Bonjour '.htmlentities($_POST['nom']).'.<br/><br/>

						Quelqu\'un (probablement vous) a utilisé votre adresse email pour s\'inscrire sur le site de pronostics de l\'association Hekla avec
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


					$html.='<p>Un compte a été créé sur le site du concours. Pour l\'activer, veuillez suivre le lien que vous allez recevoir par email d\'ici quelques minutes.
						ATTENTION, il est fort possible qu\'il finisse en SPAM .. <br/>
						Une fois votre mot de passe choisi, vous pourrez soumettre vos pronostiques. Bonne chance !!</p>';
				}
			}
		} else {
			// si il y a un token fourni et qu'il vaut new
			if ($_GET['token']=='new' or $_POST['token']=='new') {
				$s_check_user="SELECT email FROM users WHERE login='".$_POST['login']."' AND email='".$_POST['email']."'";
				$r_check_user=mysqli_query($db_pronos, $s_check_user);
				if (mysqli_num_rows($r_check_user)) {
					$token=md5(date('Y-m-d h:i:s'));
					$s_update="UPDATE users SET token='".$token."' WHERE login='".$_POST['login']."' AND email='".$_POST['email']."'";
					mysqli_query($db_pronos, $s_update)
						or die('Impossible de recréer un token <br/>'.$s_update.'<br/>'.mysql_error());
					// Envoi du mail de confirmation
					$headers ='From: "Pronos 2014" <'.$admin_email.">\n".
						'Bcc: "Pronos 2014" <'.$admin_email.">\n";
					$headers .='Content-Type: text/html; charset="utf-8"'."\n";
					$headers .='Content-Transfer-Encoding: 8bit';

					$uri=explode('&',$_SERVER['REQUEST_URI']);
					$message='Bonjour '.htmlentities($_POST['nom']).'.<br/><br/>

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

					$html.='<p>Un lien pour réinitialiser votre mot de passe vous a été envoyé.</p>';
				} else {
					if (isset($_POST['login'])) {
						$aucun='Aucun compte correspondant aux informations soumises n\'a été trouvé.';
					} else {
						$aucun='Veuillez renseignez les informations suivantes';
					}
					$html.='
							<div style="width:500px;margin:auto;">
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
			} else {
				$token=$_GET['token'];
				$s_check_token="SELECT token FROM users WHERE token='".$token."'";
				$r_check_token=mysqli_query($db_pronos, $s_check_token);
				if (mysqli_num_rows($r_check_token)) {
					if (empty($_POST['activation'])) {
						$html.='
							<div style="width:500px;margin:auto;">
							<h3>Confirmation de votre inscription au concours</h3>
							<p>Veuillez choisir un mot de passe</p>
							<form id="choix_mot_passe" method="post" action="#">
							<p>
							<input type="hidden" name="page" value="inscription"/>
							<input type="hidden" name="activation" value="oui"/>
							<input type="hidden" name="token" value="'.$token.'"/>
							</p>
								<table id="inscription">
									<tr>
										<th>Mot de passe</th><td><input type="password" name="password" id="password"/></td>
									</tr>
									<tr>
										<th>Confirmez</th><td><input type="password" name="password2" id="password2"/></td>
									</tr>
								</table>
							<p><input type="submit" value="Activer mon compte"/></p>
							</form>
							</div>';
					} else {
						$token=$_POST['token'];

						if ($_POST['password']!=$_POST['password2'] or empty($_POST['password'])) {
							$html.='<h3>Confirmation de votre inscription au concours de pronostiques</h3>
								<div style="width:500px;margin:auto;">
								<p><img src="public/images/icons/danger.png" alt="danger"/> Les deux mots de passe ne sont pas identiques</p>
								<form id="choix_mot_passe" method="post" action="#">
								<p>
								<input type="hidden" name="page" value="inscription"/>
								<input type="hidden" name="activation" value="oui"/>
								<input type="hidden" name="token" value="'.$token.'"/>
								</p>
									<table id="inscription">
										<tr>
											<th>Mot de passe</th><td><input type="password" name="password" id="password"/></td>
										</tr>
										<tr>
											<th>Confirmez</th><td><input type="password" name="password2" id="password2"/></td>
										</tr>
									</table>
								</form>
								<p style="text-align:left;"><input type="submit" value="Activer mon compte"/></p>
								</div>';
						} else {
							$s_user="SELECT id_user,login,nom_reel, email, classement FROM users WHERE token='".$token."'";
							$r_user=mysqli_query($db_pronos, $s_user);
							$d_user=mysqli_fetch_array($r_user);
							$_SESSION['id_user']=htmlentities($d_user['id_user']);
							$_SESSION['login']=htmlentities($d_user['login']);
							$_SESSION['nom_reel']=htmlentities($d_user['nom_reel']);
							$_SESSION['email']=htmlentities($d_user['email']);
							$_SESSION['is_admin']=$d_user['is_admin'];
							$_SESSION['classement']=$d_user['classement'];
							$_SESSION['points']=$d_user['points'];
							$s_update="UPDATE users SET `password`='".md5($_POST['password'])."', token='', date_recup=CURDATE(), actif=1, classement=10000 WHERE token='".$token."'";
							mysqli_query($db_pronos, $s_update)
								or die(mysql_error());
							$html.='<div>Votre compte a été activé. <a href="index.php?page=mon_espace#mes_pronos">Accéder à mes pronostiques</a></div>';

						}
					}
				} else {
					$html.='
						<div style="width:500px;margin:auto;">
						<p>Le token d\'inscription n\'est pas valide. Recommencez votre inscription :</p>
						<form method="post" action="#" id="frm_inscription">
						<p>
						<input type="hidden" name="page" value="inscription"/>
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
						</table>
						<p style="text-align:left;"><input type="submit" value="Effectuer une demande d\'inscription"/></p>
						</form>
						</div>';
				}
			}
		}
	} else {
		$html.='Vous etes deja inscrit sur le site. N\'hesitez pas a partager ce site avec vos amis.';
	}
} else {
	$html.='Les inscriptions sont terminees.';
}
echo $html;
?>
