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
 * Current file: /entity/Shopkeeper.php
 * Description: The plugin's custom entity, useful because of the local data saved in Shopkeeper::$shopconfig
 */

namespace FoxWorn3365\Shopkeepers\entity;

use pocketmine\entity\Villager;
use pocketmine\entity\Location;
use pocketmine\entity\EntitySizeInfo;

class Shopkeeper extends Villager {
    public ?object $shopconfig = null;
    public ?int $customShopkeeperEntityId = null;

    public function __construct(Location $loc, ?object $generalizedConfig = null, ?int $customId = null) {
        parent::__construct($loc, null);

        $this->setCanSaveWithChunk(false);

        $this->shopconfig = $generalizedConfig;
        $this->customShopkeeperEntityId = $customId;
    }

    public function getName(): string {
        return "Shop Villager";
    }

    public static function getNetworkTypeId(): string {
        return "minecraft:villager";
    }

    protected function getInitialSizeInfo() : EntitySizeInfo { 
        return new EntitySizeInfo(1.8, 0.6, 1.62); 
    }

    public function setConfig(object $config) : void {
        $this->shopconfig = $config;
    }
    
    public function getConfig() : object {
        return $this->shopconfig;
    }

    public function setCustomShopkeeperEntityId(int $id) : void {
        $this->setCustomShopkeeperEntityId = $id;
    }

    public function getCustomShopkeeperEntityId() : ?int {
        return $this->customShopkeeperEntityId;
    }

    public function hasCustomShopkeeperEntityId() : bool {
        if ($this->customShopkeeperEntityId === null) {
            return false;
        }
        return true;
    }
}