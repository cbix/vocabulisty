<?php
function vformat($text) {
        return(trim(vformat_long(str_replace(array("\r\n", "\r", "\n"), "", nl2br(vformat_italic(htmlentities($text)))), true)));
}
function vformat_export($type, $text) {
    switch($type) {
        case 'granule': return vformat_granule($text);
        case 'pauker': return vformat_pauker($text);
        case 'anki': return vformat_anki($text);
    }
    return $text;
}
function vformat_anki($text) {
    return(trim(nl2br(str_replace("\t", "&#09;", vformat_long(vformat_newline(vformat_italic(vformat_percent(utf8_encode($text)))), false)))));
}
function vformat_granule($text) {
    return(trim(htmlspecialchars(vformat_long(vformat_newline(vformat_italic(vformat_percent($text))), true))));
}
function vformat_pauker($text) {
    return(trim(vformat_long(utf8_encode(vformat_newline(vformat_italic(vformat_percent($text), false))), false)));
}
function vformat_percent($text) {
    return(str_replace("%", "%%", $text));
}
function vformat_long($text, $ent = true) {
    $vokale = array("a_", "e_", "i_", "o_", "u_", "A_", "E_", "I_", "O_", "U_");
    if($ent) {
        $macrons = array("&#257;", "&#275;", "&#299;", "&#333;", "&#363;", "&#256;", "&#274;", "&#298;", "&#332;", "&#362;");
    } else {
        $macrons = array("ā", "ē", "ī", "ō", "ū", "Ā", "Ē", "Ī", "Ō", "Ū");
    }
    
    return(str_replace($vokale, $macrons, $text));
    
}
function vformat_italic($text, $html=true) {
    if($html) return(preg_replace('/\/\.(.*?)\/\./', '<i>$1</i>', preg_replace('/\/\/(.*?)\/\//', '<i>$1</i>', $text)));
    return(preg_replace('/\/\.(.*?)\/\./', '($1)', preg_replace('/\/\/(.*?)\/\//', '$1', $text)));
}
function vformat_newline($text) {
    return(str_replace(array("\r\n", "\r", "\n"), "\n", $text));
}
function filename_safe($filename) {
    $temp = $filename;
    $temp = strtolower($temp);
    $temp = str_replace(".", "_", str_replace(" ", "_", $temp));
    $result = '';
    for ($i=0; $i<strlen($temp); $i++) {
        if (preg_match('([0-9]|[a-z]|_|-)', $temp[$i])) {
            $result = $result . $temp[$i];
        }
    }
    return $result;
}
?>
