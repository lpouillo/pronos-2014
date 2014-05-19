<?php
if (empty($_POST['section'])) {
	/* Par défaut on affiche le classement des parieurs et des groupes */
	// récupération des parieurs 
	$s_parieurs="SELECT id_user, login, nom_reel, classement, points, date_in FROM users WHERE actif=1 ORDER BY classement, date_in DESC, login";
	$r_parieurs=mysql_query($s_parieurs);
	$class=0;
	$cols_parieurs=1;
	$count_parieurs=0;
	$count_date=0;
	$old_date_in='';
	if (mysql_num_rows($r_parieurs)) {
		$html_parieurs='<ul class="reglement" style="margin:0px; padding:0px;">';
		while ($d_parieurs=mysql_fetch_array($r_parieurs)) {
			$count_parieurs++;
			// On met en rouge le login du joueur connecté
			if ($d_parieurs['date_in']!=$old_date_in and $d_parieurs['date_in']!='' and time()<$timestamp_poules_debut) {
				$html_parieurs.='<li style="background-image:none;padding-left:0px;list-style-type:none;padding:px;font-variant: small-caps;font-weight:bold;color:#00774B;";>
					'.dateMysqlToFormatted($d_parieurs['date_in'],'00:00','%A %d %B').'</li>';
				$count_parieurs++;
				$count_date++;	
			}
			$cestmoi=($_SESSION['id_user']==$d_parieurs['id_user'])?'<strong>'.$d_parieurs['login'].'</strong>':$d_parieurs['login'];
			$html_parieurs.='<li title="'.$d_parieurs['nom_reel'].'" style="margin-left:40px;">'.$d_parieurs['classement'].' 
				'.htmlentities($d_parieurs['login'],ENT_QUOTES,'UTF-8').' ('.$d_parieurs['points'].')</li>';
			if ($count_parieurs==round((mysql_num_rows($r_parieurs)+$count_date)/3) or $count_parieurs==2*round((mysql_num_rows($r_parieurs)+$count_date)/3)) {
				$html_parieurs.='</ul></td><td <td style="vertical-align:center;" width="20%"><ul class="reglement">';
				$cols_parieurs++;
			}
			$old_date_in=$d_parieurs['date_in'];
		}
		$html_parieurs.='</ul>';
	} else {
		$html_parieurs='<p>Il n\'y a aucun utilisateur actif.</p>';
	}
	
	// On récupérer les groupes actifs (il faut récupérer le nombre de membres par groupe ..) 
	$s_groupes="SELECT G.id_groupe, G.nom, G.description, U.login FROM groupes G 
		INNER JOIN users U
			ON G.id_owner=U.id_user
		
		WHERE G.actif=1
		ORDER BY G.classement, G.nom";
	// echo $s_groupes;
	$r_groupes=mysql_query($s_groupes);
	$n_groupes=mysql_num_rows($r_groupes);
	
	if (mysql_num_rows($r_groupes)) {
		
		$html_groupe.='<ul class="reglement">';
		while ($d_groupes=mysql_fetch_array($r_groupes)) {
			/*echo '<pre>';
			print_r($d_groupes);
			echo'</pre>';*/
			$html_groupe.='<li title="'.$d_groupes['description'].' - géré par '.htmlentities($d_groupes['login'],ENT_QUOTES,'UTF-8').'"
			onclick="affElement(\'concours\',\'par_groupe\',\''.$d_groupes['id_groupe'].'\',\'\',\'page\');"
			 style="cursor:pointer; font-weight:bold;">'.htmlentities($d_groupes['nom'],ENT_QUOTES,'UTF-8').'</li>';
		}
		$html_groupe.='</ul>';
	} else {
		$html_groupe.='<p style="text-align:center;">Aucun groupe actif.</p>';
	}
	
	$html='<table id="tbl_concours">
			   <tr>
				   <td colspan="'.$cols_parieurs.'"><h2>Classement général du concours</h2></td>
				   <td><h2>Classement par groupes</h2></td>
			   </tr>
			   <tr>
			   <td colspan="'.$cols_parieurs.'">Le championnat d\'Europe n\'a pas encore commencé mais il y a déjà '.mysql_num_rows($r_parieurs).' participants
		au concours.
		 <img src="index.php?page=graphs&type=distribution_generale"/></td>
			   <td>Il y a '.$n_groupes.' groupe(s) d\'utilisateurs. N\'hésitez pas à créer le votre pour faire un mini-concours avec vos amis.</td>
			  </tr><tr>
			   <td style="vertical-align:center;">'.$html_parieurs.'</td>
			   <td>'.$html_groupe.'</td>
	</tr></table>';
} else {
	switch($_POST['section']) {
		case 'par_groupe':
			$s_user="SELECT G.nom, U.login, U.points, U.classement FROM users U 
				INNER JOIN l_users_groupes UG
					ON U.id_user=UG.id_user
				INNER JOIN groupes G
					ON UG.id_groupe=G.id_groupe
				WHERE G.id_groupe='".$_POST['id']."'
					AND UG.actif=1
				ORDER BY U.classement, U.login";
			$r_user=mysql_query($s_user)
				or die(mysql_error());
			$html.='<h2>Classement du groupe </h2><ul>';
			while ($d_user=mysql_fetch_array($r_user)) {
				$html.='<li>'.$d_user['classement'].' - '.$d_user['login'].' '.$d_user['points'].' points</li>';
			}
			$html.='</ul>';
		break;
	}
}
echo $html;