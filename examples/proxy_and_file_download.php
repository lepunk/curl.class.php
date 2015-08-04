<?php

require_once("../curl.class.php");

$proxy = "IP:PORT"
$auth = "user:pass";

$curl = new Curl();

//Using proxy

$curl->setProxy($proxy, $auth); //With auth

$curl->setProxy($proxy);//Without auth


//Using file download

$url = "remote_file_path";
$fpath = "save_to_path";

$fp = fopen($fpath, 'w+');

$curl->disableRedirect();
$curl->setFileDownload($fp);

if($curl->get($url)){
    fclose($fp);
    print("Downloaded!");
} else {
    fclose($fp);
    //Do error processing
}