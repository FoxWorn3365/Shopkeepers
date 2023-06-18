<?php

namespace FoxWorn3365\Shopkeepers\Menu;

use muqsit\invmenu\InvMenu;
use pocketmine\item\VanillaItems;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use muqsit\invmenu\InvMenuTransactionResult;
use muqsit\invmenu\InvMenuTransaction;

use FoxWorn3365\Shopkeepers\utils\Utils;
use FoxWorn3365\Shopkeepers\utils\Draw;
use FoxWorn3365\Shopkeepers\utils\Factory;

class InfoMenu {
    protected InvMenu $menu;

    function __construct() {
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
    }

    public function create(Player $player, string $basedir) : InvMenu {
        $this->menu->setName("Welcome to Shopkeepers");
        $inventory = $this->menu->getInventory();

        // Draw the upper and downer line
        Draw::line(0, 8, $inventory, Factory::item(160, 8, ""));
        Draw::line(18, 26, $inventory, Factory::item(160, 8, ""));

        // Now set the various options
        $inventory->setItem(10, Factory::item(339, 0, "Shop List")); // Item list option
        $inventory->setItem(16, Factory::item(35, 14, "Delete all Shops")); // Item list option
        $inventory->setItem(13, Factory::item(322, 1, "Author:\n§lFoxWorn3365")); // Author

        $this->menu->setListener(function($transaction) use ($player, $basedir) {
            $slot = $transaction->getAction()->getSlot();
            switch ($slot) {
                case 10:
                    // We must create a ListMenu menu so
                    $list = new ListMenu($player, $basedir);
                    $list->create()->send($player);
                    break;
                case 16:
                    @unlink("{$basedir}{$player->getName()}.json");
                    $player->sendMessage("Done, all shops of {$player->getName()} were eliminated!");
                    break;
            }
            return $transaction->discard();
        });
        return $this->menu;
    }
}