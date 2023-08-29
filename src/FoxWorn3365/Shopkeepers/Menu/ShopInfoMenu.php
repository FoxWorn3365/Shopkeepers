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
use FoxWorn3365\Shopkeepers\EntityManager;
use FoxWorn3365\Shopkeepers\utils\SkinUtils;

use FoxWorn3365\Shopkeepers\entity\Shopkeeper;
use FoxWorn3365\Shopkeepers\entity\HumanShopkeeper;

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
        $this->menu->setName("§b§lInfo §r§l- §r{$this->cm->getSingleKey()}");
        $inventory = $this->menu->getInventory();

        // Draw the useful line
        Draw::line(0, 8, $inventory, Factory::item(160, 8, ""));

        // Now set the villager egg with name in the middle (slot 4)
        $inventory->clear(4);
        $inventory->setItem(4, Factory::egg($this->cm->getSingleKey()));

        // Now set the informations
        $inventory->setItem(10, Factory::item(377, 0, "§l§6Config\n\n§r§oSee settings for this Shopkeeper"));

        // Villager inventory
        if (!$this->config->admin) {
            $inventory->setItem(12, Factory::stringItem("minecraft:chest", "§l§9Inventory\n\n§r§oSee the inventory of the Shopkeeper"));
        } else {
            $inventory->setItem(12, Factory::barrier("§l§cShop inventory\n\n§rDisabled!\n§oThis is an admin shop!"));
        }

        // Shop discounts announcer for v1.0
        if (@!$this->config->enabled) {
            $inventory->setItem(20, Factory::stringItem("minecraft:torch", "§2§lEnable\n\n§r§oEnable this Shopkeeper. Yeeeee"));
        } else {
            $inventory->setItem(20, Factory::stringItem("minecraft:torch", "§4§lDisable\n\n§r§oDisable this Shopkeeper until a new order (yes, from you)"));
        }

        // Summon option
        $inventory->setItem(22, Factory::stringItem("minecraft:skull", "§l§8Summon\n\n§r§oSummon an entity for this shop.\n§e§oNOTE: §r§oIt will look in your current direction!"));

        // Misteryous option 
        $inventory->setItem(24, Factory::stringItem("minecraft:writable_book", "§b§lTrades History\n\n§r§oSee the trades history of this Shopkeeper"));

        // Edit Shopkeepers trades
        $inventory->setItem(14, Factory::stringItem("minecraft:smithing_table", "§d§lTrades\n\n§r§oView and edit the Shopkeeper's trades"));

        // Delete options
        $inventory->setItem(16, Factory::item(35, 14, "§c§lDelete\n\n§r§oThis Shopkeeper will be deleted §cFOREVER§r§o!"));

        // Slot 8: count
        $entitiesCount = EntityManager::getCountFromConfig($this->cm->basedir, $this->cm->player, $this->cm->getSingleKey());
        $inventory->setItem(8, Factory::stringItem("minecraft:comparator", "§8§lEntities\n\n§r§l{$entitiesCount} §rentities summoned."));

        $cm = $this->cm;
        $config = $this->config;
        $local = $this->local;

        $this->menu->setListener(function($transaction) use ($cm, &$config, $local) {
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
                    if (SkinUtils::find($cm->getSingleKey(), $transaction->getPlayer()->getName(), $transaction->getPlayer()->getServer()->getPluginManager()->getPlugin("Shopkeepers")->getDataFolder())) {
                        // Has a skin, let's summon an human entity after getting the skin
                        $skin = SkinUtils::get($cm->getSingleKey(), $transaction->getPlayer()->getName(), $transaction->getPlayer()->getServer()->getPluginManager()->getPlugin("Shopkeepers")->getDataFolder());
                        $villager = new HumanShopkeeper($transaction->getPlayer()->getLocation(), $skin, $shopdata);
                    } else {
                        // A simple Shopkeeper, so summon a villager-like entity
                        $villager = new Shopkeeper($transaction->getPlayer()->getLocation(), $shopdata);
                    }
                    $villager->setNameTag($cm->getSingleKey());
                    $villager->setNameTagAlwaysVisible($config->namevisible);
                    $villager->spawnToAll();
                    $transaction->getPlayer()->removeCurrentWindow();
                    break;
                case 20:
                    if (!$transaction->getPlayer()->hasPermission("shopkeepers.shop.enableDisable")) {
                        $transaction->getPlayer()->removeCurrentWindow();
                        $transaction->getPlayer()->sendMessage(self::NOT_PERM_MSG);
                        break;
                    }

                    if (@!$config->enabled) {
                        $config->enabled = true;
                    } else {
                        $config->enabled = false;
                    }

                    $cm->set($cm->getSingleKey(), $config);
                    $transaction->getPlayer()->removeCurrentWindow();
                    break;
                case 24:
                    if (!$transaction->getPlayer()->hasPermission("shopkeepers.shop.history")) {
                        $transaction->getPlayer()->removeCurrentWindow();
                        $transaction->getPlayer()->sendMessage(self::NOT_PERM_MSG);
                        break;
                    }

                    $transaction->getPlayer()->removeCurrentWindow();
                    $transaction->getPlayer()->sendMessage("For the complete history please use /sk history <SHOPKEEPER> [PAGE]");
                    $array = (array)json_decode(base64_decode($config->history));
                    if (count($array) > 20) {
                        $message = "§lLast 20 trades for this Shopkeeper:§r\n";
                        $count = count($array) - 20;
                    } else {
                        $message = "§lLast " . count($array) . " trades for this Shopkeeper:§r\n";
                        $count = 0;
                    }

                    for ($a = $count; $a < count($array); $a++) {
                        $item = $array[$a];
                        $message .= "\n{$item}";
                    }
                    $transaction->getPlayer()->sendMessage($message);
                    break;
            }
            return $transaction->discard();
        });
        return $this->menu;
    }
}