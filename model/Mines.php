<?php

class Mines {
	private $boxes;
	private $mines;
	private $array;

	public function __construct($array, $mines)
	{
		$this->array = $array;
		$this->boxes = count($this->array);
		$this->mines = $mines;
		$this->createMines();
	}

	// Lägger slumpvis till minor i Arrayen.
	private function createMines()
	{
		while($this->countMines() < $this->mines) {
			$r = $this->getRandomNumber();

			if (!$this->array[$r]->getMine())
			{
				$this->array[$r]->changeMine();
			}
		} 
	}
	
	// Ger ett slumpat tal.
	private function getRandomNumber()
	{
		return $r = rand(0,($this->boxes - 1));
	}

	public function getMines()
	{
		return $this->array;
	}

	// Räknar antal minor i Arrayen.
	private function countMines()
	{
		$counter = 0;
		foreach ($this->array as $v) {
			if ($v->getMine())
			{
				$counter++;
			}
		}
		return $counter;
	}
}