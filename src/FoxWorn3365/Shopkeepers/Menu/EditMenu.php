<?php

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
        $this->menu->setName("Edit shop {$this->config->title}");
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
            $item = @$this->config->items[$a];
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
            } else {
                $displayname = $itemconstructor->getVanillaName();
                // Load buy block and add to the description
                if (@$bk->buy !== null) {
                    $buy = SerializedItem::decode($bk->buy);
                    $setname = "{$buy->getCount()} {$buy->getName()}";
                } else {
                    $setname = "Nothing!";
                }
            }
            $itemconstructor->setCustomName("§r{$displayname}\nSold for: {$setname}");
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