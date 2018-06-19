<?php

namespace drmyo\SocketAPI;

use drmyo\SocketAPI\events\SocketReceiveEvent;
use drmyo\SocketAPI\events\SocketSendEvent;

class UDPSocket {
	private $plugin;
	private $socket;
	public $binded = true;
	public $status = self::RUNNING;
	const RUNNING = "§aRunning§f";
	const CLOSED = "§cClosed§f";
	public function __construct($port, SocketAPI $plugin){
		$this->plugin = $plugin;
		$this->socket = @socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		$bind = socket_bind($this->socket, "0.0.0.0", $port);
		if($bind === false){
			$this->plugin->getLogger()->error($this->plugin->translate("bind.failed"));
			$this->binded = false;
			return;
		}
		@socket_set_nonblock($this->socket);
	}
	public function processTick(){
		if($this->status === self::CLOSED)
			return;
		$this->readPacket($ip, $port, $message);
		if($message !== null){
			$assoc = json_decode(base64_decode($message), true);
			if(! is_array($assoc))
				return;
			$data = $assoc["data"] ?? "invalid message";
			$ev = new SocketReceiveEvent($this->plugin, new DataPacket($ip, $port, $data));
			$this->plugin->getServer()->getPluginManager()->callEvent($ev);
		}
	}
	public function readPacket(&$ip, &$port, &$message){
		return socket_recvfrom($this->socket, $message, 1024, 0, $ip, $port);
	}
	public function sendPacket(DataPacket $pk){
		$ev = new SocketSendEvent($this->plugin, $pk);
		$this->plugin->getServer()->getPluginManager()->callEvent($ev);
		if($ev->isCancelled())
			return false;
		$message = $pk->encodeMessage();
		return socket_sendto($this->socket, $message, strlen($message), 0, $pk->getIp(), $pk->getPort());
	}
	public function close(){
		$this->status = self::CLOSED;
		return socket_close($this->socket);
	}
}