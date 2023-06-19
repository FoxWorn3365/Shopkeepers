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
 * Description: The simplest file: id:meta to item parser
 */

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