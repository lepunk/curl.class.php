<?php

if (isset($_GET['keyword']) && $_GET['keyword']){
	$keyword = $_GET['keyword'];
} elseif (isset($argv[1]) && $argv[1]){
	$keyword = $argv[1];
} else {
	die("usage: php {$argv[0]} [keyword]\n");
}

require_once("../curl.class.php");

$curl = new Curl();
$page = $curl->get("http://www.ebay.com/sch/i.html?_trksid=p2050601.m570.l1313&_nkw=".$keyword."&_sacat=0&_from=R40");

if ($page && $curl->getHttpCode()>=200 && $curl->getHttpCode()<400){
	
	$dom = new DOMDocument();
	@$dom->loadHTML($page);
	
	$tables = $dom->getElementsByTagName('table');
	for($i=0;$i<$tables->length;$i++){
		
		if ($tables->item($i)->getAttribute("itemtype")!="http://schema.org/Offer"){
			continue;
		}

		$h4s = $tables->item($i)->getElementsByTagName('h4');
		if (!$h4s->length){
			continue;
		}
		
		$links = $h4s->item(0)->getElementsByTagName('a');
		if (!$links->length){
			continue;
		}
		
		$item_title = $links->item(0)->textContent;
		$item_url = $links->item(0)->getAttribute("href");
		
		print($item_title."\t".$item_url."\n");
		
	}
	
	
} else {
	print("unexpected error occured\n");
}