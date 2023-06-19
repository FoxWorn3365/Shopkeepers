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
 * Description: Load and save custom entities into a simple string
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

    function __construct(string $base) {
        $this->base = $base;
        $this->retrive();
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
        $this->elements[] = "{$shop->getLocation()->getX()},{$shop->getLocation()->getY()},{$shop->getLocation()->getYaw()},{$shop->getLocation()->getZ()},{$shop->getLocation()->getPitch()},{$shop->getWorld()->getId()}," . base64_encode(json_encode($shop->getConfig()));
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
        return "{$shop->getLocation()->getX()},{$shop->getLocation()->getY()},{$shop->getLocation()->getYaw()},{$shop->getLocation()->getZ()},{$shop->getLocation()->getPitch()},{$shop->getWorld()->getId()}," . base64_encode(json_encode($shop->getConfig()));
    }

    public function remove(string $hash) : void {
        $this->retrive();
        $count = 0;
        foreach ($this->elements as $element) {
            if ($element == $hash) {
                $this->elements[$count] = null;
                unset($this->elements[$count]);
                $this->update();
                return;
            }
            $count++;
        }
    }

    public function loadPlayer(Player $player) : void {
        $server = $player->getServer();
        foreach ($this->elements as $data) {
            $data = explode(",", $data);
            $location = new Location($data[0], $data[1], $data[3], $server->getWorldManager()->getWorld($data[5]), $data[2], $data[4]);
            $shopkeeper = new Shopkeeper($location);
            $shopkeeper->setConfig(json_decode(base64_decode($data[6])));
            $shopkeeper->spawnTo($player);
        }
    }

    public function cache($server) : void {
        $this->retrive();
        foreach ($this->elements as $data) {
            $data = explode(",", $data);
            $location = new Location($data[0], $data[1], $data[3], $server->getWorldManager()->getWorld($data[5]), $data[2], $data[4]);
            $shopkeeper = new Shopkeeper($location);
            $shopkeeper->setConfig(json_decode(base64_decode($data[6])));
            $this->entities[] = $shopkeeper;
        }
    }
}