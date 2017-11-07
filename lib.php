<?php
class UrlShorter{
	public $DATA_FOLDER = 'UrLoS/';
	public $FORWARD_LINK = null; //USER MUST FORWARD TO THIS , it TAKEN FROM USER SHORT LINK ID
	public $HTML_FOLDER = 'htm/';
	public $SHORT_URL = null;
	public function urlValidate($url){
		if(filter_var($url,FILTER_VALIDATE_URL))
			return true;
		else
			return false;
	}

	public function loadURL($short_url,$read=null){
		if($this->findSame($short_url)){
			$d =  json_decode(file_get_contents($this->DATA_FOLDER.$short_url),true);
			if($read==null)
				$this->saveURL($short_url,$d['url'],$d['clicked']+1);
			if($read=='json')
				return file_get_contents($this->DATA_FOLDER.$short_url);
			if($read != null)
				return $d['clicked'];
			else
				return $d['url'];
		}else{
			return null;
		}
	}

	public function saveURL($short_url,$url,$add = null){
		$add = ($add==null)?0:$add;
		$data = ['clicked'=>$add,'url' => $url];
		file_put_contents($this->DATA_FOLDER.$short_url,json_encode($data));
	}

	public function findSame($short_url){ //         DO NOT USE OUT OF GENERATE() AND CALLBACK() FUNCTION
		if(!is_dir($this->DATA_FOLDER))
			mkdir($this->DATA_FOLDER);
		if(file_exists($this->DATA_FOLDER.$short_url) and is_dir($this->DATA_FOLDER))
			return true;
		else
			return false;
	}

	public function generate($url,$len=3,$key=null){
		if($key == null){
			$generated = base64_encode(md5($url.time()));
			$splited = substr($generated, 10,-strlen($generated)+10+intval($len));
			if($this->findSame($splited)){
				return $this->generate($url,$len,'GOD');
			}else{
				return $splited;
			}
		}else{
			if(strlen($key) > $len)
				return $this->generate($url,$len+1,'GOD');
			$generated = base64_encode(md5($url.$key.time()));
			$splited = substr($generated, 10,-strlen($generated)+10+intval($len));
			if($this->findSame($splited)){
				return $this->generate($url,$len,$key.$len);
			}else{
				return $splited;
			}
		}
	}

	public function Create($url){
		if($this->urlValidate($url)){
			$short_url = $this->generate($url);
			$this->saveURL($short_url,$url);
			return $short_url;
		}else{
			return null;
		}
	}

	public function callBack($short_url){
		$short_url = (substr($short_url,-1) == "/")?substr($short_url,0,-1):$short_url;
		if($this->findSame($short_url))
			return $this->loadURL($short_url);
		else
			include $this->HTML_FOLDER.'404.htm';
	}

	public function createToken($b=null){
		if($b!=null)
			$time = $b;
		else
			$time = intval(time()/60);
		$tk = md5($time.'GOD');
		return substr($tk,10,-10);
	}

	public function checkToken($token){
		$time = intval(time()/60);
		$tk_before = substr(md5(($time-1).'GOD'),10,-10);
		$tk_now = substr(md5($time.'GOD'),10,-10);
		$tk_after = substr(md5(($time+1).'GOD'),10,-10);
		if($token == $tk_now)
			return true;
		elseif($token == $tk_after)
			return true;
		elseif ($token == $tk_before) 
			return true;
		else
			return false;
		
	}
}

?>