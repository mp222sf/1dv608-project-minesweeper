<?php
require_once('model/Board.php');
require_once('model/Timer.php');

class MinesweeperGame {
	private $b;
	private $t;

	public function __construct($ver, $hor, $mines)
	{
		$this->t = new Timer();
		$this->b = new Board($ver,$hor,$mines,$this->t);
	}

	// Flaggar en Box.
	public function flagBox($x)
	{
		$this->b->flagBox($x);
	}

	// Öppnar en Box.
	public function openBox($x)
	{
		$this->b->openBox($x);
	}

	// Hämtar Board-objektet
	public function getTheBoard()
	{
		return $this->b;
	}

	// Hämtar Timer-objektet.
	public function getTheTimer()
	{
		return $this->t;
	}
}