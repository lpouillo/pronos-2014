<?php

if (empty($_GET['action'])) {
	$s_groups="SELECT G.id_groupe, G.nom, G.description, G.id_owner,
			IF(G.actif,'actif','en attente') AS actif, UG.id_user, U.login
			FROM groupes G
			LEFT JOIN l_users_groupes UG
				ON UG.id_groupe=G.id_groupe
			LEFT JOIN users U
				ON U.id_user=G.id_owner
			WHERE (G.actif=1 AND UG.actif=1)
				OR G.id_owner='".$_SESSION['id_user']."'
			ORDER BY G.nom";


	$r_groups=mysqli_query($db_pronos, $s_groups)
		or die(mysqli_error($db_pronos));

	$groupes=array('proprio' => array(),
					'membre' => array(),
					'other' => array());
	while ($d_groups=mysqli_fetch_array($r_groups)) {
		if ($_SESSION['id_user']==$d_groups['id_owner']) {
			$groupes['proprio'][$d_groups['id_groupe']]=array(
					'nom' => $d_groups['nom'],
					'description' => $d_groups['description'],
					'actif' => $d_groups['actif']);
		} elseif ($_SESSION['id_user']==$d_groups['id_user']) {
			$groupes['membre'][$d_groups['id_groupe']]=array(
					'nom' => $d_groups['nom'],
					'manager' => $d_groups['login'],
					'description' => $d_groups['description']);
		} else {
			$groupes['other'][$d_groups['id_groupe']]=array(
					'nom'=> $d_groups['nom'],
					'manager' => $d_groups['login'],
					'description' => $d_groups['description']);
		}
	}

	$html_mine='<div class="4u box">' .
			'<header>' .
			'Les groupes auxquels j\'appartiens' .
			'</header>'.
			'<ul>';
	$html_other='<div class="8u box">' .
			'<header>' .
			'Autres groupes du concours' .
			'</header>'.
			'<ul>';

	foreach($groupes as $role => $groupe) {

		foreach($groupe as $id_groupe => $infos) {
			if ($role == 'proprio') {
				$html_mine.='<li>' .
					'<a href="index.php?page=concours&section=groupe&id='.
						$id_groupe.'"' .'title="'.$infos['description'].'">' .
						'<img src="public/images/icons/modifier.png" alt="modifier"/> '.
						$infos['nom'].'</a></li>';
			} else if ($role == 'membre') {
				$html_mine .= '<li>' .
					'<a href="index.php?page=concours&section=groupe&id='.
						$id_groupe.'" title="'.$infos['description'].'">' .
						'<img src="public/images/icons/voir.png" alt="voir"/> '.
						$infos['nom'].'</a></li>';
			} else {
				$html_other .= '<li style="display: inline; padding-left:5px;">' .
					'<a href="index.php?page=concours&section=groupe&id='
						.$id_groupe.'" title="'.$infos['description'].'">' .
						'<img src="public/images/icons/voir.png" alt="voir"/> '.
						$infos['nom'].' ('.$infos['manager'].')</a></li>';
			}

		}
	}

	$html_mine .= '</ul><div style="text-align:center">
		<a class="button" href="index.php?page=mon_espace&section=mes_groupes&action=ajouter">' .
				'Créer un nouveau groupe</a>' .
			'</div></div>';
	$html_other .= '</ul></div>';

	$html .= '<div class="12u" id="mes_groupes">' .
			'<header>
	<h2>Mes groupes</h2>' .
	'</header><div class="row">' .
			$html_mine .
			$html_other.

	'</div></div>';
} else {
	switch($_GET['action']) {
		case 'activer':
			$tmp_id=explode('_',$_GET['id']);
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
						or die(mysqli_error($db_pronos));
					if (mysqli_num_rows($r_groupes)) {
						$error='<span class="special">CE NOM EXISTE DÉJA</span>';
					}
				}

			} else {
				$error='lesieur';
			}
			if ($error!='') {
				$html.='<h2>Ajouter un groupe</h2>

				<form id="ajouter_groupe" method="post" action="#">' .
						'<input type="submit" value="Valider" onclick="submitForm(\'ajouter_groupe\');"/>
				<input type="hidden" name="page" value="mon_espace"/>
				<input type="hidden" name="section" value="mes_groupes"/>
				<input type="hidden" name="action" value="ajouter"/>
				<table>
					<tr>
						<td>Nom du groupe</td><td><input type="text" name="nom" /></td><td >'.$error.'</td>
					</tr>
					<tr>
						<td>Description</td><td colspan="2"><textarea name="description"></textarea></td>
					</tr>
				</table>
				</form>';
			} else {
				$s_insert="INSERT INTO groupes (`date_in`,`date_modif`,`id_owner`,`nom`,`description`)
					VALUES (CURDATE(),CURDATE(),'".$_SESSION['id_user']."','".$_POST['nom']."','".$_POST['description']."')";

				$r_insert=mysqli_query($db_pronos, $s_insert);
				$id_groupe_last=mysqli_insert_id($db_pronos);
				$s_user_group="INSERT INTO l_users_groupes (`date_in`,`date_modif`,`id_user`,`id_groupe`,`actif`)
					VALUES (CURDATE(),CURDATE(),'".$_SESSION['id_user']."','".$id_groupe_last."',1)";


				$r_user_group=mysqli_query($db_pronos, $s_user_group)
					or die('impossible d\'ajouter le proprio au groupe'.mysqli_error($db_pronos));

				$html.='<p>Votre groupe a été créé. Veuillez attendre la validation par le webmaster du site</p>';
				sendmail($admin_email,'Nouveau groupe créé par '.$_SESSION['login'],
						'Un nouveau groupe a été créé sous le nom de '.htmlentities($_POST['nom']));
			}
		break;
		case 'rejoindre':
			$s_groupe="SELECT G.nom, U.login, U.email FROM groupes G
				INNER JOIN users U
					ON U.id_user=G.id_owner
				WHERE G.id_groupe='".$_GET['id']."'";

			$r_groupe=mysqli_query($db_pronos, $s_groupe);
			$d_groupe=mysqli_fetch_array($r_groupe);

			$message=$_SESSION['login'].' ('.$_SESSION['nom_reel'].')a demandé à rejoindre le groupe '.$d_groupe['nom'].'. Connectez vous sur votre espace pour
			valider ou refuser son inscription.<br/><br/>
			<br/><br/>
			Le webmaster du site de pronostiques';
			sendmail($d_groupe['email'],'Demande d\'adhésion au groupe '.$d_groupe['nom'].' par '.$_SESSION['login'],$message);
			$html.='<p>Une demande d\'adhésion au groupe '.$d_groupe['nom'].' a été effectuée auprès de '.$d_groupe['login'].'</p>';
			$s_insert="REPLACE INTO l_users_groupes (`id_user`,`id_groupe`,`date_in`,`date_modif`)
				VALUES ('".$_SESSION['id_user']."','".$_GET['id']."',CURDATE(),CURDATE())";
			$r_insert=mysqli_query($db_pronos, $s_insert)
				or die(mysqli_error($db_pronos));
		break;
		case 'modifier':
		case 'voir':
			$s_groupe="SELECT G.nom, G.id_owner, U.id_user, U.login, U.nom_reel, UG.actif
				FROM groupes G
				INNER JOIN l_users_groupes UG
					ON G.id_groupe=UG.id_groupe
				INNER JOIN users U
					ON U.id_user=UG.id_user
				WHERE G.id_groupe='".$_GET['id']."'";
			$r_groupe=mysqli_query($db_pronos, $s_groupe);
			$html.='<table id="tbl_groupe">';
			$html_ligne = '';
			while ($d_groupe=mysqli_fetch_array($r_groupe)) {
				$nom_groupe=$d_groupe['nom'];
				$activer=($d_groupe['actif'])?'':'<a href="index.php?page=mon_espace&section=mes_groupes' .
						'&action=activer&id='.$_GET['id'].'_'.$d_groupe['id_user'].'">' .
								'<img src="public/images/icons/user_add.png" alt="activer" title="ajouter l\'utilisateur à ce groupe"/>' .
								'</a>';
				$html_ligne.='<tr>';
				if ($d_groupe['id_owner']==$_SESSION['id_user']) {
					$html_ligne.=($_GET['action']=='modifier')?'<td>'.$activer.'<a href="index.php?page=mon_espace&section=mes_groupes' .
						'&action=supprimer&id='.$_GET['id'].'_'.$d_groupe['id_user'].'"> <img src="public/images/icons/user_delete.png" alt="supprimer"/>' .
								'<a/></td>':'';
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
			$tmp_id=explode('_',$_GET['id']);

			$s_del_user="DELETE FROM l_users_groupes WHERE id_user=".$tmp_id[1]." and id_groupe=".$tmp_id[0];
			$r_del_user=mysqli_query($db_pronos, $s_del_user)
				or die(mysqli_error($db_pronos));
			$html.='<p>L\'utilisateur a été supprimé de votre groupe</p>';
		break;
	}
}

?>

