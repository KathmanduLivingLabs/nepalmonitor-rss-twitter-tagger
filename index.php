<?php

mb_http_output('UTF-8'); 

$feedXML = file_get_contents("https://www.nepalmonitor.org/index.php/feed");

$xml=simplexml_load_string($feedXML) or die("There's an error in our Twitter feed system. We're working on it. Until then you can view new reports directly at nepalmonitor.org");

$arr = array();

foreach ($xml->xpath('//item') as $item) {
   
    $r = '';
    foreach($item->children() as $category){

        if($category->getName() == 'category'){
            $r .= '#'.preg_replace('/ |\/|\(|\)/','_',strtolower((string)$category)).' ';
        }

    }
    array_push($arr, $r);
}

function str_replace_nth($search, $replace, $subject, $nth)
{
    $found = preg_match_all('/'.preg_quote($search).'/', $subject, $matches, PREG_OFFSET_CAPTURE);
    if (false !== $found && $found > $nth) {
        return substr_replace($subject, $replace, $matches[0][$nth][1], strlen($search));
    }
    return $subject;
}
$feedXML=htmlspecialchars($feedXML);

foreach($arr as $m=>$s){
    $feedXML = str_replace_nth('&lt;title&gt;', '&lt;title&gt;'.$arr[$m], $feedXML, $m);
}

$report = fopen("feed.xml", w);
fwrite($report, htmlspecialchars_decode($feedXML));
fclose($report);

header('Location: feed.xml');

?>