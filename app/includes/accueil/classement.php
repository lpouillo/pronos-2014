<?php
$html='<div id="div_acc_classement">';
// Recuperation des parieurs
$parieurs=array();
$s_parieurs="SELECT id_user, login, nom_reel, date_in, classement, points
			FROM users
			WHERE actif=1
			ORDER BY classement, login, date_in DESC, login";
$r_parieurs=mysqli_query($db_pronos, $s_parieurs);
while($d_parieurs=mysqli_fetch_array($r_parieurs)) {
	$parieurs[]=$d_parieurs;
}

// Recuperation des groupes
$groupes=array();
$s_groupes="SELECT G.id_groupe, G.nom, G.description, G.classement, G.moyenne, G.n_user, U.login
			FROM groupes G
			INNER JOIN users U
				ON G.id_owner=U.id_user
			WHERE G.actif=1
			ORDER BY moyenne, n_user";
if ($timestamp_poules_debut-time()<0) {
	$s_groupes.=' LIMIT 10';
}
$r_groupes=mysqli_query($db_pronos, $s_groupes);
while($d_groupes=mysqli_fetch_array($r_groupes)) {
	$groupes[]=$d_groupes;
}

// On met un bloc different si le tournoi a commence ou pas
switch(abs($timestamp_poules_debut-time())-($timestamp_poules_debut-time())){
	case 0:
		// Le tournoi a pas commence, on affiche un message et la liste des parieurs et des groupes
		$html.='
		<p>Le tournoi ne démarre que le '.strftime('%A %d %B',$timestamp_poules_debut).'.
		En attendant vous pouvez toujours modifier vos pronostics et
		consulter la liste des parieurs et des groupes.</p>';

		$html .='<h3>Liste des parieurs</h3>
				<p>';
		if (count($parieurs)>0) {
			foreach($parieurs as $parieur) {
				$color=(isset($_SESSION['id_user']) and $parieur['id_user']==$_SESSION['id_user'])?'style="color:red"':'';
				$html.='<span title="'.$parieur['nom_reel'].'" '.$color.'>'.
					htmlentities($parieur['login'],ENT_QUOTES,'UTF-8').'</span>, ';
			}
			$html=substr($html, 0, -2);
		} else{
			$html.='<p>Aucun utilisateur actif.</p>';
		}
		$html.='<p>';

		$html.='<h3>Liste des groupes</h3>'.
				'<p>';
		if (count($groupes)>0) {
			foreach($groupes as $groupe) {
				$html.='<span title="'.$groupe['description'].' - géré par '.$groupe['login'].'"
					><a href="index.php?page=concours&section=groupes">'.htmlentities($groupe['nom'],ENT_QUOTES,'UTF-8').'</a>, ';
			}
			$html=substr($html, 0, -2);
		} else{
			$html.='<p>Aucun groupe actif.</p>';
		}
		$html.='</p>';
		if (isset($_SESSION['id_user'])) {
			$html.='<div class="bouton" style="text-align:center;margin-bottom:10px;">
				<a href="index.php?page=mon_espace&section=mes_groupes&action=ajouter">
				Créer un groupe de parieurs !</a>
			</div>';
		}
	break;
	default:
		// Le tournoi a commence, on affiche les 15 premiers du classement
		$html.='<h3>Les 15 premiers du classement</h3>';
		if (count($parieurs)==0) {
			$html.='<p>Aucun utilisateur actif.</p>';
		} else {
			$html.='<ul>';
			$i_max=min(15,count($parieurs));
			for ($i=0;$i<$i_max;$i++)  {
				$puce=get_puce($parieurs[$i]['classement']);
				$html.='<li>
							<span style="padding-right:10px;text-align:center">'.$puce.'</span>
							<span>
								<a style="text-decoration:none;"
									href="index.php?page=concours&section=parieurs&id='.
								$parieurs[$i]['id_user'].'">'.
								htmlentities($parieurs[$i]['login'],ENT_QUOTES,'UTF-8').
								'</a>' .
							'</span>
							<span width="20px">
								<img border="0" src="public/images/icons/arrow_right.png"
								height="10px" alt="evolution"/> '
							.$parieurs[$i]['points'].'</span>
						</li>';


			}
			$html.='</ul>
					<div style="margin:auto;margin-bottom:20px;width:250px;">
						<a href="index.php?page=concours" class="button">Le classement complet</a>
					</div>';
		}
		// on liste les groupes existant
		$html.='<h3>Classement par groupes</h3>';
		if (count($groupes)==0) {
			$html.='<p style="text-align:center;">Aucun groupe actif.</p>';
		} else {
			$html.='<ul>';
			foreach ($groupes as $groupe) {
				$html.='<li>
							<span style="padding-right:10px;text-align:center;">'.
							get_puce($groupe['classement']).'</span>
							<span>
								<a style="text-decoration:none;" href="index.php?page=concours&section=groupe&id='.
									$groupe['id_groupe'].'">'.
									htmlentities($groupe['nom'],ENT_QUOTES,'UTF-8').
								'</a>
							</span>
							<td><img height="12px" src="public/images/icons/user_green.png" alt="user" />'.$groupe['n_user'].'</td>
							<td style="text-align:right">'. round($groupe['moyenne'],2).'</td>

						</tr>';
			}
			$html.='</table>';

		}
}
$html.='</div>';
echo $html;
?>