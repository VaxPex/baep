<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\entity\Attribute;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use function count;

class AddActorPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ADD_ACTOR_PACKET;

	/*
	 * Really really really really really nasty hack, to preserve backwards compatibility.
	 * We can't transition to string IDs within 3.x because the network IDs (the integer ones) are exposed
	 * to the API in some places (for god's sake shoghi).
	 *
	 * TODO: remove this on 4.0
	 */
	public const LEGACY_ID_MAP_BC = [
		//NEW
		FEK::AXOLOTL => "minecraft:axolotl",
		FEK::GOAT => "minecraft:goat",
		FEK::GLOW_SQUID => "minecraft:glow_squid",
		FEK::PILLAGER => "minecraft:pillager",

		//OLD
		FEK::NPC => "minecraft:npc",
		FEK::PLAYER => "minecraft:player",
		FEK::WITHER_SKELETON => "minecraft:wither_skeleton",
		FEK::HUSK => "minecraft:husk",
		FEK::STRAY => "minecraft:stray",
		FEK::WITCH => "minecraft:witch",
		FEK::ZOMBIE_VILLAGER => "minecraft:zombie_villager",
		FEK::BLAZE => "minecraft:blaze",
		FEK::MAGMA_CUBE => "minecraft:magma_cube",
		FEK::GHAST => "minecraft:ghast",
		FEK::CAVE_SPIDER => "minecraft:cave_spider",
		FEK::SILVERFISH => "minecraft:silverfish",
		FEK::ENDERMAN => "minecraft:enderman",
		FEK::SLIME => "minecraft:slime",
		FEK::ZOMBIE_PIGMAN => "minecraft:zombie_pigman",
		FEK::SPIDER => "minecraft:spider",
		FEK::SKELETON => "minecraft:skeleton",
		FEK::CREEPER => "minecraft:creeper",
		FEK::ZOMBIE => "minecraft:zombie",
		FEK::SKELETON_HORSE => "minecraft:skeleton_horse",
		FEK::MULE => "minecraft:mule",
		FEK::DONKEY => "minecraft:donkey",
		FEK::DOLPHIN => "minecraft:dolphin",
		FEK::TROPICALFISH => "minecraft:tropicalfish",
		FEK::WOLF => "minecraft:wolf",
		FEK::SQUID => "minecraft:squid",
		FEK::DROWNED => "minecraft:drowned",
		FEK::SHEEP => "minecraft:sheep",
		FEK::MOOSHROOM => "minecraft:mooshroom",
		FEK::PANDA => "minecraft:panda",
		FEK::SALMON => "minecraft:salmon",
		FEK::PIG => "minecraft:pig",
		FEK::VILLAGER => "minecraft:villager",
		FEK::COD => "minecraft:cod",
		FEK::PUFFERFISH => "minecraft:pufferfish",
		FEK::COW => "minecraft:cow",
		FEK::CHICKEN => "minecraft:chicken",
		FEK::BALLOON => "minecraft:balloon",
		FEK::LLAMA => "minecraft:llama",
		FEK::IRON_GOLEM => "minecraft:iron_golem",
		FEK::RABBIT => "minecraft:rabbit",
		FEK::SNOW_GOLEM => "minecraft:snow_golem",
		FEK::BAT => "minecraft:bat",
		FEK::OCELOT => "minecraft:ocelot",
		FEK::HORSE => "minecraft:horse",
		FEK::CAT => "minecraft:cat",
		FEK::POLAR_BEAR => "minecraft:polar_bear",
		FEK::ZOMBIE_HORSE => "minecraft:zombie_horse",
		FEK::TURTLE => "minecraft:turtle",
		FEK::PARROT => "minecraft:parrot",
		FEK::GUARDIAN => "minecraft:guardian",
		FEK::ELDER_GUARDIAN => "minecraft:elder_guardian",
		FEK::VINDICATOR => "minecraft:vindicator",
		FEK::WITHER => "minecraft:wither",
		FEK::ENDER_DRAGON => "minecraft:ender_dragon",
		FEK::SHULKER => "minecraft:shulker",
		FEK::ENDERMITE => "minecraft:endermite",
		FEK::MINECART => "minecraft:minecart",
		FEK::HOPPER_MINECART => "minecraft:hopper_minecart",
		FEK::TNT_MINECART => "minecraft:tnt_minecart",
		FEK::CHEST_MINECART => "minecraft:chest_minecart",
		FEK::COMMAND_BLOCK_MINECART => "minecraft:command_block_minecart",
		FEK::ARMOR_STAND => "minecraft:armor_stand",
		FEK::ITEM => "minecraft:item",
		FEK::TNT => "minecraft:tnt",
		FEK::FALLING_BLOCK => "minecraft:falling_block",
		FEK::XP_BOTTLE => "minecraft:xp_bottle",
		FEK::XP_ORB => "minecraft:xp_orb",
		FEK::EYE_OF_ENDER_SIGNAL => "minecraft:eye_of_ender_signal",
		FEK::ENDER_CRYSTAL => "minecraft:ender_crystal",
		FEK::SHULKER_BULLET => "minecraft:shulker_bullet",
		FEK::FISHING_HOOK => "minecraft:fishing_hook",
		FEK::DRAGON_FIREBALL => "minecraft:dragon_fireball",
		FEK::ARROW => "minecraft:arrow",
		FEK::SNOWBALL => "minecraft:snowball",
		FEK::EGG => "minecraft:egg",
		FEK::PAINTING => "minecraft:painting",
		FEK::THROWN_TRIDENT => "minecraft:thrown_trident",
		FEK::FIREBALL => "minecraft:fireball",
		FEK::SPLASH_POTION => "minecraft:splash_potion",
		FEK::ENDER_PEARL => "minecraft:ender_pearl",
		FEK::LEASH_KNOT => "minecraft:leash_knot",
		FEK::WITHER_SKULL => "minecraft:wither_skull",
		FEK::WITHER_SKULL_DANGEROUS => "minecraft:wither_skull_dangerous",
		FEK::BOAT => "minecraft:boat",
		FEK::LIGHTNING_BOLT => "minecraft:lightning_bolt",
		FEK::SMALL_FIREBALL => "minecraft:small_fireball",
		FEK::LLAMA_SPIT => "minecraft:llama_spit",
		FEK::AREA_EFFECT_CLOUD => "minecraft:area_effect_cloud",
		FEK::LINGERING_POTION => "minecraft:lingering_potion",
		FEK::FIREWORKS_ROCKET => "minecraft:fireworks_rocket",
		FEK::EVOCATION_FANG => "minecraft:evocation_fang",
		FEK::EVOCATION_ILLAGER => "minecraft:evocation_illager",
		FEK::VEX => "minecraft:vex",
		FEK::AGENT => "minecraft:agent",
		FEK::ICE_BOMB => "minecraft:ice_bomb",
		FEK::PHANTOM => "minecraft:phantom",
		FEK::TRIPOD_CAMERA => "minecraft:tripod_camera",
		FEK::RAVAGER => "minecraft:ravager",
		FEK::FOX => "minecraft:fox",
		FEK::BEE => "minecraft:bee",
		FEK::STRIDER => "minecraft:strider",
		FEK::PIGLIN => "minecraft:piglin",
		FEK::HOGLIN => "minecraft:hoglin",
		FEK::ZOGLIN => "minecraft:zoglin",

	];

	/** @var int|null */
	public $entityUniqueId = null; //TODO
	/** @var int */
	public $entityRuntimeId;
	/** @var string */
	public $type;
	/** @var Vector3 */
	public $position;
	/** @var Vector3|null */
	public $motion;
	/** @var float */
	public $pitch = 0.0;
	/** @var float */
	public $yaw = 0.0;
	/** @var float */
	public $headYaw = 0.0;

	/** @var Attribute[] */
	public $attributes = [];
	/**
	 * @var mixed[][]
	 * @phpstan-var array<int, array{0: int, 1: mixed}>
	 */
	public $metadata = [];
	/** @var EntityLink[] */
	public $links = [];

	protected function decodePayload(){
		$this->entityUniqueId = $this->getEntityUniqueId();
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->type = $this->getString();
		$this->position = $this->getVector3();
		$this->motion = $this->getVector3();
		$this->pitch = $this->getLFloat();
		$this->yaw = $this->getLFloat();
		$this->headYaw = $this->getLFloat();

		$attrCount = $this->getUnsignedVarInt();
		for($i = 0; $i < $attrCount; ++$i){
			$name = $this->getString();
			$min = $this->getLFloat();
			$current = $this->getLFloat();
			$max = $this->getLFloat();
			$attr = Attribute::getAttributeByName($name);

			if($attr !== null){
				$attr->setMinValue($min);
				$attr->setMaxValue($max);
				$attr->setValue($current);
				$this->attributes[] = $attr;
			}else{
				throw new \UnexpectedValueException("Unknown attribute type \"$name\"");
			}
		}

		$this->metadata = $this->getEntityMetadata();
		$linkCount = $this->getUnsignedVarInt();
		for($i = 0; $i < $linkCount; ++$i){
			$this->links[] = $this->getEntityLink();
		}
	}

	protected function encodePayload(){
		$this->putEntityUniqueId($this->entityUniqueId ?? $this->entityRuntimeId);
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putString($this->type);
		$this->putVector3($this->position);
		$this->putVector3Nullable($this->motion);
		$this->putLFloat($this->pitch);
		$this->putLFloat($this->yaw);
		$this->putLFloat($this->headYaw);

		$this->putUnsignedVarInt(count($this->attributes));
		foreach($this->attributes as $attribute){
			$this->putString($attribute->getName());
			$this->putLFloat($attribute->getMinValue());
			$this->putLFloat($attribute->getValue());
			$this->putLFloat($attribute->getMaxValue());
		}

		$this->putEntityMetadata($this->metadata);
		$this->putUnsignedVarInt(count($this->links));
		foreach($this->links as $link){
			$this->putEntityLink($link);
		}
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAddActor($this);
	}
}

