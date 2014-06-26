<?php
// Fonction d'échappement des quotes pour éviter les injections SQL
function secure_mysql($string) {
	global $db_pronos;
	if (is_array($string)) {

		foreach($string as &$el) {
			$el = secure_mysql($el);
		}
	} else {
		$string=mysqli_real_escape_string($db_pronos, $string);
	}


 	return $string;
}

// récupération des données
function recuperation_donnees ($sql) {
	global $db_pronos;
	$result=mysqli_query($db_pronos, $sql)
		or die($sql.'<br/><strong>'.mysqli_error($db_pronos).'</strong>');
	$return_array=array();
	$i=0;
	while ($data=mysqli_fetch_array($result,MYSQL_ASSOC)) {
		foreach ($data as $champ => $valeur) {
			$return_array[$i][$champ]=$valeur;
		}
		$i++;
	}
	mysqli_free_result($result);

	return $return_array;
}

// création d'un tableau filtrant
function creation_table($sql,$champs,$post_sql) {
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
	$html='<form id="form_filtrage"  method="POST">
			<input type="hidden" name="page" value="'.$_POST['page'].'"/>
			<input type="hidden" name="section" value="'.$section.'"/>
			<input type="hidden" name="filtrage_soumis" value="oui"/>
		<table class="table_sel" id="table_liste" cellpadding="0" cellspacing="0" style="display:block;">
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
  					$html.='<td class="td_selection">' .
  								'<a href="index.php?page=admin&section='.$section.'&action=modifier&id='.$ligne[$k_champ].'">' .
  										'<img src="public/images/icons/modifier.png" /></a>' .
  							'</td><td class="td_selection">'.
		  					  	'<a href="index.php?page=admin&section='.$section.'&action=supprimer&id='.$ligne[$k_champ].'">' .
		  						'<img src="public/images/icons/supprimer.png"/></a>
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


function flux_match($rss) {

	$matchs = array();
	if (url_exists($rss)) {
		$xml=simplexml_load_file($rss);
		if (isset($xml)) {
			foreach($xml->channel->item as $item) {
				if (strpos($item->title, 'score final') !== false) {
					$matchs[] = $item->title;
				}
			}
		}
	}
	return $matchs;
}

function get_puce($classement) {
	switch($classement) {
		case 1:
			$puce='<img height="14px" src="public/images/icons/concours.png" alt="'.
				$classement.'"/>';

			break;
		case 2:
			$puce='<img height="14px" src="public/images/icons/medal_silver_2.png" alt="'.
				$classement.'"/>';
			break;
		case 3:
			$puce='<img height="14px" src="public/images/icons/medal_bronze_3.png" alt="'.
				$classement.'"/>';
			break;
		case 10000:
			$puce=' - ';
			break;
		default:
			$puce=$classement;
	}
	return $puce;
}

function array_date_match_to_ts($array) {

	return strtotime($array[0].' '.$array[1]);
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

function delta_1N2($score1,$score2,$prono1,$prono2) {
	if ($score1>$score2) {
		$res_match='1';
	} elseif ($score1<$score2) {
		$res_match='2';
	} else {
		$res_match='N';
	}
	if ($prono1>$prono2) {
		$res_prono='1';
	} elseif ($prono1<$prono2) {
		$res_prono='2';
	} else {
		$res_prono='N';
	}
	return ($res_prono==$res_match)?1:0;
}


function aff_match($match, $layout='horizontal') {
	global $timestamp_poules_debut;
	global $timestamp_tableau_debut;

	$date = 'Le '.dateMysqlToFormatted($match['date_match'],$match['heure'],'%A %d %B à %H:%M');

	$is_poule = ($match['type'] =='poule')?true:false;
	$type=($is_poule)?'Match '.$match['id_match']:$match['type'];
	if ($is_poule) {
		$cote=(time()<$timestamp_poules_debut)?'non disponible':$match['cote_1'].'/'.$match['cote_N'].'/'.$match['cote_2'];
		$spec = ($match['special'])?' special':'';
	} else {
		$cote=(time()<$timestamp_tableau_debut)?'à venir':$match['cote_1'].'<br/>'.
			$match['cote_N'].'<br/>'.$match['cote_2'];
		$spec = '';
	}

	if ($match['joue']) {
		$draw=$match['score1']==$match['score2'];
		$aff1=($draw and !$is_poule)?$match['score1'].'('.$match['tab1'].')':$match['score1'];
		$aff2=($draw and !$is_poule)?$match['score2'].'('.$match['tab2'].')':$match['score2'];
		$win1=(vainqueur_match($match)==$match['id_equipe1'])?' winner':'';
		$win2=(vainqueur_match($match)==$match['id_equipe2'])?' winner':'';
	} else {
		$aff1='';
		$aff2='';
		$win1='';
		$win2='';
	}
	$eq1 = ($match['eq1']== '')?'en attente':$match['eq1'];
	$eq2 = ($match['eq2']== '')?'en attente':$match['eq2'];
	$flag1=($match['ac1'] == '')?'50px-Drapeau_noir.svg':$match['ac1'];
	$flag2=($match['ac2'] == '')?'50px-Drapeau_noir.svg':$match['ac2'];

	$score = $aff1.'-'.$aff2;
	$html='<a href="index.php?page=resultats&section=match&id='.$match['id_match'].
			'"><table class="box match'.$spec.'">
				<tbody>';
	if ($layout=='horizontal') {
		$html.=	' 	<tr>
						<th colspan="5">'.
						$type.', '.
						$date.
						'</th>
					</tr>
					<tr>
						<td class="flag">
							<img height="12px" alt="flag" src="public/images/flags/'.$flag1.'.png">
						</td>
						<td class="eq1'.$win1.'">&nbsp;'.$eq1.'</td>
						<td class="score">'.$score.'</td>
						<td class="eq2'.$win2.'">'.$eq2.'&nbsp;</td>
						<td class="flag">
							<img height="12px" alt="flag" src="public/images/flags/'.$flag2.'.png">
						</td>
					</tr>
					<tr>
						<td colspan="5" class="cote">Cote: '.$cote.'</td>
					</tr>';
	} else if ($layout=='vertical') {
		$html.='			<th colspan="2"><strong>'.$type.'</strong></th><th>Cote</th><th>Rés</th></tr>
					<tr>
						<td class="flag">' .
						'<img src="public/images/flags/'.$flag1.'.png" alt="flag" height="12px"></td>
						<td class="eq1">'.$eq1.'</td>
						<td class="cote" rowspan="2">'.$cote.'</td>
						<td class="score">'.$aff1.'</td></tr>
					<tr>
						<td class="flag">' .
						'<img src="public/images/flags/'.$flag2.'.png" alt="flag" height="12px"></td>
						<td class="eq1">'.$eq2.'</td>
						<td class="score">'.$aff2.'</td></tr>
					<tr>
						<td class="date link" colspan="4">'.$date.'</td>
					</tr>';
	}

	$html.='	</tbody>' .
			'</table></a>';


	return $html;
}

function aff_parieur($parieur) {
	print_r($parieur);
}

function aff_prono($match, $edit) {

	$cote=($edit)?'à venir':$match['cote_1'].' / '.$match['cote_N'].' / '.$match['cote_2'];
	$pari1=($edit)?'<input class="score" type="text" size="1" name="pronos['.$match['id_match'].'][score1]" value="'.$match['score1'].'"/>':
		'<span class="score">'.$match['score1'].'</span>';
	$pari2=($edit)?'<input class="score" type="text" size="1" name="pronos['.$match['id_match'].'][score2]" value="'.$match['score2'].'" />':
		'<span class="score">'.$match['score2'].'</span>';
	$resultat=($match['joue'])?'<td class="score">'.$match['res1'].'</td><td class="score">'.$match['res2'].'</td><td style="color:red;">'.$match['points'].' points</td>':
		'<td colspan="3" style="text-align:center;">à venir</td>';
	$spec=(array_key_exists('special', $match) and $match['special'])?'special ':'';

	// On constuire le bloc de ligne correspondant au match
	$html= '<table class="prono box">' .
				'<tbody>' .
				'<tr>
				<td colspan="4">
					<span class="date">Le '.dateMysqlToFormatted($match['date_match'],$match['heure']).'</span>
				</td>
			</tr>
				<tr>
					<td class="'.$spec.' eq1">
					<img src="public/images/flags/'.$match['ac1'].'.png" alt="flag"/>&nbsp;'
					.$match['eq1'].'</td>
					<td>'.$pari1.'</td>
					<td>'.$pari2.'</td>
					<td class="'.$spec.' eq2" style="text-align:right;">
					'.$match['eq2'].'&nbsp;
					<img src="public/images/flags/'.$match['ac2'].'.png" alt="flag"/>
					</td>
			</tr>
			<tr><td>Résultat</td>'.$resultat.'</tr>
			<tr><td style="border-bottom:1px dotted #00774B;">Cote</td>
			<td colspan="3" style=" border-bottom:1px dotted #00774B;text-align:center">'.$cote.'</td></tr>
		</tbody></table>
		<input class="score" type="hidden" size="1" name="pronos['.$match['id_match'].'][tab1]" ' .
				'value="'.$match['tab1'].'"/>
		<input class="score" type="hidden" size="1" name="pronos['.$match['id_match'].'][tab2]" ' .
				'value="'.$match['tab2'].'"/>';
	return $html;
}

function aff_poule($i_poule, $poule) {

	$html='<table class="poule">
				<tr>
					<th>Rang</th><th>Equipe</th><th>Pts</th><th>V</th><th>N</th><th>D</th><th>&#177;</th>
				</tr>';

	for ($k=0;$k<=3;$k++) {
		$html.='<tr>
					<td>'.($k+1).'</td>
					<td style="text-align:left;">' .
						'<img src="public/images/flags/'.$poule[$k]['acronym'].'.png" alt="flag"/> '.
						$poule[$k]['nom'].'</td>
					<td>'.$poule[$k]['pts'].'</td>
					<td>'.$poule[$k]['V'].'</td>
					<td>'.$poule[$k]['N'].'</td>
					<td>'.$poule[$k]['D'].'</td>
					<td>'.$poule[$k]['diff'].'</td>

				</tr>';
	}
	$html.='</table>';

	return $html;
}

function pronostableau($match, $edit) {
	$res1 = ($match['m_score1']==$match['m_score2'])?$match['m_score1'].
		'('.$match['m_tab1'].')':$match['m_score1'];
	$res2 = ($match['m_score1']==$match['m_score2'])?$match['m_score2'].
		'('.$match['m_tab2'].')':$match['m_score2'];
	$resultat = ($match['joue'])?$match['m_eq1'].' '.$res1.
				'-'.$res2.' '.$match['m_eq2']:'à venir';
	$flag1=($match['ac1'] == '')?'50px-Drapeau_noir.svg':$match['ac1'];
	$flag2=($match['ac2'] == '')?'50px-Drapeau_noir.svg':$match['ac2'];
	$cote= (!$edit)?'<div style="height:15px;">'.$match['cote_1'].
			'</div><div style="height:15px;">'.$match['cote_N'].
			'</div><div style="height:15px;">'.$match['cote_2'].'</div>':
		'à venir';
	$html='<table class="prono box">
		<tr>
			<th>'.$match['type'].'</th>
			<th>Buts</th>
			<th>TAB</th>
			<th>Cote</th>
			<th>Points</th>
		</tr>';
	$html.='
		<tr>
			<td class="eq1"><img src="public/images/flags/'.$flag1.'.png" height="12px"/> '
				.$match['eq1'].'
			</td>
			<td>
				<input type="text" class="score" name="pronos['.$match['id_match'].'][score1]"
			 	value="'.$match['score1'].'" />
			</td>
			<td>
				<input type="text" class="score" name="pronos['.$match['id_match'].'][tab1]"
				value="'.$match['tab1'].'"/>
			</td>
			<td rowspan="2" style="text-align:center;vertical-align:middle;">'.$cote.'</td>
			<td rowspan="2" style="vertical-align:middle;color:red;padding-left:20px;">'.$match['points'].'</strong></td>
		</tr>
		<tr>
			<td class="eq1"><img src="public/images/flags/'.$flag2.'.png" height="12px"/> '
			.$match['eq2'].'</td>
			<td>
				<input type="text" class="score" name="pronos['.$match['id_match'].'][score2]"' .
				' value="'.$match['score2'].'"/>
			</td>
			<td>
				<input type="text" class="score" name="pronos['.$match['id_match'].'][tab2]"' .
				' value="'.$match['tab2'].'"/>
			</td>
		</tr>
		<tr>
			<td colspan="5">
				<strong>Résultat :</strong> '.$resultat.'</td>
		</tr>
		<tr>
			<td colspan="5">
				<span class="date">Le '.dateMysqlToFormatted($match['date_match'],$match['heure']).'</span>
			</td>
		</tr>
	</table>';
	return $html;
}


function sendmail($email, $titre, $message) {
	global $admin_email;
	$headers ='From: "Pronos 2014" <'.$admin_email.">\n".
		'Bcc: "Pronos 2014" <'.$admin_email.">\n";
	$headers .='Content-Type: text/html; charset="utf-8"'."\n";
	$headers .='Content-Transfer-Encoding: 8bit';

	mail($email,'[Pronos 2014] '.$titre,$message,$headers,'-f'.$admin_email);
}
