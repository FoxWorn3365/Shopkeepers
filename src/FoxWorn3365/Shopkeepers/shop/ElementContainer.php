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
 * Current file: /shop/ElementContainer.php
 * Description: Add items as "trade recepies" and then return as NBT Tag (ListTag)
 */

namespace FoxWorn3365\Shopkeepers\shop;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

use FoxWorn3365\Shopkeepers\utils\NbtManager;

class ElementContainer {
    protected array $elements = [];
    public ListTag $nbt;

    public function add(Item|string $sell, Item|string $buy, array $inventory, bool $admin = false) : void {
        $tag = CompoundTag::create();

        // Simple tag management to optimize the speed. Anyways the system will give always a string so it's kida useless
        if ($buy instanceof Item) {
            $tag->setTag("buyA", $buy->nbtSerialize(-1));
        } else {
            $tag->setTag("buyA", NbtManager::partialDecode($buy));
        }

        if ($sell instanceof Item) {
            $tag->setTag("sell", $sell->nbtSerialize(-1));
        } else {
            $tag->setTag("sell", NbtManager::partialDecode($sell));
            // We need to load the item!
            $sell = NbtManager::decode($sell);
        }

        $tag->setInt("tier", 0);
        $tag->setInt("uses", 0);

        // If not an admin shop let's check the max qta
        if (!$admin) {
            // Load the inventory to see the max uses!
            $quota = 0;
            foreach ($inventory as $item) {
                $item = NbtManager::decode($item);
                if ($item->equals($sell)) {
                    // How much?
                    $quota = $quota + $item->getCount();
                }
            }

            // Setup the real qta
            $maxuses = $quota / $sell->getCount();
            if (strpos($maxuses, '.') !== false) {
                // Is not an int, let's remove the .
                $maxuses = (int)explode('.', (string)$maxuses)[0];
            }

            $tag->setInt("maxUses", $maxuses);
        } else {
            $tag->setInt("maxUses", 9999);
        }

        $tag->setInt("rewardExp", 0);
        $tag->setInt("demand", 1);
        $tag->setInt("traderExp", 0);
        $tag->setInt("priceMultiplierA", 0.0);
        $this->elements[] = $tag;
    }

    public function toNBT() : ListTag {
        $this->nbt = new ListTag($this->elements);
        return $this->nbt;
    }
}