class FEK {
	public const CHICKEN = 10;
	public const COW = 11;
	public const PIG = 12;
	public const SHEEP = 13;
	public const WOLF = 14;
	public const VILLAGER = 15;
	public const MOOSHROOM = 16;
	public const SQUID = 17;
	public const RABBIT = 18;
	public const BAT = 19;
	public const IRON_GOLEM = 20;
	public const SNOW_GOLEM = 21;
	public const OCELOT = 22;
	public const HORSE = 23;
	public const DONKEY = 24;
	public const MULE = 25;
	public const SKELETON_HORSE = 26;
	public const ZOMBIE_HORSE = 27;
	public const POLAR_BEAR = 28;
	public const LLAMA = 29;
	public const PARROT = 30;
	public const DOLPHIN = 31;
	public const ZOMBIE = 32;
	public const CREEPER = 33;
	public const SKELETON = 34;
	public const SPIDER = 35;
	public const ZOMBIE_PIGMAN = 36;
	public const SLIME = 37;
	public const ENDERMAN = 38;
	public const SILVERFISH = 39;
	public const CAVE_SPIDER = 40;
	public const GHAST = 41;
	public const MAGMA_CUBE = 42;
	public const BLAZE = 43;
	public const ZOMBIE_VILLAGER = 44;
	public const WITCH = 45;
	public const STRAY = 46;
	public const HUSK = 47;
	public const WITHER_SKELETON = 48;
	public const GUARDIAN = 49;
	public const ELDER_GUARDIAN = 50;
	public const NPC = 51;
	public const WITHER = 52;
	public const ENDER_DRAGON = 53;
	public const SHULKER = 54;
	public const ENDERMITE = 55;
	public const AGENT = 56, LEARN_TO_CODE_MASCOT = 56;
	public const VINDICATOR = 57;
	public const PHANTOM = 58;
	public const RAVAGER = 59;

