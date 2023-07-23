<?php

/*
 * Shopkeepers for PocketMine-MP
 * Add custom shopkeepers to your PocketMine-MP server!
 * 
 * Copyright (C) 2023-now FoxWorn3365
 * Relased under GNU General Public License v3.0 (https://github.com/FoxWorn3365/Shopkeepers/blob/main/LICENSE)
 * You can find the license file in the root folder of the project inside the LICENSE file!
 * If not, see https://www.gnu.org/licenses/
 * 
 * Useful links:
 * - GitHub: https://github.com/FoxWorn3365/Shopkeepers
 * - Contribution guidelines: https://github.com/FoxWorn3365/Shopkeepers#contributing
 * - Author GitHub: https://github.com/FoxWorn3365
 * 
 * Current file: /shop/Manager.php
 * Description: The "Shop Manager", implements and use the ElementContainer and Shop class to be easy.
 */

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
            if ($itemconfig === null) { continue; }
            if (!(!empty($itemconfig->sell) && !empty($itemconfig->buy)) && gettype($this->config->inventory) !== 'array') {
                continue;
            }
            $this->container->add($itemconfig->sell, @$itemconfig->buy, $this->config->inventory, $this->config->admin, @$itemconfig->buy2);
        }
        
        $shop = new Shop($this->container->toNBT(), $player, $entity, $this->config->title);
        $shop->send();
    }
}