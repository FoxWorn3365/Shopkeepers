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
 * Current file: /Core.php
 * Description: The core of the plugin, manage all events and commands
 */

declare(strict_types=1);

namespace FoxWorn3365\Shopkeepers;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use muqsit\invmenu\InvMenuHandler;
use muqsit\invmenu\InvMenu;
use pocketmine\item\Item;
use pocketmine\command\Command;
use pocketmine\command\PluginCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Server;
use pocketmine\level\Position;

// Events
use pocketmine\event\entity\EntityDamageEvent as Damage;
use pocketmine\event\player\PlayerEntityInteractEvent as Interaction;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\DataPacketReceiveEvent;

// Packets
use pocketmine\network\mcpe\protocol\ActorEventPacket as EntityEventPacket;
use pocketmine\network\mcpe\protocol\ItemStackRequestPacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;

// Custom
use FoxWorn3365\Shopkeepers\Menu\InfoMenu;
use FoxWorn3365\Shopkeepers\Menu\ShopInventoryMenu;
use FoxWorn3365\Shopkeepers\Menu\EditMenu;
use FoxWorn3365\Shopkeepers\Menu\ShopInfoMenu;
use FoxWorn3365\Shopkeepers\entity\Shopkeeper;
use FoxWorn3365\Shopkeepers\shop\Manager;
use FoxWorn3365\Shopkeepers\utils\NbtManager;
use FoxWorn3365\Shopkeepers\utils\Utils;

// Newtork Parts
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\ItemStackRequest;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\PlaceStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\CraftingConsumeInputStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\DeprecatedCraftingResultsStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use pocketmine\network\mcpe\convert\TypeConverter;

class Core extends PluginBase implements Listener {
    protected object $menu;
    protected EntityManager $entities;
    protected object $trades;
    protected object $tradeQueue;

    protected const NOT_PERM_MSG = "§cSorry but you don't have permissions to use this command!\nPlease contact your server administrator";
    protected const AUTHOR = "FoxWorn3365";
    protected const VERSION = "0.7.0-beta";

    public function onLoad() : void {
        $this->menu = new \stdClass;
        $this->trades = new \stdClass;
        $this->tradeQueue = new \stdClass;
    }

    public function onEnable() : void {
		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}

        $this->entities = new EntityManager($this->getDataFolder());

        // Create the config folder if it does not exists
        @mkdir($this->getDataFolder());

        // Check for file integrity
        Utils::integrityChecker($this->getDataFolder());

