<?php

namespace FoxWorn3365\Shopkeepers\Menu;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuTransactionResult;
use pocketmine\item\VanillaItems;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use muqsit\invmenu\InvMenuTransaction;

use FoxWorn3365\Shopkeepers\Utils;

class EditMenu {
    protected InvMenu $menu;
    protected object $config;
    protected string $dir;

    function __construct(object $config, string $dir) {
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->config = $config;
        $this->dir = $dir;
    }

    public function create() : InvMenu {
        $this->menu->setName("Edit shop {$this->config->title}");
        $saveitem = Utils::getIntItem(160, 13);
        $saveitem->setCustomName("§rSave the Store");
        $this->menu->getInventory()->setItem(26, $saveitem);
        $slotcount = 9;
        for ($a = 0; $a < 8; $a++) {
            var_dump($this->config);
            $item = @$this->config->items[$a];
            if ($item === null) {
                $item = (object)['id' => 160, 'meta' => 8, 'price' => 'ND', 'count' => 1];
            } else {
                $item = (object)$item;
            }

            $itemconstructor = Utils::getItem("{$item->id}:{$item->meta}");
            $itemconstructor->setCount($item->count);
            if ($itemconstructor->getVanillaName() == 'Stained Glass Pane') {
                $displayname = "No item set!";
            } else {
                $displayname = $itemconstructor->getVanillaName();
            }
            $itemconstructor->setCustomName("§r{$displayname}\nPrice: {$item->price}$");
            $itemmenu = Utils::getIntItem(35, 1);
            $itemmenu->setCustomName("§rEdit this item");
            $this->menu->getInventory()->setItem($slotcount, $itemconstructor);
            $this->menu->getInventory()->setItem($slotcount+9, $itemmenu);
            $slotcount++;
        }

        $config = $this->config;
        $dir = $this->dir;

        $this->menu->setListener(function($transaction) use ($config, $dir) {
            $item = $transaction->getItemClicked();
            $slot = $transaction->getAction()->getSlot();
            if (str_replace(' ', '_', strtolower($item->getVanillaName())) !== 'green_stained_glass_pane') {
                // Let's edit the item
                $menu = new EditItemMenu($config, str_replace(' ', '_', strtoupper($item->getVanillaName())), $slot, $dir);
                $menu = $menu->edit();
                $menu->send($transaction->getPlayer());
            }
            return $transaction->discard();
        });
        return $this->menu;
    }
}