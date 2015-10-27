<?php 

class Timer {
	private $startTime;
	private $endTime;

	public function __construct()
	{
		$this->startTime = time();
	}

	public function setEndTime() {
		$this->endTime = time();
	}

	public function getGameTime()
	{
		return $this->endTime - $this->startTime;
	}

	public function getStartTime()
	{
		return $this->startTime;
	}

	public function getEndTime()
	{
		return $this->endTime;
	}
}