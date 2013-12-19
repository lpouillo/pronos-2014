<?
// Pages du site
$s_pages="SELECT * FROM pages ORDER BY position_menu";
$r_pages=mysql_query($s_pages);
$pages=array();
while($d_pages=mysql_fetch_array($r_pages)) {
	$pages[$d_pages['libelle']]= array(
		'position' => $d_pages['position_menu'], 
		'titre_menu' => $d_pages['titre_menu'], 
		'titre' => $d_pages['titre'] );
}
// Vérification de la page demandée	
if (isset($_GET['page']) and file_exists('app/pages/'.$_GET['page'].'.php') and array_key_exists($_GET['page'],$pages)) {
	$page=$_GET['page'];
} elseif (empty($_POST['page'])) {
		$page='accueil';
} else {
	 if (file_exists('app/pages/'.$_POST['page'].'.php') and array_key_exists($_POST['page'],$pages)) {
		$page=$_POST['page'];
	} else {
		$page='not_found';
	} 
}

// Gestion du droit d'accès à la page
$titre=$pages[$page]['titre'];  
$url='http://'.$_SERVER['HTTP_HOST'].'/'.$site_path.'/index.php?page='.$page;
$last_mod=filemtime('app/pages/'.$page.'.php');

?>
