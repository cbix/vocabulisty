<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
require_once("vformat.php");
$db = new MySQLi('localhost', 'dbuser', 'dbpass', 'db');
if (mysqli_connect_errno()) {
    die('Fehler bei der Verbindung zur Datenbank: '.mysqli_connect_error());
}
$sql = "SELECT *
	FROM la_vok ";
if(isset($_GET['from']) and isset($_GET['to'])) {
	$sql .= "WHERE created_at BETWEEN \"".$_GET['from']."\" AND \"".$_GET['to']."\" ORDER BY ID DESC, created_at DESC";
}
else {
	if(isset($_GET['list']) && is_numeric($_GET['list'])) {
/*		$lid = $_GET['list'];
		if($lid == 'all') {
			$sql .= "ORDER BY `ID` ASC";
			echo "\n<br>".$sql;
		}
		else {
*/			$sql .= "WHERE list_id = '".$_GET['list']."' ORDER BY ID ASC";
//		}
	}
}
$liste = $db->query($sql);
if(!$liste) die("Fehler beim Laden der Liste: ".$db->error);
$listname = "Alle Vokabeln";
if(isset($_GET['list']) && is_numeric($_GET['list'])) {
	$sql = "SELECT *
	FROM la_vok_lists
	WHERE ID=".$_GET['list'];
	$lists = $db->query($sql);
	if(!$lists) die("Fehler beim Abrufen der Vokabellisten: ".$db->error);
	$lists = $lists->fetch_assoc();
	if($lists) $listname = $lists['title'];
}
?>
<html>
	<head>
		<title><?=$listname?> - la632.de.hm</title>
		<style>
			table, td {
				border: 1px solid black;
				border-collapse: collapse;
				margin: 2px;
			}
		</style>
	</head>
	<body>
    <!-- Zeichensatz test: äöüÄÖÜß -->
		<center><b>Vokabelliste</b></center>
		<p>&nbsp;<b><?=$listname?></b></p>
		<table width="100%" border="1">
<?php
/*
function kursiv($text) {
	return(preg_replace('/\/\.(.*)\/\./m', '<i>$1</i>', $text));
}
*/
$i=0;
while($entry=$liste->fetch_assoc()) {
if(++$i % 5 == 0) $lnr = $i;
else $lnr = "&nbsp;";
?>
			<tr>
				<td style="width: 0.5cm;"><i><?=$lnr?></i></td>
				<td style="width: 3.25cm;"><b><?=vformat($entry['latin1'])?></b></td>
				<td style="width: 5.5cm;"><?=vformat($entry['latin2'])?></td>
				<td><?=vformat($entry['german'])?></td>
			</tr>
<?php } ?>
		</table><!--
		<br />
		<center><a href="neu.php">zur&uuml;ck</a></center>-->
	</body>
</html>
