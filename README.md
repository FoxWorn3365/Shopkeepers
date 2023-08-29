<p align="center">
    <img src='https://raw.githubusercontent.com/FoxWorn3365/Shopkeepers/resources/plugin-banner.png'><br>
    <strong>Add Shopkeepers to your PocketMine-MP world! Allow the creation of simple barter stores between players or create adminshops!</strong>
    <div align="center">
        <a href="https://poggit.pmmp.io/p/Shopkeepers"><img src="https://poggit.pmmp.io/shield.state/Shopkeepers"></a>
        <a href="https://poggit.pmmp.io/p/Shopkeepers"><img src="https://poggit.pmmp.io/shield.api/Shopkeepers"></a>
    </div>
    <div align="center">
        <a href="https://poggit.pmmp.io/p/Shopkeepers"><img src="https://poggit.pmmp.io/shield.dl.total/Shopkeepers"></a>
        <a href="https://poggit.pmmp.io/p/Shopkeepers"><img src="https://poggit.pmmp.io/shield.dl/Shopkeepers"></a>
    </div>
    <div align="center" style="margin-top: 15px">
        <strong>Created with ❤️ by <a href='https://github.com/FoxWorn3365'>FoxWorn3365</a></strong>
        <br><br>
    </div>
</p>

---
<h1 align="center"><b>Shopkeepers v1.3</b> for PocketMine-MP <b>5</b></h1>

<br>
<img src='https://pmmpstats.xyz/api/v1/static/playersServers?name=Shopkeepers' style='width: 100%'>
<div align="center"><b>Shopkeepers</b> stats by <a href='https://pmmpstats.xyz'>pmmpStats</a></div>
<br><br>

