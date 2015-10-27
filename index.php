<?php

mb_http_output('UTF-8'); 

$feedXML = file_get_contents("https://www.nepalmonitor.org/index.php/feed");
$categoryHashtags = json_decode(file_get_contents("category-hashtags.json"), true);

$xml=simplexml_load_string($feedXML) or die("There's an error in our Twitter feed system. We're working on it. Until then you can view new reports directly at nepalmonitor.org");

$arr = array(' ');

foreach ($xml->xpath('//item') as $item) {
    $r = '...#Nepal';
    foreach($item->children() as $category){
        if($category->getName() == 'category'){
            $categoryString = (string)$category;
            if($categoryHashtags[$categoryString]){
                if(strlen($r.' #'.$categoryHashtags[$categoryString])<=48)
                $r .= ' #'.$categoryHashtags[$categoryString];
            }else{
                $t = ' #'.(preg_split('/ |\//', $categoryString)[0]);
                if(strlen($r.$t)<=38)
                $r .= $t;
            }
        }
    }
    array_push($arr, $r);
}

function str_replace_nth($search, $replace, $subject, $nth)
{
    $found = preg_match_all('/'.$search.'/u', $subject, $matches, PREG_OFFSET_CAPTURE);
    if (false !== $found && $found > $nth) {
        return substr_replace($subject, $replace, $matches[0][$nth][1], strlen($search));
    }
    return $subject;
}
$feedXML=htmlspecialchars($feedXML);

$feedXML = preg_replace('/&lt;title&gt;(.{1,70})(.+)&lt;\/title&gt;/u', "&lt;title&gt;$1&lt;/title&gt;", $feedXML);

foreach($arr as $m=>$s){
    $feedXML = str_replace_nth('&lt;\/title&gt;', $arr[$m].'&lt;/title&gt;', $feedXML, $m);
}

$report = fopen("feed.xml", w);
fwrite($report, htmlspecialchars_decode($feedXML));
fclose($report);

header('Location: feed.xml');

?>
