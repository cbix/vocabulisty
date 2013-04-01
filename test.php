<?php
$db = new MySQLi('localhost', 'dbuser', 'dbpass', 'db');
if (mysqli_connect_errno()) {
    die('Fehler bei der Verbindung zur Datenbank: '.mysqli_connect_error());
}
$word = $_GET['v'];
$list = $_GET['l'];
$sql = "SELECT * FROM la_vok WHERE `list_id`='".addslashes($list)."' AND latin1 LIKE '".str_replace('_', '\_', addslashes($word))."'";
$vok = $db->query($sql);
if(!$vok) die("['','','']");
$result = array();
$entry = $vok->fetch_assoc();
$result[0] = utf8_encode($entry['latin1']);
if(!$entry) $result[0] = $word;
$result[1] = utf8_encode($entry['latin2']);
$result[2] = utf8_encode($entry['german']);
echo json_encode($result);
?>
