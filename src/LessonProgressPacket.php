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

use pocketmine\network\mcpe\NetworkSession;

/**
 * Handled only in Education mode. Used to fire telemetry reporting on the client.
 */
class LessonProgressPacket extends DataPacket {
	public const NETWORK_ID = ProtocolInfo::LESSON_PROGRESS_PACKET;

	public const ACTION_START = 0;
	public const ACTION_FINISH = 1;
	public const ACTION_RESTART = 2;

	private int $action;
	private int $score;
	private string $activityId;

	/**
	 * @generate-create-func
	 */
	public static function create(int $action, int $score, string $activityId) : self{
		$result = new self;
		$result->action = $action;
		$result->score = $score;
		$result->activityId = $activityId;
		return $result;
	}

	public function getAction() : int{ return $this->action; }

	public function getScore() : int{ return $this->score; }

	public function getActivityId() : string{ return $this->activityId; }

	protected function decodePayload(){
		$this->action = $this->getVarInt();
		$this->score = $this->getVarInt();
		$this->activityId = $this->getString();
	}

	protected function encodePayload(){
		$this->putVarInt($this->action);
		$this->putVarInt($this->score);
		$this->putString($this->activityId);
	}

	public function handle(NetworkSession $handler) : bool{
		return $handler->handleLessonProgress($this);
	}
}