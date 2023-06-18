# Shopkeepers
Shopkeepers on PMMP? Are u serious?!'!?1''1'!?!1

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