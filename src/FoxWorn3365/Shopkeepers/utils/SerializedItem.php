<?php

namespace FoxWorn3365\Shopkeepers\utils;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

final class SerializedItem {
    public static function serialize(Item $item) : string {
        return NbtManager::encode($item);
    }

    public static function deserialize(object|string $object) : Item {
        if (gettype($object) == 'string') {
            // It's a NBT, let's deserialize it!
            return NbtManager::decode($object);
        }

        if (@$object->networkitem === null) {
            $item = ItemUtils::typeDecode($object);
            if ($item instanceof Item) {
                return $item;
            }
            return VanillaItems::FLINT_AND_STEEL();
        }

        $item = ItemUtils::objectDecode($object->networkitem);
        if (@$object->customname !== null) {
            $item->setCustomName($object->customname);
        }

        $item->setNamedTag(self::jsonCompoundier($object->serializednbt));
        return $item;
    }

    public static function decode(object|string $object) : Item {
        return self::deserialize($object);
    }

    public static function encode(Item $item) : string {
        return self::serialize($item);
    }
}