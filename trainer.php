<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>la632 - Vokabeltrainer</title>
		<script type="text/javascript">
		<!--
			var cur = -1;
			var cards = new Array();
<?php
require_once("vformat.php");
$db = new MySQLi('localhost', 'dbuser', 'dbpass', 'db');
if (mysqli_connect_errno()) {
    die('Fehler bei der Verbindung zur Datenbank: '.mysqli_connect_error());
}
$sql =
        "SELECT *
        FROM la_vok ";
if(!(($lid=$_GET['trainlid'])=="all" or !$lid)) $sql .= "WHERE list_id='" . addslashes($lid) . "' ";
$sql .= "ORDER BY RAND()";
$liste = $db->query($sql);
if(!$liste) die("Fehler beim Laden der Liste: ".$db->error);
/*
function kursiv($text) {
        return(str_replace(array("\r", "\r\n", "\n"), "", nl2br(preg_replace('/\/\.(.*)\/\./m', '<i>$1</i>', $text))));
}
*/
$i=0;
$js_vok = "\t\t\tcards = [";
while($entry=$liste->fetch_assoc()) {
/*
?>
			cards[<?=$i?>] = new Object();
			cards[<?=$i?>]['l'] = "<?=vformat($entry['latin1'])?>";
			cards[<?=$i?>]['e'] = "<?=vformat($entry['latin2'])?>";
			cards[<?=$i?>]['g'] = "<?=vformat($entry['german'])?>";
<?php
*/
$js_vok .= "[\"".vformat($entry['latin1'])."\",\"".vformat($entry['latin2'])."\",\"".vformat($entry['german'])."\"],";
$i++;
}
mysqli_free_result($liste);
$js_vok = substr($js_vok, 0, -1)."];\n";
echo $js_vok;
?>
		var max = <?=$i?>;
		function show() {
			document.getElementById("flashcard").innerHTML = "<b>"+cards[cur][0]+"</b><br />"+cards[cur][1]+"<br /><br />"+cards[cur][2];
		}
		function next() {
			cur = (cur+max+1)%max;
			document.getElementById("flashcard").innerHTML = "<b>"+cards[cur][0]+"</b>";
		}
		function prev() {
			cur = (cur+max-1)%max;
			document.getElementById("flashcard").innerHTML = "<b>"+cards[cur][0]+"</b>";
		}
		-->
		</script>
	</head>
	<body bgcolor="#CCCCCC" onload="next()">
		<center>
			<div style="text-align: center;">
				<input type="button" onclick="prev()" value="<-- vorherige" />
				<input type="button" onclick="show()" value="L&ouml;sung" />
				<input type="button" onclick="next()" value="n&auml;chste -->" />
			</div>
			<div id="flashcard" style="background: white; text-align: center; width: 300px; height: 200px; border: 3px inset;">
				&nbsp;
			</div>
			<br /><br /><br /><br />
			<a href="javascript:location.reload(true)">Neu mischen</a>
			<br />
			<a href="neu.php">zur&uuml;ck</a>
		</center>
	</body>
</html>
