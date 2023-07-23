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
use pocketmine\entity\Entity;

// Events
use pocketmine\event\entity\EntityDamageEvent as Damage;
use pocketmine\event\player\PlayerEntityInteractEvent as Interaction;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\entity\EntitySpawnEvent;

// Packets
use pocketmine\network\mcpe\protocol\ActorEventPacket as EntityEventPacket;
use pocketmine\network\mcpe\protocol\ItemStackRequestPacket;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\BookEditPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;

// Custom
use FoxWorn3365\Shopkeepers\Menu\InfoMenu;
use FoxWorn3365\Shopkeepers\Menu\ShopInventoryMenu;
use FoxWorn3365\Shopkeepers\Menu\EditMenu;
use FoxWorn3365\Shopkeepers\Menu\ListMenu;
use FoxWorn3365\Shopkeepers\Menu\ShopInfoMenu;
use FoxWorn3365\Shopkeepers\entity\Shopkeeper;
use FoxWorn3365\Shopkeepers\entity\HumanShopkeeper;
use FoxWorn3365\Shopkeepers\shop\Manager;
use FoxWorn3365\Shopkeepers\utils\NbtManager;
use FoxWorn3365\Shopkeepers\utils\Utils;
use FoxWorn3365\Shopkeepers\utils\SkinUtils;

// Newtork Parts
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\ItemStackRequest;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\PlaceStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\CraftingConsumeInputStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\DeprecatedCraftingResultsStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;

// Exceptions
use pocketmine\network\mcpe\convert\TypeConversionException;

class Core extends PluginBase implements Listener {
    protected object $menu;
    protected EntityManager $entities;
    protected object $trades;
    protected object $tradeQueue;
    protected object $handle;

    protected string $defaultConfig = "IwojIFNob3BrZWVwZXJzIHYwLjkuMSBieSBGb3hXb3JtMzM2NQojIChDKSAyMDIzLW5vdyBGb3hXb3JuMzM2NQojIAojIFJlbGFzZWQgdW5kZXIgdGhlIEdQTC0zLjAgbGljZW5zZSAKIyBodHRwczovL2dpdGh1Yi5jb20vRm94V29ybjMzNjUvU2hvcGtlZXBlcnMvYmxvYi9tYWluL0xJQ0VOU0UKIwoKZW5hYmxlZDogdHJ1ZQoKIyBNYXggc2hvcGtlZXBlcidzIGVudGl0aWVzIGZvciBvbmUgcGxheWVyIChQRVIgU0hPUCkKbWF4LWVudGl0aWVzLWZvci1wbGF5ZXI6IDUKIyBQbGF5ZXIgdGhhdCBjYW4gYnlwYXNzIHRoaXMgbGltaXRhdGlvbgptYXgtZW50aXRpZXMtYnlwYXNzOgogIC0gWW91ck1pbmVjcmFmdFVzZXJuYW1lCgojIE1vZGVyYXRpb24gc2V0dGluZ3MgICAtIFRISVMgSVMgQSBDT05UQUlOIENPTkRJVElPTiBzbyBpZiB5b3Ugc2V0ICdwcm8nIGFsc28gbmFtZXMgbGlrZSAnYXByb24nLCAncHJvdG90eXB1cycsICdwcm90bycsICdwcm8nIGFuZCBpdCdzIGNhc2UgSU5TRU5TSVRJVkUKYmFubmVkLXNob3AtbmFtZXM6CiAgLSBoaXRsZXIKICAtIG5hemkKCiMgQmFubmVkIHNob3AgaXRlbSBuYW1lcyBzbyB0aGV5IGNhbid0IGJlIHNvbGQgb3IgYm91Z2h0CmJhbm5lZC1pdGVtLW5hbWVzOgogIC0gZGlhbW9uZF9heGUKCiMgQmFubmVkIGl0ZW0gSURzIApiYW5uZWQtaXRlbS1pZHM6CiAgLSAyNTU=";
    
    protected const NOT_PERM_MSG = "§cSorry but you don't have permissions to use this command!\nPlease contact your server administrator";
    public const AUTHOR = "FoxWorn3365";
    public const VERSION = "1.0.0";

    public function onLoad() : void {
        $this->menu = new \stdClass;
        $this->trades = new \stdClass;
        $this->tradeQueue = new \stdClass;
        $this->handle = new \stdClass;
    }

