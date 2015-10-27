<?php

class HighscoreView {

	private $resDAL;

	public function __construct($rDAL)
	{
		$this->resDAL = $rDAL;
	}
	
	// Ger en lista med topplista, beroende på nivå.
	public function renderHighscore($lvl)
	{
		$resultsArray = $this->resDAL->getTop20($lvl);
		$nameString = '<div class="highscoreNumber">#</div>
						<div class="highscoreName">Namn</div>
						<div class="highscoreTime">Tid (m:s)</div>
						<div class="highscoreDate">Datum</div>';
						
		for ($x = 0; $x < count($resultsArray); $x++) {
			if ($x == 0)
			{
				$nameString .= '<div class="highscoreNumber">1.</div>
							<div class="highscoreName">' . $resultsArray[$x]->getName() . '</div>
							<div class="highscoreTime">' . $this->getTimeString($resultsArray[$x]->getTime()) . '</div>
							<div class="highscoreDate">' . $this->getDateString($resultsArray[$x]->getDate()) . '</div>';
			}
			else if ($resultsArray[$x]->getTime() == $resultsArray[$x - 1]->getTime())
			{
				$nameString .= '<div class="highscoreNumber"></div>
							<div class="highscoreName">' . $resultsArray[$x]->getName() . '</div>
							<div class="highscoreTime">' . $this->getTimeString($resultsArray[$x]->getTime()) . '</div>
							<div class="highscoreDate">' . $this->getDateString($resultsArray[$x]->getDate()) . '</div>';
			}
			else {
				$nameString .= '<div class="highscoreNumber">' . ($x + 1) . '. </div>
							<div class="highscoreName">' . $resultsArray[$x]->getName() . '</div>
							<div class="highscoreTime">' . $this->getTimeString($resultsArray[$x]->getTime()) . '</div>
							<div class="highscoreDate">' . $this->getDateString($resultsArray[$x]->getDate()) . '</div>';
			}
		} 

		if (count($resultsArray) == 0)
		{
			$nameString .= '<div>Inga resultat finns registrerade.</div>';
		}

		return '<h2>Topplista ' . $lvl . '</h2>' . $this->highscoreMenuChoices() . 
		'<div id="highscoreBox">' . 
			$nameString
		 . '</div>
		'
		. $this->goBackToStart();
	}

	// Meny till topplista.
	private function highscoreMenuChoices()
	{
		return '<a class="linkButtonInline" href="?highscore=9">9x9</a>
				<a class="linkButtonInline" href="?highscore=16">16x16</a>
				<a class="linkButtonInline" href="?highscore=22">22x22</a>
				<br>
				<br>
				';
	}

	// "Tillbaka-knapp".
	private function goBackToStart()
	{
		return '<br>
				<br>
				<a class="linkButton" href="?">
					Gå till startmenyn
				</a>
				';
	}

	// Tidssträng.
	public function getTimeString($t)
	{
		$mins = floor($t / 60);
		$secs = $t % 60;

		if ($mins >= 0 && $mins < 10)
		{
			$mins = '0' . $mins;
		}

		if ($secs >= 0 && $secs < 10)
		{
			$secs = '0' . $secs;
		}

		return $mins . ':' . $secs; 
	}

	// Datumsträng.
	public function getDateString($d)
	{
		return date("Y-m-d",$d);
	}
}