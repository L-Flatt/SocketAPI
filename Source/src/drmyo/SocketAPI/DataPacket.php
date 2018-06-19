<?php

namespace drmyo\SocketAPI;

class DataPacket {
	protected $ip;
	protected $port;
	protected $message;
	
	public function __construct($ip, $port, $message){
		$this->ip = gethostbyname($ip);
		$this->port = $port;
		$this->message = $message;
	}
	public function getIp(){
		return $this->ip;
	}
	public function getPort(){
		return $this->port;
	}
	public function getMessage(){
		return $this->message;
	}
	public function encodeMessage(){
		return base64_encode(json_encode(["data"=>$this->message]));
	}
}
?>
