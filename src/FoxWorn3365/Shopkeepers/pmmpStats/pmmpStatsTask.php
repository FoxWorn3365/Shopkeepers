<?php

namespace FoxWorn3365\Shopkeepers\pmmpStats;

use pocketmine\scheduler\Task;
use pocketmine\plugin\PluginBase;

class pmmpStatsTask extends Task {
    protected PluginBase $plugin;

    function __construct(PluginBase $plugin) {
        $this->plugin = $plugin;
    }

    public function onRun() : void {
        $this->plugin->getScheduler()->scheduleAsyncTask(new pmmpStats($this->plugin));
    }
}