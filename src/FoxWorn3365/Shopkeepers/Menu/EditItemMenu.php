<?php

namespace FoxWorn3365\Shopkeepers\Menu;

use muqsit\invmenu\InvMenu;
use pocketmine\item\VanillaItems;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use muqsit\invmenu\InvMenuTransactionResult;
use muqsit\invmenu\InvMenuTransaction;

use FoxWorn3365\Shopkeepers\Utils;
use FoxWorn3365\Shopkeepers\ConfigManager;

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
            $item = Utils::getIntItem($object->id, $object->meta);
            $item->setCount($object->count);
            $slot = 13;
        }
        $this->menu->setName("Edit shop {$this->config->title}");
        $saveitem = Utils::getIntItem(160, 14);
        $saveitem->setCustomName("§rDelete the item");
        $this->menu->getInventory()->setItem(17, $saveitem);
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

        $cm = $this->cm;
        $config = $this->config;

        $reservedslot = 13;

        $this->menu->setListener(function($transaction) use (&$object, $cm, $config, $index, $reservedslot) {
            $item = $transaction->getItemClicked();
            $action = $transaction->getAction();
            if ($action->getSlot() == 17) {
                // Oh shit, we need to delete this!
                $config->items[$index] = null;
                unset($config->items[$index]);
                $cm->set($cm->getSingleKey(), json_encode($config));
                $retmenu = new EditMenu($cm, $cm->getSingleKey());
                $retmenu->create()->send($transaction->getPlayer());
            }
            $change = "nothing";
            if ($item->getName() == "§r+1") {
                if ($object->count +1 > 64) {
                    $transaction->getPlayer()->sendMessage("You can't sell more than 64 items!");
                } else {
                    $change = "count";
                    $object->count++;
                }
            } elseif ($item->getName() == "§r-1") {
                if ($object->count -1 <= 0) {
                    $transaction->getPlayer()->sendMessage("You can't sell 0 items!");
                } else {
                    $object->count--;
                    $change = 'count';
                }
            } elseif ($item->getName() == "§r-1$") {
                if ($object->price - 1 <= 0) {
                    $transaction->getPlayer()->sendMessage("You can't sell this item for 0$");
                } else {
                    $change = "price";
                    $object->price--;
                }
            } elseif ($item->getName() == "§r+1$") {
                $object->price++;
                $change = "price";
            } elseif ($action->getSlot() == $reservedslot && $action->getSourceItem() != null) {
                $object->id = $action->getTargetItem()->getTypeId();
                $object->meta = 0;
                $change = "object";
            } else {
                $change = "nothing";
            }
            if ($change == "price") {
                $money = Utils::getIntItem(160, 8);
                $money->setCustomName("§rPrice: {$object->price}$");
                foreach ($transaction->getTransaction()->getInventories() as $inventory) {
                    $inventory->setItem(10, $money);
                    break;
                }
            } elseif ($change == "object") {
                $sellitem = $action->getSourceItem();
                foreach ($transaction->getTransaction()->getInventories() as $inventory) {
                    $inventory->setItem($reservedslot, $sellitem);
                    break;
                }
            } elseif ($change == 'count') {
                foreach ($transaction->getTransaction()->getInventories() as $inventory) {
                    $item = $inventory->getItem($reservedslot);
                    $item->setCount($object->count);
                    $inventory->setItem($reservedslot, $item);
                    break;
                }
            }
            $config->items[$index] = $object;
            $cm->set($cm->getSingleKey(), json_encode($config));
            return $transaction->discard();
        });
        return $this->menu;
    }
}