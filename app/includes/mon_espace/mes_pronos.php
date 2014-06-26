<?php
/* On va tester selon la date et l'heure ce qu'il faut afficher
 * en utilisant les variables timestamp */


if (time()<$timestamp_poules_debut) {
	// On affiche les poules modifiables et le tableau en grisé ainsi la DL
	$en_premier=&$html_poules;
	$en_second=&$html_tableau;
	$poule_edit=1;
	$tableau_edit=0;
	$message='Il vous reste encore '.transforme($timestamp_poules_debut-time()).' secondes pour
	parier sur la phase de poules. La seconde phase concernant le tableau final débutera le '
	.strftime('%A %d %B à %H:%M',$timestamp_poules_fin).'.<br/> Les matchs sur
	<span class="special">&nbsp;fond vert&nbsp;</span> comptent double.';
} elseif (time()<$timestamp_poules_fin-6300) {
	// On affiche tout en grisé avec la date de la seconde phase de paris
	$en_premier=&$html_poules;
	$en_second=&$html_tableau;
	$poule_edit=0;
	$tableau_edit=0;
	$message='Voici vos pronostics pour les poules et les points qu\'ils vous ont rapportés. Vous pourrez
	parier pour le tableau final à compter du '.strftime('%A %d %B à %H:%M',$timestamp_poules_fin).' et
	jusqu\'au '.strftime('%A %d %B à %H:%M',$timestamp_tableau_debut).'.<br/> Les matchs encadrés en
	<span class="special">&nbsp;fond vert&nbsp;</span> comptent double.';

} elseif (time()<$timestamp_tableau_debut) {
	// On affiche les poules en grisé et en second ainsi que le tableau éditable
	$en_premier=&$html_tableau;
	$en_second=&$html_poules;
	$poule_edit=0;
	$tableau_edit=1;
	$message='Il vous reste encore '.transforme($timestamp_tableau_debut-time()).' secondes pour
	parier sur le tableau final.';
} elseif (time()<$timestamp_tableau_fin) {
	// On affiche tout en grisé avec la date de la finale de l'euro
	$en_premier=&$html_tableau;
	$en_second=&$html_poules;
	$poule_edit=0;
	$tableau_edit=0;
	$message='Suivez les points que chacun des matchs vous ajouté ou retranché';

}


/* On récupère toutes les données des matchs de poules */
// Récupération des matchs
$s_matchs="SELECT M.id_match, M.date_match, M.heure, M.id_equipe1, M.score1 AS res1, M.score2 AS res2, M.id_equipe2, M.joue, M.cote_1, M.cote_N, M.cote_2,
			EQ1.acronym AS ac1, EQ1.nom AS eq1, EQ1.poule AS poule, EQ2.acronym AS ac2, EQ2.nom AS eq2, M.special,
			P.score1, P.score2, P.tab1, P.tab2, P.points
			FROM matchs M
			INNER JOIN equipes EQ1
				ON EQ1.id_equipe=M.id_equipe1
			INNER JOIN equipes EQ2
				ON EQ2.id_equipe=M.id_equipe2
			LEFT JOIN pronos P
				ON P.id_user='".$_SESSION['id_user']."'
				AND P.id_match=M.id_match
			WHERE M.type='poule'
			ORDER BY EQ1.poule, M.date_match, M.heure";

$r_matchs=mysqli_query($db_pronos, $s_matchs)
	or die($s_matchs.'<br/>'.mysqli_error());
$mat_par_poule=array();
while ($d_matchs=mysqli_fetch_array($r_matchs)) {
	$mat_par_poule[$d_matchs['poule']][]=$d_matchs;
}

