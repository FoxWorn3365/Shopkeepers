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
 * Current file: /utils/NbtManager.php
 * STATIC CLASS
 * Description: Implements the v4 item encoding system (as NBT)
 */

namespace FoxWorn3365\Shopkeepers\utils;

use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\Tag;
use pocketmine\item\Item;

final class NbtManager {
    public static function encode(Item $item) : string {
        if (function_exists('zlib_encode')) {
            echo "OK";
            return bin2hex((new BigEndianNbtSerializer())->write(new TreeRoot($item->nbtSerialize(-1))));
        }
        return (new BigEndianNbtSerializer())->write(new TreeRoot($item->nbtSerialize(-1)));
    }

    public static function decode(string $nbt) : Item {
        if (function_exists('zlib_encode')) {
            $nbt = hex2bin($nbt);
        }
        return Item::nbtDeserialize((new BigEndianNbtSerializer())->read($nbt)->getTag());
    }

    public static function partialDecode(string $nbt) : CompoundTag {
        if (function_exists('zlib_encode')) {
            $nbt = hex2bin($nbt);
        }
        return (new BigEndianNbtSerializer())->read($nbt)->getTag();
    }
}