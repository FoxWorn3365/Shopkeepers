<?php

namespace FoxWorn3365\Shopkeepers\item;

use pocketmine\item\Item;

class Banknote extends Item {
    protected float $credits = 1;
    protected string $name = "Paper";
    protected string $networkid = "minecraft:paper";
    protected int $id = 386;
    protected int $meta = 0;

    function __construct(int $value = 1) {
        $this->value = $value;
    }

    public function getValue() : int {
        return $this->credits;
    }

    public function setValue(int $howmuch) : void {
        $this->credits = $howmuch;
    }
}