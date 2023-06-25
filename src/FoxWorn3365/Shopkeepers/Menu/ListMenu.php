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
 * Current file: /Menu/ListMenu.php
 * Description: Do you have a shop? Then here are shown all of your shops (max 45)
 */

namespace FoxWorn3365\Shopkeepers\Menu;

use muqsit\invmenu\InvMenu;
use pocketmine\item\VanillaItems;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;

// Inv lib
use muqsit\invmenu\InvMenuTransactionResult;
use muqsit\invmenu\InvMenuTransaction;

// Custom
use FoxWorn3365\Shopkeepers\utils\Utils;
use FoxWorn3365\Shopkeepers\utils\Draw;
use FoxWorn3365\Shopkeepers\utils\Factory;
use FoxWorn3365\Shopkeepers\ConfigManager;

// WARNING: HARD SHOPS LIMIT: 45

class ListMenu {
    protected InvMenu $menu;
    protected ConfigManager $cm;
    protected Player $player;
    protected object $config;

    function __construct(Player $player, string $basedir) {
        $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $this->cm = new ConfigManager($player, $basedir);
        $this->config = $this->cm->get();
        $this->player = $player;
    }

    public function create() : InvMenu {
        $this->menu->setName("{$this->player->getName()}'s shops");
        $inventory = $this->menu->getInventory();

        // Draw the decoration line
        Draw::line(0, 8, $inventory, Factory::item(160, 8, ""));

        $nameassociations = [];
        
        $slotindex = 9;
        foreach ($this->config as $name => $config) {
            if ($slotindex > 44) {
                break;
            }
            $nameassociations[$slotindex] = $name;
            if ($config->admin) {
                $shop = "§2true";
            } else {
                $shop = "§4false";
            }
            $inventory->setItem($slotindex, Factory::egg("§l{$name}\n\n§lTrades: §r" . count($config->items) . "/9\n§lAdmin shop:§r {$shop}"));
            $slotindex++;
        }

        $cm = $this->cm;

        $this->menu->setListener(function($transaction) use ($nameassociations, $cm) {
            $slot = $transaction->getAction()->getSlot();
            if ($slot > 8) {
                if (@$nameassociations[$slot] !== null) {
                    $cm->setSingleKey($nameassociations[$slot]);
                    // Correct, let's open the info page of the villager
                    $menu = new ShopInfoMenu($cm);
                    $menu->create()->send($transaction->getPlayer());
                }
            }
            return $transaction->discard();
        });
        return $this->menu;
    }
}