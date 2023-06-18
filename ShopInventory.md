I loved this file because it was beautiful

```php
<?php

namespace FoxWorn3365\Shopkeepers\utils;

use pocketmine\inventory\Inventory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\Tag;
use pocketmine\item\Item;

final class ShopInventory {
    public static function serialize(Inventory $inventory) : array {
        $items = [];
        for ($a = 0; $a < $inventory->getSize(); $a++) {
            if (!$inventory->isSlotEmpty($a)) {
                $items[$a] = json_decode(self::itemSerializator($inventory->getItem($a)));
            }
        }
        return $items;
    }

    public static function unserialize(string $json) : array {
        $items = [];
        foreach (json_decode($json) as $slot => $itemInfo) {
            $items[$slot] = SerializedItem::decode($itemInfo);
        }
        return $items;
    }

    public static function itemSerializator(Item $item) : string {
        $object = self::compoundJsonizer($item->getNamedTag());
        $iteminfo = new \stdClass;
        $iteminfo->networkitem = ItemUtils::encode($item, true);
        $iteminfo->networkitem->type = 0;
        $iteminfo->count = $item->getCount();
        $iteminfo->customname = $item->getCustomName() ?? null;
        $iteminfo->serializednbt = $object;
        return json_encode($iteminfo);
    }

    public static function compoundJsonizer(CompoundTag $tag, bool $toObject = false) : object|string {
        $object = new \stdClass;
        $object->__mapping = new \stdClass;
        foreach ($tag->getValue() as $name => $value) {
            //print_r($value);
            if ($value instanceof CompoundTag) {
                $t = "CompoundTag";
                $value = self::compoundJsonizer($value, true);
            } elseif ($value instanceof Tag) {
                $t = $value::class;
                $value = $value->getValue();
            }

            if ($value instanceof Tag) {
                // OK WTF?
                $value = $value->getValue();
            }

            $object->__mapping->{$name} = str_replace('pocketmine\nbt\tag\\', '', $t);

            $object->{$name} = $value;
        }

        if ($toObject) {
            return $object;
        }

        return json_encode($object);
    }

    public static function jsonCompoundier(string $json) : CompoundTag {
        $tag = new CompoundTag();
        $mapping = null;
        foreach (json_decode($json) as $key => $value) {
            if ($key == "__mapping") {
                $mapping = $value;
                continue;
            }

            $type = str_replace('Tag', '', 'set' . $mapping->{$key});

            if ($type == 'setCompound') {
                $tag->setTag($key, self::jsonCompoundier(json_encode($value)));
                continue;
            }
        
            $tag->{$type}($key, $value);
        }
        return $tag;
    }
}
```