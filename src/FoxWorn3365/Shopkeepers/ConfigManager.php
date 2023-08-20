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
 * Current file: /ConfigManager.php
 * Description: Manage the config file without rewriting the dir several times!
 */

namespace FoxWorn3365\Shopkeepers;

use pocketmine\player\Player;

class ConfigManager {
    protected Player|string $player;
    public string $dir;
    protected $key;

    function __construct(Player|string $player, string $basedir) {
        if ($player instanceof Player) {
            $player = $player->getName();
        }
        $this->player = $player;
        $this->dir = $basedir . $player . '.json';
    }

    public function is() : bool {
        if (file_exists($this->dir)) {
            return true;
        }
        return false;
    }

    public function get() : ?object {
        if ($this->is()) {
            return json_decode(file_get_contents($this->dir));
        } else {
            return new \stdClass;
        }
    }

    public function update(object $content) : void {
        file_put_contents($this->dir, json_encode($content));
    }

    public function set(string $key, mixed $value) : void {
        $config = $this->get();
        //$key = $this->key ?? $key;
        $config->{$key} = $value;
        $this->update($config);
    }

    public function remove(string $key) : void {
        $config = $this->get();
        unset($config->{$key});
        $this->update($config);
    }

    public function setSingleKey(string $key) : void {
        $this->key = $key;
    }

    public function getSingleKey() : string {
        return $this->key;
    }
}