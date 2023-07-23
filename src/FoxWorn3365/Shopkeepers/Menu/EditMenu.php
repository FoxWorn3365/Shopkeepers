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
 * Current file: /Menu/EditMenu.php
 * Description: Here is generated the trade edit menu where all 9 trades are listed
 */

namespace FoxWorn3365\Shopkeepers\Menu;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuTransactionResult;
use pocketmine\item\VanillaItems;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use muqsit\invmenu\InvMenuTransaction;
use pocketmine\nbt\tag\StringTag;

use FoxWorn3365\Shopkeepers\utils\Utils;
use FoxWorn3365\Shopkeepers\ConfigManager;
use FoxWorn3365\Shopkeepers\utils\ItemUtils;
use FoxWorn3365\Shopkeepers\utils\SerializedItem;

class EditMenu {
    protected InvMenu $menu;
    protected object $config;
    protected ConfigManager $cm;

    function __construct(ConfigManager $cm, string $name) {
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->cm = $cm;
        $this->config = $cm->get()->{$name};
        $cm->setSingleKey($name);
    }

    public function create() : InvMenu {
        $this->menu->setName("§l§bTrades §r§l- §r{$this->config->title}");
        // LAST SLOT: 26
        $slotcount = 9;
        $defaultconfig = (object)[
            'sell' => (object)[
                'count' => 1,
                'type' => 1,
                'id' => 160,
                'meta' => 8,
                'allowed' => true,
                'network' => null
            ],
            'buy' => null
        ];
        for ($a = 0; $a < 9; $a++) {
            if (gettype($this->config->items) !== 'array') {
                $item = null;
            } else {
                $item = @$this->config->items[$a];
            }

            if ($item === null) {
                $item = (object)$defaultconfig->sell;
            } else {
                $bk = clone $item;
                $item = @$item->sell;
            }

            if ($item !== null) {
                $itemconstructor = SerializedItem::decode($item);
            } else {
                $itemconstructor = SerializedItem::decode($defaultconfig->sell);
            }
            if ($itemconstructor->getVanillaName() == 'Stained Glass Pane') {
                $displayname = "No item set!";
                $setname = "Nothing";
                $displaycount = "";
            } else {
                $displayname = $itemconstructor->getVanillaName();
                $displaycount = $itemconstructor->getCount() . ' ';
                // Load buy block and add to the description
                if (@$bk->buy !== null) {
                    $buy = SerializedItem::decode($bk->buy);
                    $setname = "§l{$buy->getCount()} §r{$buy->getName()}";
                } else {
                    $setname = "Nothing!";
                }

                if (@$bk->buy2 !== null) {
                    $buy2 = SerializedItem::decode($bk->buy2);
                    $setname = "{$setname} and for §l{$buy2->getCount()} §r{$buy2->getName()}";
                }
            }
            $itemconstructor->setCustomName("§r§l{$displaycount}{$displayname}\n\n§r§oSold for:§r {$setname}");
            $itemmenu = Utils::getIntItem(35, 1);
            $itemmenu->setCustomName("§rEdit this item");
            $this->menu->getInventory()->setItem($slotcount, $itemconstructor);
            $this->menu->getInventory()->setItem($slotcount+9, $itemmenu);
            $slotcount++;
        }
        $cm = $this->cm;

        $this->menu->setListener(function($transaction) use ($cm) {
            $item = $transaction->getItemClicked();
            $slot = $transaction->getAction()->getSlot();
            if ($slot > 17) {
                if (str_replace(' ', '_', strtolower($item->getVanillaName())) !== 'stained_glass_pane') {
                    // Let's edit the item
                    $menu = new EditItemMenu($cm, str_replace(' ', '_', strtoupper($item->getVanillaName())), $slot);
                    $menu = $menu->edit();
                    $menu->send($transaction->getPlayer());
                }
            }
            return $transaction->discard();
        });
        return $this->menu;
    }
}