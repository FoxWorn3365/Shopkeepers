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
 * Current file: /EntityManager.php
 * Description: Load and save custom entities into and from a simple string
 */

namespace FoxWorn3365\Shopkeepers;

use pocketmine\player\Player;
use pocketmine\entity\Location;
use pocketmine\Server;

use FoxWorn3365\Shopkeepers\utils\SkinUtils;
use FoxWorn3365\Shopkeepers\utils\Utils;
use FoxWorn3365\Shopkeepers\entity\Shopkeeper;
use FoxWorn3365\Shopkeepers\entity\HumanShopkeeper;

class EntityManager {
    protected string $base;
    protected array $elements = [];
    public array $entities = [];
    public object $list;
    public bool $loaded = false;

    function __construct(string $base) {
        $this->base = $base;
        $this->retrive();
        $this->list = new \stdClass;
    }

    protected function update() : void {
        file_put_contents("{$this->base}.entities.json", json_encode($this->elements));
    }

    protected function retrive() : void {
        $data = @json_decode(file_get_contents("{$this->base}.entities.json"));
        if (gettype($data) == 'array') {
            $this->elements = json_decode(file_get_contents("{$this->base}.entities.json"));
            return;
        }
    }

    public function add(Shopkeeper|HumanShopkeeper $shop) : void {
        // x, y, yw, z, pitch, world, (base64)data
        //$this->elements[] = "{$shop->getLocation()->getX()},{$shop->getLocation()->getY()},{$shop->getLocation()->getYaw()},{$shop->getLocation()->getZ()},{$shop->getLocation()->getPitch()},{$shop->getWorld()->getId()}," . base64_encode(json_encode($shop->getConfig()));
        $this->elements[] = $this->generateEntityHash($shop);
        $this->update();
    }

    public function get(int $slot) : ?string {
        $this->update();
        return $this->elements[$slot];
    }

    public function generateEntityHash(Shopkeeper|HumanShopkeeper $shop) : string {
        // GitHub user: "Use object plz"
        // Me: okok i'll do it    AND NOW ITZ TIMW!
        return base64_encode(json_encode([
            'x' => $shop->getLocation()->getX(),
            'y' => $shop->getLocation()->getY(),
            'yaw' => $shop->getLocation()->getYaw(),
            'z' => $shop->getLocation()->getZ(),
            'pitch' => $shop->getLocation()->getPitch(),
            'world' => $shop->getWorld()->getId(),
            'config' => base64_encode(json_encode($shop->getConfig())),
            'id' => $shop->getCustomShopkeeperEntityId(),
            'nametag' => base64_encode(json_encode([
                'visible' => $shop->isNameTagAlwaysVisible(),
                'tag' => $shop->getNameTag()
            ]))
        ]));
        // Old entity string: return "{$shop->getLocation()->getX()},{$shop->getLocation()->getY()},{$shop->getLocation()->getYaw()},{$shop->getLocation()->getZ()},{$shop->getLocation()->getPitch()},{$shop->getWorld()->getId()}," . base64_encode(json_encode($shop->getConfig()));
    }

    public function remove(string $hash) : void {
        $this->retrive();
        $count = 0;
        foreach ($this->elements as $element) {
            if ($element == $hash) {
                $this->elements[$count] = null;
                $this->update();
                return;
            }
            $count++;
        }
    }

    public function loadPlayer(Player $player) : void {
        if (@$this->list->{$player->getName()} === null) {
            $this->list->{$player->getName()} = new \stdClass;
        }

        $server = $player->getServer();

        if (!$this->loaded) {
            foreach ($this->elements as $shop) {
                if ($shop !== null) {
                    $entity = self::createEntity($shop, $player->getServer());
                    if ($entity === null) {
                        continue;
                    }
                    if (@$this->list->{$entity->getConfig()->author} === null) {
                        $this->list->{$entity->getConfig()->author} = new \stdClass;
                        $this->list->{$entity->getConfig()->author}->{$entity->getConfig()->shop} = 1;
                    } else {
                        if (@$this->list->{$entity->getConfig()->author}->{$entity->getConfig()->shop} !== null) {
                            $this->list->{$entity->getConfig()->author}->{$entity->getConfig()->shop}++;
                        } else {
                            $this->list->{$entity->getConfig()->author}->{$entity->getConfig()->shop} = 1;
                        }
                    }
                    $entity->spawnTo($player);
                }
            }
            $this->loaded = true;
        }
    }

    protected static function createEntity(string $rawdata, Server $server) : Shopkeeper|HumanShopkeeper|null {
        $data = (object)json_decode(base64_decode($rawdata));
        $location = new Location($data->x, $data->y, $data->z, $server->getWorldManager()->getWorld($data->world), $data->yaw, $data->pitch);
        if ($location->isValid() && @$location->getWorld() !== null) {
            if (SkinUtils::find(json_decode(base64_decode($data->config))->shop, json_decode(base64_decode($data->config))->author, $server->getPluginManager()->getPlugin("Shopkeepers")->getDataFolder())) {
                $skin = SkinUtils::load(json_decode(base64_decode($data->config))->shop, json_decode(base64_decode($data->config))->author, $server->getPluginManager()->getPlugin("Shopkeepers")->getDataFolder());
                $entity = new HumanShopkeeper($location, $skin, json_decode(base64_decode($data->config)), $data->id);
            } else {
                $entity = new Shopkeeper($location, json_decode(base64_decode($data->config)), $data->id);
            }
        } else {
            return null;
        }
        $tags = json_decode(base64_decode($data->nametag));
        $entity->setNameTag($tags->tag);
        $entity->setNameTagAlwaysVisible($tags->visible);
        return $entity;
    }

    public function cache(Server $server) : void {
        $this->retrive();
        foreach ($this->elements as $data) {
            $this->entities[] = self::createEntity($data, $server);
        }
    }

    public static function getCountFromConfig(string $basedir, string $shopauthor, string $name) : int {
        $data = @json_decode(file_get_contents("{$basedir}.entities.json"));
        if (gettype($data) === 'object') {
            $data = Utils::fixArray($data);
        }

        $count = 0;

        foreach ($data as $entity) {
            $entity = (object)json_decode(base64_decode($entity));

            if (json_decode(base64_decode($entity->config))->author === $shopauthor && json_decode(base64_decode($entity->config))->shop === $name) {
                $count++;
            }
        }

        return $count;
    }
}