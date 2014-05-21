<?php
// Fonction d'échappement des quotes pour éviter les injections SQL
function secure_mysql($string) {
 	//$string=addslashes($string);
 	$string=str_replace("'","\'",$string);
 	$string=str_replace('"','\"',$string);
 	return $string;
}

// récupération des données
function recuperation_donnees ($sql) {
	$result=mysql_query($sql)
		or die($sql.'<br/><strong>'.mysql_error().'</strong>');
	$return_array=array();
	$i=0;
	while ($data=mysql_fetch_array($result,MYSQL_ASSOC)) {
		foreach ($data as $champ => $valeur) {
			$return_array[$i][$champ]=$valeur;
		}
		$i++;
	}
	mysql_free_result($result);

	return $return_array;
}

// création d'un tableau filtrant
function creation_table($sql,$champs,$post_sql,$mode) {
	global $params;
	$section=$_POST['section'];

	// Application des filtres
	if (isset($_POST['filtres'])) {
		$filtres=$_POST['filtres'];
		if ($_POST['conserver_filtres']=='oui')	{
			$_SESSION['filtres'.$section]=$_POST['filtres'];
		}
	} elseif (isset($_SESSION['filtres'.$section])){
		$filtres=$_SESSION['filtres'.$section];
	}
	if (isset($filtres)) {
		$sql.=" WHERE ";
		$i_champ=0;
		foreach	($filtres as $champ => $value) {
			if (!empty($value)) {
				if ($i_champ==0 and is_int($value)) {
					$sql.=$champ."=".secure_mysql($value)." AND ";
				} else {
					$sql.=$champ." LIKE '%".secure_mysql($value)."%' AND ";
				}
			}
			$i_champ++;
		}
		$sql.="1=1";
	}


	$sql.=$post_sql;

	if (isset($_POST['limite']) AND is_numeric($_POST['limite'])) {
		$sql.=" LIMIT ".round($_POST['limite']);
		$limite=round($_POST['limite']);
	}
	$tableau=recuperation_donnees($sql);

	// Création du formulaire de filtrage
	$html.='<form id="form_filtrage"  method="POST">
			<input type="hidden" name="page" value="'.$_POST['page'].'"/>
			<input type="hidden" name="section" value="'.$_POST['section'].'"/>
			<input type="hidden" name="filtrage_soumis" value="oui"/>'.
		$input_table.'
		<table class="table_sel" id="table_liste" cellpadding="0" cellspacing="0" style="display:block;" width="'.$_POST['table_width'].'">
		<thead id="liste_header">
			<tr>';
	$html.='<td colspan="3" class="liste_container_bt">
					<input type="button" value="AJOUTER UNE ENTRÉE"
						onclick="affElement(\''.$_POST['page'].'\',\''.$_POST['section'].'\',\'\',\'ajouter\',\'content\');">
				</td>
				<td>';
	$html.='<span id="n_record">'.sizeof($tableau).' entrées</span> <input type="checkbox" name="conserver_filtres" value="oui" checked="checked"> Conserver les filtres</td>';




	$colspan='2';
	$html.='</td>
		</tr>
		<tr>
			<th colspan="'.$colspan.'"> Afficher
				<input name="limite" onkeyup="lancetimer();" type="text" value="'.$limite.'" size="3"></th>';
	foreach($champs as $k_champ =>$champ) {
		$html.='<th>'.$champ[0].'</th>';
	}

	$html.='</tr><tr><th colspan="'.$colspan.'">Filtrer</th>';
	// Affichage des entêtes de champs
	foreach($champs as $k_champ =>$champ) {
		$html.='<th>
			<input style="width:100%;" onkeyup="lancetimer();" onsubmit="lancetimer();" type="text"
				name="filtres['.$champ[1].']." value="'.$filtres[$champ[1]].'">
			</th>';
	}
	$html.='</tr></thead><tbody id="table_body">';

	// Affichage des lignes
  	if (empty($tableau)) {
  		$html.='<tr><td colspan="'.(sizeof($champs)+$colspan).'">Aucune données dans la base.</td></tr>';
  	} else {
  		foreach ($tableau as $ligne) {
  			$html.='<tr>';
  			$i_champ=0;
  			foreach($champs as $k_champ => $champ) {
  				if($i_champ<1)  {
  					$html.='<td class="td_selection">
		 						<img src="public/images/icons/modifier.png"
		  						onclick="affElement(\''.$_POST['page'].'\',\''.$_POST['section'].'\',\''.$ligne[$k_champ].'\',\'modifier\',\'content\')"/>
		  					</td><td class="td_selection">
		  						<img src="public/images/icons/supprimer.png"
		  						onclick="affElement(\''.$_POST['page'].'\',\''.$_POST['section'].'\',\''.$ligne[$k_champ].'\',\'supprimer\',\'content\')"/>
		  					</td>';
  				} else {
					$html.='<td>'.$ligne[$k_champ].'</td>';
  				}
				$i_champ++;
  			}
  			$html.='</tr>';
  		}
  	}

	$html.='</tbody></table></form>';
	return $html;
}

