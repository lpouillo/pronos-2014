<?php 
$token=secure_mysql($_GET['token']);
$s_check_token="SELECT token FROM users WHERE token='".$token."'";
$r_check_token=mysqli_query($db_pronos, $s_check_token);
if (!mysqli_num_rows($r_check_token)) {
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
} else {
	if (empty($_POST['activation'])) {
		$html.='
			<div class="box">
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
		
		$token=secure_mysql($_POST['token']);
		
		if ($_POST['password']!=$_POST['password2'] or empty($_POST['password'])) {
			$html.='<h3>Confirmation de votre inscription au concours de pronostiques</h3>
				<div class="box">
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
			$s_update="UPDATE users SET `password`='".md5($_POST['password'])."', token='', ".
				"date_recup=CURDATE(), actif=1, classement=10000 WHERE token='".$token."'";
			mysqli_query($db_pronos, $s_update)
				or die(mysqli_error($db_pronos));
			$html.='<div class="box">Votre compte a été activé. 
					<a href="index.php?page=mon_espace#mes_pronos">Accéder à mes pronostiques</a>
					</div>';
		}
	}
}

?>