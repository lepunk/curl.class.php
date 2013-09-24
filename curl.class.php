<?php

class Curl {
	
    public $curl;
    public $manual_follow;
    public $redirect_url;
    public $cookiefile = null;
    public $headers = array();

    function Curl() {
        $this->curl = curl_init();
        $this->headers[] = "Accept: */*";
        $this->headers[] = "Cache-Control: max-age=0";
        $this->headers[] = "Connection: keep-alive";
        $this->headers[] = "Keep-Alive: 300";
        $this->headers[] = "Accept-Charset: utf-8;ISO-8859-1;iso-8859-2;q=0.7,*;q=0.7";
        $this->headers[] = "Accept-Language: en-us,en;q=0.5";
        $this->headers[] = "Pragma: "; // browsers keep this blank.

        
        curl_setopt($this->curl, CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.0; en-GB; rv:1.9.0.14) Gecko/2009082707 Firefox/3.0.14 (.NET CLR 3.5.30729)');
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($this->curl, CURLOPT_VERBOSE, false);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($this->curl, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($this->curl, CURLOPT_AUTOREFERER, true);
        
        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')){
            curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        } else {
            $this->manual_follow = true;
        }
        
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_HEADER, false);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 0);
    
        $this->setRedirect();
    }
    
    function addHeader($header){
        $this->headers[] = $header;
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);        
    }
    
    function header($val){
        curl_setopt($this->curl, CURLOPT_HEADER, $val);
    }
    
    function noAjax(){
        foreach($this->headers as $key => $val){
            if ($val == "X-Requested-With: XMLHttpRequest"){
                unset($this->headers[$key]);
            }
        }
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
    }
    
    function setAjax(){
        $this->headers[] = "X-Requested-With: XMLHttpRequest";
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
    }
    
    function setSsl($username = null, $password = null){
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        if ($username && $password){
            curl_setopt($this->curl, CURLOPT_USERPWD, "$username:$password");        
        }    
    }
    
    function setBasicAuth($username,$password){
        curl_setopt($this->curl, CURLOPT_HEADER, false);
        curl_setopt($this->curl, CURLOPT_USERPWD, "$username:$password");    
    }

    
    function setCookieFile($file){
        if (file_exists($file)) {
            
        } else {
            $handle = fopen($file, 'w+') or print('The cookie file could not be opened. Make sure this directory has the correct permissions');
            fclose($handle);
        }
        curl_setopt($this->curl, CURLOPT_COOKIESESSION, true);
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, $file);
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $file);
        $this->cookiefile = $file;
    }
    
    function getCookies(){
          $contents = file_get_contents($this->cookiefile);
          $cookies = array();
          if ($contents){
            $lines = explode("\n",$contents);
            if (count($lines)){
                  foreach($lines as $key=>$val){
                    $tmp = explode("\t",$val);
                    if (count($tmp)>3){
                          $tmp[count($tmp)-1] = str_replace("\n","",$tmp[count($tmp)-1]);
                          $tmp[count($tmp)-1] = str_replace("\r","",$tmp[count($tmp)-1]);
                          $cookies[$tmp[count($tmp)-2]]=$tmp[count($tmp)-1];
                    }
                  }
            }
          }
          return $cookies;
    }

    function setDataMode($val){
         curl_setopt($this->curl, CURLOPT_BINARYTRANSFER, $val);
    }
    
    function close() {
          curl_close($this->curl);
    }
    
    function getInfo(){
          return curl_getinfo($this->curl);
    }
    
    function getInstance() {
        static $instance;
        if (!isset($instance)) {
            $curl = new Curl;
            $instance = array($curl);
        }
        return $instance[0];
    }

    function setTimeout($connect, $transfer) {
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $connect);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $transfer);
    }

    function getError() {
        return curl_errno($this->curl) ? curl_error($this->curl) : false;
    }

    function disableRedirect() {
        $this->setRedirect(false);
    }

    function setRedirect($enable = true) {
        if ($enable) {
            $this->manual_follow = !curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        } else {
            curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, false);
            $this->manual_follow = false;
        }
    }

    function setProxy($proxy,$pauth=0){
        curl_setopt($this->curl, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($this->curl, CURLOPT_PROXY, $proxy);
        if ($pauth) curl_setopt($this->curl, CURLOPT_PROXYUSERPWD, $pauth);

    }

    function setFileDownload(&$fp){
        curl_setopt($this->curl, CURLOPT_FILE, $fp);
    }

    function getHttpCode() {
        return curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
    }


    function makeQuery($data) { 
        if (is_array($data)) {
            $fields = array();
            foreach ($data as $key => $value) {
                 $fields[] = $key . '=' . urlencode($value);
            }
            $fields = implode('&', $fields);
        } else {
            $fields = $data;
        }

        return $fields;
    }
    
    // FOLLOWLOCATION manually if we need to
    function maybeFollow($page) {
        if (strpos($page, "\r\n\r\n") !== false) {
            list($headers, $page) = explode("\r\n\r\n", $page, 2);
        }      
        
        $code = $this->getHttpCode();
        
        if ($code > 300 && $code < 310) {  
            $info = $this->getInfo(); 
            
            preg_match("#Location: ?(.*)#i", $headers, $match);
            $this->redirect_url = trim($match[1]);
            
            if (substr_count($this->redirect_url,"http://") == 0 && isset($info['url']) && substr_count($info['url'],"http://")){
                $url_parts = parse_url($info['url']);
                if (isset($url_parts['host']) && $url_parts['host']){
                    $this->redirect_url = "http://".$url_parts['host'].$this->redirect_url;
                }
            }
            
            if ($this->manual_follow) {
                return $this->get($this->redirect_url);
            }
        } else {
            $this->redirect_url = '';
        }
            
        return $page;
    }
    
    
    function plainPost($url,$data){
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);
        
        $page = curl_exec($this->curl);
            
        $error = curl_errno($this->curl);    
        if ($error != CURLE_OK || empty($page)) {
            return false;
        }

        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, '');
        
        return $this->maybeFollow($page);
    }
    
    function post($url, $data) {
        $fields = $this->makeQuery($data);
        
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $fields);
        $page = curl_exec($this->curl);
            
        $error = curl_errno($this->curl);    
        if ($error != CURLE_OK || empty($page)) {
            return false;
        }

        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, '');
        
        return $this->maybeFollow($page);
    }
    
    function get($url, $data = null) {
        
        curl_setopt($this->curl, CURLOPT_FRESH_CONNECT, true);
        if (!is_null($data)) {
            $fields = $this->makeQuery($data);
            $url .= '?' . $fields;
        }

        curl_setopt($this->curl, CURLOPT_URL, $url);
        $page = curl_exec($this->curl);
        
        $error = curl_errno($this->curl);

        if ($error != CURLE_OK || empty($page)) {
            return false;
        }
        
        return $this->maybeFollow($page);
    }
}

?>
