<?php

namespace FoxWorn3365\Shopkeepers\Menu;

use muqsit\invmenu\InvMenu;
use pocketmine\item\VanillaItems;
use pocketmine\item\ItemFactory;
use muqsit\invmenu\InvMenuTransactionResult;
use muqsit\invmenu\InvMenuTransaction;

use FoxWorn3365\Shopkeepers\Utils;

class CreateMenu {
    protected InvMenu $menu;
    protected $dir;

    function __construct(string $dir) {
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->dir = $dir . 'tts.json';
    }

    public function create() : InvMenu {
        $this->menu->setName("Create your shop!");
        $item = Utils::getIntItem(160, 13);
        $item->setCustomName("§rCreate the Shop");
        $this->menu->getInventory()->setItem(26, $item);

        $dir = $this->dir;
        $this->menu->setListener(function($transaction) use ($dir) {
            $item = $transaction->getItemClicked();
            if ($item->getName() == "§rCreate the Shop") {
                $mn = new EditMenu((object)['title' => 'jhbsdfbhjsdfu32', 'items' => [['id' => 257, 'meta' => 0, 'count' => 2, 'price' => 30]]], $dir);
                $mn->create()->send($transaction->getPlayer());
            }
            return $transaction->discard();
        });
        return $this->menu;
    }
}