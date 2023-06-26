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
use FoxWorn3365\Shopkeepers\utils\NbtManager;
use FoxWorn3365\Shopkeepers\ConfigManager;

use FoxWorn3365\Shopkeepers\entity\Shopkeeper;

class ShopInfoMenu {
    protected InvMenu $menu;
    protected ConfigManager $cm;
    protected object $config;
    protected bool $local;

    protected const NOT_PERM_MSG = "§cSorry but you don't have permissions to use this command!\nPlease contact your server administrator";

    function __construct(ConfigManager $cm, bool $local = false) {
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->cm = $cm;
        $this->config = $cm->get()->{$cm->getSingleKey()};
        $this->local = $local;
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
        $inventory->setItem(10, Factory::item(377, 0, '§lConfig'));

        // Villager inventory
        if (!$this->config->admin) {
            $inventory->setItem(12, Factory::item(54, 0, "§lInventory"));
        } else {
            $inventory->setItem(12, Factory::barrier("§l§cShop inventory\n§rDisabled!\n§oThis is an admin shop!"));  // ID: -161 Meta: 0 BRID: 10390
        }

        // Shop discounts announcer for v1.0
        $inventory->setItem(20, Factory::item(388, 0, "§o§lSales\n\n§r§oThis function will be implemented with the §bSales & Shops §r§oupdate AKA §lv1.0"));

        // Summon option
        $inventory->setItem(22, Factory::nbt("0a0000010005436f756e74010800044e616d65000f6d696e6563726166743a736b756c6c02000644616d616765000304000f504d4d504461746156657273696f6e000000000000000100", "§lSummon"));

        // Misteryous option 
        $inventory->setItem(24, Factory::barrier("§oUnknown\n\nThis function will be implemented with the §bSales & Shops §r§oupdate AKA §lv1.0"));

        // Edit Shopkeepers trades
        $st = Utils::getItem("minecraft:smithing_table");
        $st->setCustomName("§r§lTrades");
        $inventory->setItem(14, $st);

        $inventory->setItem(16, Factory::item(35, 14, "§c§lDelete"));

        $cm = $this->cm;
        $config = $this->config;
        $local = $this->local;

        $this->menu->setListener(function($transaction) use ($cm, $config, $local) {
            $slot = $transaction->getAction()->getSlot();
            switch ($slot) {
                case 10:
                    // Shop config
                    $menu = new ShopConfigMenu($cm);
                    $menu->create()->send($transaction->getPlayer());
                    break;
                case 12:
                    // Shop inventory
                    if (!$transaction->getPlayer()->hasPermission("shopkeepers.shop.allowRemoteInventoryOpen") && !$local) {
                        $transaction->getPlayer()->removeCurrentWindow();
                        $transaction->getPlayer()->sendMessage(self::NOT_PERM_MSG);
                        break;
                    }

                    if (!$config->admin) {
                        $menu = new ShopInventoryMenu($cm);
                        $menu->create()->send($transaction->getPlayer());
                    }
                    break;
                case 14:
                    if (!$transaction->getPlayer()->hasPermission("shopkeepers.shop.edit")) {
                        $transaction->getPlayer()->removeCurrentWindow();
                        $transaction->getPlayer()->sendMessage(self::NOT_PERM_MSG);
                        break;
                    }
                    $edit = new EditMenu($cm, $cm->getSingleKey());
                    $edit->create()->send($transaction->getPlayer());
                    break;
                case 16:
                    // F, we need to delete this
                    $cm->remove($cm->getSingleKey());
                    $transaction->getPlayer()->removeCurrentWindow();
                    $transaction->getPlayer()->sendMessage("Your shop named {$cm->getSingleKey()} has been §cdeleted§r with success!");
                    break;
                case 22:
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