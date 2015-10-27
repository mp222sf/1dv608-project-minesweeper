<?php
require_once('model/Mines.php');
require_once('model/Box.php');


class Board {
	private static $minesIndex = 0;
	private static $openIndex = 1;
	private static $flagIndex = 2;

	private $boxV;
	private $boxH;
	private $boxes;
	private $mines;
	private $boxArray = array();
	private $minesGen;
	private $explodedMine;
	private $time;
	private $formUp;
	private $formDown;
	private $formRight;
	private $formLeft;
	private $formUpLeft;
	private $formDownLeft;
	private $formUpRight;
	private $formDownRight;

	public function __construct($boxesV, $boxesH, $mines, $time)
	{
		$this->boxV = $boxesV;
		$this->boxH = $boxesH;
		$this->boxes = $this->boxV * $this->boxH;
		$this->mines = $mines;

		// Skapar Form-variabler.
		$this->formUp = - $this->boxV;
		$this->formDown = + $this->boxV;
		$this->formRight = + 1;
		$this->formLeft = - 1;
		$this->formUpLeft = - $this->boxV - 1;
		$this->formDownLeft = + $this->boxV - 1;
		$this->formUpRight = - $this->boxV + 1;
		$this->formDownRight = + $this->boxV + 1;

		$this->time = $time;
		$this->createBoxes();
	}

	// Skapar Box-objekt, innehåller minor.
	private function createBoxes()
	{
		for ($x = 0; $x < $this->boxes; $x++) {
		    $this->boxArray[$x] = new Box();
		} 

		$this->minesGen = new Mines($this->boxArray, $this->mines);
		$this->boxArray = $this->minesGen->getMines();
	}

	// Öppnar en Box, kontrollerar om det är en mina.
	// Om öppnad Box är angränsar till 0 minor, så öppnas Box:arna runt denna Box.
	public function openBox($box)
	{
		if ($this->getBoxByID($box) instanceof Box && !$this->getBoxByID($box)->getFlag())
		{
			if ($this->getBoxByID($box)->getOpen())
			{
				if ($this->countAroundX($box, self::$minesIndex) == $this->countAroundX($box, self::$flagIndex))
				{
					$this->openBoxesAroundX($box);
				}
			}

			$this->getBoxByID($box)->changeOpen();
			if ($this->checkIfGameIsLost())
			{
				$this->explodedMine = $box;
				$this->time->setEndTime();
			}
			else {
				if ($this->countAroundX($box, self::$minesIndex) == 0)
				{
					$this->openEmptyBoxes();
				}
			}

			if ($this->checkIfGameWon())
			{
				$this->time->setEndTime();
			}
		}
	}

	// Markerar en Box med en flagga. Tar bort flagga om den redan finns.
	public function flagBox($box)
	{
		if ($this->getBoxByID($box) instanceof Box && !$this->getBoxByID($box)->getOpen())
		{
			$this->getBoxByID($box)->changeFlag();
		}
	}

	// Kontrollerar om alla minfria Box:ar är öppna, dvs om spelet är vunnet.
	public function checkIfGameWon()
	{
		for ($i = 0; $i < count($this->boxArray); $i++) {
			if (!$this->getBoxByID($i)->getMine() && !$this->getBoxByID($i)->getOpen())
			{
				return false;
			}
		}
		return true;
	}

	// Kontrollerar om en Box som innehåller en mina är öppen, dvs att spelet är förlorat.
	public function checkIfGameIsLost()
	{
		for ($i = 0; $i < count($this->boxArray); $i++) {
			if ($this->getBoxByID($i)->getMine() && $this->getBoxByID($i)->getOpen())
			{
				return true;
			}
		}
		return false;
	}

	public function checkCanPlay()
	{
		if ($this->checkIfGameWon() || $this->checkIfGameIsLost())
		{
			return false;
		}
		return true;
	}

	// Räknar antalet placerade flaggor.
	public function countPlacedFlags()
	{
		$count = 0;
		for ($i = 0; $i < count($this->boxArray); $i++) {
			if ($this->getBoxByID($i)->getFlag())
			{
				$count++;
			}
		}
		return $count;
	}

