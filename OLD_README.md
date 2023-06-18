# Shopkeepers
Shopkeepers on PMMP? Are u serious?!'!?1''1'!?!1

## InventoryAPI
Thanks to [Muqsit](https://github.com/Muqsit) for the [InvMenu](https://github.com/Muqsit/InvMenu) virion who have contributed to the creation for this plugin!

## Objects
**Shopkeepers**, to be as configurable and manageable as possible saves IRT (In-Real-Time) player configurations in a .json file located in its PocketMine-MP plugins folder (`/path/to/server/plugin_data/`) and these are the objects that are serialized and then saved:
### Shop object
The main object, if a player have one or more shop will have also a shop object in his .json.
> **Note**<br>
> The Shop object is in an object array and have the title as key!

```json
{
    "author":"[PlayerName]",
    "admin":false,
    "title":"[ShopName]",
    "economyBased":false,   // NOT SUPPORTED YET!
    "inventory":[],  // A list of all items in the villager inventory. (DOUBLE_CHEST)!
    "items":[
        itemObjects
    ]
}
```

### Item Object
How we save items to sell and buy.
```json
{
    "sell":{
        serializedItemObject
    },
    "buy":{
        serializedItemObject
    }
}
```

### serializedItemObject
The plugin needs to save items as accurately as possible, so we created this new object called `serializedItemObject` which replaced the previous `itemObject` and provides great reliability due to the fact that:
- NBT tags are also saved, serialized
- A potential custom name is also saved
Thanks to this we can load items into inventories and shopkeepers with great accuracy and ease!

| Name | Type | Description |
| --- | --- | --- |
| networkitem | object | The network item to translate the base item |
| count | int | The number of the item(s) |
| customname | string\|null | The custom name if present |
| serializednbt | object | The serialized NBT data of the item (NOT DOCUMENTED HERE!) |
### NetworkItem
| Name | Type | Description |
| --- | --- | --- |
| id | int | The Vanilla Block ID |
| meta | int | The meta value |
| blockRuntimeId | int | | 
| type | int | The loading type: 0 is network, 1 is static |


```json
{
    "networkitem":{
        "id":35,
        "meta":1,
        "blockRuntimeId":0,
        "type":0
    },
    "count":1,
    "customname":null,
    "serializednbt":serializedNbtObject*
}
```


## Roadmap
- [x] Update the item object because now it's the old one
- [x] Make if the ContainerClose packet is received unset the `$this->trades->{PLAYER}` object because otherwise is OP!
- [x] Updated the item object with nbt
- [x] Working Admin shop (without inventory)
- [x] Inventory saving correctly 
- [ ] Player shop