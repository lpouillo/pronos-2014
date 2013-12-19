<?php
$html='<div id="div_acc_classement">';
// Recuperation des parieurs
$parieurs=array();
$s_parieurs="SELECT id_user, login, nom_reel, date_in, classement, points 
			FROM users
			WHERE actif=1
			ORDER BY classement, login, date_in DESC, login";
$r_parieurs=mysql_query($s_parieurs);
while($d_parieurs=mysql_fetch_array($r_parieurs)) {
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
$r_groupes=mysql_query($s_groupes);
while($d_groupes=mysql_fetch_array($r_groupes)) {
	$groupes[]=$d_groupes;
}

// On met un bloc different si le tournoi a commence ou pas
switch(abs($timestamp_poules_debut-time())-($timestamp_poules_debut-time())){
	case 0:
		// Le tournoi a pas commence, on affiche un message et la liste des parieurs et des groupes
		$html.='<h3>Le classement</h3>
		<p>Le tournoi ne démarre que le '.strftime('%A %d %B',$timestamp_poules_debut).'.
		Vous recevrez un mail de notification quand le classement sera mis à jour.
		En attendant vous pouvez toujours modifier vos pronostics et 
		consulter la liste des parieurs et des groupes.</p>
		
		<div class="bouton" style="text-align:center">
			<a href="#" onclick="affElement(\'concours\',\'\',\'\',\'\',\'page\');">Accéder au classement complet</a>
		</div>		
		<h3>Liste des parieurs</h3>
		<p>';
		if (count($parieurs)>0) {
			foreach($parieurs as $parieur) {
				$color=($parieur['id_user']==$_SESSION['id_user'])?'style="color:red"':'';
				$html.='<span title="'.$parieur['nom_reel'].'" '.$color.'>'.htmlentities($parieur['login'],ENT_QUOTES,'UTF-8').'</span>, ';
			}
			$html=substr($html, 0, -2);
		} else{
			$html.='<p>Aucun utilisateur actif.</p>';
		}
		$html.='<p>';
		if (empty($_SESSION['id_user'])) {
			$html.='<div class="bouton" style="text-align:center">
				<a href="#" onclick="affElement(\'inscription\',\'\',\'\',\'ajouter\',\'page\');">
				S\'inscrire au concours !</a>
			</div>';
		}
		$html.='<h3>Liste des groupes</h3>  ';
		$html.='<p>';
		foreach($groupes as $groupe) {
			$html.='<span title="'.$groupe['description'].' - géré par '.$groupe['login'].'"
				onclick="affElement(\'concours\',\'par_groupe\',\''.$groupe['id_groupe'].'\',\'\',\'page\');">'.htmlentities($groupe['nom'],ENT_QUOTES,'UTF-8').'</span>, ';
		}
		
		$html=substr($html, 0, -2);
		$html.='</p>';
		if (isset($_SESSION['id_user'])) {
			$html.='<div class="bouton" style="text-align:center;margin-bottom:10px;">
				<a href="#" onclick="affElement(\'mon_espace\',\'mes_groupes\',\'\',\'ajouter\',\'page\');">
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
			$html.='<table width="250px;">';
			$i_max=min(15,count($parieurs));
			for ($i=0;$i<$i_max;$i++)  {
				switch($parieurs[$i]['classement']) {
					case 1:
						$puce='<img height="20px" src="public/images/icons/concours.png" alt="'.$parieurs[$i]['classement'].'"/>';
						
						break;
					case 2:
						$puce='<img height="17px" src="public/images/icons/medal_silver_2.png" alt="'.$parieurs[$i]['classement'].'"/>';
						break;
					case 3:
						$puce='<img height="14px" src="public/images/icons/medal_bronze_3.png" alt="'.$parieurs[$i]['classement'].'"/>';
						break;
					case 10000:
						$puce=' - ';
						break;
					default:
						$puce=$parieurs[$i]['classement'];
		/*$puce='<img height="10px" src="public/images/icons/sport_soccer.png" alt="'.$parieurs[$i]['classement'].'"/>';*/
						
				}
				$html.='<tr>
							<td class="match" width="20px">'.$puce.'</td>
							<td>'.htmlentities($parieurs[$i]['login'],ENT_QUOTES,'UTF-8').'</td>
							<td  width="20px"><img  border="0" src="public/images/icons/arrow_right.png" height="10px" alt="evolution"/></td>
							<td>'.$parieurs[$i]['points'].'</td>
						</tr>';
				/*$html.='<li title="'.$parieurs[$i]['nom_reel'].'" style="width:200px;"><table><tr>
				<td>'.$puce.'</td><td>'.htmlentities($parieurs[$i]['login'],ENT_QUOTES,'UTF-8').'</td>
				<td style="padding-left:50px;text-align:right">'.$parieurs[$i]['points'].'</td> 
				</table></li>';*/
			
			}
			$html.='</table>
					<div class="bouton" style="text-align:center">
						<a href="#" onclick="affElement(\'concours\',\'\',\'\',\'\',\'page\');">Accéder au classement complet</a>
					</div>';
		}
		// on liste les groupes existant
		$html.='<h3>Classement par groupes</h3>';
		if (count($groupes)==0) {
			$html.='<p style="text-align:center;">Aucun groupe actif.</p>';
			
		} else {
			$html.='<table width="250px;">';
			foreach ($groupes as $groupe) {
				switch($groupe['classement']) {
						case 1:
							$puce='<img height="20px" src="public/images/icons/concours.png" alt="'.$groupe['classement'].'"/>';
							
							break;
						case 2:
							$puce='<img height="17px" src="public/images/icons/medal_silver_2.png" alt="'.$groupe['classement'].'"/>';
							break;
						case 3:
							$puce='<img height="14px" src="public/images/icons/medal_bronze_3.png" alt="'.$groupe['classement'].'"/>';
							break;
						case 10000:
							$puce=' - ';
							break;
						default:
							$puce=$groupe['classement'];
			/*$puce='<img height="10px" src="public/images/icons/sport_soccer.png" alt="'.$groupe['classement'].'"/>';*/
							
					}
				$html.='<tr style="cursor:pointer;" onclick="affElement(\'concours\',\'par_groupe\',\''.$groupe['id_groupe'].'\',\'\',\'page\');"  title="'.$groupe['description'].' - géré par '.$groupe['login'].'">
							<td width="20px" style="text-align:center">'.$puce.'</td>
							<td width="150px">'.htmlentities($groupe['nom'],ENT_QUOTES,'UTF-8').'</td>
							<td><img height="12px" src="public/images/icons/user_green.png" alt="user" />'.$groupe['n_user'].'</td>
							<td style="text-align:right">'. round($groupe['moyenne'],2).'</td>
							
						</tr>';
			/*	$html.='<li title="'.$groupe['description'].' - géré par '.$groupe['login'].'"
				onclick="affElement(\'concours\',\'par_groupe\',\''.$groupe['id_groupe'].'\',\'\',\'page\');"
				style="cursor:pointer; font-weight:bold;">'.htmlentities($groupe['nom'],ENT_QUOTES,'UTF-8').' '.$groupe['moyenne'].'</li>';*/
			}
			$html.='</table>';
			
		}
}
$html.='</div>';
echo $html;
?>