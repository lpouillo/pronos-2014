<?php
$s_user="SELECT * FROM users WHERE id_user='".$_SESSION['id_user']."'";
$r_user=mysqli_query($db_pronos, $s_user);
$d_user=mysqli_fetch_array($r_user);

$html.='<div class="row">
		<div id="mes_infos" class="6u">
		<h2>Mes informations</h2>';


$checked=($d_user['news'])?'checked="checked"':'';
$html.='<table>
			
			<tr>
				<td rowspan="4" style="vertical-align:bottom"><img width="100px" src="public/images/photos/inconnu.jpg" alt="mon_avatar"/></td>
				<th>id_user</th><td><input type="text" name="id_user" value="'.$d_user['id_user'].'" size=3 readonly/></td>
				<th>Login</th><td><input type="text" name="login" value="'.$d_user['login'].'" size=16 readonly/></td>
				
			</tr>
			<tr>
				<th>Date d\'inscription</th><td><input type="text" name="date_in" value="'.$d_user['date_in'].'" size=8 readonly/></td>
				<th>Nom réel</th><td><input type="text" name="date_in" value="'.$d_user['nom_reel'].'" size=16 /></td>
			</tr>
			<tr>
				<th>Email</th><td colspan="3"><input type="text" name="date_in" value="'.$d_user['email'].'" size=35 /></td>
			</tr>
			<tr>
				<td colspan="4"><input type="checkbox" name="news" '.$checked.'/> Recevoir la newsletter
				(1 mail après chaque journée de poule et chaque tour) </td>
				
			</tr>
	</table></div>';

$html.='<div id="mon_classement" class="6u">
		<h2>Mon classement</h2>';


switch($d_user['classement']) {
	case 1:
		$puce='<img src="public/images/icons/concours.png" alt="'.$d_user['classement'].'">';
		break;
	case 2:
		$puce='<img src="public/images/icons/medal_silver_2.png" alt="'.$d_user[$i]['classement'].'">';
		break;
	case 3:
		$puce='<img src="public/images/icons/medal_bronze_3.png alt="'.$d_user[$i]['classement'].'">';
		break;
	case 10000:
		$puce=' - ';
		break;
}
if ((time()<$timestamp_poules_debut) or ($puce==' - ')) {
	$html.='Le tournoi n\'a pas encore démarré. Rendez vous le '.strftime('%A %d %B &agrave; %H:%m',$timestamp_poules_debut+120*60).'
	pour la première mise à jour du classement.';
} else {
	$html.='Vous êtes '.$puce.' '.$d_user['classement'].' avec '.$d_user['points'].'.';
	$html.=	'<div><img src="index.php?page=graphs&type=evolution_points"/></div>';
}
$html.='</div></div>';


?>
