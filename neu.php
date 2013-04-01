<?php
//error_reporting(E_ALL|E_STRICT);
//ini_set('display_errors', 1);
$db = new MySQLi('localhost', 'dbuser', 'dbpass', 'db');
if(mysqli_connect_errno()) {
    die('Fehler bei der Verbindung zur Datenbank: '.mysqli_connect_error());
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>la632 - Vokabelliste anlegen</title>
	</head>
	<body>
		<!--
        <i><p>Sorry, da mein Server zur Zeit aus irgendeinem Grund nicht erreichbar ist, habe ich das Vokabelsystem auf diesen (leider etwas langsameren...) portiert! Ich hoffe, alle Daten sind erhalten geblieben...</p>
        <p>Gru&szlig;, Flo</p></i>
        -->
<?php
if($_POST['vok']) {
	$i=0;
	$j=0;
	$sql = "";
	//$sql="INSERT INTO `la_vok` (`ID`, `latin1`, `latin2`, `german`, `list_id`, `created_at`) VALUES ";
	$lid=$_POST['lid'];
	foreach($_POST['v'] as $v) {
		if($v['l'] != "") {
			if($v['u']=="on") {
			$sql .= "UPDATE `la_vok` SET `latin2`='".addslashes(trim($v['e']))."', `german`='".addslashes(trim($v['g']))."'
				WHERE `latin1`='".addslashes($v['l'])."';
				";
			$j++;
			}
			else {
			$sql .= "INSERT INTO `la_vok` (`ID`, `latin1`, `latin2`, `german`, `list_id`, `created_at`, `card_id`)
				VALUES (NULL, '".addslashes(trim($v['l']))."', '".addslashes(trim($v['e']))."', '".addslashes(trim($v['g']))."', '".addslashes($lid)."', CURDATE(), UNIX_TIMESTAMP()+ID);
				";
			$i++;
			}
		}
	}
	//if($i<2) $sql = str_replace("VALUES", "VALUE", $sql);
	//$sql = substr($sql, 0, -1);
	//echo "<textarea rows=\"15\" cols=\"100\">".$sql."</textarea>";
	if(!($result=$db->multi_query($sql))) die("Fehler beim Eintragen der Vokabeln: ".$db->error);
	echo "<p>Einf&uuml;gen von ".$i." und Bearbeiten von ".$j." Vokabeln erfolgreich. Dankesch&ouml;&ouml;&ouml;n ;-)</p><p>Zur&uuml;ck zur <a href=\"neu.php\">&Uuml;bersicht</a>, weitere Vokabeln zu dieser Liste <a href=\"#add_form\">hinzuf&uuml;gen</a> oder die Liste <a target=\"_blank\" href=\"liste.php?list=".$lid."\">anzeigen</a> oder <a href=\"trainer.php?trainlid=".$lid."\">abfragen</a></p>";
	//$result->free();
}
if($_POST['newlist'] and $_POST['title'] != "") {
	$title = trim($_POST['title']);
	$sql = "INSERT INTO
			la_vok_lists (title, created_at)
		VALUES
			(?, CURDATE())";
	$stmt = $db->prepare($sql);
	if(!$stmt) die("Fehler beim Vorbereiten des Queries zum Anlegen der Liste ".$title.": ".$db->error);
	$stmt->bind_param('s', $title);
	if(!$stmt->execute()) die("Die Liste ".$title." konnte nicht angelegt werden: ".$stmt->error);
	echo "\t\t<p>Liste \"".$title."\" erfolgreich angelegt!</p>";
}
if(!$_REQUEST['list']) {
	$sql = "SELECT *
		FROM la_vok_lists
		ORDER BY ID DESC, created_at DESC";
	$lists = $db->query($sql);
	if(!$lists) die("Fehler beim Abrufen der Vokabellisten: ".$db->error."\n<center><a href=\"neu.php\">zur&uuml;ck</a></center>");
	$sum_lists = mysqli_num_rows($lists);
?>
		<table width="100%">
			<tr>
				<td><b>Vokabeln zur Liste hinzuf&uuml;gen/bearbeiten</b></td>
				<td><b>Eine neue Vokabelliste anlegen</b></td>
			</tr>
			<tr>
				<td>
					<form method="post" action="" name="select">
						<select name="list">
<?php
$options="";
$selected = " selected";
while($entry = $lists->fetch_assoc()) {
$options .="							<option value=\"".$entry['ID']."\"".$selected.">".$entry['title']."</option>\n";
$selected = "";
}
mysqli_free_result($lists);
echo $options;
?>
						</select>
						<input type="submit" name="select" value="Vokabeln hinzuf&uuml;gen/bearbeiten">
					</form>
				</td>
				<td>
					<form method="post" action="" name="newlist">
						<input type="text" name="title">
						<input type="submit" name="newlist" value="Liste anlegen">
					</form>
				</td>
			</tr>
			<tr>
				<td colspan="2"><hr></td>
			</tr>
			<tr><td colspan="2"><b>Eine Vokabelliste anzeigen/ausdrucken</b></td></tr>
			<tr>
				<td>
					<form method="get" action="liste.php" target="_blank">
						<select name="list">
							<option value="all">Alle</option>
<?=$options?>
						</select>
						<input type="submit" value="Liste anzeigen">
					</form>
				</td>
				<td>
					<p>Bei Fragen, Problemen oder Anregungen einfach kurz eine <a href="mailto:fh@cbix.de">E-Mail</a> an mich schreiben - Danke!</p>
				</td>
			</tr>

			<tr>
				<td colspan="2"><hr></td>
			</tr>
			<tr>
				<td>
					<b>Vokabeltrainer</b>
				</td>
				<td>
					<b>Hinweis</b>
				</td>
			</tr>
			<tr>
				<td>
					<form method="get" action="trainer.php">
						<select name="trainlid">
							<option value="all">Alle</option>
<?=$options?>
						</select>
						<input type="submit" value="Liste abfragen">
					</form>
				</td>
				<td>
					<p>Bitte auch zus&auml;tzlich einfach irgendwelche Listen anlegen (z.B. aus dem Textanhang in eine Liste <i>"Text 2 Lernwortschatz"</i> &uuml;bernehmen), welche dann z.B. von allen zum Lernen benutzt werden k&ouml;nnen :-) <i>Danke!</i> Auf Anfrage kann ich auch eine API einbauen, falls jemand die Daten woanders nutzen will...</p>
				</td>
			</tr>
			<tr>
				<td colspan="2"><hr></td>
			</tr>
			<tr><td colspan="2"><b>Vokabelliste exportieren</b></td></tr>
			<tr>
				<td>
					<form method="get" action="export.php">
						<select name="list">
							<option value="all">Alle</option>
<?=$options?>
						</select><br>
                        <b>Export-Format:</b><br>
                        <select name="type">
                            <option value="anki" selected>Anki</option>
                            <option value="pauker">Pauker</option>
                            <option value="granule">Granule</option>
                        </select>
						<input type="submit" value="Liste exportieren">
					</form>
				</td>
				<td>
                    <p><i><a href="http://pauker.sourceforge.net/pauker.php?page=home&lang=de" target="_blank">Pauker</a></i> ist eine freie, sehr effektive Lernsoftware, f&uuml;r die eine <i>Java-JRE</i> ben&ouml;tigt wird, sie l&auml;uft also auf Windows, Linux, Mac OS und sogar auf Handys :-) Testen (mit <i>Java Web Start</i>) kann man das Programm <a href="http://pauker.sourceforge.net/webstart/pauker.jnlp">hier</a>.</p>
                    <p><a href="http://ankisrs.net/">Anki</a> ist auch eine freie Flashcard-Software. Downloads gibt es f&uuml;r Windows, Linux, Mac OS, iPhone, Android, maemo und weitere Betriebssysteme. In der Desktop-Version kann man die exportierten Karten mit <i>Datei</i> &rarr; <i>Importieren</i> in einen bereits angelegten Karten-Stapel importieren.</p>
					<p><i><a href="http://granule.sourceforge.net" target="_blank">granule</a></i> ist ebenfalls eine freie Lernkartei-Software f&uuml;r Linux und Windows (<a href="http://sourceforge.net/projects/granule/files/granule/1.4/Granule-1.4.0-7-win32.zip/download" target="_blank">Download f&uuml;r Windows 32bit</a> nach c:/Programme entpacken!). Zum Importieren auf <i>Deck</i> &rarr; <i>Open</i> klicken.</p>
                    <p>Wer vielleicht f&uuml;r ein anderes Vokabellern-Programm eine Exportfunktion haben will, kann mir eine E-Mail mit einer Beispiel-Liste in dem entsprechenden Format schicken.</p>
				</td>
			</tr>
			<tr>
				<td colspan="2"><hr></td>
			</tr>
