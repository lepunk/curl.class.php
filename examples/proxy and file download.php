<?php

require_once("../curl.class.php");

$proxy = "IP:PORT"
$auth = "user:pass";

$curl = new Curl();

//Using proxy

$curl->setProxy($proxy,$auth); //With auth

$curl->setProxy($proxy);//Without auth


//Using file download

$url = "remote_file_path";
$fpath = 'save_to_path';

$fp = fopen($fpath, 'w+');
$this->curl->disableRedirect();
$this->curl->setFileDownload($fp);

if($this->curl->get($url)){
	fclose($fp);
	echo "Downloaded!";
}else{
	fclose($fp);
	//Do error processing
}