    public function onEnable() : void {
		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}

        $this->entities = new EntityManager($this->getDataFolder());

        // Create the config folder if it does not exists
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder() . 'skins');

        // Check for file integrity
        Utils::integrityChecker($this->getDataFolder());

        // Set server version
        $this->server = (float)$this->getServer()->getApiVersion();

        // Register event listener
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        // Load the config
        if (!file_exists($this->getDataFolder() . "config.yml")) {
            file_put_contents($this->getDataFolder() . "config.yml", base64_decode($this->defaultConfig));
        }

        // Open the config
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

        // Shall we need to disable the plugin?
        if (!$this->config->get('enabled', true)) {
            $this->getServer()->getPluginManager()->disablePlugin($this); // F
        }
    }

    public function onPlayerJoin(PlayerJoinEvent $event) {
        // Load all cached entities
        $this->entities->loadPlayer($event->getPlayer());

        // Create the player's object queue for managing event procession
        $this->tradeQueue->{$event->getPlayer()->getName()} = false;
        $this->handle->{$event->getPlayer()->getName()} = false;
    }

    public function onPlayerEntityInteract(Interaction $event) : void {
        if (!$this->handle->{$event->getPlayer()->getName()}) {
            $this->handle->{$event->getPlayer()->getName()} = true;
            $this->entityInteractionLoad($event->getEntity(), $event->getPlayer());
        }
    }

    public function onEntitySpawn(EntitySpawnEvent $event) : void {
        if ($event->getEntity() instanceof Shopkeeper || $event->getEntity() instanceof HumanShopkeeper) {
            // Add the shopkeeper to entity interface
            if (!$event->getEntity()->hasCustomShopkeeperEntityId()) {
                // FIRST, check if the limit is not trepassed
                $name = $event->getEntity()->getConfig()->shop;
                $author = $event->getEntity()->getConfig()->author;
                if (@$this->entities->list->{$author}->{$name} !== null) {
                    if ($this->entities->list->{$author}->{$name} + 1 > $this->config->get('max-entities-for-player', 3) && !in_array($author, $this->config->get('max-entities-bypass', []))) {
                        // Do not consent
                        $event->getEntity()->getWorld()->getServer()->getPlayerExact($author)->sendMessage("§cSorry but you have reached the max shopkeepers entity for the shop {$name}\n§rUsed: " . $this->entities->list->{$author}->{$name} ."/" . $this->config->get('max-entities-for-player', 3));
                        $event->getEntity()->kill();
                        return;
                    } else {
                        $this->entities->list->{$author}->{$name}++;
                    }
                } else {
                    $this->entities->list->{$author}->{$name} = 1;
                }
                $entity = $event->getEntity();
                $entity->setCustomShopkeeperEntityId(Utils::randomizer(10));
                $this->entities->add($event->getEntity());
            }
        }
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) : bool {
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
                // Open the list menu
                $menu = new ListMenu($sender, $this->getDataFolder());
                $menu->create()->send($sender);
                return true;
            } else {
                $sender->sendMessage("You don't have any shop(s) here!");
            }
        } elseif ($args[0] == "create") {
            if (!$sender->hasPermission("shopkeepers.shop.create")) {
                $sender->sendMessage(self::NOT_PERM_MSG);
            }

            if (empty($name = @$args[1])) {
                $name = $this->generateRandomString(6);
            }

            foreach ($this->config->get('banned-shop-names', []) as $banned) {
                if (strpos($name, $banned) !== false) {
                    // Oh crap, this is banned!
                    $sender->sendMessage("§cSorry but this name is banned!\n§rPlase contact your server administrator");
                    return false;
                }
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
            if (SkinUtils::find($name, $sender->getName(), $this->getDataFolder())) {
                // Has a skin, let's summon an human entity after getting the skin
                $skin = SkinUtils::get($name, $sender->getName(), $this->getDataFolder());
                $villager = new HumanShopkeeper($pos, $skin, $shopdata);
            } else {
                // A simple Shopkeeper, so summon a villager-like entity
                $villager = new Shopkeeper($pos, $shopdata);
            }
            $villager->setNameTag($name);
            $villager->setNameTagAlwaysVisible($shop->get()->{$name}->namevisible);
            $villager->spawnToAll();
            // Will be managed by EntitySpawnEvent $this->entities->add($villager);
            return true;
        } elseif ($args[0] === "remove" || $args[0] === "despawn") {
            $sender->sendMessage("To remove a shopkeeper just hit it!");
            return true;
        } elseif ($args[0] === "history" && !empty($args[1])) {
            if (!$sender->hasPermission("shopkeepers.shop.history")) {
                $sender->sendMessage(self::NOT_PERM_MSG);
            }

            $name = $args[1];
            if (@$shop->get()?->{$name} === null) {
                $sender->sendMessage("You don't have a shop called {$name}!");
                return false;
            }

            if (!empty($args[2])) {
                $page = $args[2];
            } else {
                $page = 1;
            }

            $history = (array)json_decode(base64_decode(@$shop->get()->{$name}->history));

            // Let's divide in pages
            $pages = ceil(count($history)/20);

            if ($page > $pages) {
                $sender->sendMessage("§cSorry but there are only {$pages} pages!");
            } else {
                $message = "§lTrade history for Shopkeeper {$name}.§r\nPage §l{$page}§r/{$pages}\n";
                for ($a = ($page-1)*20; $a < $page*20; $a++) {
                    if (!empty($history[$a])) {
                        $message .= "\n" . $history[$a];
                    }
                }
                $sender->sendMessage($message);
            }
            return true;
        } elseif ($args[0] === "rename" && !empty($args[1]) && !empty($args[2])) {
            if (!$sender->hasPermission("shopkeepers.shop.rename")) {
                $sender->sendMessage(self::NOT_PERM_MSG);
            }

            $name = $args[1];
            if (@$shop->get()?->{$name} === null) {
                $sender->sendMessage("You don't have a shop called {$name}!");
                return false;
            }

            // Fix name
            $shop->set($args[2], $shop->get()->{$name});
            $shop->remove($name);

            $sender->sendMessage("Shop {$name} successfully renamed!");
            return true;
        } elseif (empty($args[0])) {
            if (!$sender->hasPermission("shopkeepers.shop.defaultGUI")) {
                $sender->sendMessage(self::NOT_PERM_MSG);
            }

            $menu = new InfoMenu();
            $menu->create($sender, $this->getDataFolder())->send($sender);
            return true;
        } else {
            return false;
        }
        return false;
    }

    public function onPacket(DataPacketReceiveEvent $event) : void {
        $ic = new \stdClass; // We need to pass ONLY SOME REQUESTS and with a defined order!
        $ic->crafting = false;
        $ic->slot = false;
        $ic->cconsume = false;
        $ic->specialstack = false;
        $ic->finalconsume = false;
        $ic->count = 1;
        $ic->added = false;
        $count = 1;
        $cm = null;
        $quota = 0;
        $itemglobal = null;
        $inventoryInsideConfig = null;
        $log = "";
        $stackcount = 1;
        $maxcount = 1;
        
        if ($event->getPacket() instanceof ItemStackRequestPacket) {
            $inventory = $event->getOrigin()->getPlayer()->getInventory();
            $maxcount = count($event->getPacket()->getRequests());
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
                                $this->trades->{$event->getOrigin()->getPlayer()->getName()}->itemsAdd = [];
                                $this->trades->{$event->getOrigin()->getPlayer()->getName()}->quota = [];
                                // Update the log with the player
                                $log = date("d/m/Y - H:i:s") . " >> Player §l{$event->getOrigin()->getPlayer()->getName()} §rpurchased ";
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
                                                $translator = new TypeConverter();
                                                try {
                                                    $item = $translator->netItemStackToCore($result);
                                                } catch (TypeConversionException $e) {
                                                    $item = NbtManager::decode(Utils::comparator($this->trades->{$event->getOrigin()->getPlayer()->getName()}->item, $result->getCount(), $cm->get()->{$cm->getSingleKey()}->items));
                                                }
                                                /*
                                                if ($this->server < 5) {
                                                    $translator = new TypeConverter();
                                                    $item = $translator->netItemStackToCore($result);
                                                }
                                                */
                                                $item->setCount($result->getCount());
                                                $log .= "§l{$item->getCount()}§r {$item->getName()} for ";
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

                                                    if (gettype($inventoryInsideConfig) !== 'array') {
                                                        Utils::errorLogger($this->getDataFolder(), "ERROR", "InventoryInsideConfig at Core.php#476 was an object and not an array!\nPlase report this with an issue!");
                                                        $inventoryInsideConfig = [];
                                                    }
    
                                                    $object = $cm->get()->{$cm->getSingleKey()};
                                                    $object->inventory = $inventoryInsideConfig;
                                                    $cm->set($cm->getSingleKey(), $object);
                                                }
                                                $this->trades->{$event->getOrigin()->getPlayer()->getName()}->itemsAdd[] = $item;
                                            }
                                        }
                                    }
                                }
                            }
                        } elseif ($action instanceof CraftingConsumeInputStackRequestAction) {
                            if (!$ic->cconsume && $ic->crafting && !$ic->slot) {
                                $ic->cconsume = true;
                                $this->trades->{$event->getOrigin()->getPlayer()->getName()}->quota[] = $action->getCount();
                            } elseif (!$ic->slot && $ic->cconsume && $ic->crafting && !$ic->finalconsume) {
                                $ic->finalconsume = true;
                                $ic->count = 2;
                                $this->trades->{$event->getOrigin()->getPlayer()->getName()}->quota[] = $action->getCount();
                            }
                        } elseif ($action instanceof PlaceStackRequestAction) {
                            if (!$ic->slot && $ic->crafting && $ic->cconsume && $cm instanceof ConfigManager) {
                                $ic->slot = true;
                                for ($a = 0; $a < $ic->count; $a++) {
                                    $localCount = $a;

                                    $dest = $action->getDestination()->getSlotId();
                                    // Put cctr to slot
                                    if (@$this->trades->{$event->getOrigin()->getPlayer()->getName()} !== null) {
                                        $data = $this->trades->{$event->getOrigin()->getPlayer()->getName()};
                                        $quota = $data->quota[$localCount];
                                        $referredItem = clone $data->items[$localCount]->item;
                                        if ($quota <= 0) {
                                            // Error on qta
                                            return;
                                        }

                                        if ($a === 0) {
                                            $log .= "§l" . $quota . "§r " . $data->items[$localCount]->item->getName();
                                        } else {
                                            $log .= " and §l" . $quota . "§r " . $data->items[$localCount]->item->getName();
                                            $config = $cm->get()->{$cm->getSingleKey()};
                                            $inv = (array)json_decode(base64_decode($config->history));
                                            $inv[] = $log;
                                            $config->history = base64_encode(json_encode($inv));
                                            $cm->set($cm->getSingleKey(), $config);
                                        }
    
                                        if ($data->items[$localCount]->count > 0) {
                                            $this->trades->{$event->getOrigin()->getPlayer()->getName()}->count = $this->trades->{$event->getOrigin()->getPlayer()->getName()}->items[$localCount]->count - $quota;
                                            $data->items[$localCount]->item->setCount($data->count - $quota);
                                            foreach ($this->trades->{$event->getOrigin()->getPlayer()->getName()}->itemsAdd as $item) {
                                                if (!$ic->added) {
                                                    $ic->added = true;
                                                    $event->getOrigin()->getPlayer()->getInventory()->addItem($item);
                                                }
                                            }
                                            $first = $event->getOrigin()->getPlayer()->getInventory()->first($data->items[$localCount]->item);
                                            if ($first > -1) {
                                                $item = $event->getOrigin()->getPlayer()->getInventory()->getItem($first);
                                                $item->setCount($item->getCount() - $quota);
                                                $event->getOrigin()->getPlayer()->getInventory()->setItem($first, $item);
                                            } elseif ($first == -1 && $data->items[$localCount]->item->getCount() !== 0) {
                                                $item = $data->items[$localCount]->item;
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
                                            $count++;
                                        }  
                                    } else {
                                        $event->getOrigin()->getPlayer()->sendMessage("§cError!\nIt seems that you are not trading rn!");
                                    }
                                }
                                $this->trades->{$event->getOrigin()->getPlayer()->getName()} = null;
                            } else {
                                if (!$ic->specialstack) {
                                    if ($count > $maxcount) {
                                        $ic->specialstack = true;
                                    }

                                    if (@$this->trades->{$event->getOrigin()->getPlayer()->getName()} !== null && $action->getSource()->getContainerId() != 47) {
                                        // So we need to get the item from the slot
                                        if ($this->trades->{$event->getOrigin()->getPlayer()->getName()} instanceof \stdClass) {
                                            // If it's stdClass it's beautiful!
                                            if ($event->getOrigin()->getPlayer()->getInventory()->getSize() > $action->getSource()->getSlotId()) {
                                                $item = new \stdClass;
                                                $item->item = @$event->getOrigin()->getPlayer()->getInventory()->getItem($action->getSource()->getSlotId()) ?? VanillaItems::AIR();
                                                $item->count = $action->getCount();
                                                $this->trades->{$event->getOrigin()->getPlayer()->getName()}->items[] = $item;
                                                $stackcount++;
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
            $this->handle->{$event->getOrigin()->getPlayer()->getName()} = false;
            if (@$this->trades->{$event->getOrigin()->getPlayer()->getName()} !== null) {
                $this->trades->{$event->getOrigin()->getPlayer()->getName()} = null;
            }
        } elseif ($event->getPacket() instanceof InventoryTransactionPacket && $event->getPacket()->trData instanceof UseItemOnEntityTransactionData && $event->getPacket()->trData->getActionType() === UseItemOnEntityTransactionData::ACTION_INTERACT) {
            //$entity = $this->getServer()->getWorldManager()->findEntity($event->getPacket()->trData->getEntityRuntimeId());
            $entity = $this->getServer()->getWorldManager()->findEntity($event->getPacket()->trData->getActorRuntimeId());
            $player = $entity->getWorld()->getNearestEntity($event->getPacket()->trData->getPlayerPosition(), 2);

            if (($entity instanceof Shopkeeper || $entity instanceof HumanShopkeeper) && $player instanceof Player) {
                if (!$this->handle->{$player->getName()}) {
                    $this->handle->{$player->getName()} = true;
                    $this->entityInteractionLoad($entity, $player);
                }
            }
        }
    }

    public function onEntityDamage(Damage $event) : void {
        if ($event->getEntity() instanceof Shopkeeper || $event->getEntity() instanceof HumanShopkeeper) {
            if ($event instanceof EntityDamageByEntityEvent) {
                if (@$event->getDamager() === null) {
                    $event->cancel();
                    return;
                }
                if ($event->getDamager() instanceof Player) {
                    if ($event->getEntity()->getConfig()->author === $event->getDamager()->getName() && $event->getDamager()->hasPermission("shopkeepers.shop.remove")) {
                        $this->entities->remove($this->entities->generateEntityHash($event->getEntity()));
                        $this->entities->list->{$event->getEntity()->getConfig()->author}->{$event->getEntity()->getConfig()->shop}--;
                        $event->getEntity()->kill();
                    } elseif ($event->getEntity()->getConfig()->author === $event->getDamager()->getName() && $event->getDamager()->hasPermission("shopkeepers.shop.kill")) {
                        $this->entities->remove($this->entities->generateEntityHash($event->getEntity()));
                        $this->entities->list->{$event->getEntity()->getConfig()->author}->{$event->getEntity()->getConfig()->shop}--;
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

    public function entityInteractionLoad(Entity $entity, Player $player) : void {
        if (!$player->hasPermission("shopkeepers.shop.use")) {
            $player->sendMessage(self::NOT_PERM_MSG);
        }
        if ($entity instanceof Shopkeeper || $entity instanceof HumanShopkeeper) {
            $data = $entity->getConfig();
            $cm = new ConfigManager($data->author, $this->getDataFolder());
            $cm->setSingleKey($data->shop);
            if (@$cm->get()->{$data->shop} === null) {
                // Oh no, no config!
                $player->sendMessage("§cSorry but this shop does not exists anymore!");
                // Remove the shop
                $this->entities->remove($this->entities->generateEntityHash($entity));
                $entity->kill();
                return;
            } elseif ($data->author === $player->getName() && !$player->isSneaking()) {
                // Open the shopkeeper's ~~inventory~~ info page RN!
                $menu = new ShopInfoMenu($cm, true);
                $menu->create()->send($player);
            } else {
                // It's a shopkeeper!
                // BEAUTIFUL!
                // Now let's open the shopkeeper interface
                // First, check if the shop is enabled
                if (@$cm->get()->{$cm->getSingleKey()}->enabled) {
                    $manager = new Manager($cm);
                    $this->trades->{$player->getName()} = new \stdClass;
                    $this->trades->{$player->getName()}->config = $data;
                    $this->trades->{$player->getName()}->items = [];
                    $manager->send($player, $entity);
                } else {
                    $event->getPlayer()->sendMessage("Sorry but this shop is §cdisabled§r!");
                }
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