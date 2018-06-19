<?php

namespace drmyo\SocketAPI;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use drmyo\SocketAPI\tasks\SocketUpdateTask;
use pocketmine\utils\Config;
use pocketmine\command\{Command, CommandSender};

class SocketAPI extends PluginBase implements Listener {
	private $sockets = [ ];
	private static $instance;
	public static function getInstance(){
		return self::$instance;
	}
	public function onLoad(){
		self::$instance = $this;
	}
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		@mkdir($this->getDataFolder());
		$this->saveResource("translations.yml", false);
	}
	public function createSocket($port){
		$socket = new UDPSocket($port, $this);
		if($socket->binded === false){
			return null;
		}
		$task = new SocketUpdateTask($this, $socket);
		$this->getScheduler()->scheduleRepeatingTask($task, 1);
		$this->sockets[$port] = $socket;
		return $socket;
	}
	public function getSocket($port){
		return $this->sockets[$port];
	}
	public function translate($key){
		$lang = $this->getServer()->getLanguage()->getLang();
		$conf = new Config($this->getDataFolder() . "translations.yml");
		return $conf->getAll()[$lang . "." . $key] ?? $conf->get("en." . $key);
	}
	public function onCommand(CommandSender $sender, Command $command, $label, array $args) : bool {
		if($command->getName() === "socklist"){
			$sender->sendMessage($this->translate("command.socklist"));
			foreach($this->sockets as $port=>$socket){
				$sender->sendMessage("- Socket Running on $port : " . $socket->status);
			}
			return true;
		}
		return true;
	}
}
?>