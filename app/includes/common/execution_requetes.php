<?php
foreach ($_POST as &$var) {
	$var=secure_mysql($var);
}
if (isset($_SESSION['id_user']) and isset($_POST['requete'])) {
	switch ($_POST['requete']) {
		case 'update_pronos':
			$s_update="REPLACE INTO pronos (`id_match`,`id_user`,`score1`,`score2`,`tab1`,`tab2`) VALUES ";
			foreach($_POST['pronos'] as $id_match => $scores) {
				 $s_update.="('".$id_match."','".$_SESSION['id_user']."','".$scores['score1']."','".$scores['score2']."','".$scores['tab1']."','".$scores['tab2']."'), ";
			}
			mysql_query(substr($s_update,0,-2));
		break;
		case 'ajouter_equipe':
			$s_insert="INSERT INTO equipes (`date_in`,`date_modif`,`nom`,`acronym`,`poule`) 
				VALUES (CURDATE(),CURDATE(),'".$_POST['nom']."','".$_POST['acronym']."','".$_POST['poule']."')";
			$r_insert=mysql_query($s_insert)
				or die($s_insert.'<br/>'.mysql_error());
			$_POST['action']='modifier';
			$_POST['id']=mysql_insert_id();
		break;
		case 'modifier_equipe':
			$s_update="UPDATE equipes SET `date_modif`=CURDATE(),`nom`='".$_POST['nom']."',`acronym`='".$_POST['acronym']."',
			`poule`='".$_POST['poule']."' WHERE id_equipe='".$_POST['id_equipe']."'";
			$r_update=mysql_query($s_update)
				or die($s_update.'<br/>'.mysql_error());
			$_POST['action']='';
		break;
		case 'ajouter_match':
			$s_insert="INSERT INTO matchs (`date_in`,`date_modif`,`id_equipe1`,`id_equipe2`,
				`date_match`,`heure`,`score1`,`score2`,
				`tab1`,`tab2`,`joue`,`type`,`poule`,`special`) 
				VALUES (CURDATE(),CURDATE(),'".$_POST['id_equipe1']."','".$_POST['id_equipe2']."',
				'".$_POST['date_match']."','".$_POST['heure']."','".$_POST['score1']."','".$_POST['score2']."',
				'".$_POST['tab1']."','".$_POST['tab2']."','".$_POST['joue']."','".$_POST['type']."',
				'".$_POST['poule']."','".$_POST['special']."')";
			$r_insert=mysql_query($s_insert)
				or die($s_insert.'<br/>'.mysql_error());
			$_POST['action']='modifier';
			$_POST['id']=mysql_insert_id();
		break;
		case 'modifier_match':
			$s_update="REPLACE INTO matchs (`id_match`,`date_in`,`date_modif`,`id_equipe1`,`id_equipe2`,
				`date_match`,`heure`,`score1`,`score2`,
				`tab1`,`tab2`,`joue`,`type`,`poule`,`special`) 
				VALUES ('".$_POST['id_match']."',CURDATE(),CURDATE(),'".$_POST['id_equipe1']."','".$_POST['id_equipe2']."',
				'".$_POST['date_match']."','".$_POST['heure']."','".$_POST['score1']."','".$_POST['score2']."',
				'".$_POST['tab1']."','".$_POST['tab2']."','".$_POST['joue']."','".$_POST['type']."',
				'".$_POST['poule']."','".$_POST['special']."')";;
			$r_update=mysql_query($s_update)
				or die($s_update.'<br/>'.mysql_error());
			$_POST['action']='';
		break;
		case 'ajouter_user':
			$s_insert="INSERT INTO users (`date_in`,`date_modif`,`login`,`nom_reel`,`actif`) 
				VALUES (CURDATE(),CURDATE(),'".$_POST['login']."','".$_POST['nom_reel']."','".$_POST['actif']."')";
			$r_insert=mysql_query($s_insert)
				or die($s_insert.'<br/>'.mysql_error());
			$_POST['action']='modifier';
			$_POST['id']=mysql_insert_id();
		break;
		case 'modifier_user':
			$s_update="UPDATE users SET `date_modif`=CURDATE(),`login`='".$_POST['login']."',`nom_reel`='".$_POST['nom_reel']."',
			`actif`='".$_POST['actif']."' WHERE id_user='".$_POST['id_user']."'";
			$r_update=mysql_query($s_update)
				or die($s_update.'<br/>'.mysql_error());
			$_POST['action']='';
		break;
		case 'ajouter_groupe':
			$s_insert="INSERT INTO groupes (`date_in`,`date_modif`,`nom`,`description`,`actif`) 
				VALUES (CURDATE(),CURDATE(),'".$_POST['nom']."','".$_POST['description']."','".$_POST['actif']."')";
			$r_insert=mysql_query($s_insert)
				or die($s_insert.'<br/>'.mysql_error());
			$_POST['action']='modifier';
			$_POST['id']=mysql_insert_id();
		break;
		case 'modifier_groupe':
			$s_update="UPDATE groupes SET `date_modif`=CURDATE(),`nom`='".$_POST['nom']."',`description`='".$_POST['description']."',
			`actif`='".$_POST['actif']."', id_owner='".$_POST['id_owner']."' WHERE id_groupe='".$_POST['id_groupe']."'";
			$r_update=mysql_query($s_update)
				or die($s_update.'<br/>'.mysql_error());
			$_POST['action']='';
			if ($_POST['actif']) {
				$s_email="SELECT login, email FROM users WHERE id_user='".$_POST['id_owner']."'";
				$r_email=mysql_query($s_email);
				$d_email=mysql_fetch_array($r_email);
				
				$headers ='From: "'.$admin_name.'" <'.$admin_email.'>'."\n".'Bcc:"'.$admin_name.'" <'.$admin_email.'>'."\n";
				$headers .='Content-Type: text/html; charset="utf8"'."\n";
				$headers .='Content-Transfer-Encoding: 8bit';  
				mail ($d_email['email'],'[Pronos 2012 IPGP] Le groupe '.$_POST['nom'].' a été activé','Son classement est effectif sur le site.',$headers)
					or die('mail activation groupe non envoyé');
			}
		break;
	}
	
	
}

?>
