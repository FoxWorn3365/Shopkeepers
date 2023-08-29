<?php

namespace FoxWorn3365\Shopkeepers\pmmpStats;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;

class pmmpStatsAsync extends AsyncTask {
    protected string $url;

    function __construct(string $url) {
        //$this->server = $plugin->getServer();
        $this->url = $url;
    }

    public function onRun() : void {
        // Send the http request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($data);
        if ($data->status !== 200) {
            var_dump("ERROR WHILE PUTTING DATA ON pmmpStats, server says: '{$data->message}'\n");
        }
    }
}