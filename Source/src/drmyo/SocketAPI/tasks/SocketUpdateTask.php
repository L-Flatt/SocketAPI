<?php

namespace drmyo\SocketAPI\tasks;

use drmyo\SocketAPI\{SocketAPI, UDPSocket};
use pocketmine\scheduler\Task;

class SocketUpdateTask extends Task {
	public function __construct(SocketAPI $plugin, UDPSocket $socket){
		$this->plugin = $plugin;
		$this->socket = $socket;
	}
	public function onRun(int $currentTick){
		$this->socket->processTick();
	}
}
?>