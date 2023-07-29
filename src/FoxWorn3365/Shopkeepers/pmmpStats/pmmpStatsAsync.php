<?php

namespace FoxWorn3365\Shopkeepers\pmmpStats;

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;

class pmmpStatsAsync extends AsyncTask {
    protected Server $server;
    protected PluginBase $plugin;
    protected array $needed = [
        'name',
        'version',
        'server_version',
        'api_version',
        'players',
        'server_ip',
        'server_port',
        'php_version',
        'os',
        'xbox_auth',
        'cores'
    ];

    function __construct(PluginBase $plugin) {
        $this->server = $plugin->getServer();
        $this->plugin = $plugin;
    }

    public function onRun() : void {
        // Retrive informations about the server
        $dataToSend = new \stdClass;
        $data->name = $this->plugin->getName();
        $data->version = (object)(yaml_parse_file(__DIR__ . '../../../plugin.yml'))->version;
        $data->server_version = $this->server->getVersion();
        $data->api_version = $this->server->getApiVersion();
        $data->players = (int)count($this->server->getOnlinePlayers());
        $data->server_ip = $this->server->getIp();
        $data->server_port = $this->server->getPort();
        $data->php_version = phpversion();
        $data->os = PHP_OS;
        $data->xbox_auth = $this->server->getOnlineMode();
        if (strpos(strtolower(PHP_OS), 'win') === false) {
            $data->cores = (int)(shell_exec('cat /proc/cpuinfo | grep processor | wc -l'));
        } else {
            $data->cores = 0;
        }
        // Now put all toghether in a get request
        $url = "https://pmmpstats.xyz/api/v1/collect?";
        foreach ($data as $key => $value) {
            $url .= "{$key}={$value}&";
        }
        $data = json_decode(file_get_contents($url));
        if ($data->status !== 200) {
            $this->plugin->getLogger()->warning("HTTP GET Request to pmmpStats API went wrong!\nServer says: {$data->message}");
        }
    }
}