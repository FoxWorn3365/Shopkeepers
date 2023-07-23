<?php

/*
 * Shopkeepers for PocketMine-MP
 * Add custom shopkeepers to your PocketMine-MP server!
 * 
 * Copyright (C) 2023-now FoxWorn3365
 * Relased under GNU General Public License v3.0 (https://github.com/FoxWorn3365/Shopkeepers/blob/main/LICENSE)
 * You can find the license file in the root folder of the project inside the LICENSE file!
 * If not, see https://www.gnu.org/licenses/
 * 
 * Useful links:
 * - GitHub: https://github.com/FoxWorn3365/Shopkeepers
 * - Contribution guidelines: https://github.com/FoxWorn3365/Shopkeepers#contributing
 * - Author GitHub: https://github.com/FoxWorn3365
 * 
 * Current file: /Menu/ShopConfigMenu.php
 * Description: Here the menu to configurate your shop is generated
 */

namespace FoxWorn3365\Shopkeepers\Menu;

use muqsit\invmenu\InvMenu;
use pocketmine\item\VanillaItems;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use muqsit\invmenu\InvMenuTransactionResult;
use muqsit\invmenu\InvMenuTransaction;
use muqsit\invmenu\inventory\InvMenuInventory;

use FoxWorn3365\Shopkeepers\utils\Utils;
use FoxWorn3365\Shopkeepers\utils\Draw;
use FoxWorn3365\Shopkeepers\utils\Factory;
use FoxWorn3365\Shopkeepers\ConfigManager;

class ShopConfigMenu {
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
        $this->menu->setName("§6§lConfig §r§l- §r{$this->cm->getSingleKey()}");
        $inventory = $this->menu->getInventory();

        // Draw the upper and downer line
        Draw::line(0, 8, $inventory, Factory::item(160, 8, ""));
        Draw::line(18, 26, $inventory, Factory::item(160, 8, ""));

        $inventory->clear(4);
        $inventory->setItem(4, Factory::egg($this->cm->getSingleKey() . "\n§oClick to return back!"));

        // Pass first option
        if ($this->config->namevisible) {
            $inventory->setItem(10, Factory::item(35, 5, "Shop's name Visible\nStatus: §2§lActive\n§r§oClick to disable!"));
        } else {
            $inventory->setItem(10, Factory::item(35, 14, "Shop's name Visible\nStatus: §4§lDisabled\n§r§oClick to active!"));
        }

        // Pass second option
        if ($this->config->admin) {
            $inventory->setItem(13, Factory::item(35, 5, "Admin shop\nStatus: §2§lActive\n§r§oClick to disable!"));
        } else {
            $inventory->setItem(13, Factory::item(35, 14, "Admin shop\nStatus: §4§lDisabled\n§r§oClick to active!"));
        }

        $inventory->setItem(16, Factory::item(339, 0, "Shop Name\nCurrent: {$this->cm->getSingleKey()}\nTo change use /sk rename <OLDNAME> <NEWNAME>!"));

        $config = $this->config;
        $cm = $this->cm;

        $this->menu->setListener(function($transaction) use ($config, $cm) {
            $slot = $transaction->getAction()->getSlot();

            foreach ($transaction->getTransaction()->getInventories() as $inventory) {
                if ($inventory instanceof InvMenuInventory) {
                    break;
                }
            }

            switch ($slot) {
                case 10:
                    // Change the config - edit the showname part
                    if (!$transaction->getPlayer()->hasPermission("shopkeepers.shop.namevisible")) {
                        $transaction->getPlayer()->removeCurrentWindow();
                        $transaction->getPlayer()->sendMessage(self::NOT_PERM_MSG);
                        break;
                    }

                    $inventory->clear(10);
                    if ($config->namevisible) {
                        $inventory->setItem(10, Factory::item(35, 14, "Shop's name Visible\nStatus: §4§lDisabled\n§r§oClick to active!"));
                        $config->namevisible = false;
                    } else {
                        $inventory->setItem(10, Factory::item(35, 5, "Shop's name Visible\nStatus: §2§lActive\n§r§oClick to disable!"));
                        $config->namevisible = true;
                    }
                    break;
                case 13:
                    // Change the config - edit the adminshop part
                    if (!$transaction->getPlayer()->hasPermission("shopkeepers.shop.admin")) {
                        $transaction->getPlayer()->removeCurrentWindow();
                        $transaction->getPlayer()->sendMessage(self::NOT_PERM_MSG);
                        break;
                    }

                    $inventory->clear(13);
                    if ($config->admin) {
                        $inventory->setItem(13, Factory::item(35, 14, "Admin shop\nStatus: §4§lDisabled\n§r§oClick to active!"));
                        $config->admin = false;
                    } else {
                        $inventory->setItem(13, Factory::item(35, 5, "Admin shop\nStatus: §2§lActive\n§r§oClick to disable!"));
                        $config->admin = true;
                    }
                    break;
                case 4:
                    // Return back to the ShopInfoMenu menu
                    $menu = new ShopInfoMenu($cm);
                    $menu->create()->send($transaction->getPlayer());
                    break;
            }

            $cm->set($cm->getSingleKey(), $config);

            return $transaction->discard();
        });
        return $this->menu;
    }
}