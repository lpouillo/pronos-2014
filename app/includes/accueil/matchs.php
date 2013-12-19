

<?php 
$html='<h3>Derniers matchs joués</h3>
<div style="border:1px solid #00774B;border-radius:0px 0px 5px 5px;padding-bottom:5px;">';

$s_matchs="SELECT M.id_match, M.date_match, M.heure, EQ1.nom AS eq1, EQ2.nom AS eq2, EQ1.acronym AS acro1, EQ2.acronym AS acro2,
M.score1, M.score2, M.score1-M.score2 AS diff
FROM matchs M
INNER JOIN equipes EQ1
ON M.id_equipe1=EQ1.id_equipe
INNER JOIN equipes EQ2
ON M.id_equipe2=EQ2.id_equipe
WHERE joue=1
ORDER BY M.date_match, M.heure
LIMIT 4";

$r_matchs=mysql_query($s_matchs);
$count_matchs_joues=0;

$html.='<table>';
if (mysql_num_rows($r_matchs)>0) {
	while ($d_matchs=mysql_fetch_array($r_matchs)) {
		$win1=($d_matchs['diff']>0)?'bold':'normal';
		$win2=($d_matchs['diff']<0)?'bold':'normal';
		
		$html.='<tr>
		<th colspan="5" style="text-align:center;"><span class="date">Le '.dateMysqlToFormatted($d_matchs['date_match'],$d_matchs['heure']).'</span></th>
		</tr>
		<tr>
			<td><img src="public/images/flags/'.$d_matchs['acro1'].'.gif" alt="flag"/></td>
			<td style="font-weight:'.$win1.'">'.$d_matchs['eq1'].'</td>
			<td>'.$d_matchs['score1'].' - '.$d_matchs['score2'].'</td>
			<td style="font-weight:'.$win2.'">'.$d_matchs['eq2'].'</td>
			<td><img src="public/images/flags/'.$d_matchs['acro2'].'.gif" alt="flag"/></td>
		</tr>';
		$count_matchs_joues++;
	}
} else {
	$html.='<tr><td><p style="text-align:center;">Aucun match n\'a encore été joué. <br/> Le premier match aura lieu le '.strftime('%A %d %B à %H:%M',$timestamp_poules_debut).'.</p></td></tr>';
}
if ($timestamp_poules_debut > time()) {
	$texte='Consultez le calendrier !';
} else {
	$texte='Tous les résultats';
}
$html.='</table>
<div style="margin:auto;width:200px;text-align:center;">
<a href="#" onclick="affElement(\'resultats\',\'\',\'\',\'\',\'page\');">'.$texte.'</a>
</div>';
echo $html;
?>

<br/>
		<h3>Prochains matchs</h3>
<?php

$s_matchs="SELECT M.id_match, M.date_match, M.heure, 
				EQ1.acronym AS acro1, EQ1.nom AS eq1, EQ2.acronym AS acro2, EQ2.nom AS eq2, M.score1, M.score2, 
				M.joue, M.cote_1, M.cote_N, M.cote_2
				FROM matchs M
				INNER JOIN equipes EQ1 
					ON M.id_equipe1=EQ1.id_equipe
				INNER JOIN equipes EQ2 
					ON M.id_equipe2=EQ2.id_equipe
				WHERE joue<>1
				ORDER BY M.date_match, M.heure
				LIMIT ".(10-$count_matchs_joues);
$r_matchs=mysql_query($s_matchs);
$html='<table>';
while ($d_matchs=mysql_fetch_array($r_matchs)) {
	$html.='<tr>
			<td colspan="5" style="text-align:center;"><span class="date">Le '.dateMysqlToFormatted($d_matchs['date_match'],$d_matchs['heure']).'</span></td>
			</tr><tr>
			<td><img src="public/images/flags/'.$d_matchs['acro1'].'.gif" alt="flag"/></td>
			<td> '.$d_matchs['eq1'].'</td>
			<td>'.$d_matchs['cote_1'].'/'.$d_matchs['cote_N'].'/'.$d_matchs['cote_2'].' </td>
			<td style="text-align:right;">'.$d_matchs['eq2'].' </td>
			<td><img src="public/images/flags/'.$d_matchs['acro2'].'.gif" alt="flag"/></td>
			</tr>';
}
$html.='</table></div>';
echo $html;		
?>