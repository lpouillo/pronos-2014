<div class="box">
<h2>Dates limites de paris</h2>
<p>
Les pronostics vont se dérouler en 2 etapes : une première étape concerne la phase des poules, avec des
matchs du <?php echo strftime('%A %d %B',$timestamp_poules_debut).' au '.strftime('%A %d %B',$timestamp_poules_fin);?> ; une seconde phase pour le tableau final du <?php echo  strftime('%A %d %B',$timestamp_tableau_debut).' au '.strftime('%A %d %B',$timestamp_tableau_fin);?>.
</p>
<ul class="foot">
	<li>pour les matchs de poules : le <strong><?php echo strftime('%A %d %B &agrave; %H:%M',$timestamp_poules_debut);?>.</strong></li>
	<li>pour le tableau final : le <strong><?php echo strftime('%A %d %B &agrave; %H:%M',$timestamp_tableau_debut);?></strong> (attention vous n'aurez que deux jours pour parier)</li>
</ul>


<h2>Calcul des points pour les phases de poules</h2>
Pour la première phase, le barème est simple. Soit un match A vs. B.<br/><br/>

<table class="table_reglement">
	<tr>
		<th>&nbsp;</th><th>Résultat</th><th>Pari</th>
	</tr>
	<tr>
		<td><img src="public/images/icons/flag_blue.png" alt="flag" /> Équipe 1</td>
		<td class="score">R<sub>1</sub></td><td class="score">P<sub>1</sub></td>
	</tr>
	<tr>
		<td><img src="public/images/icons/flag_red.png" alt="flag" /> Équipe 2</td><td class="score">R<sub>2</sub></td><td class="score">P<sub>2</sub></td>
	</tr>
</table>
Le parieur se voit attribuer un nombre de points égal à:<br/><br/>


<div class="table_reglement">
-5  &delta; <sub>1N2</sub> + |R<sub>1</sub>-P<sub>1</sub>| + |R<sub>2</sub> - P<sub>2</sub>|
</div>

ou |x| est la valeur absolue de x. Le &delta; <sub>1N2</sub> est défini par :<br/><br/>

<table class="table_reglement">
	<tr>
		<td rowspan="2">&delta; <sub>1N2</sub> =</td>
		<td>1 si le joueur a bien pronostiqué le sens du resultat <br/>
			(victoire de 1, Nul, ou victoire de 2),</td>

	</tr>
	<tr>
		<td>0  sinon. </td>
	</tr>
</table>
On rajoute quelques points de pénalité pour quantifier l'ecart au score vrai.<br/><br/>

Vous l'avez compris, il faut obtenir le moins de points à la fin pour gagner.

<h3>Matchs Spéciaux</h3>
3 matchs du premier tour sont classés comme matchs speciaux : le resultat du calcul precedent sera multiplie par 2 pour mettre
un peu de piment dans cette premiere phase. Ces 4 matchs sont les suivants :
<ul class="foot">
<?php
$s_matchs_speciaux="SELECT M.id_match, UNIX_TIMESTAMP( M.date_match ) as date_match , M.heure, EQ1.nom AS eq1, EQ2.nom AS eq2
				FROM matchs M
				INNER JOIN equipes EQ1
					ON M.id_equipe1=EQ1.id_equipe
				INNER JOIN equipes EQ2
					ON M.id_equipe2=EQ2.id_equipe
				WHERE special=1
				ORDER BY M.date_match, M.heure";
$r_matchs_speciaux=mysqli_query($db_pronos, $s_matchs_speciaux);
while ($d_matchs_speciaux=mysqli_fetch_array($r_matchs_speciaux)) {
	echo '<li>'.$d_matchs_speciaux['eq1'].' - '.$d_matchs_speciaux['eq2'].
	', le '.strftime('%A %d %B %G',$d_matchs_speciaux['date_match']).' à '.$d_matchs_speciaux['heure'].' </li>';
}
?>
</ul>

<h3>Exemple :</h3>

<table class="table_reglement">
	<tr>
		<th>&nbsp;</th><th>Résultat</th><th>Pari</th>
	</tr>
	<tr>
		<td><img src="public/images/flags/gr.png" alt="flag" /> Grèce</td><td class="score">2</td><td class="score">2</td>
	</tr>
	<tr>
		<td><img src="public/images/flags/ru.png" alt="flag" /> Russie</td><td class="score">2</td><td class="score">1</td>
	</tr>
</table>
Le parieur se voit attribuer 2 &bull; (-5 &bull; 0+|2-2|+|2-1|) = 2 pt.<br/><br/>


<h2>Phase Finale</h2>
Pour la phase finale, le meme type de barème s'applique. Les coefficients multiplicateurs seront de 2, 3, 4 et 6 pour
1/8, 1/4, 1/2 + petite finale et finale.<br/>
Pour les demi-finales (et a fortiori pour la finale), il se peut
qu'un parieur n'ait pas correctement prédit le nom des demi-finalistes (des
finalistes). Il se voit dans ce cas attribuer un
malus de +2 par participant non pronostiqué.<br/><br/>

Une qualification au TAB a valeur de match nul.<br/><br/>

<h3>Exemple :</h3>

<table class="table_reglement">
	<tr>
		<th>1/2 Finale</th><th>Résultat</th>
	</tr>
	<tr>
		<td><img src="public/images/flags/pt.png" alt="flag" /> PORTUGAL</td><td class="score">1</td><td>TAB : 3</td>
	</tr>
	<tr>
		<td><img src="public/images/flags/it.png" alt="flag" />  ITALIE</td><td class="score">1</td><td>TAB : 4</td>
	</tr>
</table>
<p>Alors que le pronostiqueur avait envisagé</p>

<table class="table_reglement">
	<tr>
		<th>Equipes</th><th>Résultat</th>
	</tr>
	<tr>
		<td><img src="public/images/flags/fr.png" alt="flag" /> FRANCE</td><td class="score">0</td>
	</tr>
	<tr>
		<td><img src="public/images/flags/it.png" alt="flag" /> ITALIE</td><td class="score">1</td>
	</tr>
</table>
La Portugal ayant ete eliminée par la France en 1/4, le parieur
prend :<br/><br/>

<div class="table_reglement">
4 &bull; ( 2 + (-5 * 0) + |1-0| + |1-1|) = 4*(3) = 12 points.
</div>
<p>
L'idee est de penaliser les gens n'ayant pas les bonnes équipes
en demi par rapport aux autres.
</p>
<p>
Enfin, pour recompenser les bons pronostiqueurs :<strong>
LE JOUEUR AYANT PRONOSTIQUE LE BON VAINQUEUR DE LA COUPE DU MONDE SE VOIT
ATTRIBUER UN BONUS DE -30 PTS.</strong>
</p>


</div>
