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
 * Current file: /Menu/ShopInfoMenu.php
 * Description: Shows the info of the shop with various options as a menu
 */

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

use FoxWorn3365\Shopkeepers\entity\Shopkeeper;

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
        $this->menu->setName("View shop {$this->cm->getSingleKey()}");
        $inventory = $this->menu->getInventory();

        // Draw the useful line
        Draw::line(0, 8, $inventory, Factory::item(160, 8, ""));

        // Now set the villager egg with name in the middle (slot 4)
        $inventory->clear(4);
        $inventory->setItem(4, Factory::egg($this->cm->getSingleKey()));

        // Now set the informations
        $inventory->setItem(10, Factory::item(377, 0, 'Shop config'));

        // Villager inventory
        if (!$this->config->admin) {
            $inventory->setItem(13, Factory::item(54, 0, "Shop inventory"));
        } else {
            $inventory->setItem(13, Factory::barrier("§cShop inventory\n§rDisabled!\n§oThis is an admin shop!"));  // ID: -161 Meta: 0 BRID: 10390
        }

        // Summon option
        $inventory->setItem(21, Factory::egg("Summon"));

        // Misteryous option 
        $inventory->setItem(23, Factory::barrier("§oUnknown\n\nv1.0"));

        // Edit Shopkeepers trades
        $st = Utils::getItem("minecraft:smithing_table");
        $st->setCustomName("§rEdit shop trades");
        $inventory->setItem(16, $st);

        $cm = $this->cm;
        $config = $this->config;

        $this->menu->setListener(function($transaction) use ($cm, $config) {
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
                case 21:
                    if (!$transaction->getPlayer()->hasPermission("shopkeepers.shop.summon")) {
                        $transaction->getPlayer()->removeCurrentWindow();
                        $transaction->getPlayer()->sendMessage(self::NOT_PERM_MSG);
                        break;
                    }
                    // Summon entity
                    $shopdata = new \stdClass;
                    $shopdata->author = $transaction->getPlayer()->getName();
                    $shopdata->shop = $cm->getSingleKey();
                    $villager = new Shopkeeper($transaction->getPlayer()->getLocation());
                    $villager->setNameTag($cm->getSingleKey());
                    $villager->setNameTagAlwaysVisible($config->namevisible);
                    $villager->setConfig($shopdata);
                    $villager->spawnToAll();
                    $transaction->getPlayer()->removeCurrentWindow();
                    break;
            }
            return $transaction->discard();
        });
        return $this->menu;
    }
}