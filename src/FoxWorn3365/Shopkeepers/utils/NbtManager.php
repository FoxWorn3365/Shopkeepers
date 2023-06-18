<?php

namespace FoxWorn3365\Shopkeepers\utils;

use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\Tag;
use pocketmine\item\Item;

final class NbtManager {
    public static function encode(Item $item) : string {
        return (new BigEndianNbtSerializer())->write(new TreeRoot($item->nbtSerialize(-1)));
    }

    public static function decode(string $nbt) : Item {
        return Item::nbtDeserialize((new BigEndianNbtSerializer())->read($nbt)->getTag());
    }

    public static function partialDecode(string $nbt) : CompoundTag {
        return (new BigEndianNbtSerializer())->read($nbt)->getTag();
    }
}