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
 * Current file: /Menu/ShopInventoryMenu.php
 * Description: The shopkeeper's inventory. Easy
 */

namespace FoxWorn3365\Shopkeepers\Menu;

use muqsit\invmenu\InvMenu;
use pocketmine\inventory\Inventory;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use muqsit\invmenu\InvMenuTransactionResult;
use muqsit\invmenu\InvMenuTransaction;

use FoxWorn3365\Shopkeepers\utils\Utils;
use FoxWorn3365\Shopkeepers\utils\NbtManager;
use FoxWorn3365\Shopkeepers\utils\Factory;
use FoxWorn3365\Shopkeepers\utils\SerializedItem;
use FoxWorn3365\Shopkeepers\ConfigManager;

class ShopInventoryMenu {
    protected InvMenu $menu;
    protected ConfigManager $cm;
    protected object $config;

    function __construct(ConfigManager $cm) {
        $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $this->cm = $cm;
        $this->config = $cm->get()->{$cm->getSingleKey()};
    }

    public function create() : InvMenu {
        $this->menu->setName("§c§lInventory §r§l- §r{$this->cm->getSingleKey()}");
        $inventory = $this->menu->getInventory();
        // First, let's import the inventory
        foreach ($this->config->inventory as $slot => $item) {
            $inventory->setItem($slot, SerializedItem::decode($item));
        }

        // Now set the save item. It will be removed before saving the inventory so the slot 53 will be always free!
        $inventory->setItem(53, Factory::item(160, 5, "Save the Inventory"));

        $config = $this->config;
        $cm = $this->cm;

        $this->menu->setListener(function($transaction) use ($config, $cm) {
            $slot = $transaction->getAction()->getSlot();

            if ($slot < 53) {
                return $transaction->continue();
            }

            // Deny transaction because is the "SUS" element, then remove it and save the element
            return $transaction->discard()->then(static function(Player $player) use ($transaction, $cm, $config) {
                // et the current inventory via loop
                foreach ($transaction->getTransaction()->getInventories() as $inventory) {
                    if ($inventory->getSize() == 54) {
                        break;
                    }
                }

                $inventory->clear(53);

                // Save the inventory yee

                // BUT BEFORE lemme close the window
                $player->removeCurrentWindow();
    
                $items = [];
                for ($a = 0; $a < $inventory->getSize(); $a++) {
                    if (!$inventory->isSlotEmpty($a)) {
                        $items[$a] = NbtManager::encode($inventory->getItem($a));
                    }
                }
                
                $config->inventory = $items;
                $cm->set($cm->getSingleKey(), $config);
            });
        });

        // Oh shit, bro tried to close the inventory without save!
        $this->menu->setInventoryCloseListener(static function (Player $player, Inventory $inventory) use ($cm, $config) {
            // Save the inventory yee

            // The windows is closing, no needs to force the close!
            //$player->removeCurrentWindow();
                
            $inventory->clear(53);
            
            $items = [];
            for ($a = 0; $a < $inventory->getSize(); $a++) {
                if (!$inventory->isSlotEmpty($a)) {
                    $items[$a] = NbtManager::encode($inventory->getItem($a));
                }
            }
            
            $config->inventory = $items;
            $cm->set($cm->getSingleKey(), $config);
        });
        return $this->menu;
    }
}