<?php
/*if (empty($_POST['message'])) {
	$html.='<form method="post" id="frm_email">
		<input type="hidden" name="page" value="admin"/>
		<input type="hidden" name="section" value="email"/>
		<input type="text" name="sujet" /><br/>
		<textarea name="message" cols="60" rows="5"></textarea>
		</form>
		<input type="submit" value="envoyer l\'email" onclick="submitForm(\'frm_email\');"/>';
} else {*/
	$s_email="SELECT email FROM users WHERE actif=1";
	$r_email=mysql_query($s_email);
	$headers ='From: "Pronos 2010 IPGP" <pronos2010@ipgp.fr>'."\n".'Bcc:"Pronos 2010 IPGP" <pronos2010@ipgp.fr>'."\n";
	$headers .='Content-Type: text/html; charset="utf8"'."\n";
	$headers .='Content-Transfer-Encoding: 8bit';  
	
	while ($d_email=mysql_fetch_array($r_email)) {
		echo $d_email['email'].', ';
		/*mail($d_email['email'],'[Pronos IPGP 2012] '.$_POST['sujet'],$_POST['message'],$headers)
			or die('impossible d\'envoyer l\'email');	
			echo $d_email['email'].' '.$_POST['sujet'].' '.$_POST['message'];*/
	}
//}
?>
