<?
// initialisation
mysql_query("UPDATE equipes SET classement=10");

$html.='<h3>Calcul du classement des poules</h3>
	<ul>';
for ($i=1;$i<=$cup_groups;$i++) {
	$html.='<li><strong>Poule '.$i.'</strong><br/>';
	$points=0;
	$s_equipes="SELECT id_equipe, nom FROM equipes WHERE poule='".$i."'";
	$r_equipes=mysql_query($s_equipes)
		or die($s_equipes.'<br/>'.mysql_error());
	while ($d_equipes=mysql_fetch_array($r_equipes)){
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
	
		$r_matchs_eq=mysql_query($s_matchs_eq)
			or die($s_matchs_eq.'<br/>'.mysql_error());
		while($d_matchs_eq=mysql_fetch_array($r_matchs_eq)) {
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
		$r_update=mysql_query($s_update)
			or die(mysql_error());
	}
	// mise à jour du classement
	$s_classement="SELECT id_equipe, nom, pts, but_p-but_c AS diff FROM equipes 
			WHERE poule=".$i." AND joues<>0
			ORDER by pts DESC, but_p-but_c DESC, but_p DESC";
	$r_classement=mysql_query($s_classement);
	$class=1;
	while($d_classement=mysql_fetch_array($r_classement)) {
		$html.=$class. ' : '.$d_classement['nom'].' '.$d_classement['pts'].' '.$d_classement['diff'].'<br/>';
		$query="UPDATE equipes SET classement=".$class." WHERE id_equipe='".$d_classement['id_equipe']."'";
		mysql_query($query);
		$class++;
	}
}



$html.='</ul>';
?>
