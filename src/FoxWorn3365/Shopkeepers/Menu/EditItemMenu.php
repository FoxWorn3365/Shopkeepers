<?php

namespace FoxWorn3365\Shopkeepers\Menu;

use pocketmine\item\VanillaItems;
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

        $sell = $object->sell;
        $buy = $object->buy;
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

        /*
        NO MORE MONEY MUAHAHAHAHAHA
        // Money part
        $money = Utils::getIntItem(160, 8);
        $money->setCustomName("§rPrice: {$object->price}$");
        $this->menu->getInventory()->setItem(10, $money);
        */

        // Buy qta increasator and decreasator
        $this->menu->getInventory()->setItem(1, Factory::item(35, 13, "+1"));
        $this->menu->getInventory()->setItem(19, Factory::item(35, 14, "-1"));

        // Sell qta increasator and decreasator
        $this->menu->getInventory()->setItem(4, Factory::item(35, 13, "+1"));
        $this->menu->getInventory()->setItem(22, Factory::item(35, 14, "-1"));

        // Put data
        $this->menu->getInventory()->setItem(10, $buyitem);
        $this->menu->getInventory()->setItem(13, $sellitem);

        // Buttons mapping for actions
        /*
        $buttons = [
            1 => 'buymore',
            19 => 'buyless',
            4 => 'sellmore',
            22 => 'sellless',
            17 => 'delete',
            10 => 'changebuy',
            13 => 'changesell'
        ];
        */

        $cm = $this->cm;
        $config = $this->config;

        $this->menu->setListener(function($transaction) use (&$object, $cm, $config, $index, $slot) {
            /*
            $object = @$cm->get()->{$cm->getSingleKey()}->items[$index];

            if ($object === null) {
                $object = new \stdClass;
            }

            if (@$object->sell === null) {
                $object->sell = new \stdClass;
                $object->sell->count = 1;
            }

            if (@$object->buy === null ) {
                $object->buy = new \stdClass;
                $object->buy->count = 1;
            }
            */
            // Nel caso il bro non si aggiornasse bene perché PHP con il & è stupido allora
            // ci toccherà importare nuovamente la configurazione qua :(
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
                case 7:
                    // Oh crap, we need to delete this!
                    $config->items[$index] = null;
                    unset($config->items[$index]);
                    $cm->set($cm->getSingleKey(), json_encode($config));
                    $retmenu = new EditMenu($cm, $cm->getSingleKey());
                    $retmenu->create()->send($transaction->getPlayer());
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
                case 4:
                    $item = $inventory->getItem(13);
                    if ($item->getCount()+1 > 64) {
                        $transaction->getPlayer()->sendMessage("§cYou can't sell more than 64 items!");
                    } else {
                        $item->setCount($item->getCount()+1);
                        $inventory->setItem(13, $item);
                        $object->sell = SerializedItem::encode($item);
                    }
                    break;
                case 22:
                    $item = $inventory->getItem(13);
                    if ($item->getCount()-1 < 1) {
                        $transaction->getPlayer()->sendMessage("§cYou can't sell less than 1 item!");
                    } else {
                        $item->setCount($item->getCount()-1);
                        $inventory->setItem(13, $item);
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
                case 13:
                    if ($transaction->getItemClickedWith() !== null && $transaction->getItemClickedWith() != VanillaItems::AIR()) {
                        // Let's change the object also in the inventory
                        $inventory->clear(13);
                        // Now let's decode the item
                        $object->sell = SerializedItem::encode($transaction->getItemClickedWith());
                        usleep(5000);
                        $inventory->setItem(13, $transaction->getItemClickedWith());
                    }
                    break;
            }

            // Finally, save the edited $object Object
            $config->items[$index] = $object;
            $cm->set($cm->getSingleKey(), $config);

            // Then, discard the transaction because we don't want to duplicate items!
            return $transaction->discard();
        });
        /*
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
                $translator = (new TypeConverter())->getItemTranslator()->toNetworkIdQuiet($transaction->getItemClickedWith());
                $object->id = $translator[0];
                $object->meta = $translator[1];
                var_dump($object);
                var_dump("NAME:", Utils::getIntItem($object->id, $object->meta)->getName());
                $change = "object";
            } else {
                $change = "nothing";
            }
            if ($change == "price") {
                $money = Utils::getIntItem(160, 8);
                $money->setCustomName("§rPrice: {$object->price}$");
                foreach ($transaction->getTransaction()->getInventories() as $inventory) {
                    if (!($inventory instanceof InvMenuInventory)) {
                        continue;
                    }
                    $inventory->setItem(10, $money);
                    break;
                }
            } elseif ($change == "object") {
                $sellitem = $transaction->getItemClickedWith();
                foreach ($transaction->getTransaction()->getInventories() as $inventory) {
                    if ($inventory instanceof InvMenuInventory) {
                        $inventory->clear($reservedslot);
                        usleep(250000);
                        $inventory->setItem($reservedslot, $sellitem);
                        //var_dump($inventory->getItem($reservedslot)->getName());
                        break;
                    }
                }
            } elseif ($change == 'count') {
                foreach ($transaction->getTransaction()->getInventories() as $inventory) {
                    if (!($inventory instanceof InvMenuInventory)) {
                        continue;
                    }
                    $item = $inventory->getItem($reservedslot);
                    $item->setCount($object->count);
                    $inventory->setItem($reservedslot, $item);
                    break;
                }
            }
            $config->items[$index] = $object;
            $cm->set($cm->getSingleKey(), $config);
            return $transaction->discard();
        });
        */
        return $this->menu;
    }
}