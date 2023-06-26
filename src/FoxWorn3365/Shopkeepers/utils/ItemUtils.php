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
 * Current file: /utils/ItemUtils.php
 * STATIC CLASS
 * Description: Original use: Manage the v3 item encoding system, now here only for retrocompatibility and some uses but kinda useless
 *            | Anyways manages the objectToItem actions
 */

namespace FoxWorn3365\Shopkeepers\utils;

use pocketmine\item\Item;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\item\StringToItemParser;

class ItemUtils {
    public static final function encode(Item $item, bool $toObject = true) : array|object|null {
        trigger_error("Encoding item with ItemUtils::encode is deprecated! Use SerializedItem::encode to encode correctly!\nThrow in ItemUtils.php:10, item {$item->getName()}", E_USER_DEPRECATED);
        if ($toObject) {
            $ret = new \stdClass;
            $data = (new TypeConverter())->getItemTranslator()->toNetworkIdQuiet($item);
            $ret->id = $data[0];
            $ret->meta = $data[1];
            $ret->network = $data[2];
            return $ret;
        }
		return (new TypeConverter())->getItemTranslator()->toNetworkIdQuiet($item);
	}

    public static final function decode(int $id, int $meta, int $network) : ?Item {
        return (new TypeConverter())->netItemStackToCore(Factory::itemStack($id, $meta, $network));
    }

    public static final function objectDecode(object $object) : ?Item {
        return self::decode($object->id, $object->meta, $object->network);
    }

    public static final function typeDecode(object $object) : ?Item {
        if (@$object->allowed != true) { trigger_error("Decoding item with ItemUtils::typeDecode is deprecated! Use SerializedItem::decode to decode correctly!\nThrow in ItemUtils.php:31 - WARNING: This error can be show also for a inside plugin error - PLEASE UPGRADE TO SerializedItem SCHEMA!", E_USER_DEPRECATED); }
        if (@$object->type === null) { $object->type = 1; }
        if ($object->type === 1) {
            return Utils::getIntItem($object->id, $object->meta);
        } else {
            return self::objectDecode($object);
        }
    }

    public static final function stringParser(string $string) : ?Item {
        return (new StringToItemParser())->parse($string);
    }

    public static function getId(Item $item) : int {
        $translator = (new TypeConverter())->getItemTranslator();
        return $translator->toNetworkIdQuiet($item)[0];
    }

    public static function getMeta(Item $item) : int {
        $translator = (new TypeConverter())->getItemTranslator();
        return $translator->toNetworkIdQuiet($item)[1];
    }
}