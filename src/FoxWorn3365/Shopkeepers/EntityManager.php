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

use FoxWorn3365\Shopkeepers\entity\Shopkeeper;

class EntityManager {
    protected string $base;
    protected array $elements = [];
    public array $entities = [];
    public object $list;

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

    public function add(Shopkeeper $shop) : void {
        // x, y, yw, z, pitch, world, (base64)data
        //$this->elements[] = "{$shop->getLocation()->getX()},{$shop->getLocation()->getY()},{$shop->getLocation()->getYaw()},{$shop->getLocation()->getZ()},{$shop->getLocation()->getPitch()},{$shop->getWorld()->getId()}," . base64_encode(json_encode($shop->getConfig()));
        $this->elements[] = $this->generateEntityHash($shop);
        $this->update();
    }

    public function get(int $slot) : ?string {
        $this->update();
        return $this->elements[$slot];
    }

    public function loadAll(Server $server) : void {
        foreach ($this->elements as $data) {
            $data = explode(",", $data);
            $location = new Location($data[0], $data[1], $data[3], $server->getWorldManager()->getWorld($data[5]), $data[2], $data[4]);
            $shopkeeper = new Shopkeeper($location);
            $shopkeeper->setConfig(json_decode(base64_decode($data[6])));
            $shopkeeper->spawnToAll();
        }
    }

    public function generateEntityHash(Shopkeeper $shop) : string {
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
        foreach ($this->elements as $shop) {
            if ($shop !== null) {
                $entity = self::createEntity($shop, $player->getServer());
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
    }

    protected static function createEntity(string $rawdata, Server $server) : Shopkeeper {
        $data = (object)json_decode(base64_decode($rawdata));
        $location = new Location($data->x, $data->y, $data->z, $server->getWorldManager()->getWorld($data->world), $data->yaw, $data->pitch);
        $entity = new Shopkeeper($location, json_decode(base64_decode($data->config)), $data->id);
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
}