// Création des poules
$poules=array();
// Initialisation des poules
for ($i=1;$i<=8;$i++) {
	foreach (array($mat_par_poule[$i][0], $mat_par_poule[$i][1]) as $match) {
		$poules[$i][$match['id_equipe1']]['pts']=0;
		$poules[$i][$match['id_equipe1']]['diff']=0;
		$poules[$i][$match['id_equipe1']]['but_p']=0;
		$poules[$i][$match['id_equipe1']]['but_c']=0;
		$poules[$i][$match['id_equipe1']]['nom']=$match['eq1'];
		$poules[$i][$match['id_equipe1']]['acronym']=$match['ac1'];
		$poules[$i][$match['id_equipe1']]['V']=0;
		$poules[$i][$match['id_equipe1']]['N']=0;
		$poules[$i][$match['id_equipe1']]['D']=0;
		$poules[$i][$match['id_equipe2']]['pts']=0;
		$poules[$i][$match['id_equipe2']]['diff']=0;
		$poules[$i][$match['id_equipe2']]['but_p']=0;
		$poules[$i][$match['id_equipe2']]['but_c']=0;
		$poules[$i][$match['id_equipe2']]['nom']=$match['eq2'];
		$poules[$i][$match['id_equipe2']]['acronym']=$match['ac2'];
		$poules[$i][$match['id_equipe2']]['V']=0;
		$poules[$i][$match['id_equipe2']]['N']=0;
		$poules[$i][$match['id_equipe2']]['D']=0;
	}
}
for ($i=1;$i<=8;$i++) {
// Pour toutes les poules, on calcule les V, N, D de chaque équipe
	foreach($mat_par_poule[$i] as $match) {
		$poules[$i][$match['id_equipe1']]['but_p']+=$match['score1'];
		$poules[$i][$match['id_equipe1']]['but_c']+=$match['score2'];
		$poules[$i][$match['id_equipe1']]['V']+=($match['score1']>$match['score2'])?1:0;
		$poules[$i][$match['id_equipe1']]['N']+=($match['score1']==$match['score2'])?1:0;
		$poules[$i][$match['id_equipe1']]['D']+=($match['score1']<$match['score2'])?1:0;
		$poules[$i][$match['id_equipe2']]['but_p']+=$match['score2'];
		$poules[$i][$match['id_equipe2']]['but_c']+=$match['score1'];
		$poules[$i][$match['id_equipe2']]['V']+=($match['score1']<$match['score2'])?1:0;
		$poules[$i][$match['id_equipe2']]['N']+=($match['score1']==$match['score2'])?1:0;
		$poules[$i][$match['id_equipe2']]['D']+=($match['score1']>$match['score2'])?1:0;
	}
// on calcule ensuite ses points et la différence
	foreach($poules[$i] as $nom => &$equipe) {
		$equipe['pts']=(3*$equipe['V']+$equipe['N']);
		$equipe['diff']=$equipe['but_p']-$equipe['but_c'];
	}
	rsort($poules[$i]);
}


// Création du html des poules
$html_poules.='<section id="poules">' .
		'<div class="row">
	<header><h3>Poules</h3></header></div>' .
	'<div class="row">';

$i_poule=1;
foreach($poules as $poule) {
	$html_poules.='<div class="3u">' .
			'<header><h3>Poule '.$i_poule.'</h3></header>';
	$html_poules.='<ul>';
	if (sizeof($mat_par_poule[$i_poule])>0) {
		foreach($mat_par_poule[$i_poule] as $match) {
			$html_poules .='<li>';
			$html_poules .= aff_prono($match, $poule_edit, 'horizontal');
			$html_poules.='</li>';
		}
		$html_poules .='</ul>';
	} else {
		$html_poules.='<p>Aucun match dans la base pour l\'instant</p>';
	}
	$html_poules.=aff_poule($i_poule, $poule);
	$html_poules .= '</div>';
	if ($i_poule == 4 ) {
		$html_poules.='</div>';
		if ($poule_edit) {
			$html_poules.='<div style="text-align:center">
					<input type="submit" value="Sauvez mes pronos"/>
				</div>';
		}
		$html_poules.='<div class="row">';
	}
	$i_poule += 1;
}
$html_poules.='</div></div></section>';


// génération du tableau

$tableau_edit=1;

// On récupère toutes les données des matchs
$s_pronos="SELECT M.id_match, M.date_match, M.heure, M.cote_1, M.cote_N, M.cote_2,
				M.score1 AS m_score1, M.score2 AS m_score2, M.tab1 AS m_tab1, M.tab2 AS m_tab2, M.type,
				M.id_equipe1 AS m_id1, M.id_equipe2 AS m_id2, M.joue,
				P.score1, P.score2,
				P.tab1, P.tab2, P.points,

				E1.nom AS m_eq1, E2.nom AS m_eq2,
				E1.acronym AS m_ac1, E2.acronym AS m_ac2
				FROM matchs M
				LEFT JOIN pronos P
					ON P.id_user='".$_SESSION['id_user']."'
					AND P.id_match=M.id_match
				LEFT JOIN equipes E1
					ON E1.id_equipe=M.id_equipe1
				LEFT JOIN equipes E2
					ON E2.id_equipe=M.id_equipe2
				WHERE M.type<>'poule'";

$r_pronos=mysqli_query($db_pronos,$s_pronos)
	or die($s_pronos.'<br/>'.mysqli_error($db_pronos));

