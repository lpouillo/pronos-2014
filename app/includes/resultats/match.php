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

	$evol_parieurs=array(
		'+' => array(),
		'=' => array(),
		'-' => array());
	$signs = array('+'=> 'fa-stop', '=' => 'fa-pause', '-' => 'fa-check');
	$s_pronos = "SELECT P.points, U.login, U.nom_reel
			FROM pronos P
			INNER JOIN users U
				ON U.id_user=P.id_user
			WHERE P.id_match=".$_GET['id']."
			ORDER BY P.points";
	$r_pronos=mysqli_query($db_pronos, $s_pronos);

	while ($d=mysqli_fetch_array($r_pronos)) {
		if ($d['points']<0) {
			$evol_parieur['-'][]=$d;
		} else if ($d['points']>0) {
			$evol_parieur['+'][]=$d;
		} else {
			$evol_parieur['='][]=$d;
		}
	}
	$html.='<div class="row">';

	foreach($evol_parieur as $evol => $parieurs) {
		$html.='<div class="4u">
				<span style="text-align:center" class="pennant"><span class="fa '.$signs[$evol].
				'"></span></span>
				<ul>';
		foreach($parieurs as $parieur) {
			$color=(isset($_SESSION['id_user']) and $parieur['id_user']==$_SESSION['id_user'])?'style="color:red"':'';
			$html.='<li title="'.$parieur['nom_reel'].'" '.$color.'>'.
				htmlentities($parieur['login'],ENT_QUOTES,'UTF-8').'('.$parieur['points'].')</li>';
		}
		$html.='</ul></div>';
	}

} else {
	$html.= '<p>Match non trouvé</p>';
}
?>
