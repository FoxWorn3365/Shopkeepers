<?php

namespace FoxWorn3365\Shopkeepers\entity;

use pocketmine\entity\Villager;
use pocketmine\entity\Location;
use pocketmine\entity\EntitySizeInfo;

class Shopkeeper extends Villager {
    public ?object $shopconfig;

    public function __construct(Location $loc, ?object $generalizedConfig = null) {
        parent::__construct($loc, null);

        $this->setCanSaveWithChunk(false);

        $this->shopconfig = $generalizedConfig;
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
}