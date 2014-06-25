<?php
/*
 * Created on 22 juin 2014
 *
 */
$s_parieurs="SELECT id_user, login, nom_reel, classement, points, date_in
		FROM users
		WHERE actif=1
		ORDER BY classement, date_in DESC, login";
$r_parieurs=mysqli_query($db_pronos, $s_parieurs);
$class=0;
$cols_parieurs=1;
$count_parieurs=0;
$count_date=0;
$old_date_in='';
$old_classement=0;

if (mysqli_num_rows($r_parieurs)) {
	$html_parieurs='<div class="8u">' .
		'<div class="8u" style="margin:auto;">'.
		'<a class="button" href="#cestmoi">Accédez à mon classement</a>'.
		'</div>';
	while ($d_parieurs=mysqli_fetch_array($r_parieurs)) {

		$count_parieurs++;
		$cestmoi=(isset($_SESSION['id_user']) and $_SESSION['id_user']==$d_parieurs['id_user'])?
			'<strong id="cestmoi">'.htmlentities($d_parieurs['login'],ENT_QUOTES,'UTF-8').'</strong>':
			htmlentities($d_parieurs['login'],ENT_QUOTES,'UTF-8');
		if (time()<$timestamp_poules_debut) {
			if ($d_parieurs['date_in']!=$old_date_in and $d_parieurs['date_in']!='') {
				$html_parieurs.='<li class="date" style="color:#00774B;display:inline;">
				'.dateMysqlToFormatted($d_parieurs['date_in'], '00:00:00', '%A %d %B').'</li>';
				$count_parieurs++;
				$count_date++;
			}
			$html_parieurs .= '<span title="'.$d_parieurs['nom_reel'].'" ' .
					'style="margin-left:20px;display:inline;">'.
					$cestmoi.'</span>';
		} else {

			if ($old_classement<$d_parieurs['classement'] and $old_classement != 0) {
				$html_parieurs .= '('.$d_parieurs['points'].')</div>';
			}
			$style = (($d_parieurs['classement']) % 10 == 0)?'color:#00774B;font-weight:bold;':
				'';
			if ($old_classement<$d_parieurs['classement']) {
				$html_parieurs.='<div id="ligne_concours" style="'.$style.'">'.
					'<span style="width:50px;">'.get_puce($d_parieurs['classement']).
					'</span>';
			}
			$html_parieurs .='<span title="'.$d_parieurs['nom_reel'].'" ' .
					'style="color:black;font-weight:normal;padding:20px;">'.
					$cestmoi.' </span>';

		}


		$old_classement=$d_parieurs['classement'];
	}
	$html_parieurs.='</div></div>';
} else {
	$html_parieurs='<p>Il n\'y a aucun utilisateur actif.</p>';
}
?>
