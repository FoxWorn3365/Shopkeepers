<?php

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
            $inventory->setItem($slotindex, Factory::item(388, 0, "{$name}\nStatus: §2Active"));
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