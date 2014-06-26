<?php
/*
 * Created on 26 juin 2014
 *
 */
$html='<div class="row"><div class="6u box">
		<h2>Classement en relief</h2>';
$html.=(isset($_SESSION['id_user']))?
			'<div class="8u" style="margin:auto;">'.
			'<a class="button" href="#cestmoi">Accédez à mon classement</a>'.
			'</div>':'';
$s_parieurs="SELECT id_user, login, nom_reel, classement, points, date_in
		FROM users
		WHERE actif=1
		ORDER BY classement, date_in DESC, login";
$r_parieurs=mysqli_query($db_pronos, $s_parieurs);
if (mysqli_num_rows($r_parieurs)) {

	$parieurs = array();
	while ($d_parieurs=mysqli_fetch_array($r_parieurs)) {
		if (!array_key_exists($d_parieurs['points'], $parieurs)) {
			$parieurs[$d_parieurs['points']] = array();
		}
		$parieurs[$d_parieurs['points']][] = $d_parieurs;
	}

	for ($points = min(array_keys($parieurs));$points<max(array_keys($parieurs));$points++) {
		$html.='<div id="ligne_concours">
			<span style="width:20px;">'.$points.'</span>';
		if (array_key_exists($points, $parieurs)) {
			$list_parieurs= '';
			foreach ($parieurs[$points] as $parieur) {
				$cestmoi=(isset($_SESSION['id_user']) and $_SESSION['id_user']==$parieur['id_user'])?
				'<strong id="cestmoi">'.htmlentities($parieur['login'],ENT_QUOTES,'UTF-8').'</strong>':
				htmlentities($parieur['login'],ENT_QUOTES,'UTF-8');
				$list_parieurs .= '<span title="'.$parieur['nom_reel'].'" ' .
						'style="margin-left:20px;display:inline;">'.
						$cestmoi.'</span>';
			}
			$html.='<span style="margin-left:20px;color:#00774B;font-weight:bold;">'.
					get_puce($parieur['classement']).'</span> '.$list_parieurs;

		}
		$html.='</div>';
	}
} else {
	$html='<p>Il n\'y a aucun utilisateur actif.</p>';
}

$s_groupes="SELECT G.id_groupe, G.nom, G.description, G.classement, U.login FROM groupes G
	INNER JOIN users U
		ON G.id_owner=U.id_user
	WHERE G.actif=1
	ORDER BY G.classement, G.nom";
$r_groupes=mysqli_query($db_pronos, $s_groupes);


$html.='</div>

		<div class="6u box">
		<h2>Classement des groupes</h2>';
$html.='</div></div>';


?>
