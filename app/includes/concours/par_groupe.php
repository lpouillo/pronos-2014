<?php
/*
 * Created on 24 juin 2014
 *
 */
$s_user="SELECT G.nom, U.id_user, U.login, U.points, U.classement,
		IF(G.id_owner=U.id_user, 'owner', '') as owner
		FROM users U
		INNER JOIN l_users_groupes UG
			ON U.id_user=UG.id_user
		INNER JOIN groupes G
			ON UG.id_groupe=G.id_groupe
		WHERE G.id_groupe='".secure_mysql($_GET['id'])."'
			AND UG.actif=1
		ORDER BY U.classement, U.login";

$r_user=mysqli_query($db_pronos, $s_user)
	or die(mysqli_error());
$html='<h2>Classement du groupe';


$close_h2=true;
while ($d_user=mysqli_fetch_array($r_user)) {
	if ($close_h2)
		$html.=' '.$d_user['nom'].'</h2><ul>';
		$close_h2=false;
	$puce=get_puce($d_user['classement']);
	$cestmoi=(isset($_SESSION['id_user']) and $_SESSION['id_user']==$d_user['id_user'])?
			'<strong id="cestmoi">'.htmlentities($d_user['login'],ENT_QUOTES,'UTF-8').'</strong>':
			htmlentities($d_user['login'],ENT_QUOTES,'UTF-8');

	$html.='<li>'.$puce.' '.
		$d_user['login'].' '.$d_user['points'].' points';
	$html.='</li>';
}
$html.='</ul>';
?>
