<?php

namespace FoxWorn3365\Shopkeepers\utils;

use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\LegacyStringToItemParserException;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

class Utils {
    static function getItem(string $itemid) {
		try {
			return LegacyStringToItemParser::getInstance()->parse(trim($itemid));
		} catch (LegacyStringToItemParserException) {
			return VanillaItems::FLINT_AND_STEEL();
		}
    }

    static function getIntItem(int $id, int $meta = 0) {
        $itemid = "{$id}:{$meta}";
        try {
			return LegacyStringToItemParser::getInstance()->parse(trim($itemid));
		} catch (LegacyStringToItemParserException) {
			return VanillaItems::FLINT_AND_STEEL();
		}
    }
}