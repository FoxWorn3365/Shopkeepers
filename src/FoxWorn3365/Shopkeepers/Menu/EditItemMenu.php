<?php

namespace FoxWorn3365\Shopkeepers\Menu;

use muqsit\invmenu\InvMenu;
use pocketmine\item\VanillaItems;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use muqsit\invmenu\InvMenuTransactionResult;
use muqsit\invmenu\InvMenuTransaction;

use FoxWorn3365\Shopkeepers\Utils;

class EditItemMenu {
    protected InvMenu $menu;
    protected object $config;
    public string|int $id;
    public int $slot;
    public string $dir;

    function __construct(object $config, string $id, int $slot, string $dir) {
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->config = $config;
        $this->id = $id;
        $this->slot = $slot;
        $this->dir = $dir;
    }

    function edit() : InvMenu {
        $index = $this->slot-18;
        $object = @$this->config->items[$index];
        if ($object === null) {
            $object = (object)['id' => null, 'price' => 1, 'count' => 1];
        } else {
            $object = (object)$object;
        }

        $item = $object->id;
        if ($item === null) {
            $item = Utils::getIntItem(160, 4);
            $item->setCustomName('Change the block at my right!');
            $item->setCount(1);
            $slot = 12;
        } else {
            $item->setCount($object->count);
            $slot = 13;
        }
        $this->menu->setName("Edit shop {$this->config->title}");
        $saveitem = Utils::getIntItem(160, 13);
        $saveitem->setCustomName("§rSave the item");
        $this->menu->getInventory()->setItem(26, $saveitem);
        $this->menu->getInventory()->setItem($slot, $item);

        // Money part
        $money = Utils::getIntItem(160, 8);
        $money->setCustomName("§rPrice: {$object->price}$");
        $this->menu->getInventory()->setItem(10, $money);

        // Price increasator and decreasator
        $moneyincrease = Utils::getIntItem(35, 13);
        $moneyincrease->setCustomName("§r+1$");
        $this->menu->getInventory()->setItem(1, $moneyincrease);
        $moneydecrease = Utils::getIntItem(35, 14);
        $moneydecrease->setCustomName("§r-1$");
        $this->menu->getInventory()->setItem(19, $moneydecrease);

        // Qta increasator and decreasator
        $qtaincrease = Utils::getIntItem(35, 13);
        $qtaincrease->setCustomName("§r+1");
        $this->menu->getInventory()->setItem(4, $qtaincrease);
        $qtadecrease = Utils::getIntItem(35, 14);
        $qtadecrease->setCustomName("§r-1");
        $this->menu->getInventory()->setItem(22, $qtadecrease);

        $dir = $this->dir;
        $config = $this->config;

        $reservedslot = 13;

        $this->menu->setListener(function($transaction) use (&$object, $dir, $config, $index, $reservedslot) {
            $item = $transaction->getItemClicked();
            $action = $transaction->getAction();
            $change = "price";
            if ($item->getName() == "§r+1") {
                $object->count++;
            } elseif ($item->getName() == "§r-1") {
                $object->count--;
            } elseif ($item->getName() == "§r-1$") {
                $object->price--;
            } elseif ($item->getName() == "§r+1$") {
                $object->price++;
            } elseif ($action->getSlot() == $reservedslot && $action->getTargetItem() == null && $action->getSourceItem() != null) {
                $object->id = $action->getTargetItem()->getId();
                $object->meta = 0;
                $change = "object";
            }
            if ($change == "price") {
                $money = Utils::getIntItem(160, 8);
                $money->setCustomName("§rPrice: {$object->price}$");
                foreach ($transaction->getTransaction()->getInventories() as $inventory) {
                    $inventory->setItem(10, $money);
                    break;
                }
            } elseif ($change == "object") {
                $sellitem = Utils::getIntItem(160, 8);
                foreach ($transaction->getTransaction()->getInventories() as $inventory) {
                    $inventory->setItem($reservedslot, $sellitem);
                    break;
                }
            }
            $config->items[$index] = $object;
            file_put_contents($dir, json_encode($config));
            return $transaction->discard();
        });
        return $this->menu;
    }
}