<?php
//$result = $db->query("SELECT ID from la_vok");
$result = $db->query("SELECT COUNT(ID) from la_vok");
if(!$result) $sum_voks="[Fehler beim Z&auml;hlen der Vokabeln: $db->error ]";
//$sum_voks = mysqli_num_rows($result);
$sum_voks_row = $result->fetch_row();
$sum_voks = $sum_voks_row[0];
$result->close();
?>
			<tr>
				<td><b>Statistiken</b></td>
				<td></td>
			</tr>
			<tr>
				<td>
					<p>&nbsp;Insgesamt <?=$sum_lists?> Listen mit <?=$sum_voks?> Vokabeln in der Datenbank!</p>
					<a href="liste.php" target="_blank">Alle anzeigen!</a>
				</td>
                <!-- SELECT COUNT(ID) from la_vok:
                <?= print_r($sum_voks_row) ?>
                
                -->
				<td></td>
			</tr>
		</table>
<?php
}
if($_REQUEST['list']) {
?>
		<script type="text/javascript" src="jquery.min.js"></script>
		<script type="text/javascript">
		<!--
			function test(id) {
				word = $("#l"+id).val();
				if(word != "") {
				list = <?=$_REQUEST['list']?>;
				$.ajax({
					url: "test.php",
					data: "l="+list+"&v="+word,
					dataType: 'json',
					type: "GET",
					success: function(data){
						if(data[0] != "") $("#l"+id).val(data[0]);
						if(data[1] != "") $("#e"+id).text(data[1]);
						if(data[2] != "") $("#g"+id).text(data[2]);
						if(data[1]!="" || data[2]!="") $("#u"+id).attr('checked', true);
					}
				});
				}
			}
		-->
		</script>
		<h1>Vokabeln hinzuf&uuml;gen/bearbeiten</h1>
		<h4>Zum Bearbeiten einer bereits vorhandenen Vokabel einfach das Lateinische Wort eingeben und auf das Textfeld daneben klicken - wenn die alten Daten dort erscheinen, diese &auml;ndern und evtl. die Checkbox ganz rechts aktivieren.</h4>
		<h4 style="color: red;"><i>kursive</i> W&ouml;rter bitte zwischen zwei /. (Schr&auml;gstrich und Punkt) schreiben, alternativ zwei Schr&auml;gstriche (z. B. "//Text//"); L&auml;ngsstriche werden durch einen _ (Unterstrich) hinter dem Vokal erzeugt, also z.B.:</h4>
		<h5>Eingabe: ... secu_tus sum //m. Akk.//</h5>
		<h5>Ausgabe: ... sec&#363;tus sum <i>m. Akk.</i></h5>
		<hr />
		<center><a href="neu.php">zur&uuml;ck</a></center>
		<form method="post" name="addForm" action="neu.php?list=<?=$_REQUEST['list']?>">
		<input type="submit" name="vok" value="Eintragen" />
        <a name="add_form">
		<table border="1" bgcolor="#CDCDCD">
			<thead>
				<!--<th>Zeile</th>-->
				<th>Lat. Vokabel</th>
				<th>lat. Erg&auml;nzung</th>
				<th>Dt. Bedeutung(en)</th>
				<th>vorh. Vokabel bearbeiten?</th>
			</thead>
			<tbody>
<?php
	for($i=0; $i<10; $i++) {
?>
				<tr>
					<!--<td><input type="text" name="v[<?=$i?>][z]" /></td>-->
					<td><input type="text" id="l<?=$i?>" name="v[<?=$i?>][l]" onblur="test(<?=$i?>);" tabindex="<?=3*$i+1?>"></td>
					<td><textarea rows="3" id="e<?=$i?>" name="v[<?=$i?>][e]" tabindex="<?=3*$i+2?>"></textarea></td>
					<td><textarea rows="3" id="g<?=$i?>" name="v[<?=$i?>][g]" tabindex="<?=3*$i+3?>"></textarea></td>
					<td><center><input type="checkbox" id="u<?=$i?>" name="v[<?=$i?>][u]"></center></td>
				</tr>
<?php
	}
?>
			</tbody>
		</table>
        </a>
		<input type="hidden" name="lid" value="<?=$_REQUEST['list']?>">
		<input type="submit" name="vok" value="Eintragen" tabindex="<?=3*$i+4?>">
		</form>
<?php
}
?>
	</body>
</html>
