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
 * Current file: /utils/Factory.php
 * STATIC CLASS
 * Description: Create various elements (as Items) in a single row
 */

namespace FoxWorn3365\Shopkeepers\utils;

use pocketmine\item\Item;
use pocketmine\block\VanillaBlocks;

// NBT utils for head
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;

// Network
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;

final class Factory {
    public static function sign(int $meta, string $text) : ?Item {
        $item = Utils::getIntItem(160, $meta);
        $item->setCustomName("§r{$text}");
        $item->setCount(1);
        return $item;
    }

    public static function item(int $id, int $meta, string $name, int $count = 1) : ?Item {
        $item = Utils::getIntItem($id, $meta);
        $item->setCustomName("§r{$name}");
        $item->setCount($count);
        return $item;
    }

    public static function rawItem(int $id, int $meta, string $name, int $count = 1) : ?Item {
        $item = Utils::getIntItem($id, $meta);
        $item->setCustomName($name);
        $item->setCount($count);
        return $item;
    }

    public static function egg(string $name, int $count = 1) : ?Item {
        $egg = ItemUtils::decode(451, 0, 0);
        $egg->setCustomName("§r{$name}");
        $egg->setCount($count);
        return $egg;
    }

    public static function barrier(string $name, int $count = 1) : ?Item {
        $barrier = ItemUtils::decode(-161, 0, 10390);
        $barrier->setCustomName("§r{$name}");
        $barrier->setCount($count);
        return $barrier;
    }

    public static function nbt(string $nbt, string $name, int $count = 1) {
        $item = NbtManager::decode($nbt);
        $item->setCustomName("§r{$name}");
        $item->setCount($count);
        return $item;
    }

    public static function itemStack(int $id, int $meta, int $netId, int $count = 1) : ItemStack {
        return new ItemStack($id, $meta, $count, $netId, new CompoundTag(), [], []);
    }
}