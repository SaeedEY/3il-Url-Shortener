<?php
require 'lib.php';

$url = isset($_POST['url'])?htmlspecialchars($_POST['url']):'';
$redirect = isset($_GET['fwdToUrl'])?true:false;
$type = isset($_POST['type'])?htmlspecialchars($_POST['type']):'simple';
$token = isset($_POST['token'])?htmlspecialchars($_POST['token']):null;
$manage = ($redirect and strpos($_GET['fwdToUrl'],'-')!=0)?true:false;

$us = new UrlShorter();
$short_link2Link = null;
$short_link = null;

if (!$redirect and ($us->checkToken($token) or $token == null)){ //HOME PAGE Visitor
	$short_link = $us->Create($url);
	include $us->HTML_FOLDER.'index.htm';
}elseif ($redirect and $token == null){ // SHORT LINK FORWARD Visitor
	if($manage){
		$p1 = explode('-',$_GET['fwdToUrl'])[0];
		$p2 = explode("-",$_GET['fwdToUrl'])[1];
		if(strpos($_SERVER['REQUEST_URI'],'?json')>=10){
			echo json_encode(json_decode($us->loadURL($p1,'json'),true),JSON_PRETTY_PRINT);
			die();
		}
		if($p2 == $us->createToken($p1) and $us->findSame($p1)){
			include $us->HTML_FOLDER.'admin.htm';
		}
		else{
			// $short_link2Link = htmlspecialchars($_REQUEST['fwdToUrl']);
			// $red = $us->callBack($short_link2Link);
			$short_url = htmlspecialchars($_GET['fwdToUrl']);
			include $us->HTML_FOLDER.'404.htm';
		}
	}else{
		include $us->HTML_FOLDER.'index.htm';
		$short_link2Link = htmlspecialchars($_REQUEST['fwdToUrl']);
		$red = $us->callBack($short_link2Link);
		header("location:".$red);
	}
}

?>