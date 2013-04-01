<?php
require_once("vformat.php");

if(isset($_GET['type']) && is_string($_GET['type'])) {
    $type = $_GET['type'];
} else {
    $type = "granule";
}
$template = array(
    'granule' => '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE deck SYSTEM "http://granule.sourceforge.net/granule.dtd">
<deck>
  <author>la632.cbix.de</author>
  <description>%2$s</description>
  <sound_path relative="no"/>
  <pics_path relative="no"/>
  <appearance enabled="no"/>
%3$s</deck>',
    'pauker' => '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<!--This is a lesson file for Pauker (http://pauker.sourceforge.net)-->
<!-- Export URL: http://la632.cbix.de/export.php?type=pauker&list=%1$d -->
<Lesson LessonFormat="1.7">
  <Description>%2$s

Exportiert von http://la632.cbix.de
Liste zum ausdrucken: http://la632.cbix.de/liste.php?list=%1$d</Description>
  <Batch>
%3$s  </Batch>
  <Batch/>
  <Batch/>
</Lesson>',
    'anki' => '# %2$s
# Exportiert von http://la632.cbix.de
# Liste zum ausdrucken: http://la632.cbix.de/liste.php?list=%1$d
# Importieren als Tab-getrennte Liste!
%3$s',
/*    'pauker' => '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<!--This is a lesson file for Pauker (http://pauker.sourceforge.net)-->
<Lesson LessonFormat="1.7">
  <Description>%1$s</Description>
  <Batch>
%2$s  </Batch>
  <Batch/>
  <Batch/>
</Lesson>',
*/
);
$vok_templ = array(
    'granule' => '    <card id="_%1$d">
        <front>%2$s</front>
        <back>%3$s

%4$s</back>
        <back_example></back_example>
    </card>
',
    'pauker' => '    <Card>
      <FrontSide Orientation="LTR" RepeatByTyping="false">
        <Text>%2$s</Text>
      </FrontSide>
      <ReverseSide Orientation="LTR" RepeatByTyping="false">
        <Text>%3$s

%4$s</Text>
      </ReverseSide>
    </Card>
',
    'anki' => "%2\$s\t%3\$s<br /><br />%4\$s\tlatein la632\n",
/*    'pauker' => '    <Card>
      <FrontSide Orientation="LTR" RepeatByTyping="false">
        <Text>%2$s</Text>
        <Font Background="-1" Bold="false" Family="Dialog" Foreground="-16777216" Italic="false" Size="12"/>
      </FrontSide>
      <ReverseSide Orientation="LTR" RepeatByTyping="false">
        <Text>%3$s

%4$s</Text>
        <Font Background="-1" Bold="false" Family="Dialog" Foreground="-16777216" Italic="false" Size="12"/>
      </ReverseSide>
    </Card>
',
*/
);
$out = "";
$out_vok = "";
/* derzeit unterstützte Typen:
 * granule
 * pauker
 * anki
 * 
 * in planung:
 * csv
 */
$list = $_GET['list'];
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
if(!$liste) die("Fehler beim Abrufen der Liste: ".$db->error);
$listname = "Alle Vokabeln";
if(isset($_GET['list']) && is_numeric($_GET['list'])) {
	$sql = "SELECT *
	FROM la_vok_lists
	WHERE ID=".$_GET['list'];
	$lists = $db->query($sql);
	if(!$lists) die("Fehler beim Abrufen der Vokabellisten: ".$db->error);
	$lists = $lists->fetch_assoc();
	if($lists) $listname = $lists['title'];
    $export_suffix = $_GET['list'].'_'.filename_safe($listname);
} else {
    $export_suffix = "alle";
}
if($type == "granule") {
    header('Content-type: application/xml; charset=utf-8');
    header('Content-Disposition: attachment; filename="Latein_'.$export_suffix.'.dkf"');
} else if($type == "pauker") {
    header('Content-type: application/x-gzip');
    header('Content-Disposition: attachment; filename="Latein_'.$export_suffix.'.pau.gz"');
} else if($type == "anki") {
    header('Content-type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename="Latein_'.$export_suffix.'.anki.txt"');
}
while($entry=$liste->fetch_assoc()) {
    $out_vok .= sprintf($vok_templ[$type], $entry['card_id'], vformat_export($type, $entry['latin1']), vformat_export($type, $entry['latin2']), vformat_export($type, $entry['german']));
}
$out = sprintf($template[$type], $list, $listname, $out_vok);
$cur_enc = mb_detect_encoding($out, 'auto');
$out = iconv($cur_enc, 'UTF-8', $out);
if($type == "pauker") {
    $out = gzencode($out, 9);
}
$size = strlen($out);
header('Content-Length: '.$size);
echo $out;
?>
