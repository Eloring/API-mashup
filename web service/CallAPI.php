<?php

	//Baidu translator
	define("CURL_TIMEOUT",   10); 
	define("URL",            "http://api.fanyi.baidu.com/api/trans/vip/translate"); 
	define("APP_ID",         "20170317000042420");
	define("SEC_KEY",        "1qf5P3c7rPfQRZWvZsDA");

Class CallAPI{

   function CallNewsAPI($url){
		   $curl=curl_init();
		   curl_setopt($curl, CURLOPT_URL,$url);
		   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($curl, CURLOPT_HEADER, 0);
           curl_setopt($curl,CURLOPT_TIMEOUT,10); 
		   $dataset=curl_exec($curl);
		   $errorCode = curl_errno($curl);
		   curl_close($curl);
		   if(0 !== $errorCode) {
                return false;
           }
		   else{
            return $dataset;
		   }
	}

	//==========paese news data (author, title, date, image)===============
	function parseData($dataset){
	$myArray = array();
	$php_content = json_decode($dataset);
	 foreach($php_content->articles as $track) { 
		 $track_title=$track->title;
		 $track_description=$track->description; 
	     $track_url=$track->url;
		 $track_image=$track->urlToImage;
		 $track_date=$track->publishedAt;
		 $myArray[]=array("title"=>$track_title,"description"=>$track_description,"url"=>$track_url,"urlToImage"=>$track_image,"publishedAt"=>$track_date);
		}
		$myArray[]=array("title"=>"English","description"=>"English","url"=>"English","urlToImage"=>"English","publishedAt"=>"English");
		return $myArray;
	}
	
	function translateAPI($newsData){
		$youArray = array();
		$count = 5;
		for($i=0;$i<$count;$i++){
			$title=$newsData[$i]['title'];
			$description=$newsData[$i]['description'];
			$url=$newsData[$i]['url'];
			$urlToImage=$newsData[$i]['urlToImage'];
			$publishedAt=$newsData[$i]['publishedAt'];
			
			$targetTitle = $this->translate($title);
			$targetTitle = $targetTitle['trans_result'][0]['dst'];
			$targetDes = $this->translate($description);
			$targetDes = $targetDes['trans_result'][0]['dst'];
			$youArray[]=array("title"=>$targetTitle,"description"=>$targetDes,"url"=>$url,"urlToImage"=>$urlToImage,"publishedAt"=>$publishedAt);
			//$youArray[]=array("title"=>$title,"description"=>$description,"url"=>$url,"urlToImage"=>$urlToImage,"publishedAt"=>$publishedAt);
		}
		$jsondata=json_encode($youArray);
		return $jsondata;	 
	}

	//翻译入口
	function translate($query)
	{
	    $args = array(
	        'q' => $query,
	        'appid' => APP_ID,
	        'salt' => rand(10000,99999),
	        'from' => "en",
	        'to' => "swe",
	    );
	    $args['sign'] = $this->buildSign($query, APP_ID, $args['salt'], SEC_KEY);
	    $ret = $this->call(URL, $args);
	    $ret = json_decode($ret, true);
	    //echo $ret;
	    return $ret; 
	}

	//加密
	function buildSign($query, $appID, $salt, $secKey)
	{/*{{{*/
	    $str = $appID . $query . $salt . $secKey;
		//echo $str;
	    $ret = md5($str);
		//echo "buildSign". $ret;
	    return $ret;
	}/*}}}*/

	//发起网络请求
	function call($url, $args=null, $method="post", $testflag = 0, $timeout = CURL_TIMEOUT, $headers=array())
	{/*{{{*/
	    $ret = false;
	    $i = 0; 
	    while($ret === false) 
	    {
	        if($i > 1)
	            break;
	        if($i > 0) 
	        {
	            sleep(1);
	        }
	        $ret = $this->callOnce($url, $args, $method, false, $timeout, $headers);
	        $i++;
	    }
		//echo "call". $ret;
	    return $ret;
	}/*}}}*/

	function callOnce($url, $args=null, $method="post", $withCookie = false, $timeout = CURL_TIMEOUT, $headers=array())
	{/*{{{*/
	    $ch = curl_init();
	    if($method == "post") 
	    {
	        $data = $this->convert($args);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	        curl_setopt($ch, CURLOPT_POST, 1);
	    }
	    else 
	    {
	        $data = $this->convert($args);
	        if($data) 
	        {
	            if(stripos($url, "?") > 0) 
	            {
	                $url .= "&$data";
	            }
	            else 
	            {
	                $url .= "?$data";
	            }
	        }
	    }
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    if(!empty($headers)) 
	    {
	        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    }
	    if($withCookie)
	    {
	        curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
	    }
	    $r = curl_exec($ch);
	    curl_close($ch);
	    return $r;
	}/*}}}*/

	function convert(&$args)
	{/*{{{*/
	    $data = '';
	    if (is_array($args))
	    {
	        foreach ($args as $key=>$val)
	        {
	            if (is_array($val))
	            {
	                foreach ($val as $k=>$v)
	                {
	                    $data .= $key.'['.$k.']='.rawurlencode($v).'&';
	                }
	            }
	            else
	            {
	                $data .="$key=".rawurlencode($val)."&";
	            }
	        }
	        return trim($data, "&");
	    }
	    return $args;
	}/*}}}*/	
}

?>