<?php

namespace FoxWorn3365\Shopkeepers;

use pocketmine\player\Player;

class ConfigManager {
    protected Player $player;
    public string $dir;
    protected $key;

    function __construct(Player $player, string $basedir) {
        $this->player = $player;
        $this->dir = $basedir . $player->getName() . '.json';
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
        $key = $this->key ?? $key;
        $config->{$key} = $value;
        $this->update($config);
    }

    public function setSingleKey(string $key) : void {
        $this->key = $key;
    }

    public function getSingleKey() : string {
        return $this->key;
    }
/*
    public function add(object|array $content) : void {
        $this->update($this->get()[] = $content);
    }
*/
}