function url_exists($url) {
    if (!$fp = curl_init($url)) return false;
    return true;
}


function lire_rss($url,$nbr=5) {
	$tout.='<ul style="border-radius:5px;background-color:white;border:1px solid #00774B;padding-left:20px;padding-bottom:5px;padding-top:5px;margin-top:0px;margin-bottom:0px;margin-right:20px;">';
	if ((url_exists($url))) {
		$xml=simplexml_load_file($url);
		if (isset($xml)) {
			foreach($xml->channel->item as $item) {
				$i++;
				if($i<=$nbr){
					$txt=utf8_decode($item->description); $lien=utf8_decode($item->link); $titre=utf8_decode($item->title);
					$tout.='
					<li><a href="'.htmlentities($lien).'"><b class="news_link">'.htmlentities($titre).'</b></a></li>';
				}
			}
		}
	} else {
		$tout.='Le flux RSS est indisponible';
	}

	$tout.='</ul>';

	return $tout;
}

/* Transforme date et heure de mysql vers date formattée */
function dateMysqlToFormatted($date,$heure,$fmt='%A %d %B à %H:%M') {
	list($year, $month, $day) = explode("-", $date);
	list($heures, $minutes, $secondes) = explode(":", $heure);
	$time = mktime($heures, $minutes, $secondes, $month, $day, $year);
	return strftime($fmt,$time);
}

/* Transformer les secondes en jours, ... */
function transforme($time) {
    if ($time>=86400) {
    /* 86400 = 3600*24 c'est à dire le nombre de secondes dans un seul jour ! donc là on vérifie si le nombre de secondes donné contient des jours ou pas */
    // Si c'est le cas on commence nos calculs en incluant les jours

    // on divise le nombre de seconde par 86400 (=3600*24)
    // puis on utilise la fonction floor() pour arrondir au plus petit
    $jour = floor($time/86400);
    // On extrait le nombre de jours
    $reste = $time%86400;

    $heure = floor($reste/3600);
    // puis le nombre d'heures
    $reste = $reste%3600;

    $minute = floor($reste/60);
    // puis les minutes

    $seconde = $reste%60;
    // et le reste en secondes

    // on rassemble les résultats en forme de date
    $result = $jour.'j '.$heure.'h '.$minute.'min '.$seconde.'s';
    }
    elseif ($time < 86400 AND $time>=3600)
    // si le nombre de secondes ne contient pas de jours mais contient des heures
    {
    // on refait la même opération sans calculer les jours
    $heure = floor($time/3600);
    $reste = $time%3600;

    $minute = floor($reste/60);

    $seconde = $reste%60;

    $result = $heure.'h '.$minute.'min '.$seconde.' s';
    }
    elseif ($time<3600 AND $time>=60)
    {
    // si le nombre de secondes ne contient pas d'heures mais contient des minutes
    $minute = floor($time/60);
    $seconde = $time%60;
    $result = $minute.'min '.$seconde.'s';
    }
    elseif ($time < 60)
    // si le nombre de secondes ne contient aucune minutes
    {
    $result = $time.'s';
    }
    return $result;
    }

/* Fonctions pour les calculs des matchs, des vainqueur */
function vainqueur_match($match) {
	if ($match['score1']>$match['score2'] or ($match['score1']==$match['score2'] and $match['tab1']>$match['tab2'])) {
		$vainqueur=$match['id_equipe1'];
	} elseif ($match['score1']<$match['score2']  or ($match['score1']==$match['score2'] and $match['tab1']<$match['tab2'])) {
		$vainqueur=$match['id_equipe2'];
	} else {
		$vainqueur=0;
	}
	return $vainqueur;

}
function perdant_match($match) {
	if ($match['score1']<$match['score2'] or ($match['score1']==$match['score2'] and $match['tab1']<$match['tab2'])) {
		$perdant=$match['id_equipe1'];
	} elseif ($match['score1']>$match['score2']  or ($match['score1']==$match['score2'] and $match['tab1']>$match['tab2'])) {
		$perdant=$match['id_equipe2'];
	} else {
		$perdant=0;
	}
	return $perdant;
}

