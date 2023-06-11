<?php

namespace FoxWorn3365\Shopkeepers\Menu;

use muqsit\invmenu\InvMenu;
use pocketmine\item\VanillaItems;

class CreateMenu {
    protected InvMenu $menu;

    function __construct(string $dir) {
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->dir = $dir;
    }

    public function create() : InvMenu {
        $this->menu->setName("Create your shop!");
        $item = VanillaItems::GREEN_STAINED_GLASS_PANE();
        $item->setName("§rCreate the Shop");
        $this->menu->setItem(31, $item);

        $dir = $this->dir;
        $this->menu->setListener(function(InvMenuTransaction $transaction) use ($dir) : InvMenuTransactionResult {
            $item = $transaction->getItemClicked();
            if ($item->getName() == "§rCreate the Shop") {
                $mn = new EditMenu((object)['items' => ['id' => 'iron_pickaxe', 'count' => 2, 'price' => 30]]);
                $mn->create()->send($transaction->getPlayer());
            }
        });
        return $this->menu;
    }
}