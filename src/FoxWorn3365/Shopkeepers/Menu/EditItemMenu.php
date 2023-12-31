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
 * Current file: /Menu/EditItemMenu.php
 * Description: Here is generated the menu to edit a specific trade, selected with /Menu/EditMenu.php
 */

namespace FoxWorn3365\Shopkeepers\Menu;

use pocketmine\item\VanillaItems;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;

// Pocketmine Network part
use pocketmine\network\mcpe\convert\TypeConverter;

// Inventory API (InvMenu)
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuTransactionResult;
use muqsit\invmenu\InvMenuTransaction;
use muqsit\invmenu\inventory\InvMenuInventory;

// Custom
use FoxWorn3365\Shopkeepers\utils\Utils;
use FoxWorn3365\Shopkeepers\utils\Factory;
use FoxWorn3365\Shopkeepers\utils\ItemUtils;
use FoxWorn3365\Shopkeepers\ConfigManager;
use FoxWorn3365\Shopkeepers\utils\SerializedItem;

class EditItemMenu {
    protected InvMenu $menu;
    protected ConfigManager $cm;
    protected object $config;
    public string|int $id;
    public int $slot;

    function __construct(ConfigManager $cm, string $id, int $slot) {
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->config = $cm->get()->{$cm->getSingleKey()};
        $this->id = $id;
        $this->slot = $slot;
        $this->cm = $cm;
    }

