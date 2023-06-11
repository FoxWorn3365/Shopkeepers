<?php

namespace FoxWorn3365\Shopkeepers\Menu;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuTransactionResult;
use pocketmine\item\VanillaItems;

class EditMenu {
    protected InvMenu $menu;
    protected object $config;
    public string|int $id;
    public int $slot;

    function __construct(object $config, string $id, int $slot, string $dir) {
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->config = $config;
        $this->id = $id;
        $this->slot = $slot;
        $this->dir = $dir;
    }

    function edit() : void {
        $object = $this->config->items[$this->slot-9] ?? (array)['id' => null, 'price' => 1, 'count' => 1];
        $item = $object->id;
        $index = $this->slot-9;
        if ($item === null) {
            $item = VanillaItems::YELLOW_STAINED_GLASS_PANE();
            $item->setCustomName('Change the block at my right!');
            $item->setCount(1);
            $slot = 12;
        } else {
            $item->setCount($object->count);
            $slot = 13;
        }
        $this->menu->setTitle("Edit menu {$this->config->title}");
        $saveitem = VanillaItems::GREEN_STAINED_GLASS_PANE();
        $saveitem->setCustomName("§rSave the item");
        $this->menu->getInventory()->setItem(31, $saveitem);
        $this->menu->getInventory()->setItem($slot, $item);

        // Money part
        $money = VanillaItems::GRAY_STAINED_GLASS_PANE();
        $money->setName("§rPrice: {$object->price}$");
        $this->menu->getInventory()->setItem(9, $money);

        // Price increasator and decreasator
        $moneyincrease = VanillaItems::GREEN_STAINED_GLASS();
        $moneyincrease->setCustomName("§r+1$");
        $this->menu->getInventory()->setItem(1, $moneyincrease);
        $moneydecrease = VanillaItems::RED_STAINED_GLASS();
        $moneydecrease->setCustomName("§r-1$");
        $this->menu->getInventory()->setItem(19, $moneydecrease);

        // Qta increasator and decreasator
        $qtaincrease = VanillaItems::GREEN_STAINED_GLASS();
        $qtaincrease->setCustomName("§r+1");
        $this->menu->getInventory()->setItem(4, $qtaincrease);
        $qtadecrease = VanillaItems::RED_STAINED_GLASS();
        $qtadecrease->setCustomName("§r-1");
        $this->menu->getInventory()->setItem(22, $qatadecrease);

        $dir = $this->dir;
        $config = $this->config;

        $this->menu->setListener(function(InvMenuTransaction $transaction) use (&$object, $dir, $config, $index) : InvMenuTransactionResult {
            $item = $transaction->getItemClicked();
            if ($item->getName() == "§r+1") {
                $object->count++;
            } elseif ($item->getName() == "§r-1") {
                $object->count--;
            } elseif ($item->getName() == "§r-1$") {
                $object->price--;
            } elseif ($item->getName() == "§r+1$") {
                $object->price++;
            }
            $config->items[$index] = $object;
            file_put_contents($dir, json_encode($config));
            return $transaction->discard();
        });
    }
}