<?php
/*
 * Created on 26 juin 2014
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

$s_matchs="SELECT M.id_match, M.date_match, M.heure, " .
		"M.id_equipe1, EQ1.nom AS eq1, EQ1.acronym AS ac1, " .
		"M.id_equipe2, EQ2.nom AS eq2, EQ2.acronym AS ac2,
		M.score1, M.score2, M.tab1, M.tab2, M.type, M.joue, M.special, " .
		"M.cote_1, M.cote_N, M.cote_2
		FROM matchs M
		INNER JOIN equipes EQ1
		ON M.id_equipe1=EQ1.id_equipe
		INNER JOIN equipes EQ2
		ON M.id_equipe2=EQ2.id_equipe
		WHERE id_match='".secure_mysql($_GET['id'])."'";

$r_match=mysqli_query($db_pronos, $s_matchs);


if (mysqli_num_rows($r_match)>0) {
	$d =mysqli_fetch_array($r_match);

	$html.='<div class="row">
				<div class="2u">';
	$html.=($_GET['id']>1)?'<a class="button" href="index.php?page=resultats&section=match&id='.
					($_GET['id']-1).'">Match précédent</a>':'';
	$html.='	</div>
				<div class="8u">'.aff_match($d).'</div>
				<div class="2u">';
	$html.=($_GET['id']<68)?'<a class="button" href="index.php?page=resultats&section=match&id='.
					($_GET['id']+1).'">Match suivant </a>':'';
	$html.='	</div>
			</div>';
	$s_parieurs ="SELECT U.login, U.id_user, P.points
			FROM ";
} else {
	$html.= '<p>Match non trouvé</p>';
}
?>
