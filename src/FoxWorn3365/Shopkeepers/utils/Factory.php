<?php

namespace FoxWorn3365\Shopkeepers\utils;

use pocketmine\item\Item;
use pocketmine\block\VanillaBlocks;

// NBT utils for head
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;

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
}