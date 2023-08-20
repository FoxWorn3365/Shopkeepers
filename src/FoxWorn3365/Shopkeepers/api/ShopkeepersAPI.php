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
 * Current file: /api/ShopkeepersAPI.php
 * Description: Add an API support to interact with Shopkeepers
 */

namespace FoxWorn3365\Shopkeepers\api;

use pocketmine\player\Player;

use FoxWorn3365\Shopkeepers\Core;
use FoxWorn3365\Shopkeepers\ConfigManager;
use FoxWorn3365\Shopkeepers\EntityManager;

class ShopkeepersAPI {
    protected Core $plugin;
    protected EntityManager $entity;

    public function __construct(Core $plugin, EntityManager $entity) {
        $this->plugin = $plugin;
        $this->entity = $entity;
    }

    public function getConfigManager(Player $player) : ConfigManager {
        return new ConfigManager($player, $this->plugin->getDataFolder());
    }

    public function getConfig(Player $player, string $shopName) : object|array|bool {
        $data = (new ConfigManager($player, $this->plugin->getDataFolder()))->get();
        if (@$data->{$shopName} !== null) {
            return $data->{$shopName};
        }
        return false;
    }

    public function openTradeInventoryForPlayer(Player $player, string $shopOwner, string $shopName) : void {
        $this->server->dispatchCommand($player, "/sk trade {$shopOwner} {$shopName}");
    }

    public function setConfig(Player $player, string $shopName, object $config) : void {
        $manager = new ConfigManager($player, $this->plugin->getDataFolder());
        $manager->set($shopName, $config);
    }

    public function summonShopkeeper(Player $player, string $shopName) : void {
        $this->server->dispatchCommand($player, "/sk summon {$shopName}");
    }
}