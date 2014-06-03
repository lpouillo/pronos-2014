<?php
/* Récupération des données des matchs */
$s_matchs="SELECT M.date_match, M.heure, " .
			"M.id_equipe1, EQ1.acronym AS ac1, EQ1.nom AS eq1, EQ1.poule AS poule, " .
			"M.id_equipe2, EQ2.acronym AS ac2, EQ2.nom AS eq2,
			M.score1, M.score2, M.cote_1, M.cote_N, M.cote_2, M.joue, M.special, M.type
			FROM matchs M
			LEFT JOIN equipes EQ1
				ON EQ1.id_equipe=M.id_equipe1
			LEFT JOIN equipes EQ2
				ON EQ2.id_equipe=M.id_equipe2
			WHERE M.type<>'poule'
			ORDER BY M.date_match, M.heure";

//echo $s_matchs;
$r_matchs=mysqli_query($db_pronos,$s_matchs)
	or die($s_matchs.'<br/>'.mysql_error());
$mat_par_type=array();

while ($match=mysqli_fetch_array($r_matchs)) {
	$mat_par_type[substr($match['type'], 0, -1)][]=$match;
}


$i=0;
$j=0;
$huitiemes=$mat_par_type['Huitieme'];

foreach($huitiemes as &$huitieme) {

	if ($i==4) {
		$j=0;
	}
	$i++;
	if (empty($huitieme['eq1'])) {
		if ($i<=4) {
			$huitieme['eq1']='1er poule '.($i+$j);
		} else {
			$huitieme['eq2']='2ème poule '.(($i+$j)-4);
		}
	}
	if (empty($huitieme['eq2'])) {
		if ($i>4) {
			$huitieme['eq2']='1er poule '.(($i+$j+1)-4);
		} else {
			$huitieme['eq2']='2ème poule '.($i+$j+1);
		}
	}
	$j++;

}

/*
//foreach($huitiemes as $huitieme) {
//	print $huitieme['type'].'<br/>';
//	print $huitieme['eq1'].'<br/>';
//	print $huitieme['eq2'].'<br/>';
//}

$quarts=$mat_par_type['Quart'];
$i=0;
$j=0;
while ($d_eq_quart=mysqli_fetch_array($r_eq_quart)) {
	$i++;
	if (empty($d_eq_quart['nom1'])) {
		$d_eq_quart['nom1']='Premier poule '.($i+$j);
	}
	if (empty($d_eq_quart['nom2'])) {
		$d_eq_quart['nom2']='Premier poule '.($i+$j+1);
	}
	$quarts[$i]=$d_eq_quart;
	$j++;
}

$demis=$mat_par_type['Demi'];
$i=0;
$j=0;
while ($d_eq_demi=mysqli_fetch_array($r_eq_demi)) {
	$i++;
	if (empty($d_eq_demi['nom1'])) {
		$d_eq_demi['nom1']='Vainqueur quart '.($i+$j);
	}
	if (empty($d_eq_demi['nom2'])) {
		$d_eq_demi['nom2']='Vainqueur quart '.($i+$j+1);
	}
	$demis[$i]=$d_eq_demi;
	$j++;
}
$finales=array();
$i=0;
while ($d_eq_final=mysqli_fetch_array($r_eq_final)) {
	$i++;
	if ($d_eq_final['type']=='Finale') {
		if (empty($d_eq_final['nom1'])) {
			$d_eq_final['nom1']='Vainqueur demi '.($i-1);
		}
		if (empty($d_eq_final['nom2'])) {
			$d_eq_final['nom2']='Vainqueur demi '.($i);
		}
	} else {
		if (empty($d_eq_final['nom1'])) {
			$d_eq_final['nom1']='Perdant demi '.($i);
		}
		if (empty($d_eq_final['nom2'])) {
			$d_eq_final['nom2']='Perdant demi '.($i+1);
		}
	}
	$finales[$i]=$d_eq_final;
}*/
$html .= '
<h2>Tableau final</h2>
<div>
	<div class="row">
		<section id="huitiemes">
		<div class="3u">
		<header>Huitièmes</header>
		</div>
		</section>
		<section id="quarts">
		<div class="3u">
		Quarts
		</div>
		</section>
		<section id="quarts">
		<div class="3u">
		Demis
		</div>
		</section>
		<section id="quarts">
		<div class="3u">
		Finales
		</div>
	</div>
</div>';


/*

$html .='<div id="huitiemes" class="3u">
			<div id="huitieme1" class="match">
			'.aff_match($huitiemes[1]).'
			</div>
			<div id="huitieme2" class="match">
			'.aff_match($huitiemes[2]).'
			</div>
			<div id="huitieme3" class="match">
			'.aff_match($huitiemes[3]).'
			</div>
			<div id="huitieme4" class="match">
			'.aff_match($huitiemes[4]).'
			</div>
			<div id="huitieme1" class="match">
			'.aff_match($huitiemes[1]).'
			</div>
			<div id="huitieme2" class="match">
			'.aff_match($huitiemes[2]).'
			</div>
			<div id="huitieme3" class="match">
			'.aff_match($huitiemes[3]).'
			</div>
			<div id="huitieme4" class="match">
			'.aff_match($huitiemes[4]).'
			</div>
		</div>'.
		'<div id="quarts" class="3u">
		<div id="quart1" class="match">
		'.aff_match($quarts[1]).'
		</div>
		<div id="quart2" class="match">
		'.aff_match($quarts[2]).'
		</div>
		<div id="quart3" class="match">
		'.aff_match($quarts[3]).'
		</div>
		<div id="quart4" class="match">
		'.aff_match($quarts[4]).'
		</div>
	</div>
	<div id="demis" class="3u">
		<div id="demi1" class="match">
		'.aff_match($demis[1]).'
		</div>
		<div id="demi2" class="match">
		'.aff_match($demis[2]).'
		</div>
	</div>
	<div id="finales"  class="3u">
		<div id="finale">
		'.aff_match($finales[1]).'
		</div>
	</div>
</div>';
// On regarde le nombre d'equipe pour savoir combien de tours a elimintation directe il y a

*/
?>
