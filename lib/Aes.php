<?php

namespace Sama\lib;
 
class Aes {
	private $init_key;

	private $auto_padding;

	private $aes_bytes;

	private $block_size;

	private $key;

	private $iv;

	private $map = [
		'128' => MCRYPT_RIJNDAEL_128,
		'196' => MCRYPT_RIJNDAEL_192,
		'256' => MCRYPT_RIJNDAEL_256,
	];

	public function __construct($init_key, $aes_bytes, $auto_padding){
		$this->init_key = $init_key;
		$this->auto_padding = $auto_padding;
		$this->aes_bytes = $aes_bytes;
		$this->block_size = mcrypt_get_block_size($this->map[$this->aes_bytes], MCRYPT_MODE_CBC);

		$this->key = substr( hash('sha256',$this->init_key), 0, $this->block_size );
		$this->iv  = mcrypt_create_iv( mcrypt_get_iv_size($this->map[$this->aes_bytes], MCRYPT_MODE_CBC) );
	}

	//CBC mode
	public function encrypt($data){
		$data = trim($data);
		if(!$this->auto_padding) {
			$data = $this->addPKCS7Padding($data);
		}
		$encrypt = mcrypt_encrypt($this->map[$this->aes_bytes], $this->key, $data, MCRYPT_MODE_CBC, $this->iv);
		return base64_encode( $encrypt );
	}

	public function decrypt($aes_data){
		$aes_data = base64_decode($aes_data);	
		$decrypt =  mcrypt_decrypt($this->map[$this->aes_bytes], $this->key, $aes_data, MCRYPT_MODE_CBC, $this->iv);
		return $this->stripPKCS7Padding( trim($decrypt) );
	}

	// padding
	public function addPKCS7Padding($data){
		$data = trim($data);
		$pad = $this->block_size - (strlen($data) % $this->block_size);
		if( $pad <= $this->block_size ) {
			$padStr = chr($pad) ;
			$data .= str_repeat($padStr, $pad);
		}
		return $data;
	}

	public function stripPKCS7Padding($data){
		$num = ord(substr($data,-1));
		return substr($data,0,-$num);
	}
}