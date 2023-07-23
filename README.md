<p align="center">
    <img src='https://raw.githubusercontent.com/FoxWorn3365/Shopkeepers/resources/plugin-banner.png'>
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
<h1 align="center"><b>Shopkeepers v1.0</b> for PocketMine-MP 5</h1>
<br>

**⚠️ We are not in any way related to the [Shopkeepers plugin](https://dev.bukkit.org/projects/shopkeepers) for Bukkit!**

## Introduction video
<a href='https://youtu.be/FIAcOnTyW_Y'>Watch the video on YouTube</a>

## Features
- Players can create theyr own Shopkeepers and manage it
- Admin Shopkeepers
- Vanilla trade page
- Shopkeeper inventory for non-admin Shopkeepers
- Hit prevention for shopkeepers
- Easy configuration with in-game GUI
- Double trade supported
- Custom skin support

## Compatibility
**Shopkeepers** is made to be multi-version, in fact I announce with great joy that the plugin is available for both PocketMine-MP 5 and PocketMine-MP 4!
> **Warning**<br>
> The Shopkeepers version for PocketMine-MP 4 is available exclusively here on GitHub since InvMenu has versions that are not compatible with each other!
> The branch can be found [here](https://github.com/FoxWorn3365/Shopkeepers/tree/pmmp4)

## Configuration
The configuration of **Shopkeepers** allows you to customize some values to make it suitable for all servers.
| Name | Type | Default | Description |
| --- | --- | --- | --- |
| enabled | bool | true | Is the plugin enabled? |
| max-entities-for-player | int | 5 | Max shopkeeper's entities for one player (PER SHOP) |
| max-entities-bypass | array | [] | Player that can bypass this limitation |
| banned-shop-names | array | [] | List of banned names |

## Commands
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

## F.A.Q.
### How to create an Admin shop
There is not really an Admin shop but you can activate this function by using the command `/sk info <SHOP NAME>` and then clicking on the Blaze Powder and then clicking on the red wool block that has "Admin shop" as its name

### How to see a Shopkeeper's inventory
There are two ways:
- Use the command `/sk info <SHOP NAME>` and then click on the chest!
- Click on the Shopkeeper (Villager) entity and then click on the chest!

### I want to access to the Shopkeeper's trade page but if i click the entity i access the inventory!
Easy: shift and click on the Shopkeeper

### How to despawn a Shopkeeper
More easy: just hit it, it will die in only one hit!

### I have [ClearLag](https://poggit.pmmp.io/p/ClearLag/2.1.0) and it removes the Shopkeepers entites!
Edit the config of ClearLag changing to `false` [this option](https://github.com/tobiaskirchmaier/ClearLag/blob/03e2a03a5f8868216dfc89eb78f51523ff228d6b/resources/config.yml#L84C5-L84C24).

### How can I change the skin of a Shopkeeper
We actually support a Skin System, please read up.

### OMG I CAN'T ACCESS TO THE INVENTORYM uyigqwieduwefibef
If the Shopkeeper is an Admin Shop it does not have an inventory!

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

## Roadmap
- [x] Update the item object because now it's the old one
- [x] Make if the ContainerClose packet is received unset the `$this->trades->{PLAYER}` object because otherwise is OP!
- [x] Updated the item object with nbt
- [x] Working Admin shop (without inventory)
- [x] Inventory saving correctly 
- [x] Player shop
- [x] Shopkeeper's inventory updating when players buy
- [x] Shopkeeper deny a trade if the inventory is without the item
- [x] Double trade
- [x] Skin system
- [ ] Online shopkeeper editor
- [ ] Online shopkeeper shop (yes, you will be able to make shopping from your phone on the subway!)

## Contacts
You can contact me via:
- Discord: `@foxworn`
- Email: `foxworn3365@gmail.com`