<?php

namespace FoxWorn3365\Shopkeepers\Menu;

use muqsit\invmenu\InvMenu;
use pocketmine\item\VanillaItems;
use pocketmine\item\ItemFactory;
use muqsit\invmenu\InvMenuTransactionResult;
use muqsit\invmenu\InvMenuTransaction;

use FoxWorn3365\Shopkeepers\utils\Utils;
use FoxWorn3365\Shopkeepers\utils\Draw;
use FoxWorn3365\Shopkeepers\utils\Factory;
use FoxWorn3365\Shopkeepers\ConfigManager;

class ShopInfoMenu {
    protected InvMenu $menu;
    protected ConfigManager $cm;
    protected object $config;

    protected const NOT_PERM_MSG = "§cSorry but you don't have permissions to use this command!\nPlease contact your server administrator";

    function __construct(ConfigManager $cm) {
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->cm = $cm;
        $this->config = $cm->get()->{$cm->getSingleKey()};
    }

    public function create() : InvMenu {
        $this->menu->setName("'{$this->cm->getSingleKey()}' shop - Info");
        $inventory = $this->menu->getInventory();

        // Draw the useful line
        Draw::line(0, 8, $inventory, Factory::item(160, 8, ""));

        // Now set the villager egg with name in the middle (slot 4)
        $inventory->clear(4);
        $inventory->setItem(4, Factory::item(388, 0, $this->cm->getSingleKey()));

        // Now set the informations
        $inventory->setItem(10, Factory::item(377, 0, 'Shop config'));

        // Villager inventory
        if (!$this->config->admin) {
            $inventory->setItem(13, Factory::item(54, 0, "Shop inventory"));
        }

        // Edit Shopkeepers trades
        $st = Utils::getItem("minecraft:smithing_table");
        $st->setCustomName("§rEdit shop trades");
        $inventory->setItem(16, $st);

        $cm = $this->cm;

        $this->menu->setListener(function($transaction) use ($cm) {
            $slot = $transaction->getAction()->getSlot();
            switch ($slot) {
                case 10:
                    // Shop config
                    $menu = new ShopConfigMenu($cm);
                    $menu->create()->send($transaction->getPlayer());
                    break;
                case 13:
                    // Shop inventory
                    if (!$transaction->getPlayer()->hasPermission("shopkeepers.shop.allowRemoteInventoryOpen")) {
                        $transaction->getPlayer()->removeCurrentWindow();
                        $transaction->getPlayer()->sendMessage(self::NOT_PERM_MSG);
                        break;
                    }
                    $menu = new ShopInventoryMenu($cm);
                    $menu->create()->send($transaction->getPlayer());
                    break;
                case 16:
                    if (!$transaction->getPlayer()->hasPermission("shopkeepers.shop.edit")) {
                        $transaction->getPlayer()->removeCurrentWindow();
                        $transaction->getPlayer()->sendMessage(self::NOT_PERM_MSG);
                        break;
                    }
                    $edit = new EditMenu($cm, $cm->getSingleKey());
                    $edit->create()->send($transaction->getPlayer());
                    break;
            }
            return $transaction->discard();
        });
        return $this->menu;
    }
}