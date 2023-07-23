<?php

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
 * Current file: /utils/SkinUtils.php
 * STATIC CLASS
 * Description: Implements an easy way to manage skins using Himbeer\LibSkin\SkinConverter
 */

namespace FoxWorn3365\Shopkeepers\utils;

use pocketmine\entity\Skin;

use Himbeer\LibSkin\SkinConverter;

final class SkinUtils {
    public static function find(string $shop, string $name, string $data_dir) : bool {
        if (file_exists("{$data_dir}skins/{$name}_{$shop}.png")) {
            return true;
        }
        return false;
    }

    public static function import(string $shop, string $name, string $data_dir) : Skin|bool {
        if (self::find($shop, $name, $data_dir)) {
            return new Skin('Standard_Custom', SkinConverter::imageToSkinDataFromPngPath("{$data_dir}skins/{$name}_{$shop}.png"));
        }
        return false;
    }

    public static function load(string $shop, string $name, string $data_dir) : Skin|bool {
        return self::import($shop, $name, $data_dir);
    }

    public static function get(string $shop, string $name, string $data_dir) : Skin|bool {
        return self::import($shop, $name, $data_dir);
    }
}