	// Öppnar upp öppnade Box:ar som angränsar till 0 minor.
	public function openEmptyBoxes()
	{
		$repeat = false;
	    for ($x = 0; $x < count($this->boxArray); $x++) {
	    	$repeat = false;
			if ($this->getBoxByID($x)->getOpen() && !$this->getBoxByID($x)->getMine() && $this->countAroundX($x, self::$minesIndex) == 0)
			{
				// UP
				$repeat = $this->openEmptyBox($x + $this->formUp, $repeat);
				// DOWN
				$repeat = $this->openEmptyBox($x + $this->formDown, $repeat);
				// RIGHT
				if ($x % $this->boxV != $this->formDownLeft)
				{
					$repeat = $this->openEmptyBox($x + $this->formRight, $repeat);
				}
				// LEFT
				if ($x % $this->boxV != 0)
				{
					$repeat = $this->openEmptyBox($x + $this->formLeft, $repeat);
				}
				// UP LEFT
				if ($x % $this->boxV != 0)
				{
					$repeat = $this->openEmptyBox($x + $this->formUpLeft, $repeat);
				}
				// UP RIGHT
				if ($x % $this->boxV != $this->formDownLeft)
				{
					$repeat = $this->openEmptyBox($x + $this->formUpRight, $repeat);
				}
				// DOWN LEFT
				if ($x % $this->boxV != 0)
				{
					$repeat = $this->openEmptyBox($x + $this->formDownLeft, $repeat);
				}
				// DOWN RIGHT
				if ($x % $this->boxV != $this->formDownLeft)
				{
					$repeat = $this->openEmptyBox($x + $this->formDownRight, $repeat);
				}
			}

			// If boxes has been open, reset the loop.
			if ($repeat == true)
			{
				$x = 0;
			}
		}
	}

	// Öppnar upp en Box som angränsar till 0 minor.
	public function openEmptyBox($box, $rep)
	{
		if ($this->getBoxByID($box) instanceof Box && !$this->getBoxByID($box)->getOpen() && !$this->getBoxByID($box)->getMine())
		{
			$this->getBoxByID($box)->changeOpen();
			return true;
		}
		if ($rep == true)
		{
			return true;
		}
		return false;
	}

	// Öppnar upp Box:ar runt en specifik Box.
	public function openBoxesAroundX($box)
	{	
		// UP.
		$this->openBoxAroundX($box + $this->formUp);
		// DOWN.
		$this->openBoxAroundX($box + $this->formDown);
		// RIGHT.
		if ($box % $this->boxV != $this->formDownLeft)
		{
			$this->openBoxAroundX($box + $this->formRight);
		}
		// LEFT
		if ($box % $this->boxV != 0)
		{
			$this->openBoxAroundX($box + $this->formLeft);
		}
		// UP LEFT
		if ($box % $this->boxV != 0)
		{
			$this->openBoxAroundX($box + $this->formUpLeft);
		}
		// UP RIGHT
		if ($box % $this->boxV != $this->formDownLeft)
		{
			$this->openBoxAroundX($box + $this->formUpRight);
		}
		// DOWN LEFT
		if ($box % $this->boxV != 0)
		{
			$this->openBoxAroundX($box + $this->formDownLeft);
		}
		// DOWN RIGHT
		if ($box % $this->boxV != $this->formDownLeft)
		{
			$this->openBoxAroundX($box + $this->formDownRight);
		}
	}

	// Öppnar upp en Box runt en specifik Box.
	public function openBoxAroundX($x)
	{
		if ($this->getBoxByID($x) instanceof Box && !$this->getBoxByID($x)->getOpen() && !$this->getBoxByID($x)->getFlag())
		{
			$this->getBoxByID($x)->changeOpen();
			$this->openEmptyBoxes();
		}
	}