$mat_par_type=array();
while ($prono=mysqli_fetch_array($r_pronos)) {
	$mat_par_type[substr($prono['type'], 0, -1)][]=$prono;
}

// Récupération des équipes
$s_equipes="SELECT id_equipe, nom, acronym FROM equipes";
$r_equipes=mysqli_query($db_pronos, $s_equipes);
$equipes=array();
while ($d_equipes=mysqli_fetch_array($r_equipes)) {
	$equipes[$d_equipes['id_equipe']]=array(
		'nom' => $d_equipes['nom'], 'acronym' => $d_equipes['acronym']);
}
$equipes[0] = array('nom' => 'à venir', 'acronym' => '');


$sections = array(
	'Huitieme' => 'Huitièmes de finales',
	'Quart' => 'Quarts de finales',
	'Demi' => 'Demi-finales',
	'p_final' => 'Petite finale',
	'Final' => 'Finale'
	);

function find_match_by_type($type, $matchs) {
	foreach($matchs as $key=>$data) {
        if($data['type']==$type) {
            return $data;
        }
    }
    return false;
}

//$tableau_edit=true;

$html_tableau = '<header><h2>Tableau final</h2></header>';
foreach($sections as $nom => $text) {
	$html_tableau .= '<section id="'.$nom.'">' .
		'<div class="12u">' .
			'<div class="row">' .
				'<div class="12u" ><h3>'.$text.'</h3></div>';
	$n_u = (sizeof($mat_par_type[$nom])>4)?'3':12/sizeof($mat_par_type[$nom]);
	foreach($mat_par_type[$nom] as &$match) {

		if ($nom == 'Huitieme') {
			$match['id_equipe1'] = $match['m_id1'];
			$match['id_equipe2'] = $match['m_id2'];
		} else {
			$perdant=false;
			switch ($nom) {
				case 'Quart':
					$prev = 'Huitieme';
				break;
				case 'Demi':
					$prev = 'Quart';
				break;
				case 'p_final':
					$prev = 'Demi';
					$match['type'] .= 1;
					$perdant=true;
				break;
				case 'Final':
					$prev = 'Demi';
					$match['type'] .= 1;
				break;

			}

			if (!$perdant) {
				$match['id_equipe1'] = vainqueur_match((find_match_by_type(
					$prev.$regles[$nom][substr($match['type'],-1)][0],
					$mat_par_type[$prev])));
				$match['id_equipe2'] = vainqueur_match((find_match_by_type(
					$prev.$regles[$nom][substr($match['type'],-1)][1],
					$mat_par_type[$prev])));
			} else {
				$match['id_equipe1'] = perdant_match((find_match_by_type(
					$prev.$regles[$nom][substr($match['type'],-1)][0],
					$mat_par_type[$prev])));
				$match['id_equipe2'] = perdant_match((find_match_by_type(
					$prev.$regles[$nom][substr($match['type'],-1)][1],
					$mat_par_type[$prev])));
			}
		}
		$match['eq1'] = $equipes[$match['id_equipe1']]['nom'];
		$match['ac1'] = $equipes[$match['id_equipe1']]['acronym'];
		$match['eq2'] = $equipes[$match['id_equipe2']]['nom'];
		$match['ac2'] = $equipes[$match['id_equipe2']]['acronym'];
		$html_tableau.='<div class="'.$n_u.'u" style="text-align:center;">'.
			pronostableau($match, $tableau_edit).'</div>';
	}


	$html_tableau .= '</div>' .
		($tableau_edit)?'<div class="12u" style="text-align:center">
		<input type="submit" value="Sauvez mes pronos"/>
		</div>':'';

	'</section>';
}


/* Création de la structure totale */

// message
$html.='<div class="12u" id="mes_pronos">
	<h2>Mes pronostics</h2>
	<div>'.$message.'</div>';
// début du formulaire
if ($poule_edit or $tableau_edit) {
	$html.='<form method="post" id="frm_pronos">' .
			'<div style="text-align:center;margin:auto;" >
		<input type="submit" value="Sauvez mes pronos"/>
		</div>

	<input type="hidden" name="requete" value="update_pronos"/>
	<input type="hidden" name="page" value="mon_espace"/>
	<input type="hidden" name="section" value="mes_pronos"/>';
}
// début de la table qui contient tout
$html.=$en_premier.$en_second;
if ($poule_edit or $tableau_edit) {
	$html.='<div style="text-align:center">
		<input type="submit" value="Sauvez mes pronos"/>
		</div></form>';
}
$html.='</div>';
?>