    function edit() : InvMenu {
        $index = $this->slot-18;
        if ($index < 0) {
            return $this->menu;
        }
        $object = @$this->config->items[$index];
        $defaultconfig = (object)[
            'sell' => null,
            'buy' => null
        ];
        if ($object === null) {
            $object = (object)$defaultconfig;
        } else {
            $object = (object)$object;
        }

        // Check for buy and sell


        // SLOT MAPPING
        // 13 => Reserved slot for sell item
        // 10 => Reserved slot for buy item
        // 12 => Semi-reserved slot for sign
        // 9 => Semi-reserved slot for sign
        $slot = new \stdClass;
        $slot->sell = 13;
        $slot->buy = 10;
        $slot->sellsign = 12;
        $slot->buysign = 9;

        $sell = @$object->sell;
        $buy = @$object->buy;
        $buy2 = @$object->buy2;
        $signsell = Factory::sign(4, 'Put what do you want to sell to my right!');
        $signbuy = Factory::sign(4, 'Put what do you want to buy to my right!');

        // Load the sell item
        if ($sell === null) {
            $sellitem = VanillaItems::AIR();
        } else {
            $sellitem = SerializedItem::decode($sell);
        }

        // Load the buy item
        if ($buy === null) {
            $buyitem = VanillaItems::AIR();
        } else {
            $buyitem = SerializedItem::decode($buy);
        }

        // Now load simple screen
        $this->menu->setName("Editing shop {$this->config->title}");
        $this->menu->getInventory()->setItem(17, Factory::item(160, 14, "Delete the item"));

        // Buy qta increasator and decreasator
        $this->menu->getInventory()->setItem(1, Factory::item(35, 13, "+1"));
        $this->menu->getInventory()->setItem(19, Factory::item(35, 14, "-1"));

        // 2nd buy qta increasator and decreasator
        $this->menu->getInventory()->setItem(2, Factory::item(35, 13, "+1"));
        $this->menu->getInventory()->setItem(20, Factory::item(35, 14, "-1"));

        // Sell qta increasator and decreasator
        $this->menu->getInventory()->setItem(5, Factory::item(35, 13, "+1"));
        $this->menu->getInventory()->setItem(23, Factory::item(35, 14, "-1"));

        //$this->menu->getInventory()->setItem(11, Factory::stringItem("minecraft:chest", "porcodio"));
        // Put data
        $this->menu->getInventory()->setItem(10, $buyitem);
        if ($buy2 !== null && SerializedItem::decode($buy2)->getName() !== "Air") {
            $this->menu->getInventory()->setItem(11, SerializedItem::decode($buy2));
        }
        $this->menu->getInventory()->setItem(14, $sellitem);

        $cm = $this->cm;
        $config = $this->config;

        $this->menu->setListener(function($transaction) use (&$object, $cm, $config, $index, $slot) {
            $item = $transaction->getItemClicked();
            $action = $transaction->getAction();

            // Get inventory with loop
            foreach ($transaction->getTransaction()->getInventories() as $inventory) {
                if ($inventory instanceof InvMenuInventory) {
                    break;
                }
            }

            // Now let's analyze the slot
            switch ($action->getSlot()) {
                case 17:
                    // Oh crap, we need to delete this!
                    $config->items[$index] = null;
                    $cm->set($cm->getSingleKey(), $config);
                    $retmenu = new EditMenu($cm, $cm->getSingleKey());
                    $retmenu->create()->send($transaction->getPlayer());
                    return $transaction->discard();
                    break;
                case 1:
                    $item = $inventory->getItem(10);
                    if ($item->getCount() + 1 > 64) {
                        $transaction->getPlayer()->sendMessage("§cYou can't sell an item for more than 64 items!");
                    } else {
                        $item->setCount($item->getCount()+1);
                        $inventory->setItem(10, $item);
                        $object->buy = SerializedItem::encode($item);
                    }
                    break;
                case 19:
                    $item = $inventory->getItem(10);
                    if ($item->getCount()-1 < 1) {
                        $transaction->getPlayer()->sendMessage("§cYou can't sell an item for less than 1 item!");
                    } else {
                        $item->setCount($item->getCount()-1);
                        $inventory->setItem(10, $item);
                        $object->buy = SerializedItem::encode($item);
                    }
                    break;
                case 2:
                    $item = $inventory->getItem(11);
                    if ($item->getName() === "Air") {
                        break;
                    }
                    if ($item->getCount() + 1 > 64) {
                        $transaction->getPlayer()->sendMessage("§cYou can't sell an item for more than 64 items!");
                    } else {
                        $item->setCount($item->getCount()+1);
                        $inventory->setItem(11, $item);
                        $object->buy2 = SerializedItem::encode($item);
                    }
                    break;
                case 20:
                    $item = $inventory->getItem(11);
                    if ($item->getName() === "Air") {
                        break;
                    }
                    if ($item->getCount()-1 < 1) {
                        $transaction->getPlayer()->sendMessage("§cYou can't sell an item for less than 1 item!");
                    } else {
                        $item->setCount($item->getCount()-1);
                        $inventory->setItem(11, $item);
                        $object->buy2 = SerializedItem::encode($item);
                    }
                    break;
                case 5: 
                    $item = $inventory->getItem(14);
                    if ($item->getCount()+1 > 64) {
                        $transaction->getPlayer()->sendMessage("§cYou can't sell more than 64 items!");
                    } else {
                        $item->setCount($item->getCount()+1);
                        $inventory->setItem(14, $item);
                        $object->sell = SerializedItem::encode($item);
                    }
                    break;
                case 23:
                    $item = $inventory->getItem(14);
                    if ($item->getCount()-1 < 1) {
                        $transaction->getPlayer()->sendMessage("§cYou can't sell less than 1 item!");
                    } else {
                        $item->setCount($item->getCount()-1);
                        $inventory->setItem(14, $item);
                        $object->sell = SerializedItem::encode($item);
                    }
                    break;
                case 10:
                    if ($transaction->getItemClickedWith() !== null && $transaction->getItemClickedWith() != VanillaItems::AIR()) {
                        // Let's change the object also in the inventory
                        $inventory->clear(10);
                        // Now let's decode the item
                        $object->buy = SerializedItem::encode($transaction->getItemClickedWith());
                        usleep(5000);
                        $inventory->setItem(10, $transaction->getItemClickedWith());
                    }
                    break;
                case 14:
                    if ($transaction->getItemClickedWith() !== null && $transaction->getItemClickedWith() != VanillaItems::AIR()) {
                        // Let's change the object also in the inventory
                        $inventory->clear(14);
                        // Now let's decode the item
                        $object->sell = SerializedItem::encode($transaction->getItemClickedWith());
                        usleep(5000);
                        $inventory->setItem(14, $transaction->getItemClickedWith());
                    }
                    break;
                case 11:
                    $presence = $inventory->getItem(10);
                    if (@$presence->getName() == "Air" || $presence === null) {
                        $transaction->getPlayer()->sendMessage("§4Sorry but you cannot cannot set the first buy item!");
                        usleep(2500);
                        $inventory->clear(11);
                        $object->buy2 = null;
                        break;
                    }

                    if ($transaction->getItemClickedWith() !== null && @$transaction->getItemClickedWith()->getVanillaName() != "Air") {
                        // Let's change the object also in the inventory
                        $inventory->clear(11);
                        // Now let's decode the item
                        $object->buy2 = SerializedItem::encode($transaction->getItemClickedWith());
                        usleep(5000);
                        $inventory->setItem(11, $transaction->getItemClickedWith());
                    } elseif ($transaction->getItemClickedWith() === null || @$transaction->getItemClickedWith()->getVanillaName() == "Air") {
                        $object->buy2 = null;
                        usleep(2500);
                        $inventory->clear(11);
                    } else {
                        var_dump($transaction->getItemClickedWith()->getBlock()->getName());
                    }
                    break;
            }

            // Finally, save the edited $object Object
            $config->items[$index] = $object;
            $cm->set($cm->getSingleKey(), $config);

            // Then, discard the transaction because we don't want to duplicate items!
            return $transaction->discard();
        });
        return $this->menu;
    }
}