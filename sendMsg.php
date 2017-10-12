<?php

header('Content-Type: text/html; charset=utf-8');

ob_start('ob_gzhandler');

require  __DIR__.DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."Operation.php";
require  __DIR__.DIRECTORY_SEPARATOR."php".DIRECTORY_SEPARATOR."api_demo".DIRECTORY_SEPARATOR."SmsDemo.php";
$c = require  __DIR__.DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."config.php";
extract($c['keys']);

use Sama\lib\Operation;
use Sama\lib\Aes;

$sms = new SmsDemo($accessKeyId,$accessKeySecret);
$op  = new Operation();
$aes = new Aes('sama','128',false);

$api_result = json_decode( $op->simple_request_api($c['ip_api'].$op->getIp()) );
$params = [];
$params['time'] = date("Y-m-d H:i:s")/*." ip: ".$op->getIp()." from: ".$api_result->data->country.$api_result->data->city*/;

// 发送短信
if(empty($_COOKIE['period'])) {
	$result = $sms->sendSms($c['sign'], $c['tmp_id'], $c['phone'], $params);
	if($result->Message == "OK") {
		$op->initPdo($c['dsn'], $c['user'], $c['pass']);
		$op->insert($op->getIp(), $api_result->data->country.$api_result->data->city);
		setcookie("period","period",time()+3600);
	}
}

require "index.html";
ob_end_flush();