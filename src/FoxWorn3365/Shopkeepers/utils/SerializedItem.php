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
 * Current file: /utils/Draw.php
 * STATIC CLASS
 * Description: The real v3 item encoding manager now used as v4 item encoding manager implementing NbtManager and allow retrocompatibility with object Items
 */

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