**⚠️ We are not in any way related to the [Shopkeepers plugin](https://dev.bukkit.org/projects/shopkeepers) for Bukkit!**


**⚠️ This plugin collect some data for [pmmpStats](https://pmmpstats.xyz). Read more (and how to disable) [here](#🔸-pmmpstats-implementation)**!

> Follow me on [**Twitter**](https://twitter.com/FoxWorn3365) to remain updated!

## Introduction video
<a href='https://youtu.be/FIAcOnTyW_Y'>Watch the video on YouTube</a>

## 📰 Features
- Players can create their own Shopkeepers and manage it
- Admin Shopkeepers
- Vanilla trade page
- Shopkeeper inventory for non-admin Shopkeepers
- Hit prevention for shopkeepers
- Easy configuration with in-game GUI
- Double trade supported
- Custom skin support
- Plugin APIs

<p align="center">
  <br>
  <span>Please consider to</span>
  <h1 align="center"><a href='https://www.paypal.com/donate/?hosted_button_id=LBPVAEL3RWHKQ'><b>SUPPORT MY WORK</b></a></h1>
  <br>
</p>

## 🖥️ Compatibility
> **Warning**
> As of **v1.2** we no longer support PocketMine-MP 4, this however does not mean that we will remove **v1.0** for PMMP4, in fact it will always remain available here on GitHub at [__this branch__](https://github.com/FoxWorn3365/Shopkeepers/tree/pmmp4)!

## 🛠️ Configuration
The configuration of **Shopkeepers** allows you to customize some values to make it suitable for all servers.
| Name | Type | Default | Description |
| --- | --- | --- | --- |
| enabled | bool | true | Is the plugin enabled? |
| max-entities-for-player | int | 3 | Max shopkeeper's entities for one player (PER SHOP) |
| max-entities-bypass | array | [] | Player that can bypass this limitation |
| banned-shop-names | array | [] | List of banned names |
| banned-item-names | array | [] | List of banned items (can't be sold) |
| banned-item-ids | array | [] | List of banned item ids (can't be sold) |
| enable-remote-trade[🛈](#remote-trade) | bool | false | Allow player to use the /sk trade command |
| enable-pmmpstats-datashare | bool | true | Allow the server to share [some data](#🔸-pmmpstats-implementation) with [pmmpStats](https://pmmpstats.xyz) |
| enable-version-checker | bool | true | Allow the server to check the plugin version |

## ⌨️ Commands
The base command is `/shopkeepers` but you can also use `/sk`, `/skeepers` and `/shopk` as aliases.
Here a list of all commands that you can use:
| Command | Args | Description |
| --- | --- | --- |
| info | none | Show the plugin's informations |
| info | SHOP NAME | Show the shop configuration page |
| edit | SHOP NAME | Edit the shop recepies |
| create | SHOP NAME / NULL | Create a new shop, if the name is leaved empty will be generated |
| summon | SHOP NAME | Summon a Shopkeeper entity (as a Villager) for your Shop |
| rename | SHOP NAME and NEW NAME | [NOT AVAILABLE] Rename a current shop |
| list | none | Show all of your shops |
| history | SHOP NAME and PAGE | Show the trade history for the shopkeeper |
| trade | SHOP AUTHOR NAME and SHOP NAME | Remotely trade with a shopkeeper |

## 🔸 pmmpStats implementation
This plugin makes use of [pmmpStats](https://pmmpstats.xyz) to create and process plugin statistics, which then includes a continuous sending of the following information to the service servers:
- Server IP and port
- Server OS version
- Server PHP version
- Server cores
- Server PocketMine-MP version
- Server version (minecraft)
- Online players
- Server Xbox auth status

The Terms of Service of pmmpStats are available [here](https://pmmpstats.xyz/tos), instead the privacy polici is at [this link](https://pmmpstats.xyz/privacy).<br>
You **can** disable information sharing with pmmpStats by setting the `enable-pmmpstats-datashare` value to `false`, by default (so even if the value is not present) it is enabled.

## Shopkeepers Skin System (SSS)
Yes, the v1.0 brought an epic function: now you can set a skin of a Shopkeeper.<br>
Unfortunately players can't add a skin of a Shopkeeper for multiple reasons:
- Memory
- Memory
- Memory
- Hmm, Memory?

Anyways, to avoid abuse of this system we have made this feature usable only by the server administrator.

### How to add a skin of a Shopkeeper
You should have seen something new in the `Shopkeepers` folder, the `skins` folder, and that is where all the skins should be put.
> **Warning**<br>
> Skins MUST BE in a `.png` file!

The file name should be composed as follows: `<PLAYER NAME>_<SHOP NAME>.png`, for example `FoxWorn3365_Fox.png` is valid and will be used by the plugin.
### I don't want to select skins
Well, if no skin is provided the classic villager is spawned. yeee

## API Documentation
Shopkeepers from the **v1.3** implements the APIs.
```php
$api = FoxWorn3365\Shopkeepers\Core::$api;
```

### Get the config manager of a player
```php
$api->getConfigManager(Player $player) : FoxWorn3365\Shopkeepers\ConfigManager
```

### Get the Shopkeeper config of a player
```php
$api->getConfig(Player $player, string $shopName) : object|array|bool
```

### Open the trade page to a player from the shopkeeper author and name
```php
$api->openTradeInventoryForPlayer(Player $player, string $shopOwner, string $shopName) : void
```

### Update a Shopkeeper config
```php
$api->setConfig(Player $player, string $shopName, object $config) : void
```

### Summon a Shopkeeper
```php
$api->summonShopkeeper(Player $player, string $shopName) : void
```

## F.A.Q.
### How to create an Admin shop
There is not really an Admin shop but you can activate this function by using the command `/sk info <SHOP NAME>` and then clicking on the Blaze Powder and then clicking on the red wool block that has "Admin shop" as its name

### How to see a Shopkeeper's inventory
There are two ways:
- Use the command `/sk info <SHOP NAME>` and then click on the chest!
- Click on the Shopkeeper (Villager) entity and then click on the chest!

### I want to access to the Shopkeeper's trade page but if i click the entity i access the shopkeeper's info page!
Easy: shift and click on the Shopkeeper

### How to despawn a Shopkeeper
More easy: just hit it, it will die in only one hit!

### I have [ClearLag](https://poggit.pmmp.io/p/ClearLag/2.1.0) and it removes the Shopkeepers entites!
Edit the config of ClearLag changing to `false` [this option](https://github.com/tobiaskirchmaier/ClearLag/blob/03e2a03a5f8868216dfc89eb78f51523ff228d6b/resources/config.yml#L84C5-L84C24).

### How can I change the skin of a Shopkeeper
We actually support a Skin System, please see [here](#shopkeepers-skin-system-sss)!

### OMG I CAN'T ACCESS TO THE INVENTORYM uyigqwieduwefibef
If the Shopkeeper is an Admin Shop it does not have an inventory!

## Maximum level of customization: program the plugin!
The **v1.2** implements an incredible customization system: 🎉__programming!__🎉<br><br>
So, from now you can **handle some Shopkeeper event!** but how?<br>
Simple, you can code inside the config with the new parser [YAMLPower](https://github.com/FoxWorn3365/YAMLPower)!<br><br>
Feel free to contact me with no problem for any questions, I'll respond within a day!

## ⚠️ Unsable (BETA) features.
Here will be listed every feature of the plugin who's not stable (so is in a BETA phase).<br>
These features aren't active by default, you must edit the config to enable!<br>
### Remote trade
Remote trade is in a BETA phase because can use a lot of memory and CPU and can slow down the server.
Enable the `enable-remote-trade` at your own risk!

## Bug reporting
Reporting bugs ~~to developers~~ to the developer😢 is very important to ensure the stability of the plugin, so in order to better track and manage all reports it is **incredibly necessary** that they are reported via [GitHub Issues](https://github.com/FoxWorn3365/Shopkeepers/issues).<br>
Here is what to include in the reporting to make it perfect:
1. The **complete** crash error
> If it is not complete how do we know how it all happened?
2. All files from the `plugin_data/Shopkeepers` folder
> This way we can compare the error with those files to find a possible transcription error
3. The plugin's version
> It changes a lot from version to version, and this would help us a lot to understand where to look for
4. (OPTIONAL) The plugin download source
> Knowing where you downloaded the plugin from might help, always better to know some information
5. (OPTIONAL) The `Shopkeepers.phar`
> "Last wade", in case we can analyze the source

## Contribution guide
Any contribution is greatly appreciated because you help me to lighten my workload, so here are some small guidelines to follow when you want to contribute:
- Clear code
> I don't want to see things like this:
```php
class Fox {
    protected $fox;  // WHAT'S THE TYPE?????'?'Q'2'wqiadw sdfbuhwhwrf jhs

    function create($int, $bruh) { $bruh = 10-$int+$bruh; // AAAAAAAAAA. IT'S A PUBLIC, PRIVATE OR PROTECTED FUNCTION?!?!??!?
    return $bruh;                                       // AND WHAT'S THE RETURN TYPE???? AND WHY THE CODE IS IN THE FIRST LINE????????
    }
}
```
- Update the "headers" of the file correctly
> They allow new contributors to understand what the file is for without having them parse 250 lines, so update them with the true purpose of the file!
```php
// CORRECT: 
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


 // WRONG:
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
 * Description: Idk make entites ez
 */
```
- `utils` is reserved for Static functions and final classes
> All functions in `utils` must have all static methods and all static classes must be in that directory and namespace
- Please __DESCRIBE__ your changes in the pull request!
> I want to understand it

## Permissions
```yaml
permissions:
  shopkeepers.base:
    description: "Allows users to use the base command"
    default: true
  shopkeepers.shop.summon:
    description: "Allows users to summon player's shop"
    default: true
  shopkeepers.shop.create:
    description: "Allow users to create a shop"
    default: true
  shopkeepers.shop.edit:
    description: "Allow users to edit they'r shop"
    default: true
  shopkeepers.shop.list:
    description: "Allow users to see a list of their shops"
    default: true
  shopkeepers.shop.remove:
    description: "Allow users to despawn they'r Villager shops"
    default: true
  shopkeepers.shop.rename:
    descritpion: "Allow users to rename they'r shops"
    default: true
  shopkeepers.shop.namevisible:
    description: "Allow users to decide if the shopkepeer's name should be visible or no"
    default: true
  shopkeepers.shop.history:
    description: "Allow users to view the trade history of his Shopkeepers"
    default: true
  shopkeepers.shop.enableDisable:
    description: "Allow users to enable and disable their own Shopkeepers from the menu"
    default: true
  shopkeepers.shop.admin:
    description: "Allows users to decide if the shopkeepers should be admin or none"
    default: op
  shopkeepers.shop.kill:
    description: "Allow users to kill every shopkeepers, also of other players"
    default: op
  shopkeepers.shop.defaultGUI:
    description: "Allow users to see and use the /sk command without args to open the base GUI"
    default: true
  shopkeepers.shop.use:
    description: "Allow users to use a shopkeeper when they touch it"
    default: true
  shopkeepers.shop.allowRemoteInventoryOpen:
    description: "Allow users to open a shopkeeper's inventory with the command /sk inventory"
    default: op
  shopkeepers.shop.allowRemoteTrade:
    description: "Allow users to remotely trade with a shopkeeper with the command /sk trade <author> <shop>"
    default: true
```

## Developers: shop object
The plugin needs to save store data, and unlike other plugins, it uses .json files so they are more accessible to server owners, so here is the structure.<br>
As a reminder, the plugin saves each player's data in separate files, so there will be a `PlayerName.json` for each player that creates a shop.<br>
Now, let's see the object:
```json
{
    "author":"<PlayerNAME>",
    "enabled":true,
    "admin":false,
    "title":"<ShopNAME>",
    "namevisible":false,
    "history":base64EncodedHistoryOfTransactions,
    "inventory":[],
    "items":[
        {
            "buy":nbtSerializedItem,
            "buy2":?nbtSerializedItem,
            "sell":nbtSerializedItem
        }
    ]
}
```
| Name | Type | Description |
| --- | --- | --- |
| author | string | The username of the shop author |
| enabled | bool | Is the shop enabled? This option can be changed by the player in the menu config |
| admin | bool | If the shop is an Admin shop, so you don't have to put items in the inventory and you won't earn anything |
| title | string | The real name of the shop |
| namevisible | bool | Should the shop name be visible when summoned? |
| history | string | A base64 encoded string with all the transactions |
| inventory | array | The inventory of the shop |
| items | array | A list of all recepies (max 9) |

## Special thanks
Thanks to [Muqsit](https://github.com/Muqsit) for the [InvMenu](https://github.com/Muqsit/InvMenu) virion who have contributed to the creation for this plugin!<br>
Also [this plugin](https://github.com/FrozenArea/TradeAPI) helped me!

## Contacts
You can contact me via:
- Discord: `@foxworn`
- Email: `foxworn3365@gmail.com`