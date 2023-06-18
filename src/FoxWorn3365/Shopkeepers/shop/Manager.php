<?php

namespace FoxWorn3365\Shopkeepers\shop;

use pocketmine\player\Player;

// Packet
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

use FoxWorn3365\Shopkeepers\ConfigManager;
use FoxWorn3365\Shopkeepers\utils\Utils;
use FoxWorn3365\Shopkeepers\utils\ItemUtils;
use FoxWorn3365\Shopkeepers\entity\Shopkeeper;

class Manager {
    protected Shop $shop;
    protected ConfigManager $cm;
    protected object $config;
    protected Player $player;
    protected Shopkeeper $entity;
    protected ElementContainer $container;

    function __construct(ConfigManager $cm) {
        $this->cm = $cm;
        $this->config = $this->cm->get()->{$this->cm->getSingleKey()};
        $this->container = new ElementContainer();
    }

    public function send(Player $player, Shopkeeper $entity) : void {
        $this->player = $player;
        $this->entity = $entity;
        foreach ($this->config->items as $itemconfig) {
            if (!(!empty($itemconfig->sell) && !empty($itemconfig->buy))) {
                continue;
            }
            $this->container->add($itemconfig->sell, $itemconfig->buy, $this->config->inventory, $this->config->admin);
        }
        
        $shop = new Shop($this->container->toNBT(), $player, $entity, $this->config->title);
        $shop->send();
    }
}