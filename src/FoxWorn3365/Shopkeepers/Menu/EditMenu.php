<?php

namespace FoxWorn3365\Shopkeepers\Menu;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuTransactionResult;
use pocketmine\item\VanillaItems;

class EditMenu {
    protected InvMenu $menu;
    protected object $config;

    function __construct(object $config) {
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->config = $config;

    }

    public function create() : InvMenu {
        $this->menu->setTitle("Edit menu {$this->config->title}");
        $saveitem = VanillaItems::GREEN_STAINED_GLASS_PANE();
        $saveitem->setCustomName("§rSave the Store");
        $this->menu->getInventory()->setItem(31, $saveitem);
        $slotcount = 9;
        for ($a = 0; $a < 8; $a++) {
            $item = $this->config->items[$a] ?? (object)['id' => 'BLACK_STAINED_GLASS_PANE', 'price' => 'ND', 'count' => 1];
            $itemconstructor = VanillaItems::{$item->id}();
            $itemconstructor->setCount($item->count);
            $itemconstructor->setCustomName($itemconstructor->getVanillaName() . "\nPrice: {$item->price}$");
            $itemmenu = VanillaItems::BROWN_WOOL();
            $itemmenu->setCustomName("§rEdit this item");
            $this->menu->getInventory()->setItem($slotcount, $itemconstructor);
            $this->menu->getInventory()->setItem($slotcount+9, $itemmenu);
            $slotcount++;
        }

        $config = $this->config;
        $dir = $this->dir;

        $this->menu->setListener(function(InvMenuTransaction $transaction) use ($config, $dir): InvMenuTransactionResult {
            $item = $transaction->getItemClicked();
            $slot = $transaction->getAction()->getSlot();
            if (str_replace(' ', '_', strtolower($item->getVanillaName())) !== 'green_stained_glass_pane') {
                // Let's edit the item
                $menu = new EditItemManager($config, str_replace(' ', '_', strtoupper($item->getVanillaName())), $slot, $dir);
                $menu = $menu->edit();
                $menu->send($transaction->getPlayer());
            }
            return $transaction->discard();
        });
        return $this->menu;
    }
}