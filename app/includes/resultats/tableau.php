<?php
/* Récupération des données des matchs */

$s_eq_quart="SELECT M.date_match, M.heure, M.score1, M.score2, M.tab1, M.tab2, M.joue, M.cote_1, M.cote_N, M.cote_2,
					EQ1.id_equipe AS id_equipe1, EQ1.nom AS nom1, EQ1.acronym AS acro1,
					EQ2.id_equipe AS id_equipe2, EQ2.nom AS nom2, EQ2.acronym AS acro2
				FROM matchs M 
				LEFT JOIN equipes EQ1 
					ON M.id_equipe1=EQ1.id_equipe
				LEFT JOIN equipes EQ2 
					ON M.id_equipe2=EQ2.id_equipe
				WHERE M.type LIKE 'Quar%'";
$r_eq_quart=mysql_query($s_eq_quart)
	or die($s_eq_quart.'<br/>'.mysql_error());
$quarts=array();
$i=0;
$j=0;
while ($d_eq_quart=mysql_fetch_array($r_eq_quart)) {
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
$s_eq_demi="SELECT M.date_match, M.heure, M.score1, M.score2, M.tab1, M.tab2, M.joue, M.cote_1, M.cote_N, M.cote_2,
					EQ1.id_equipe AS id_equipe1, EQ1.nom AS nom1, EQ1.acronym AS acro1,
					EQ2.id_equipe AS id_equipe2, EQ2.nom AS nom2, EQ2.acronym AS acro2
				FROM matchs M 
				LEFT JOIN equipes EQ1 
					ON M.id_equipe1=EQ1.id_equipe
				LEFT JOIN equipes EQ2 
					ON M.id_equipe2=EQ2.id_equipe
				WHERE M.type LIKE 'Demi%'";
$r_eq_demi=mysql_query($s_eq_demi)
	or die($s_eq_demi.'<br/>'.mysql_error());
$demis=array();
$i=0;
$j=0;
while ($d_eq_demi=mysql_fetch_array($r_eq_demi)) {
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
$s_eq_final="SELECT M.date_match, M.heure, M.score1, M.score2, M.tab1, M.tab2, M.joue, M.cote_1, M.cote_N, M.cote_2, M.type,
					EQ1.id_equipe AS id_equipe1, EQ1.nom AS nom1, EQ1.acronym AS acro1,
					EQ2.id_equipe AS id_equipe2, EQ2.nom AS nom2, EQ2.acronym AS acro2
				FROM matchs M 
				LEFT JOIN equipes EQ1 
					ON M.id_equipe1=EQ1.id_equipe
				LEFT JOIN equipes EQ2 
					ON M.id_equipe2=EQ2.id_equipe
				WHERE M.type LIKE '%inale%'
				ORDER BY M.date_match";
$r_eq_final=mysql_query($s_eq_final)
	or die($s_eq_final.'<br/>'.mysql_error());
$finales=array();
$i=0;
while ($d_eq_final=mysql_fetch_array($r_eq_final)) {
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
}



$html.='<h2>Tableau final</h2>
<div id="tableau_final">';

$nb_equipe = $cup_teamnumber/4;

$nb_col = 1;
$nb_col_p = 1;
$deb_col = 0;
$esp_col = 0;
$is_case = 1;

/***********************************************/
/*            Les vars de style                */
/***********************************************/
$height = 30;
$width = 150;

/***********************************************/
/*          Calculs globaux                    */
/***********************************************/
$max_col = 0;
for($i = 0 ; pow(2 , $i) <= $nb_equipe ; $i++) $max_col += 2;
	$max_col--;
$nb_ligne = (($nb_equipe-1) *4) + 2;

/* On Commence la table (pour les colonnes) */
$html.= "<table cellpadding='0' cellspacing='0'><tr>";

/* On fait une boucle jusqu'au nombre maximum de colonne */
for($nb_col ; $nb_col <= $max_col ; $nb_col++) {
	/* On reinitilise quelques variable et on affiche une nouvelle colonne */
	$html.= "<td valign='top' width='".$width."'>";
	$is_case = 1;
	
/* Si la colonne n'est pas une colonne lien */	
if($nb_col % 2 == 1) {
	/* Certain calcul */
	$deb_col = pow(2 , $nb_col_p) - 1;
	$esp_col = $deb_col * 2;
	
	/* Une boucle du nombre de ligne dans une colonne */ 
	for($i = 1;$i < $nb_ligne ; $i++) {
	  /* Si on a pas atteint le premier affichage */
		if($i < $deb_col) {	
			$html.= "<table cellpadding='0' cellspacing='0' border='0' height='".$height."'><tr><td></td></tr></table>";
		} else if($is_case == 1 && $i % 2 == 1) {
		/* si c'est une case ( de hauteur 2 * $height ) et que c'est bien la premiere ligne de la case (grace au modulo) */
			/* Le texte d'affichage de la case */
			$html.= "<table cellpadding='0' cellspacing='0' height='".(2*$height)."' class='match' width='".$width."' align='center'><tr><td width='".$width."' ><font size='1'>coucou</font></td></tr></table>";
			$i += 2;
			$is_case = 0;
		}
		/*Sinon :) 
		else
		{
			$html.= "<table cellpadding='0' cellspacing='0' border='0' height='".$height."'><tr><td></td></tr></table>";
		}*/
		/*rapiditer on saute directement les espaces vides et on declare qu'il va y avoir de nouveau une case */
		if($is_case == 0 && $i % 2 == 1)
		{
			$html.= "<table cellpadding='0' cellspacing='0' border='0' height='".($esp_col)*$height."'><tr><td></td></tr></table>";
			$i += $esp_col -1;
			$is_case = 1;
		}
	  
	}
	
	
}

/*************************************************/
/*          Si c'est une colonne lien            */
/*            Le plus chiant                     */
/*************************************************/
else if($nb_col % 2 == 0)
{
  /* On change quelques variables */
	$nb_col_pair = $nb_col;
	
	$deb_col = pow(2 , $nb_col_p);
	$nb_col_p++;
	$esp_col = $deb_col * 2;
	
	/* Meme boucle que tout  l'heure boucle dunombre de ligne */
	for($i = 1 ; $i < $nb_ligne ; $i++)
	{
	  /* Pareil : si aucun affichage encore */
		if($i < $deb_col)
		{	
			$html.= "<table cellpadding='0' cellspacing='0' border='0' height='".$height."'><tr><td></td></tr></table>";
		}
		
		/* Si on doit afficher les liens */
		else if($is_case == 1 && $i % 2 == 0)
		{
			$html.= "<table cellpadding='0' cellspacing='0' border='0' height='".$esp_col*$height."'>";
			
			/* Je fonctionne comme �a , on fait une boucle du nombre de ligne cons�cutive pour un lien */
			for ($i2 = 1 ; $i2 <= $esp_col ; $i2++)
			{
			  /* Si premiere ligne */
				if($i2 == 1)
				{
					$html.= "<tr><td width='".($width)."' height='".$height."' valign='top'>
									<table cellpadding='0' cellspacing='0' border='0'><tr><td height='".$height."' valign='top' width='".($width/2 - 2)."'>
															<table cellpadding='0' cellspacing='0' border='0'><tr height='4'><td bgcolor='#000000' width='".($width/2 - 2)."'></td></tr>
															<tr height='".($height-4)."'><td bgcolor='#FFFFFF'></td></tr></table></td>
									<td width='4' height='".$height."' bgcolor='#000000'></td>
									<td height='".$height."' bgcolor='#FFFFFF' width='".($width/2 - 2)."'></td></tr></table></td></tr>";
				}
				/* Si ligne du milieu */
				else if($i2 == ($esp_col)/2 )
				{
					$html.= "<tr><td width='".($width)."' height='".$height."'>
									<table cellpadding='0' cellspacing='0' border='0'><tr><td height='".$height."' bgcolor='#FFFFFF' width='".($width/2 - 2)."'></td>
									<td bgcolor='#000000' width='4' height='".$height."'></td>
									<td height='".$height."' width='".($width/2 - 2)."'>
															<table cellpadding='0' cellspacing='0' border='0'>
															<tr height='".(($height/2) +2 )."'><td bgcolor='#FFFFFF'></td></tr>
															<tr height='4'><td bgcolor='#000000' width='".($width/2 - 2)."'></td></tr>
															<tr height='".(($height/2)-6)."'><td bgcolor='#FFFFFF'></td></tr>
															</table></td></tr></table></td></tr>";
				}
				/* Si derniere ligne */
				else if($i2 == ($esp_col))
				{
					$html.= "<tr><td width='".($width)."' height='".$height."' valign='bottom'>
									<table cellpadding='0' cellspacing='0' border='0'><tr><td height='".$height."' valign='bottom' width='".($width/2 - 2)."'>
															<table cellpadding='0' cellspacing='0' border='0'><tr height='".($height-4)."'><td bgcolor='#FFFFFF'></td></tr>
															<tr height='4'><td bgcolor='#000000' width='".($width/2 - 2)."'></td></tr></table></td>
									<td width='4' bgcolor='#000000' height='".$height."'></td>
									<td height='".$height."' bgcolor='#FFFFFF' width='".($width/2 - 2)."'></td></tr></table></td></tr>";
					$is_case = 0;
				}
				/* Si ligne verticale */
				else
				{
					$html.= "<tr><td width='".($width)."' height='".$height."' valign='top'>
									<table cellpadding='0' cellspacing='0' border='0'><tr><td bgcolor='#FFFFFF' width='".($width/2 - 2)."' height='".$height."'></td>
									<td width='4' bgcolor='#000000' height='".$height."'></td>
									<td bgcolor='#FFFFFF' height='".$height."' width='".($width/2 - 2)."'></td></tr></table></td></tr>";
				}
			}		 
			/* on incr�mente le nobre de ligne du nombre de ligne cons�cutive pour un lien */
			$i += $esp_col;
			
		}
		
		/* Sinon 
		else
		{
			$html.= "<table cellpadding='0' cellspacing='0' border='0' height='".$height."'><tr><td></td></tr></table>";
		}*/
		
		/*Si on doit afficher des espaces , on les affiche avant d'�ecuter une nouvelle fois la boucle , pour a rapiditer */
		if($is_case == 0 && $i % 2 == 0)
		{
			$html.= "<table cellpadding='0' cellspacing='0' border='0' height='".$esp_col*$height."'><tr><td></td></tr></table>";
			$i += $esp_col - 1;
			$is_case = 1;
	  }
	}
}

/* On ferme la colonne */
 "</td>";

/*Fin de la premiere boucle */
}

/*On ferme la table */
 "</tr></table>";





/*
switch($cup_teamnumber) {
	case 32:
		$html .='<div id="huitiemes" class="div_tour">
			<div id="huitieme1" class="match">
			'.affmatch($huitiemes[1]).'
			</div>
			<div id="huitieme2" class="match">
			'.affmatch($huitiemes[2]).'
			</div>
			<div id="huitieme3" class="match">
			'.affmatch($huitiemes[3]).'
			</div>
			<div id="huitieme4" class="match">
			'.affmatch($huitiemes[4]).'
			</div>
			<div id="huitieme1" class="match">
			'.affmatch($huitiemes[1]).'
			</div>
			<div id="huitieme2" class="match">
			'.affmatch($huitiemes[2]).'
			</div>
			<div id="huitieme3" class="match">
			'.affmatch($huitiemes[3]).'
			</div>
			<div id="huitieme4" class="match">
			'.affmatch($huitiemes[4]).'
			</div>
		</div>';
	break;
}
$html.='<div id="quarts" class="div_tour">
		<div id="quart1" class="match">
		'.affmatch($quarts[1]).'
		</div>
		<div id="quart2" class="match">
		'.affmatch($quarts[2]).'
		</div>
		<div id="quart3" class="match">
		'.affmatch($quarts[3]).'
		</div>
		<div id="quart4" class="match">
		'.affmatch($quarts[4]).'
		</div>
	</div>
	<div id="demis" class="div_tour">
		<div id="demi1" class="match">
		'.affmatch($demis[1]).'
		</div>
		<div id="demi2" class="match">
		'.affmatch($demis[2]).'
		</div>
	</div>
	<div id="finales"  class="div_tour">
		<div id="finale">
		'.affmatch($finales[1]).'
		</div>
	</div>
</div>';
// On regarde le nombre d'equipe pour savoir combien de tours a elimintation directe il y a

/*$s_eq_huitieme="SELECT M.date_match, M.heure, M.score1, M.score2, M.tab1, M.tab2, M.joue, M.cote_1, M.cote_N, M.cote_2,
					EQ1.id_equipe AS id_equipe1, EQ1.nom AS nom1, EQ1.acronym AS acro1,
					EQ2.id_equipe AS id_equipe2, EQ2.nom AS nom2, EQ2.acronym AS acro2
				FROM matchs M 
				LEFT JOIN equipes EQ1 
					ON M.id_equipe1=EQ1.id_equipe
				LEFT JOIN equipes EQ2 
					ON M.id_equipe2=EQ2.id_equipe
				WHERE M.type LIKE 'Huiti%'";

$r_eq_huitieme=mysql_query($s_eq_huitieme)
	or die($s_eq_huitieme.'<br/>'.mysql_error());
$i=0;
$j=0;
$huitiemes=array();
while($d_eq_huitieme=mysql_fetch_array($r_eq_huitieme)) {
	if ($i==4) {
		$j=0;
	}
	$i++;
	if (empty($d_eq_huitieme['nom1'])) {
		if ($i<=4) {
			$d_eq_huitieme['nom1']='1er poule '.($i+$j);
		} else {
			$d_eq_huitieme['nom1']='2ème poule '.(($i+$j)-4);
		}
	}
	
	if (empty($d_eq_huitieme['nom2'])) {
		if ($i>4) {
			$d_eq_huitieme['nom2']='1er poule '.(($i+$j+1)-4);
		} else {
			$d_eq_huitieme['nom2']='2ème poule '.($i+$j+1);
		}
	}
	$huitiemes[$i]=$d_eq_huitieme;
	$j++;
}
*/
  
?>
