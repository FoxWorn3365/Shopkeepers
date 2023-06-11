<?php

namespace FoxWorn3365\Shopkeepers\Menu;

use muqsit\invmenu\InvMenu;
use pocketmine\item\VanillaItems;
use pocketmine\item\ItemFactory;
use muqsit\invmenu\InvMenuTransactionResult;
use muqsit\invmenu\InvMenuTransaction;

class CreateMenu {
    protected InvMenu $menu;
    protected $dir;

    function __construct(string $dir) {
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->dir = $dir . 'tts.json';
    }

    public function create() : InvMenu {
        $this->menu->setName("Create your shop!");
        $item = ItemFactory::getInstance()->get(160, 13);
        $item->setName("§rCreate the Shop");
        $this->menu->getInventory()->setItem(31, $item);

        $dir = $this->dir;
        $this->menu->setListener(function(InvMenuTransaction $transaction) use ($dir) : InvMenuTransactionResult {
            $item = $transaction->getItemClicked();
            if ($item->getName() == "§rCreate the Shop") {
                $mn = new EditMenu((object)['items' => ['id' => 257, 'meta' => 0, 'count' => 2, 'price' => 30]]);
                $mn->create()->send($transaction->getPlayer());
            }
            return $transaction->discard();
        });
        return $this->menu;
    }
}