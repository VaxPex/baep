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

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;
use pocketmine\utils\UUID;
use function count;

class AddPlayerPacket extends DataPacket{
	public const NETWORK_ID = ProtocolInfo::ADD_PLAYER_PACKET;

	/** @var UUID */
	public $uuid;
	/** @var string */
	public $username;
	/** @var int|null */
	public $entityUniqueId = null; //TODO
	/** @var int */
	public $entityRuntimeId;
	/** @var string */
	public $platformChatId = "";
	/** @var Vector3 */
	public $position;
	/** @var Vector3|null */
	public $motion;
	/** @var float */
	public $pitch = 0.0;
	/** @var float */
	public $yaw = 0.0;
	/** @var float|null */
	public $headYaw = null; //TODO
	/** @var ItemStackWrapper */
	public $item;
	/**
	 * @var mixed[][]
	 * @phpstan-var array<int, array{0: int, 1: mixed}>
	 */
	public $metadata = [];
	/** @var int */
	public $gameType = 0;
	public $playerPermission = 1 >> 1;
	public $commandPermission = 1 >> 1;
	public $abilityLayersSize = 2 >> 1;
	public $baseLayerType = 2 >> 1;
	public $abilitiesSet = (1 << 18) - 1; //all
	public $abilityValues = (1 << 6) - 1; //survival
	public $flyingSpeed = 0.1;
	public $walkingSpeed = 0.05;
	
	/** @var EntityLink[] */
	public $links = [];

	/** @var string */
	public $deviceID = ""; //TODO: fill player's device ID (???)
	/** @var int */
	public $buildPlatform = DeviceOS::UNKNOWN;

	protected function decodePayload(){
		$this->uuid = $this->getUUID();
		$this->username = $this->getString();
		$this->entityRuntimeId = $this->getEntityRuntimeId();
		$this->platformChatId = $this->getString();
		$this->position = $this->getVector3();
		$this->motion = $this->getVector3();
		$this->pitch = $this->getLFloat();
		$this->yaw = $this->getLFloat();
		$this->headYaw = $this->getLFloat();
		$this->item = ItemStackWrapper::read($this);
		$this->gameType = $this->getVarInt();
		$this->metadata = $this->getEntityMetadata();
		$this->entityUniqueId = $this->getEntityUniqueId();
		$this->playerPermission = $this->getUnsignedVarInt();
		$this->commandPermission = $this->getUnsignedVarInt();
		$this->abilityLayersSize = $this->getUnsignedVarInt();
		$this->baseLayerType = $this->getLShort();
		$this->abilitiesSet = $this->getLInt();
		$this->abilityValues = $this->getLInt();
		$this->flyingSpeed = $this->getLFloat();
		$this->walkingSpeed = $this->getLFloat();

		$linkCount = $this->getUnsignedVarInt();
		for($i = 0; $i < $linkCount; ++$i){
			$this->links[$i] = $this->getEntityLink();
		}

		$this->deviceID = $this->getString();
		$this->buildPlatform = $this->getLInt();
	}

	protected function encodePayload(){
		$this->putUUID($this->uuid);
		$this->putString($this->username);
		$this->putEntityRuntimeId($this->entityRuntimeId);
		$this->putString($this->platformChatId);
		$this->putVector3($this->position);
		$this->putVector3Nullable($this->motion);
		$this->putLFloat($this->pitch);
		$this->putLFloat($this->yaw);
		$this->putLFloat($this->headYaw ?? $this->yaw);
		$this->item->write($this);
		$this->putVarInt($this->gameType);
		$this->putEntityMetadata($this->metadata);
		$this->putLLong($this->entityUniqueId ?? $this->entityRuntimeId);// targetActorUniqueId
		$this->putUnsignedVarInt($this->playerPermission);
		$this->putUnsignedVarInt($this->commandPermission);
 		$this->putUnsignedVarInt($this->abilityLayersSize);
		$this->putLShort($this->baseLayerType);
 		$this->putLInt($this->abilitiesSet);
		$this->putLInt($this->abilityValues);
		$this->putLFloat($this->flyingSpeed);
		$this->putLFloat($this->walkingSpeed);

		$this->putUnsignedVarInt(count($this->links));
		foreach($this->links as $link){
			$this->putEntityLink($link);
		}

		$this->putString($this->deviceID);
		$this->putLInt($this->buildPlatform);
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAddPlayer($this);
	}
}
