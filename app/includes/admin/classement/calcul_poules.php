<?
// initialisation
mysqli_query($db_pronos,"UPDATE equipes SET classement=10");

$html.='<h3>Calcul du classement des poules</h3>
	<ul>';
for ($i=1;$i<=8;$i++) {
	$html.='<li><strong>Poule '.$i.'</strong><br/>';
	$points=0;
	$s_equipes="SELECT id_equipe, nom FROM equipes WHERE poule='".$i."'";
	$r_equipes=mysqli_query($db_pronos,$s_equipes)
		or die($s_equipes.'<br/>'.mysql_error());
	$equipes=array();
	while ($d_equipes=mysqli_fetch_array($r_equipes)){
		$joues=0;
		$victoires=0;
		$nuls=0;
		$defaites=0;
		$but_p=0;
		$but_c=0;
		$pts=0;

		$s_matchs_eq="SELECT id_equipe1,score1,score2 FROM matchs
			WHERE (id_equipe1=".$d_equipes['id_equipe']." OR id_equipe2=".$d_equipes['id_equipe'].")
				AND type='Poule' AND joue=1";

		$r_matchs_eq=mysqli_query($db_pronos,$s_matchs_eq)
			or die($s_matchs_eq.'<br/>'.mysql_error());
		while($d_matchs_eq=mysqli_fetch_array($r_matchs_eq)) {
			$joues+=+1;
			if ($d_matchs_eq['id_equipe1']==$d_equipes['id_equipe']) {
				$but_p+=$d_matchs_eq['score1'];
				$but_c+=$d_matchs_eq['score2'];
				$victoires+=($d_matchs_eq['score1']>$d_matchs_eq['score2'])?1:0;
				$nuls+=($d_matchs_eq['score1']==$d_matchs_eq['score2'])?1:0;
				$defaites+=($d_matchs_eq['score1']<$d_matchs_eq['score2'])?1:0;
			} else {
				$but_p+=$d_matchs_eq['score2'];
				$but_c+=$d_matchs_eq['score1'];
				$victoires+=($d_matchs_eq['score2']>$d_matchs_eq['score1'])?1:0;
				$nuls+=($d_matchs_eq['score2']==$d_matchs_eq['score1'])?1:0;
				$defaites+=($d_matchs_eq['score2']<$d_matchs_eq['score1'])?1:0;
			}
		}
		$points=3*$victoires+1*$nuls;
		$html.='<span width=100>'.$d_equipes['nom'].' J='.$joues.' V='.$victoires.' N='.$nuls.' D='.$defaites.' BP='.$but_p.' BC='.$but_c.' pts='.$points.'</span> <br/>';
		$s_update="UPDATE equipes SET joues=".$joues.", victoires=".$victoires.", nuls=".$nuls.", defaites=".$defaites.",
			pts=".$points.", but_p=".$but_p.", but_c=".$but_c."
			WHERE id_equipe=".$d_equipes['id_equipe'];
		$r_update=mysqli_query($db_pronos,$s_update)
			or die(mysql_error());
		// On stocke les infos des équipes pour déterminer le classment ensuite
		$equipes[$d_equipes['id_equipe']]=array(
				'nom' => $d_equipes['nom'],
				'pts' => $points,
				'but_p' => $but_p,
				'diff' => $but_p-$but_c);
	}
	// mise à jour du classement de la poule (très chiant car dépend du tournoi ...
	$points_equipes=array();
	foreach($equipes as $id_equipe => $d_equipe) {
		$points_equipes[$id_equipe]=$d_equipe['pts'];
	}
	arsort($points_equipes);
	$class=1;
	$classement=array();
	$id_last_equipe=0;
	foreach($points_equipes as $id_equipe => $point) {
		// on test si une équipe a le même nombre de points
		$id_eq_egalite=array_keys($points_equipes,$point);

		if (sizeof($id_eq_egalite)>1) {
			// on regarde le résultat de la confrontation directe, puis la différence de but puis le
			// nombre de buts marqués
			foreach ($id_eq_egalite as $id_eq2) {
				if ($id_equipe != $id_eq2 and $id_equipe != $id_last_equipe) {
					$s_match="SELECT score1-score2 AS diff1 FROM matchs
						WHERE (id_equipe1='".$id_equipe."' AND id_equipe2='".$id_eq2."')
						UNION
						SELECT score2-score1 AS diff2 FROM matchs
						WHERE (id_equipe2='".$id_equipe."' AND id_equipe1='".$id_eq2."')";
					$r_match=mysqli_query($db_pronos,$s_match);
					$d_match=mysqli_fetch_array($r_match);

					if ($d_match['diff1']>0 or $d_match['diff2']>0) {
						$classement[$class]=$id_equipe;
						$classement[$class+1]=$id_eq2;
					} elseif ($d_match['diff1']<0 or $d_match['diff2']<0) {
						$classement[$class]=$id_eq2;
						$classement[$class+1]=$id_equipe;
					} elseif ($equipes[$id_equipe]['diff']>$equipes[$id_eq2]['diff']) {
						$classement[$class]=$id_equipe;
						$classement[$class+1]=$id_eq2;
					} elseif ($equipes[$id_equipe]['diff']<$equipes[$id_eq2]['diff']) {
						$classement[$class]=$id_eq2;
						$classement[$class+1]=$id_equipe;
					} elseif ($equipes[$id_equipe]['but_p']>$equipes[$id_eq2]['but_p']) {
						$classement[$class]=$id_equipe;
						$classement[$class+1]=$id_eq2;
					} else {
						$classement[$class]=$id_eq2;
						$classement[$class+1]=$id_equipe;
					}
					$id_last_equipe=$id_eq2;
				}
			}
		} else {
			$classement[$class]=$id_equipe;
		}
		$class++;
	}
	foreach($classement as $class => $id_equipe) {
		$s_classement="UPDATE equipes SET classement='".$class."' WHERE id_equipe='".$id_equipe."'";
		mysqli_query($db_pronos,$s_classement)
			or die(mysql_error());
	}
}


$html.='</ul>';
?>
