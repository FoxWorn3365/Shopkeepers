# Shopkeepers - Stable version
Add Shopkeepers to your PocketMine-MP server!

## Features
- Configuration via in-game GUI
- Admin shops
- Hit prevention for shopkeepers
- Shop inventory

## Installation
The plugin, as this is being written, is not yet on the [poggit](https://poggit.pmmp.io/) platform, so here are some options for installation:
### Simple installation
> Download the `Shopkeepers.phar` from the [latest relase](https://github.com/FoxWorn3365/Shopkeepers/tags) and then put it into the `plugin/` folder
### I-do-not-trust AKA Manual installation
> It is normal not to trust a `.phar` file in a plugin relase, so here is how to proceed with manual installation:<br>
> Clone this repo
```shell
$ git clone https://github.com/FoxWorn3365/Shopkeepers
```
> Put this contents in `build.php`
```php
<?php
$name = $argv[1];
$dir = $argv[2];

$phar = new Phar($name);
$phar->buildFromDirectory($dir);
```
> And then execute this command
```shell
$ php build.php /path/to/pmmp/server/plugin/Shopkeepers.phar Shopkeepers/
```
> And then you've installed the plugin!

## Special thanks
Thanks to [Muqsit](https://github.com/Muqsit) for the [InvMenu](https://github.com/Muqsit/InvMenu) virion who have contributed to the creation for this plugin!<br>
Also [this plugin](https://github.com/FrozenArea/TradeAPI) helped me!

## [Demostration video (YouTube)](https://youtu.be/sustUWTgmMo)

## Commands
The base command is `/shopkeepers` but you can also use `/sk`, `/skeepers` and `/shopk` as alias.
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
```

## F.A.Q.
### How to create an Admin shop
There is not really an Admin shop but you can activate this function by using the command `/sk info <SHOP NAME>` and then clicking on the Blaze Powder and then clicking on the red wool block that has "Admin shop" as its name

### How to see a Shopkeeper's inventory
There are two ways:
- Use the command `/sk info <SHOP NAME>` and then click on the chest at the center of the GUI
- Click on the Shopkeeper (Villager) entity

### I want to access to the Shopkeeper's trade page but if i click the entity i access the inventory!
Easy: shift and click on the Shopkeeper


## Bug reporting
Although I love to receive messages from people who are excited about something I have created it would be better if **any bugs or problems you find are reposted in the issues of this repository** so that everything is more orderly and accessible!

## Contributing
Everyone can contribute and take inspiration from my code, however for those who would like to contribute here are some guidelines:
- The code you add, please comment on it
- Before making a pull request try the code you submitted
- All classes having `FoxWorn3365Shopkeepers\utils` as namespace must be static and final
- In the PR please EXPLAIN what you changed because I don't have all the time in the world to decipher unknown and unexplained codes
- Use the used file "headers" but add a row under the `Copyright (C) 2023-now FoxWorn3365` like `Contributor(s): YouGitHubUsername` and update the file mapping (`Current file`)
That said feel free to contribute, it's not like I'm going to eat you if you make a mistake!

## Known bugs:
- When you trade your inventory on the trade page it doesn't update correctly but as soon as you close the page it all sorts out correctly, so it's not urgent.

## Objects
**Shopkeepers**, to be as configurable and manageable as possible saves IRT (In-Real-Time) player configurations in a .json file located in its PocketMine-MP plugins folder (`/path/to/server/plugin_data/`) and these are the objects that are serialized and then saved:
### Shop object
The main object, if a player have one or more shop will have also a shop object in his .json.
> **Note**<br>
> The Shop object is in an object array and have the title as key!

| Name | Type | Description |
| --- | --- | --- |
| author | string | The username of the shop author |
| admin | bool | If the shop is an Admin shop, so you don't have to put items in the inventory and you won't earn anything |
| title | string | The real name of the shop |
| namevisible | bool | Should the shop name be visible when summoned? |
| inventory | array | The inventory of the shop |
| items | array | A list of all recepies (max 9) |

```json
{
    "author":"[PlayerName]",
    "admin":false,
    "title":"[ShopName]",
    "namevisible":false,
    "inventory":[],
    "items":[
        itemObjects
    ]
}
```

### Item Object
How we save items to sell and buy.
```json
{
    "sell":nbtSerializedItemString,
    "buy":nbtSerializedItemString
}
```

### serializedItemString
It is the object string as NBT.
Ez.

## Roadmap
- [x] Update the item object because now it's the old one
- [x] Make if the ContainerClose packet is received unset the `$this->trades->{PLAYER}` object because otherwise is OP!
- [x] Updated the item object with nbt
- [x] Working Admin shop (without inventory)
- [x] Inventory saving correctly 
- [x] Player shop
- [x] Shopkeeper's inventory updating when players buy
- [x] Shopkeeper deny a trade if the inventory is without the item
