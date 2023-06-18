<?php

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