<?
// Total des points gagn�s par chaque user.
echo '<h3>Calcul du total de points pour chaque utilisateur</h3>';
// r�cup�ration des infos de l'user
$query="SELECT id_user,login from users";
$result=mysqli_query($db_pronos,$query);
while ($joueurs=mysqli_fetch_array($result))
{
	$points=0;
	$count=0;
	$query2="SELECT id_match,points FROM pronos WHERE id_user=".$joueurs['id_user'];
	$result2=mysqli_query($db_pronos,$query2);
	while($pronos=mysqli_fetch_array($result2))
	{
		$query3="SELECT joue FROM matchs WHERE id=".$pronos['id_match'];
		$result3=mysqli_query($db_pronos,$query3);
		$joue=mysqli_fetch_array($result3);
		if ($joue['joue']=='oui')
		{
			$points=$points+$pronos['points'];
		}
	}
	$query3="UPDATE users SET points=".$points." WHERE id=".$joueurs['id_user'];
	mysqli_query($db_pronos,$query3);
}
// Calcul et affichage du nouveau classement
echo '<ol>';
$query2="SELECT id_user,login,points FROM users ORDER BY points";
$result2=mysqli_query($db_pronos,$query2);
$classement=1;
while($users=mysqli_fetch_array($result2))
{
	$query3="UPDATE users SET classement=".$classement." WHERE id=".$users['id_user'];
	mysqli_query($db_pronos,$query3);
	$classement=$classement+1;
	echo '<li>'.$users['login'].' avec '.$users['points'].'</li>';
}
echo '</ol>';