	// Räknar en Box-typ (Mina, Öppnad, Flagga) runt en specifik Box.
	public function countAroundX($x, $type)
	{
		// Räknar antal minor runt X.
		$counter = 0;

		if ($x - $this->boxV < 0)
		{
			if ($x == 0)
			{
				$counter += $this->formulaCount($x, $type, $this->formRight);
				$counter += $this->formulaCount($x, $type, $this->formDownRight);
				$counter += $this->formulaCount($x, $type, $this->formDown);
			}
			else if ($x == ($this->boxV - 1))
			{
				$counter += $this->formulaCount($x, $type, $this->formLeft);
				$counter += $this->formulaCount($x, $type, $this->formDownLeft);
				$counter += $this->formulaCount($x, $type, $this->formDown);
			}
			else {
				$counter += $this->formulaCount($x, $type, $this->formLeft);
				$counter += $this->formulaCount($x, $type, $this->formDownLeft);
				$counter += $this->formulaCount($x, $type, $this->formDown);
				$counter += $this->formulaCount($x, $type, $this->formDownRight);
				$counter += $this->formulaCount($x, $type, $this->formRight);
			}
		}
		else if ($x % $this->boxV == 0)
		{
			if ($x == $this->boxes - $this->boxV)
			{
				$counter += $this->formulaCount($x, $type, $this->formUp);
				$counter += $this->formulaCount($x, $type, $this->formUpRight);
				$counter += $this->formulaCount($x, $type, $this->formRight);
			}
			else {
				$counter += $this->formulaCount($x, $type, $this->formUp);
				$counter += $this->formulaCount($x, $type, $this->formUpRight);
				$counter += $this->formulaCount($x, $type, $this->formRight);
				$counter += $this->formulaCount($x, $type, $this->formDownRight);
				$counter += $this->formulaCount($x, $type, $this->formDown);
			}
		}
		else if ($x % $this->boxV == $this->boxV - 1)
		{
			if ($x == $this->boxes - 1)
			{
				$counter += $this->formulaCount($x, $type, $this->formUp);
				$counter += $this->formulaCount($x, $type, $this->formUpLeft);
				$counter += $this->formulaCount($x, $type, $this->formLeft);
			}
			else {
				$counter += $this->formulaCount($x, $type, $this->formUp);
				$counter += $this->formulaCount($x, $type, $this->formUpLeft);
				$counter += $this->formulaCount($x, $type, $this->formLeft);
				$counter += $this->formulaCount($x, $type, $this->formDownLeft);
				$counter += $this->formulaCount($x, $type, $this->formDown);
			}
		}
		else if ($x + $this->boxV > $this->boxes) 
		{
			$counter += $this->formulaCount($x, $type, $this->formLeft);
			$counter += $this->formulaCount($x, $type, $this->formUpLeft);
			$counter += $this->formulaCount($x, $type, $this->formUp);
			$counter += $this->formulaCount($x, $type, $this->formUpRight);
			$counter += $this->formulaCount($x, $type, $this->formRight);
		}
		else {
			$counter += $this->formulaCount($x, $type, $this->formUp);
			$counter += $this->formulaCount($x, $type, $this->formUpRight);
			$counter += $this->formulaCount($x, $type, $this->formRight);
			$counter += $this->formulaCount($x, $type, $this->formDownRight);
			$counter += $this->formulaCount($x, $type, $this->formDown);
			$counter += $this->formulaCount($x, $type, $this->formDownLeft);
			$counter += $this->formulaCount($x, $type, $this->formLeft);
			$counter += $this->formulaCount($x, $type, $this->formUpLeft);
		}
		return $counter;
	}

	// Formler för att räkna Box-typer runt en Box.
	public function formulaCount($x, $type, $formula)
	{
		if ($type == 0 && $this->boxArray[$x + $formula]->getMine())
		{
			return 1;
		}
		if ($type == 1 && $this->boxArray[$x + $formula]->getOpen())
		{
			return 1;
		}
		if ($type == 2 && $this->boxArray[$x + $formula]->getFlag())
		{
			return 1;
		}
		return 0;
	}



	/* 
	 	GET-METODER.
	*/

	// Hämtar Box-arrayen.
	public function getBoard()
	{
		return $this->boxArray;
	}

	// Hämtar index för Mina i Box-arrayen.
	public function getMineIndex()
	{
		return self::$minesIndex;
	}

	// Hämtar index för Öppnad i Box-arrayen.
	public function getOpenIndex()
	{
		return self::$openIndex;
	}

	// Hämtar index för Flagga i Box-arrayen.
	public function getFlagIndex()
	{
		return self::$flagIndex;
	}

	// Hämtar en box, genom index i arrayen.
	public function getBoxByID($boxID)
	{
		if ($boxID >= 0 && $boxID < count($this->boxArray))
		{
			return $this->boxArray[$boxID];
		}
		return null;
	}

	// Hämtar antalet minor på spelplanen.
	public function getAmountOfMines()
	{
		return $this->mines;
	}

	// Returnerar Box:en som innehåller den exploderande minan.
	public function getExplodedMine()
	{
		return $this->explodedMine;
	}

}