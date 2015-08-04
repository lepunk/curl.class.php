<?php

set_time_limit(0);
require_once("../curl.class.php");

$hn_user = "";
$hn_pass = "";

$curl = new Curl();
$curl->setSsl();
$curl->setCookieFile("cookie.txt");

libxml_use_internal_errors(true);

$page = $curl->get("https://news.ycombinator.com/newslogin?whence=news");

$dom = new DOMDocument();
$dom->loadHTML($page);

$fnid = false;

$inputs = $dom->getElementsByTagName('input');
for($i=0; $i<$inputs->length; $i++){
    if ($inputs->item($i)->getAttribute("name") == "fnid"){
        $fnid = $inputs->item($i)->getAttribute("value");
        break;
    }
}

if (!$fnid){
    print("can't find fnid\n");
    exit();
}

$data = array(    "fnid" => $fnid,
                "u" => $hn_user,
                "p" => $hn_pass);

$page = $curl->post("https://news.ycombinator.com/y", $data);

print($page);
