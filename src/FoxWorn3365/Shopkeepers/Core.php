<?php
declare(strict_types=1);

namespace FoxWorn3365\Shopkeepers;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerEntityInteractEvent as Interaction;
use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use muqsit\invmenu\InvMenuHandler;
use muqsit\invmenu\InvMenu;
use pocketmine\Server;

use FoxWorn3365\Shopkeepers\Menu\CreateMenu;

class Core extends PluginBase implements Listener {
    protected object $menu;

    public function onLoad() : void {
        $this->menu = new \stdClass;
    }

    public function onEnable() : void {
		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}

        // Create the config folder if it does not exists
        @mkdir($this->getDataFolder());

        // Load config if it does not exists
        if (file_exists($this->getDataFolder() . "config.yml")) {
            $this->menu = json_decode(file_get_contents($this->getDataFolder() . "config.yml"))->menus;
        }

        // Register event listener
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onPlayerEntityInteract(Interaction $event) : void {
        $menu = new CreateMenu($this->getDataFolder());
        $menu->create()->send($event->getPlayer());
    }
}