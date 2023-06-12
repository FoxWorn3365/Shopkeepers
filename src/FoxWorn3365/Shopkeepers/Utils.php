<?php

namespace FoxWorn3365\Shopkeepers;

use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\LegacyStringToItemParserException;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

class Utils {
    static function getItem(string $itemid) {
		try {
			$item = LegacyStringToItemParser::getInstance()->parse(trim($itemid));
			return $item->getBlock()->getStateId();
		} catch (LegacyStringToItemParserException) {
			return VanillaItems::FLINT_AND_STEEL();
		}
    }

    static function getIntItem(int $id, int $meta) {
        $itemid = "{$id}:{$meta}";
        try {
			$item = LegacyStringToItemParser::getInstance()->parse(trim($itemid))->getBlock()->asItem();
			return $item->getBlock()->getStateId();
		} catch (LegacyStringToItemParserException) {
			return VanillaItems::FLINT_AND_STEEL();
		}
    }
}