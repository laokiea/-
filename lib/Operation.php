<?php
namespace Sama\lib;

class Operation {

	private $pdo_client;
	private $ip_api;
	private $mapping = array(
		// "Sama\\" => "",
	);

	public function __construct(){
		spl_autoload_register(array($this, 'autoLoad'));
	}

	public function autoload($className){
		if($className == "Sama\\Operation") return;
	}

	public function initPdo($dsn, $user, $pass){
		try {
			$this->pdo_client = new \PDO($dsn, $user, $pass,array(
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8',
				\PDO::ATTR_PERSISTENT => false,
			));
		} catch(PDOException $e) {
			$this->error($e->getMessage());
		}
	}

	public function insert($ip, $from){
		$sql = "insert into smg_count (`ip`,`from`) values (?,?)";
		$stat = $this->pdo_client->prepare($sql);
		$stat->bindParam(1,$ip);
		$stat->bindParam(2,$from);
		try {
			$stat->execute();
		} catch(PDOException $e) {
			$this->error($e->getMessage());
		}
	}

	public function getIp() {
	    if(getenv("HTTP_X_FORWARDED_FOR") && isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
	        $pos = array_search('unknown', $ips);
	        if($pos !== false) unset($ips[$pos]);
	        $ip = trim($ips[0]);
	    } elseif(getenv("HTTP_CLIENT_IP") && isset($_SERVER['HTTP_CLIENT_IP'])) {
	        $ip = getenv("HTTP_CLIENT_IP");
	    } elseif(getenv("REMOTE_ADDR") && isset($_SERVER['REMOTE_ADDR'])) {
	        $ip = getenv("REMOTE_ADDR");
	    }
	    return $ip;
	}

	public function error($msg) {
		$error = <<<ERROR
			<h2>Oop! Something Look Like Wrong!</h1>
			<h4>$msg</h4>
ERROR;
		echo $error;exit;
	}

	public function simple_request_api($api,$type = 'GET',$data = []){
		if(!$api) return;

		$curl = curl_init($api);
		$options = [];
		$options[CURLOPT_HEADER] = false;
		$options[CURLOPT_RETURNTRANSFER] = true;
		//$options[CURLOPT_FOLLOWLOCATION] = true;
		if($type == 'POST') {
			$options[CURLOPT_POST] = 1;
			$options[CURLOPT_POSTFIELDS] = $data;
		}
		curl_setopt_array($curl, $options);
		$result = curl_exec($curl);
		if(curl_errno($curl)) {
			$this->error(curl_error($curl));
		} 
		return $result;
	}
}