<?php
/*
 * Created on 26 juin 2014
 *
 */

/*$s_user="SELECT U.login, U.points
		FROM users U
		WHERE id_user='".$_GET['id']."'";

$r_user=mysqli_query($db_pronos, $s_user)
	or die(mysqli_error($db_pronos));

if (mysqli_num_rows($r_user)>0) {
	$d = mysqli_fetch_array($r_user);

	$html.='<div class="row">
				<div class="2u">';
	$html.=($_GET['id']>1)?'<a class="button" href="index.php?page=concours&section=parieurs&id='.
					($_GET['id']-1).'">Parieur précédent</a>':'';
	$html.='	</div>
				<div class="8u">'.aff_parieur($d).'</div>
				<div class="2u">';
	$html.=($_GET['id']<68)?'<a class="button" href="index.php?page=concours&section=parieurs&id='.
					($_GET['id']+1).'">Parieur suivant </a>':'';
	$html.='	</div>
			</div>';
	$s_parieurs ="SELECT U.login, U.id_user, P.points
			FROM ";
} else {
	$html.= '<p>USer non trouvé</p>';
}*/

?>
