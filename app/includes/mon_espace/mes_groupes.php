<?php

$html .= '<div class="12u">
<h2>Mes groupes</h2>
<p>La fonctionnalité est en cours de réimplémentation</p>
</div>';
/*
if (empty($_POST['action'])) {
	$s_groups="SELECT G.id_groupe, G.nom, G.description, G.id_owner,  IF(G.actif,'actif','en attente') AS actif, UG.id_user, U.login
				FROM groupes G
				LEFT JOIN l_users_groupes UG
					ON UG.id_groupe=G.id_groupe
				LEFT JOIN users U
					ON U.id_user=G.id_owner
				WHERE (G.actif=1 AND UG.actif=1) OR G.id_owner='".$_SESSION['id_user']."'";

	$r_groups=mysqli_query($db_pronos, $s_groups)
		or die(mysql_error());

	$groupes=array();
	while ($d_groups=mysqli_fetch_array($r_groups)) {
		if ($_SESSION['id_user']==$d_groups['id_owner']) {
			$groupes['proprio'][$d_groups['actif']][$d_groups['id_groupe']]=array(
					'nom'=> $d_groups['nom'],
					'description' => $d_groups['description']);
		} elseif ($_SESSION['id_user']==$d_groups['id_user']) {
			$groupes['membre'][$d_groups['login']][$d_groups['id_groupe']]=array(
					'nom'=> $d_groups['nom'],
					'description' => $d_groups['description'],
					'membre' => $membre);
		} else {
			$groupes['other'][$d_groups['login']][$d_groups['id_groupe']]=array(
					'nom'=> $d_groups['nom'],
					'description' => $d_groups['description'],
					'membre' => $membre);
		}
	}
	$html.='
	<div class="row">
	<table id="tbl_groupe">
		<tr>
			<th colspan="4">Les groupes que je gère</th>
		</tr>';

	if (sizeof($groupes['proprio'])>0) {
		foreach($groupes['proprio'] as $actif => $groupe) {
			foreach($groupe as $id_groupe => $data) {
				$html.='<tr><td style="text-align:center;"><img style="cursor:pointer;" src="public/images/icons/modifier.png" alt="détail" onclick="affElement(\'mon_espace\',\'mes_groupes\',\''.$id_groupe.'\',\'modifier\',\'page\')"/></td>
				<td>'.$data['nom'].'</td><td>'.$data['description'].'</td><td>'.$actif.'</td></tr>';
			}
		}
	}
	$html.='
		<tr>
			<td colspan="4" style="text-align:center">
				<input type="submit" value="Créer un nouveau groupe" onclick="affElement(\'mon_espace\',\'mes_groupes\',\'\',\'ajouter\',\'page\')";/>
			</td>
		</tr>
		<tr>
			<th colspan="4">Les groupes auquel j\'appartient</th>
		</tr>';
	if (sizeof($groupes['membre'])>0) {
		foreach($groupes['membre'] as $login => $groupe) {
			foreach($groupe as $id_groupe => $data) {
				$html.='<tr>
					<td style="text-align:center"><img src="public/images/icons/voir.png" alt="détail" onclick="affElement(\'mon_espace\',\'mes_groupes\',\''.$id_groupe.'\',\'voir\',\'page\')"/></td>
					<td>'.$data['nom'].'</td><td>'.$data['description'].'</td><td>'.$actif.'</td>
				</tr>';


			}
		}
	}
	$html.='<tr>
			<th colspan="4">Les autres groupes</th>
		</tr>';
	if (sizeof($groupes['other'])>0) {
		foreach($groupes['other'] as $login => $groupe) {
			foreach($groupe as $id_groupe => $data) {
				$html.='<tr>
					<td style="text-align:center"><input type="submit" value="Rejoindre"onclick="affElement(\'mon_espace\',\'mes_groupes\',\''.$id_groupe.'\',\'rejoindre\',\'page\')"/></td>
					<td>'.$data['nom'].'</td><td>'.$data['description'].'</td><td>'.$actif.'</td>
				</tr>';


			}
		}
	}
	$html.='</table></div>';
} else {
	switch($_POST['action']) {
		case 'activer':
			$tmp_id=explode('%',$_POST['id']);
			$s_groupe="SELECT id_owner FROM groupes WHERE id_groupe='".$tmp_id[0]."'";
			$r_groupe=mysqli_query($db_pronos, $s_groupe);
			$d_groupe=mysqli_fetch_array($r_groupe);
			if ($d_groupe[0]==$_SESSION['id_user']) {

				$s_active="UPDATE l_users_groupes SET actif=1 WHERE id_groupe='".$tmp_id[0]."' AND id_user='".$tmp_id[1]."'";
				$r_active=mysqli_query($db_pronos, $s_active)
					or die('Erreur lors de l\'activation');
				$html.='<p>L\'utilisateur a été ajouté à votre groupe</p>';
			} else {
				$html.='<p>Vous n\'êtes pas le propriétaire du groupe, ou alors il y a un bug ...</p>';
			}
		break;
		case 'ajouter':
			if (isset ($_POST['nom'])) {
				if ($_POST['nom']=='') {
					$error='<span class="special">NOM VIDE INTERDIT</span>';
				} else {
					$s_groupes="SELECT id_groupe FROM groupes WHERE nom='".$_POST['nom']."'";
					$r_groupes=mysqli_query($db_pronos, $s_groupes)
						or die(mysql_error());
					if (mysql_num_rows($r_groupes)) {
						$error='<span class="special">CE NOM EXISTE DÉJA</span>';
					}
				}
			} else {
				$error='lesieur';
			}
			if ($error!='') {
				$html.='<h2>Ajouter un groupe</h2>
				<input type="submit" value="Valider" onclick="submitForm(\'ajouter_groupe\');"/>
				<form id="ajouter_groupe" method="post" action="#">
				<input type="hidden" name="page" value="mon_espace"/>
				<input type="hidden" name="section" value="mes_groupes"/>
				<input type="hidden" name="action" value="ajouter"/>
				<table>
					<tr>
						<td>Nom du groupe</td><td><input type="text" name="nom" /></td><td >'.$error.'</td>
					</tr>
					<tr>
						<td>Description</td><td colspan="2"><textarea name="decription"></textarea></td>
					</tr>
				</table>
				</form>';
			} else {
				$s_insert="INSERT INTO groupes (`date_in`,`date_modif`,`id_owner`,`nom`,`description`)
					VALUES (CURDATE(),CURDATE(),'".$_SESSION['id_user']."','".$_POST['nom']."','".$_POST['description']."')";
				$r_insert=mysqli_query($db_pronos, $s_insert);
				$id_groupe_last=mysql_insert_id();
				$s_user_group="INSERT INTO l_users_groupes (`date_in`,`date_modif`,`id_user`,`id_groupe`,`actif`)
					VALUES (CURDATE(),CURDATE(),'".$_SESSION['id_user']."','".$id_groupe_last."',1)";


				$r_user_group=mysqli_query($db_pronos, $s_user_group)
					or die('impossible d\'ajouter le proprio au groupe' ) or die(mysql_error());

				$headers ='From: "Pronos 2012 IPGP" <lolo@pouilloux.org>'."\n".'Bcc: "Pronos 2012 IPGP" <lolo@pouilloux.org>'."\n";
				$headers .='Content-Type: text/html; charset="utf8"'."\n";
				$headers .='Content-Transfer-Encoding: 8bit';
				$html.='<p>Votre groupe a été créé. Veuillez attendre la validation par le webmaster du site</p>';
				mail($email_admin,'[Pronos 2012 IPGP] Nouveau groupe créé par '.$_SESSION['login'],'Un nouveau groupe a été créé sous le nom de '.htmlentities($_POST['nom']),$headers);
			}
		break;
		case 'rejoindre':
			$s_groupe="SELECT G.nom, U.login, U.email FROM groupes G
				INNER JOIN users U
					ON U.id_user=G.id_owner
				WHERE G.id_groupe='".$_POST['id']."'";
			$r_groupe=mysqli_query($db_pronos, $s_groupe);
			$d_groupe=mysqli_fetch_array($r_groupe);
			$headers ='From: "Pronos 2012 IPGP" <lolo@pouilloux.org>'."\n".'Bcc:"Pronos 2012 IPGP" <lolo@pouilloux.org>'."\n";
			$headers .='Content-Type: text/html; charset="utf8"'."\n";
			$headers .='Content-Transfer-Encoding: 8bit';
			$message=$_SESSION['login'].' ('.$_SESSION['nom_reel'].')a demandé à rejoindre le groupe '.$d_groupe['nom'].'. Connectez vous sur votre espace pour
			valider ou refuser son inscription<br/><br/>
			<a href="https://hekla.ipgp.fr/pronos2012">https://hekla.ipgp.fr/pronos2012</a>
			<br/><br/>
			Le webmaster du site de pronostiques';
			mail($d_groupe['email'],'[Pronos 2012 IPGP] Demande d\'adhésion au groupe '.$d_groupe['nom'].' par '.$_SESSION['login'],
			$message,$headers)
				or die ('Impossible de demander une adhésion');
			$html.='<p>Une demande d\'adhésion au groupe '.$d_groupe['nom'].' a été effectuée auprès de '.$d_groupe['login'].'</p>';
			$s_insert="REPLACE INTO l_users_groupes (`id_user`,`id_groupe`,`date_in`,`date_modif`)
				VALUES ('".$_SESSION['id_user']."','".$_POST['id']."',CURDATE(),CURDATE())";
			$r_insert=mysqli_query($db_pronos, $s_insert)
				or die(mysql_error());
		break;
		case 'modifier':
		case 'voir':
			$s_groupe="SELECT G.nom, G.id_owner, U.id_user, U.login, U.nom_reel, UG.actif FROM groupes G
				INNER JOIN l_users_groupes UG
					ON G.id_groupe=UG.id_groupe
				INNER JOIN users U
					ON U.id_user=UG.id_user
				WHERE G.id_groupe='".$_POST['id']."'";
			$r_groupe=mysqli_query($db_pronos, $s_groupe);
			$html.='<table id="tbl_groupe">';

			while ($d_groupe=mysqli_fetch_array($r_groupe)) {


				$nom_groupe=$d_groupe['nom'];
				$activer=($d_groupe['actif'])?'':'<img src="public/images/icons/user_add.png" alt="activer" title="ajouter l\'utilisateur à ce groupe"
					onclick="affElement(\'mon_espace\',\'mes_groupes\',\''.$_POST['id'].'%'.$d_groupe['id_user'].'\',\'activer\',\'page\');"/>';
				$html_ligne.='<tr>';
				if ($d_groupe['id_owner']==$_SESSION['id_user']) {
					$html_ligne.=($_POST['action']=='modifier')?'<td>'.$activer.' <img src="public/images/icons/user_delete.png" alt="supprimer"
								onclick="affElement(\'mon_espace\',\'mes_groupes\',\''.$_POST['id'].'%'.$d_groupe['id_user'].'\',\'supprimer\',\'page\');"/></td>':'';
				} else {
					$html_ligne.='<td></td>';
				}
				$html_ligne.='<td>'.$d_groupe['login'].'</td>
						</tr>';
			}
			$html.='<tr>
						<th colspan="2">'.$nom_groupe.'</th>
					</tr>'.$html_ligne.'
				</table>';

		break;

		case 'supprimer':
			echo $_POST['action'];
		break;
	}
}
* */
?>

