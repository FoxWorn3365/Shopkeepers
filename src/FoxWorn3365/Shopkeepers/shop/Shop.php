<?php

namespace FoxWorn3365\Shopkeepers\shop;

use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\IntMetadataProperty;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\network\mcpe\protocol\UpdateTradePacket;
use pocketmine\player\Player;

use FoxWorn3365\Shopkeepers\entity\Shopkeeper;

class Shop {
    protected ListTag $elements;
    protected Player $player;
    protected Shopkeeper $shop;
	protected string $title;

    function __construct(ListTag $elements, Player $player, Shopkeeper $shop, string $title = "Trade") {
        $this->elements = $elements;
        $this->player = $player;
        $this->shop = $shop;
		$this->title = $title;
    }

    public function send(): void {
		$offers = new CacheableNbt(
			CompoundTag::create()
				->setTag("Recipes", $this->elements)
				->setTag("TierExpRequirements", new ListTag([
					CompoundTag::create()->setInt("0", 0),
					CompoundTag::create()->setInt("1", 10),
					CompoundTag::create()->setInt("2", 20),
				])) //TODO: move to merchant recipes list
		);
		$packet = UpdateTradePacket::create(
			windowId: $this->player->getNetworkSession()->getInvManager()->getCurrentWindowId(),
			windowType: WindowTypes::TRADING,
			windowSlotCount: 0,
			tradeTier: 2,
			traderActorUniqueId: $this->shop->getId(),
			playerActorUniqueId: $this->player->getId(),
			displayName: $this->title,
			isV2Trading: true,
			isEconomyTrading: true,
			offers: $offers
		);

		$metadata = [
			EntityMetadataProperties::TRADE_TIER         => new IntMetadataProperty($packet->tradeTier),
			EntityMetadataProperties::TRADE_XP           => new IntMetadataProperty(1000),
			EntityMetadataProperties::MAX_TRADE_TIER     => new IntMetadataProperty(3),
			EntityMetadataProperties::TRADING_PLAYER_EID => new IntMetadataProperty($this->player->getId())
		];

		foreach ($metadata as $k => $metadataProperty) {
			$this->shop->getNetworkProperties()->setInt($k, $metadataProperty->getValue());
		}

		$this->player->getNetworkSession()->sendDataPacket($packet);
	}
}