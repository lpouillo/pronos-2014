<?php
/*
 * Created on 22 juin 2014
 *
 */
$html_groupe = '';
$s_groupes="SELECT G.id_groupe, G.nom, G.description, G.classement, U.login FROM groupes G
	INNER JOIN users U
		ON G.id_owner=U.id_user
	WHERE G.actif=1
	ORDER BY G.classement, G.nom";
$r_groupes=mysqli_query($db_pronos, $s_groupes);
$n_groupes=mysqli_num_rows($r_groupes);

if (mysqli_num_rows($r_groupes)) {
	$html_groupe.='<ul class="reglement">';
	while ($d_groupes=mysqli_fetch_array($r_groupes)) {
		$puce=get_puce($d_groupes['classement']);
		$html_groupe.='<li title="'.$d_groupes['description'].' - géré par '.
			htmlentities($d_groupes['login'],ENT_QUOTES,'UTF-8').'">
		 	<a href="index.php?page=concours&section=groupe&id='.$d_groupes['id_groupe'].'">'.
		 	$puce.' '.htmlentities($d_groupes['nom'],ENT_QUOTES,'UTF-8').'</a></li>';
	}
	$html_groupe.='</ul>';
} else {
	$html_groupe.='<p style="text-align:center;">Aucun groupe actif.</p>';
}

?>
