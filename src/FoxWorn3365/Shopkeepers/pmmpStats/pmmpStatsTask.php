<?php

namespace FoxWorn3365\Shopkeepers\pmmpStats;

use pocketmine\scheduler\Task;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class pmmpStatsTask extends Task {
    protected PluginBase $plugin;
    protected Server $server;

    function __construct(PluginBase $plugin) {
        $this->plugin = $plugin;
        $this->server = $this->plugin->getServer();
    }

    public function directoryUtil(string $glob) : string {
        $piece = explode("/", str_replace("phar://", "", $glob));
        $url = "phar://";
        for ($a = 0; $a < count($piece); $a++) {
            $part = @$piece[$a];
            if (stripos(strtolower($part), '.phar') !== false && @$piece[$a+1] === 'src') {
                // Found the dir
                $url .= "{$part}/plugin.yml";
                break;
            } else {
                $url .= "{$part}/";
            }
        }
        return $url;
    }

    public function onRun() : void {
        $data = new \stdClass;
        $data->name = $this->plugin->getName();
        $conf = $this->directoryUtil(__DIR__);
        $data->version = @yaml_parse(file_get_contents($conf))['version'];
        $data->server_version = $this->server->getVersion();
        $data->api_version = $this->server->getApiVersion();
        $data->players = (int)count($this->server->getOnlinePlayers());
        if ($this->server->getIp() === "0.0.0.0") {
            $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            $res = socket_connect($sock, '8.8.8.8', 53);
            // You might want error checking code here based on the value of $res
            socket_getsockname($sock, $addr);
            socket_shutdown($sock);
            socket_close($sock);
            $data->server_ip = $addr;
        } else {
            $data->server_ip = $this->server->getIp();
        }
        $data->server_port = $this->server->getPort();
        $data->php_version = phpversion();
        $data->os = PHP_OS;
        $data->xbox_auth = (bool)$this->server->getOnlineMode();
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
        $this->plugin->getServer()->getAsyncPool()->submitTask(new pmmpStatsAsync($url));
    }
}