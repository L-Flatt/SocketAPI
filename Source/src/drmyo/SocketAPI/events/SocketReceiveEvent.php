<?php

namespace drmyo\SocketAPI\events;

use pocketmine\plugin\Plugin;
use pocketmine\event\plugin\PluginEvent;
use drmyo\SocketAPI\DataPacket;

class SocketReceiveEvent extends PluginEvent {
	private $packet;
	public static $eventHandler = null;
	public function __construct(Plugin $plugin, DataPacket $packet){
		parent::__construct($plugin);
		$this->packet = $packet;
	}
	public function getPacket(){
		return $this->packet;
	}
}