	public const ARMOR_STAND = 61;
	public const TRIPOD_CAMERA = 62;
	public const PLAYER = 63;
	public const ITEM = 64;
	public const TNT = 65;
	public const FALLING_BLOCK = 66;
	public const MOVING_BLOCK = 67;
	public const XP_BOTTLE = 68;
	public const XP_ORB = 69;
	public const EYE_OF_ENDER_SIGNAL = 70;
	public const ENDER_CRYSTAL = 71;
	public const FIREWORKS_ROCKET = 72;
	public const THROWN_TRIDENT = 73, TRIDENT = 73;
	public const TURTLE = 74;
	public const CAT = 75;
	public const SHULKER_BULLET = 76;
	public const FISHING_HOOK = 77;
	public const CHALKBOARD = 78;
	public const DRAGON_FIREBALL = 79;
	public const ARROW = 80;
	public const SNOWBALL = 81;
	public const EGG = 82;
	public const PAINTING = 83;
	public const MINECART = 84;
	public const FIREBALL = 85, LARGE_FIREBALL = 85;
	public const SPLASH_POTION = 86;
	public const ENDER_PEARL = 87;
	public const LEASH_KNOT = 88;
	public const WITHER_SKULL = 89;
	public const BOAT = 90;
	public const WITHER_SKULL_DANGEROUS = 91;
	public const LIGHTNING_BOLT = 93;
	public const SMALL_FIREBALL = 94;
	public const AREA_EFFECT_CLOUD = 95;
	public const HOPPER_MINECART = 96;
	public const TNT_MINECART = 97;
	public const CHEST_MINECART = 98;

	public const COMMAND_BLOCK_MINECART = 100;
	public const LINGERING_POTION = 101;
	public const LLAMA_SPIT = 102;
	public const EVOCATION_FANG = 103;
	public const EVOCATION_ILLAGER = 104;
	public const VEX = 105;
	public const ICE_BOMB = 106;
	public const BALLOON = 107;
	public const PUFFERFISH = 108;
	public const SALMON = 109;
	public const DROWNED = 110;
	public const TROPICALFISH = 111, TROPICAL_FISH = 111;
	public const COD = 112, FISH = 112;
	public const PANDA = 113;
	public const FOX = 121;
	public const BEE = 122;
	public const PIGLIN = 123;
	public const HOGLIN = 124;
	public const STRIDER = 125;
	public const ZOGLIN = 126;
	public const AXOLOTL = 130;
	public const GLOW_SQUID = 139;
	public const GOAT = 128;
	public const PILLAGER = 114;
}