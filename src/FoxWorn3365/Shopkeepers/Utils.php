<?php

namespace FoxWorn3365\Shopkeepers;

use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\LegacyStringToItemParserException;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

class Utils {
    static function getItem(string $itemid) : ?Item {
		try {
			$item = LegacyStringToItemParser::getInstance()->parse(trim($itemid))->getItem();
			return $item->getBlock()->getStateId();
		} catch (LegacyStringToItemParserException) {
			return VanillaItems::FLINT_AND_STEEL();
		}
    }

    static function getIntItem(int $id, int $meta) : ?Item {
        $itemid = "{$id}:{$meta}";
        try {
			$item = LegacyStringToItemParser::getInstance()->parse(trim($itemid))->getItem();
			return $item->getBlock()->getStateId();
		} catch (LegacyStringToItemParserException) {
			return VanillaItems::FLINT_AND_STEEL();
		}
    }
}