        // Register event listener
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        // Cache all entities (they're only a 1kb max strings) to permit the loading for all players
        $this->entities->cache($this->getServer());
    }

    public function onPlayerJoin(PlayerJoinEvent $event) {
        // Load all cached entities
        $this->entities->loadPlayer($event->getPlayer());

        // Create the player's object queue for managing event procession
        $this->tradeQueue->{$event->getPlayer()->getName()} = false;
    }

    public function onPlayerEntityInteract(Interaction $event) : void {
        //$menu = new CreateMenu($this->getDataFolder());
        //$menu->create()->send($event->getPlayer());
        if (!$event->getPlayer()->hasPermission("shopkeepers.shop.use")) {
            $event->getPlayer()->sendMessage(self::NOT_PERM_MSG);
        }
        $entity = $event->getEntity();
        if ($entity instanceof Shopkeeper) {
            $data = $entity->getConfig();
            if ($data->author === $event->getPlayer()->getName() && !$event->getPlayer()->isSneaking()) {
                // Open the shopkeeper's inventory RN!
                $cm = new ConfigManager($data->author, $this->getDataFolder());
                $cm->setSingleKey($data->shop);
                $menu = new ShopInventoryMenu($cm);
                $menu->create()->send($event->getPlayer());
            } else {
                // It's a shopkeeper!
                // BEAUTIFUL!
                // Now let's open the shopkeeper interface
                $cm = new ConfigManager($data->author, $this->getDataFolder());
                $cm->setSingleKey($data->shop);
                $manager = new Manager($cm);
                $this->trades->{$event->getPlayer()->getName()} = new \stdClass;
                $this->trades->{$event->getPlayer()->getName()}->config = $data;
                $manager->send($event->getPlayer(), $entity);
            }
        }
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) : bool{
        if (!($sender instanceof Player)) {
            $sender->sendMessage("This command can be only executed by in-game players!");
            return false;
        }
        $shop = new ConfigManager($sender, $this->getDataFolder());

        // Empty treath
        if ($args == []) {
            $args[0] = "";
            $args[1] = "";
            $args[2] = "";
        }

        if ($args == null) {
            $args[0] = "";
            $args[1] = "";
            $args[2] = "";
        }

        if ($args[0] == "list") {
            if (!$sender->hasPermission("shopkeepers.shop.list")) {
                $sender->sendMessage(self::NOT_PERM_MSG);
            }

            if ($shop->is()) {
                $list = "";
                foreach ($shop->get() as $title => $item) {
                    $list .= "\n- {$title}";
                }
                $sender->sendMessage("Your shops: {$list}");
                return true;
            } else {
                $sender->sendMessage("You don't have any shop(s) here!");
            }
        } elseif ($args[0] == "create") {
            if (!$sender->hasPermission("shopkeepers.shop.create")) {
                $sender->sendMessage(self::NOT_PERM_MSG);
            }

            if (empty($name = $args[1])) {
                $name = $this->generateRandomString(7);
            }
            // Create the config 
            // OOOO why are u running? before, check if there's also an existing name
            if (@$shop->get()?->{$name} !== null) {
                $sender->sendMessage("You already have a shop called {$name}!");
                return false;
            }
            $newshop = new \stdClass;
            $newshop->title = $name;
            $newshop->owner = $sender->getName();
            $newshop->admin = false;
            $newshop->namevisible = true;
            $newshop->items = [];
            $newshop->inventory = [];
            $shop->set($name, $newshop);
            $menu = new EditMenu($shop, $name);
            $menu->create()->send($sender);
            return true;
        } elseif ($args[0] == "edit") {
            if (!$sender->hasPermission("shopkeepers.shop.edit")) {
                $sender->sendMessage(self::NOT_PERM_MSG);
            }

            $name = $args[1];
            if (@$shop->get()?->{$name} === null) {
                $sender->sendMessage("You don't have a shop called {$name}!");
                return false;
            }
            // Let's open the edit interface
            $menu = new EditMenu($shop, $name);
            $menu->create()->send($sender);
            return true;
        } elseif ($args[0] == "info" && !empty($args[1])) {
            // Get info of a shopkeeper with his page (Menu\ShopInfoMenu)
            //$sender->sendMessage("Shopkeepers for PMMP by FoxWorn3365\nGitHub: https://github.com/FoxWorn3365/Shopkeepers");
            $name = $args[1];
            if (@$shop->get()?->{$name} === null) {
                $sender->sendMessage("You don't have a shop called {$name}!");
                return false;
            }
            $shop->setSingleKey($name);
            $menu = new ShopInfoMenu($shop); 
            $menu->create()->send($sender);
            return true;
        } elseif ($args[0] == "info" && empty($args[1])) {
            $sender->sendMessage("§lShopkeepers v" . self::VERSION . " by " . self::AUTHOR . "\n\n§lGitHub: §rhttps://github.com/FoxWorn3365/Shopkeepers\n§lAuthor's GitHub: §rhttps://github.com/FoxWorn3365\n\nYou can contact me on §3§lDiscord§r: §l@foxworn");
            return true;
        } elseif ($args[0] == "summon") {
            if (!$sender->hasPermission("shopkeepers.shop.summon")) {
                $sender->sendMessage(self::NOT_PERM_MSG);
            }

            $name = $args[1];
            if (@$shop->get()?->{$name} === null) {
                $sender->sendMessage("You don't have a shop called {$name}!");
                return false;
            }
            // Let's summon a villager with these data
            $pos = $sender->getLocation();
            $shopdata = new \stdClass;
            $shopdata->author = $sender->getName();
            $shopdata->shop = $name;
            $villager = new Shopkeeper($pos);
            $villager->setNameTag($name);
            $villager->setNameTagVisible($shop->get()->{$name}->namevisible);
            $villager->setConfig($shopdata);
            $villager->spawnToAll();
            $this->entities->add($villager);
            return true;
        } elseif ($args[0] == "remove" || $args[0] == "despawn") {
            $sender->sendMessage("To remove a shopkeeper just hit it!");
            return true;
        } elseif (empty($args[0])) {
            if (!$sender->hasPermission("shopkeepers.shop.defaultGUI")) {
                $sender->sendMessage(self::NOT_PERM_MSG);
            }

            $menu = new InfoMenu();
            $menu->create($sender, $this->getDataFolder())->send($sender);
            return true;
        } else {
            if (!$sender->hasPermission("shopkeepers.shop.defaultGUI")) {
                $sender->sendMessage(self::NOT_PERM_MSG);
            }

            $menu = new InfoMenu();
            $menu->create($sender, $this->getDataFolder())->send($sender);
        }
        return false;
    }

    public function onPacket(DataPacketReceiveEvent $event) : void {
        $ic = new \stdClass; // We need to pass ONLY SOME REQUESTS and with a defined order!
        $ic->crafting = false;
        $ic->slot = false;
        $ic->cconsume = false;
        $ic->specialstack = false;
        $cm = null;
        $quota = 0;
        $itemglobal = null;
        $inventoryInsideConfig = null;
        if ($event->getPacket() instanceof ItemStackRequestPacket) {
            /*
            Debugging things
            print_r($event->getPacket());
            var_dump($event->getOrigin()->getPlayer()->getCurrentWindow());
            print_r($event->getOrigin()->getPlayer()->getName());
            */
            $inventory = $event->getOrigin()->getPlayer()->getInventory();

            foreach ($event->getPacket()->getRequests() as $request) {
                if ($request instanceof ItemStackRequest) {
                    foreach ($request->getActions() as $action) {
                        // For some reason from the action object the source slot id (getSource()->getSlot() is normalslot+9), so the first slot is 9 bruh
                        if ($action instanceof DeprecatedCraftingResultsStackRequestAction) {
                            if ($this->tradeQueue->{$event->getOrigin()->getPlayer()->getName()}) {
                                return; // the request can't be processed because there's another request to be processed
                            }
                            $this->tradeQueue->{$event->getOrigin()->getPlayer()->getName()} = true;
                            if (@$this->trades->{$event->getOrigin()->getPlayer()->getName()} !== null) {
                                $this->trades->{$event->getOrigin()->getPlayer()->getName()}->items = [];
                                // Save the config manager
                                $cm = new ConfigManager($this->trades->{$event->getOrigin()->getPlayer()->getName()}->config->author, $this->getDataFolder());
                                $cm->setSingleKey($this->trades->{$event->getOrigin()->getPlayer()->getName()}->config->shop);
                                if (!$ic->crafting && !$ic->cconsume && !$ic->slot) {
                                    $ic->crafting = true;
                                    foreach ($action->getResults() as $result) {
                                        if ($result instanceof ItemStack) {
                                            if ($event->getOrigin()->getPlayer()->getInventory()->firstEmpty() == -1) {
                                                $event->getOrigin()->getPlayer()->sendMessage("§cYour inventory is full!");
                                                return;
                                            } else {
                                                $translator = (new TypeConverter())->getItemTranslator();
                                                $item = $translator->fromNetworkId($result->getId(), $result->getMeta(), $result->getBlockRuntimeId());
                                                $item->setCount($result->getCount());
                                                // Before set this we need to check and update the villager's inventory
                                                $total = $result->getCount();
                                                if (!$cm->get()->{$cm->getSingleKey()}->admin) {
                                                    $inventoryInsideConfig = $cm->get()->{$cm->getSingleKey()}->inventory;
                                                    foreach ($inventoryInsideConfig as $slot => $i) {
                                                        $inventoryItem = NbtManager::decode($i);
                                                        if ($inventoryItem->equals($item)) {
                                                            if ($inventoryItem->getCount() > $total) {
                                                                // Remove the $total from the count and we're back to go!
                                                                $inventoryItem->setCount($inventoryItem->getCount()-$total);
                                                                $total = 0;
                                                                $inventoryInsideConfig[$slot] = NbtManager::encode($inventoryItem);
                                                                // Now exit, the item earned will be added next!
                                                                break;
                                                            } elseif ($inventoryItem->getCount() <= $total) {
                                                                // Simple: remove the item!
                                                                // Ez
                                                                $inventoryInsideConfig[$slot] = null;
                                                                unset($inventoryInsideConfig[$slot]);
                                                                if ($inventoryItem->getCount() <= $total) {
                                                                    break;
                                                                } else {
                                                                    $total = $total - $inventoryItem->getCount();
                                                                }
                                                            }
                                                        }
                                                    }
    
                                                    if ($total > 0) {
                                                        return;
                                                    }
    
                                                    $object = $cm->get()->{$cm->getSingleKey()};
                                                    $object->inventory = $inventoryInsideConfig;
                                                    $cm->set($cm->getSingleKey(), $object);
                                                }
                                                $this->trades->{$event->getOrigin()->getPlayer()->getName()}->items[] = $item;
                                                // Remove this item from the entity's inventory
                                                //$itemglobal = $item;
                                            }
                                        }
                                    }
                                }
                            }
                        } elseif ($action instanceof CraftingConsumeInputStackRequestAction) {
                            if (!$ic->cconsume && $ic->crafting && !$ic->slot) {
                                $ic->cconsume = true;
                                $this->trades->{$event->getOrigin()->getPlayer()->getName()}->quota = $action->getCount();
                            }
                        } elseif ($action instanceof PlaceStackRequestAction) {
                            if (!$ic->slot && $ic->crafting && $ic->cconsume && $cm instanceof ConfigManager) {
                                $ic->slot = true;
                                $dest = $action->getDestination()->getSlotId();
                                // Put cctr to slot
                                if (@$this->trades->{$event->getOrigin()->getPlayer()->getName()} !== null) {
                                    $data = $this->trades->{$event->getOrigin()->getPlayer()->getName()};
                                    $quota = $this->trades->{$event->getOrigin()->getPlayer()->getName()}->quota;
                                    $referredItem = clone $data->item;
                                    if ($quota <= 0) {
                                        // Error on qta
                                        return;
                                    }
                                    if ($data->count > 1) {
                                        $this->trades->{$event->getOrigin()->getPlayer()->getName()}->count = $this->trades->{$event->getOrigin()->getPlayer()->getName()}->count - $quota;
                                        $data->item->setCount($data->count - $quota);
                                        foreach ($this->trades->{$event->getOrigin()->getPlayer()->getName()}->items as $item) {
                                            $event->getOrigin()->getPlayer()->getInventory()->addItem($item);
                                        }
                                        $first = $event->getOrigin()->getPlayer()->getInventory()->first($data->item);
                                        if ($first > -1) {
                                            $item = $event->getOrigin()->getPlayer()->getInventory()->getItem($first);
                                            $item->setCount($item->getCount() - $quota);
                                            $event->getOrigin()->getPlayer()->getInventory()->setItem($first, $item);
                                        } elseif ($first == -1 && $data->item->getCount() !== 0) {
                                            $item = $data->item;
                                            $item->setCount($item->getCount() - $quota);
                                            $event->getOrigin()->getPlayer()->getInventory()->addItem($item);
                                        }
                                        // Now add the earned to the Shopkeeper's inventory!
                                        $count = $quota;
                                        $posed = false;
                                        $inventoryInsideConfig = $cm->get()->{$cm->getSingleKey()}->inventory;
                                        if (!$cm->get()->{$cm->getSingleKey()}->admin) {
                                            foreach ($inventoryInsideConfig as $slot => $indexedItem) {
                                                $indexedItem = NbtManager::decode($indexedItem);
                                                if ($indexedItem->equals($referredItem)) {
                                                    // Add if is not 64
                                                    if ($indexedItem->getCount() + $count <= 64) {
                                                        // Perfect, add and exit!
                                                        $indexedItem->setCount($indexedItem->getCount() + $count);
                                                        $inventoryInsideConfig[$slot] = NbtManager::encode($indexedItem);
                                                        $posed = true;
                                                        $count = 0;
                                                        break;
                                                    } elseif ($indexedItem->getCount() + $count > 64) {
                                                        // Add what we can
                                                        $count = $count - (64 - $indexedItem->getCount());
                                                        $indexedItem->setCount(64);
                                                        $inventoryInsideConfig[$slot] = NbtManager::encode($indexedItem);
                                                    }
                                                }
                                            }
    
                                            if (!$posed && $count <= 64 && $count > 0) {
                                                for ($a = 0; $a < 51; $a++) {
                                                    if (empty($inventoryInsideConfig[$a])) {
                                                        $referredItem->setCount($count);
                                                        $inventoryInsideConfig[$a] = NbtManager::encode($referredItem);
                                                        break;
                                                    }
                                                }
                                            }
    
                                            $config = $cm->get()->{$cm->getSingleKey()};
                                            $config->inventory = $inventoryInsideConfig;
                                            $cm->set($cm->getSingleKey(), $config);
                                        }
                                    }  
                                } else {
                                    $event->getOrigin()->getPlayer()->sendMessage("§cError!\nIt seems that you are not trading rn!");
                                }
                            } else {
                                if (!$ic->specialstack) {
                                    $ic->specialstack = true;
                                    if (@$this->trades->{$event->getOrigin()->getPlayer()->getName()} !== null && $action->getSource()->getContainerId() != 47) {
                                        // So we need to get the item from the slot
                                        if ($this->trades->{$event->getOrigin()->getPlayer()->getName()} instanceof \stdClass) {
                                            // If it's stdClass it's beautiful!
                                            if ($event->getOrigin()->getPlayer()->getInventory()->getSize() > $action->getSource()->getSlotId()) {
                                                $this->trades->{$event->getOrigin()->getPlayer()->getName()}->item = @$event->getOrigin()->getPlayer()->getInventory()->getItem($action->getSource()->getSlotId()) ?? VanillaItems::AIR();
                                                $this->trades->{$event->getOrigin()->getPlayer()->getName()}->count = $action->getCount();
                                            }
                                        }
                                    }
                                }
                            }

                            if ($action->getDestination()->getContainerId() == 47 || $action->getSource()->getContainerId() == 47) {
                                $event->cancel();
                            }
                        }
                    }
                    $this->tradeQueue->{$event->getOrigin()->getPlayer()->getName()} = false;
                }
            }
        } elseif ($event->getPacket() instanceof ContainerClosePacket) {
            if (@$this->trades->{$event->getOrigin()->getPlayer()->getName()} !== null) {
                $this->trades->{$event->getOrigin()->getPlayer()->getName()} = null;
            }
        }
    }

    public function onEntityDamage(Damage $event) {
        if ($event->getEntity() instanceof Shopkeeper) {
            if ($event instanceof EntityDamageByEntityEvent) {
                if (@$event->getDamager() === null) {
                    $event->cancel();
                    return;
                }
                if ($event->getDamager() instanceof Player) {
                    if ($event->getEntity()->getConfig()->author === $event->getDamager()->getName() && $event->getDamager()->hasPermission("shopkeepers.shop.remove")) {
                        $this->entities->remove($this->entities->generateEntityHash($event->getEntity()));
                        $event->getEntity()->kill();
                    } elseif ($event->getEntity()->getConfig()->author === $event->getDamager()->getName() && $event->getDamager()->hasPermission("shopkeepers.shop.kill")) {
                        $this->entities->remove($this->entities->generateEntityHash($event->getEntity()));
                        $event->getEntity()->kill();
                    } else {
                        $event->getDamager()->sendMessage("§cYou can't damage a shopkeeper!");
                        $event->cancel();
                    }
                } else {
                    $event->cancel();
                }
            } else {
                $event->cancel();
            }
        }
    }

    // https://stackoverflow.com/questions/4356289/php-random-string-generator
    // I'm only lazy
    protected function generateRandomString($length = 10) : string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}