function affmatch($match) {

	echo '<pre>';
	print($match);
	echo '</pre>';
	if (empty($match)) {

	}
	/*
	if ($match['joue']) {
		if ($match['score1']==$match['score2']) {
			$tab1='('.$match['tab1'].')';
			$tab2='('.$match['tab2'].')';
		}
		$aff1=' '.$match['score1'].' '.$tab1;
		$aff2=' '.$match['score2'].' '.$tab2;
	}
	$flag1=(empty($match['acro1']))?'drapeau_noir':$match['acro1'];
	$flag2=(empty($match['acro2']))?'drapeau_noir':$match['acro2'];

	$html='<table style="margin:auto;"><tr>
			<td colspan="3"><span class="date">Le '.dateMysqlToFormatted($match['date_match'],$match['heure']).'</span></td>
		</tr>
		<tr>
			<td><img height="12px" src="public/images/flags/'.$flag1.'.gif" alt="flag"/></td>
			<td>'.$match['nom1'].'</td>
			<td>'.$aff1.'</td>
		</tr><tr>
			<td><img height="12px" src="public/images/flags/'.$flag1.'.gif" alt="flag"/></td>
			<td>'.$match['nom2'].'</td>
			<td>'.$aff2.'</td>
		</tr></table>';
		*/
	$html='<table style="margin:auto;"><tr>
			<td colspan="3"><span class="date">Le vendredi 21 juin à 20h45</span></td>
		</tr>
		<tr>
			<td><img height="12px" src="public/images/flags/'.$flag1.'.gif" alt="flag"/></td>
			<td>'.$match['nom1'].'</td>
			<td>'.$aff1.'</td>
		</tr><tr>
			<td><img height="12px" src="public/images/flags/'.$flag1.'.gif" alt="flag"/></td>
			<td>'.$match['nom2'].'</td>
			<td>'.$aff2.'</td>
		</tr></table>';
	return $html;
}



function pronostableau($match) {
	global $equipes;
	$html.='<table width="100%">';
	$html.='
		<tr>
			<td><span class="date">'.$match['match']['date_match'].' '.substr($match['match']['heure'],0,5).'</span>
			 <td>BUTS</td><td>TAB</td>
			</tr>
		<tr>';

	$html.='<td class="equipe"><img src="public/images/'.$equipes[$match['pronos']['id_equipe1']]['acronym'].'.gif"/> '
		.$equipes[$match['pronos']['id_equipe1']]['nom'].'</td> ';
	$html.='<td><input type="text" size="1" name="pronos['.$match['match']['id_match'].'][score1]" value="'.$match['pronos']['score1'].'" '.$dis_tableau.'/></td>
			<td><input type="text" size="1" name="pronos['.$match['id_match'].'][tab1]" value="'.$match['pronos']['tab1'].'" '.$dis_tableau.'/></td><td>
			</tr><tr>';
	$html.='<td class="equipe"><img src="public/images/'.$equipes[$match['pronos']['id_equipe2']]['acronym'].'.gif"/> '
		.$equipes[$match['pronos']['id_equipe2']]['nom'].'</td> ';
	$html.='<td><input type="text" size="1" name="pronos['.$match['match']['id_match'].'][score2]" value="'.$match['pronos']['score2'].'" '.$dis_tableau.'/></td>
			<td><input type="text" size="1" name="pronos['.$match['match']['id_match'].'][tab2]" value="'.$match['pronos']['tab2'].'" '.$dis_tableau.'/></td>
		</tr>
		<tr>
			<td colspan="3"><strong>Résultat :</strong> '.$equipes[$match['match']['id_equipe1']]['nom'].' '.$match['match']['score1'].'-'.$match['match']['score2'].' '.$equipes[$match['match']['id_equipe2']]['nom'].'</td>
		</tr>';
	$html.='<tr>
		<td>'.$match['match']['cote_1'].' / '.$match['match']['cote_N'].' / '.$match['match']['cote_2'].'</td><td colspan="3" style="color:red;padding-left:20px;">'.$match['pronos']['points'].' points</strong></td>
	</tr>';
	$html.='</table>';
	return $html;
}
