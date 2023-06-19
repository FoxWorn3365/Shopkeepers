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
 * Description: Implements simple drawing tools like Paint (NOT 3D!!!)
 */

namespace FoxWorn3365\Shopkeepers\utils;

use pocketmine\item\Item;

use muqsit\invmenu\inventory\InvMenuInventory as Inventory;

final class Draw {
    public static function bucket(int $startx, int $starty, int $endx, int $endy, Inventory &$inventory, Item $item) : void {
        for ($y = $starty; $y < $endy+1; $y++) {
            for ($x = $startx; $x < $endx+1; $x++) {
                $slot = $y+9+$x;
                $inventory->setSlot($slot, $item);
            }
        }
    }

    public static function line(int $start, int $end, Inventory &$inventory, Item $item) : void {
        for ($x = $start; $x < $end+1; $x++) {
            $inventory->setItem($x, $item);
        }
    }
}