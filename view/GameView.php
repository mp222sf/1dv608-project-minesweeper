<?php
require_once('view/HighscoreView.php');

class GameView {
	private static $qsHighscore = 'highscore';
	private static $qsRules = 'rules';
	private static $postSG9x9 = 'startGame9x9';
	private static $postSG16x16 = 'startGame16x16';
	private static $postSG22x22 = 'startGame22x22';
	private static $postRG = 'restartGame';
	private static $postSS = 'submitScore';
	private static $postIHighscore = 'inputHighscore';
	private static $boxClick = "clickedBox";
	private static $flagClick = "addFlag";

	private $game;
	private $res;
	private $hsv;
	private $resDAL;

	public function __construct($g, $r, $rDAL)
	{
		if ($g instanceof MinesweeperGame)
		{
			$this->game = $g;
		}
		if ($r instanceof Result)
		{
			$this->res = $r;
		}
		if ($rDAL instanceof ResultDAL)
		{
			$this->resDAL = $rDAL;
		}
		
		$this->hsv = new HighscoreView($this->resDAL);
	}

	public function render() {

		// Finns ett aktivt spel.
		if ($this->game instanceof MinesweeperGame)
		{
			return '
			<div id="' . $this->getBoardSize() . '">
			' . $this->getBoxes() . '
			</div>
			<div id="gameButtons">
				<form method="post">
					<input type="submit" name="' . self::$postRG . '" value="Gå tillbaka" />
				</form>
				<a title="Knapp för flagga" href="' . $this->getFlagButtonOnclick() . '">
					<img class="' . $this->getIsFlagButtonActive() . '" src="view/pics/boxflag.png">
				</a>
				<br>
				<div id="ResponseBox">
					<p>' . $this->responseMessage() . $this->placedFlagsResponse() . '</p>

					' . $this->getWinForm() . '
				</div>
			</div>
			<br><br>
			
			';
		}

		// Användaren trycker på "Topplistan".
		else if (isset($_GET[self::$qsHighscore]))
		{
			if ($_GET[self::$qsHighscore] == 9)
			{
				return $this->hsv->renderHighscore('9x9');
			}
			if ($_GET[self::$qsHighscore] == 16)
			{
				return $this->hsv->renderHighscore('16x16');
			}
			if ($_GET[self::$qsHighscore] == 22)
			{
				return $this->hsv->renderHighscore('22x22');
			}

			header('Location: ?' . self::$qsHighscore . '=9');
		}

		// Användaren trycker på "Spelregler".
		else if (isset($_GET[self::$qsRules]))
		{
			return 	'<h2>Spelregler</h2>
					<div id="rulesBox">
						Spelet går ut på att så fort som möjligt öppna alla minfria rutor i ett förutbestämt område av rutor. Under varje ruta finns ett av alternativen:<br>
						<ol>
						  <li>En mina – trycker spelaren på en sådan är spelet slut.</li>
						  <li>Ingenting – indikerar att rutan ej innehåller en mina och ej heller angränsas av en sådan.</li>
						  <li>En siffra – denna indikerar antalet minor som angränsar till rutan i fråga.</li>
						</ol>
						Man kan vänsterklicka på en dold ruta för att öppna den och då visas ett av ovanstående tre alternativ. Man kan trycka på "Flaggknappen" till höger och sedan vänsterklicka på en ruta för att flagga en ruta man tror innehåller en mina. Man kan vänsterklicka på en siffra för att tömma alla rutor som angränsar till denna, detta går enbart att göra om samma antal som siffran angränsande rutor är flaggade. Man markerar för att komma ihåg var minorna befinner sig.
					</div>
					<br>
					<br>
					<a class="linkButton" href="?">
						Gå till startmenyn
					</a>';
		}

		// Annars får man tillbaka View för startsidan.
		return '<form method="post">
					<input class="greenButton" type="submit" name="' . self::$postSG9x9 . '" value="9x9, 10 minor" />
					<br>
					<input class="yellowButton" type="submit" name="' . self::$postSG16x16 . '" value="16x16, 40 minor" />
					<br>
					<input class="redButton" type="submit" name="' . self::$postSG22x22 . '" value="22x22, 99 minor" />
					<br>
					<br>
					<br>
					<a class="linkButtonInline" href="?' . self::$qsHighscore . '">
						Topplistor
					</a>
					<a class="linkButtonInline" href="?' . self::$qsRules . '">
						Spelregler
					</a>
					<br>
				</form>';
	}

	public function didUserPressStart9x9()
	{
		return isset($_POST[self::$postSG9x9]);
	}

	public function didUserPressStart16x16()
	{
		return isset($_POST[self::$postSG16x16]);
	}

	public function didUserPressStart22x22()
	{
		return isset($_POST[self::$postSG22x22]);
	}

	public function didUserPressRestart()
	{
		return isset($_POST[self::$postRG]);
	}

	public function didUserPressSubmitScore()
	{
		return isset($_POST[self::$postSS]);
	}

	public function getInputName()
	{
		return $_POST[self::$postIHighscore];
	}

	// Hämtar Querystring för ClickedBox.
	public function getClickQS()
	{
		if (isset($_GET[self::$boxClick]))
		{
			return $_GET[self::$boxClick];
		}
		return null;
	}

	// Hämtar Querystring för AddFlag.
	public function getFlagClickQS()
	{
		if (isset($_GET[self::$flagClick]))
		{
			return $_GET[self::$flagClick];
		}
		return null;
	}

	// Hämtar Querystring-nament för AddFlag.
	public function getFlagQueryStringName()
	{
		return self::$flagClick;
	}

	// Hämtar Querystring-nament för ClickedBox.
	public function getBoxQueryStringName()
	{
		return self::$boxClick;
	}

	// Ger ett meddelande när spelet vunnits eller förlorats.
	private function responseMessage()
	{
		if ($this->game->getTheBoard()->getExplodedMine() != null)
		{
			return 'Spelet är slut.<br>';
		}
		if ($this->game->getTheBoard()->checkIfGameWon())
		{
			return 'Spelet är vunnet.<br>Tid: ' . $this->getTimeString() . '<br>';
		}
		return '';
	}

	// Formulär när spelet har vunnits.
	private function getWinForm()
	{
		$wrongName = "";
		if ($this->game->getTheBoard()->checkIfGameWon() && $this->res == null && $this->didUserPressSubmitScore())
		{
			$wrongName = "Fyll i ett namn.";
		}

		if ($this->game->getTheBoard()->checkIfGameWon() && $this->res == null)
		{
			return '<form method="post">
						<p class="redText">' . $wrongName . '</p>
						<p>Nickname:</p>
						<input type="text" name="' . self::$postIHighscore . '" />
						<input type="submit" name="' . self::$postSS . '" value="Spara resultat" />
					</form>';
		}
		else if ($this->game->getTheBoard()->checkIfGameWon() && $this->res != null) {
			return '<p>Resultat sparat för "' . $this->res->getName() . '".</p>';
		}
		else {
			return '';
		}
	}

	// Antal flaggor.
	private function placedFlagsResponse()
	{
		if ($this->game->getTheBoard()->checkIfGameWon())
		{
			return '';
		}
		return 'Antal placerade flaggor: ' . $this->game->getTheBoard()->countPlacedFlags() . '/' . $this->game->getTheBoard()->getAmountOfMines();
	}

	// Ger ett ID för aktuell storlek på Board.
	private function getBoardSize()
	{
		if (count($this->game->getTheBoard()->getBoard()) == 81)
		{
			return 'gameBoard9x9';
		}
		else if (count($this->game->getTheBoard()->getBoard()) == 256)
		{
			return 'gameBoard16x16';
		}
		return 'gameBoard22x22';
	}

	// Ger ett klassnamn (css) för FlagButton.
	public function getIsFlagButtonActive()
	{
		if ($this->getFlagClickQS() == 'true')
		{
			return 'activeBox';
		}
		return 'notActiveBox';
	}

	// Ger en querystring för "addFlag", beroende på om FlagButton är aktiv eller inte.
	public function getOnClickFlagButton()
	{
		if ($this->getFlagClickQS() == 'true')
		{
			return '?' . $this->getFlagQueryStringName() . '=false';
		}
		return '?' . $this->getFlagQueryStringName() . '=true';
	}

	// Titelattribut för varje Box.
	public function getTitleInfoAtBox()
	{
		if ($this->getFlagClickQS() == 'true')
		{
			return 'Flagga den här rutan';
		}
		return 'Öppna den här rutan';
	}

	// Ger en querystring för "addFlag" (true/false), beroende på om Flagbutton är aktiv eller inte.
	public function addFlag()
	{
		if ($this->getFlagClickQS() != null)
		{
			if ($this->getFlagClickQS() == 'true')
			{
				return '?' . $this->getFlagQueryStringName() . '=true';
			}
			else {
				return '?' . $this->getFlagQueryStringName() . '=false';
			}
		}
		return '?' . $this->getFlagQueryStringName() . '=false';
	}

	// Ger en querystring i form av en sträng.
	public function getFlagButtonOnclick()
	{
		if ($this->getFlagClickQS() != null)
		{
			if ($this->getFlagClickQS() == 'true')
			{
				return '?' . $this->getFlagQueryStringName() . '=false';
			}
			else {
				return '?' . $this->getFlagQueryStringName() . '=true';
			}
		}
		return '?' . $this->getFlagQueryStringName() . '=true';
	}

	// Ger en bild som hover på varje Box.
	private function getImageHover()
	{
		if ($this->getFlagClickQS() != null)
		{
			if ($this->getFlagClickQS() == 'true')
			{
				return 'imageClosedFlag';
			}
			else {
				return 'imageClosedOpen';
			}
		}
		return 'imageClosedOpen';
	}

	// Renderar alla Box:ar till spelet.
	private function getBoxes()
	{
		$returnString = '';

		if ($this->game->getTheBoard()->getBoard() != null);
		{
			for ($x = 0; $x < count($this->game->getTheBoard()->getBoard()); $x++) {

				// If game has been won - opens up all mines.
				if ($this->game->getTheBoard()->checkIfGameWon() && $this->game->getTheBoard()->getBoxByID($x)->getMine())
				{
					$returnString = $returnString . 
									'<a href="' . $this->addFlag() . '&' . $this->getBoxQueryStringName() . '=' . $x . '">
										<img src="view/pics/boxopenmine.png">
									</a>';
				}
				// If game is over - opens up all mines with the exploded mine.
				else if ($this->game->getTheBoard()->getExplodedMine() != null && $this->game->getTheBoard()->getBoxByID($x)->getMine())
				{
					if ($this->game->getTheBoard()->getBoxByID($x)->getOpen())
					{
						$returnString = $returnString . 
										'<a href="' . $this->addFlag() . '&' . $this->getBoxQueryStringName() . '=' . $x . '">
											<img src="view/pics/boxexplodedmine.png">
										</a>';
					}
					else {
						$returnString = $returnString . 
										'<a href="' . $this->addFlag() . '&' . $this->getBoxQueryStringName() . '=' . $x . '">
											<img src="view/pics/boxopenmine.png">
										</a>';
					}
				}
				// If boxes are open - displays how many mines that are around one specific box.
				else if ($this->game->getTheBoard()->getBoxByID($x)->getOpen())
				{
					if ($this->game->getTheBoard()->countAroundX($x, $this->game->getTheBoard()->getMineIndex()) == 0)
					{
						$returnString = $returnString . '<img src="view/pics/boxopen.png">';
					}
					else if ($this->game->getTheBoard()->countAroundX($x, $this->game->getTheBoard()->getMineIndex()) == 1)
					{
						$returnString = $returnString . 
										'<a href="' . $this->addFlag() . '&' . $this->getBoxQueryStringName() . '=' . $x . '">
											<img src="view/pics/boxopen1.png">
										</a>';
					}
					else if ($this->game->getTheBoard()->countAroundX($x, $this->game->getTheBoard()->getMineIndex()) == 2)
					{
						$returnString = $returnString . 
										'<a href="' . $this->addFlag() . '&' . $this->getBoxQueryStringName() . '=' . $x . '">
											<img src="view/pics/boxopen2.png">
										</a>';
					}
					else if ($this->game->getTheBoard()->countAroundX($x, $this->game->getTheBoard()->getMineIndex()) == 3)
					{
						$returnString = $returnString . 
										'<a href="' . $this->addFlag() . '&' . $this->getBoxQueryStringName() . '=' . $x . '">
											<img src="view/pics/boxopen3.png">
										</a>';
					}
					else if ($this->game->getTheBoard()->countAroundX($x, $this->game->getTheBoard()->getMineIndex()) == 4)
					{
						$returnString = $returnString . 
										'<a href="' . $this->addFlag() . '&' . $this->getBoxQueryStringName() . '=' . $x . '">
											<img src="view/pics/boxopen4.png">
										</a>';
					}
					else if ($this->game->getTheBoard()->countAroundX($x, $this->game->getTheBoard()->getMineIndex()) == 5)
					{
						$returnString = $returnString . 
										'<a href="' . $this->addFlag() . '&' . $this->getBoxQueryStringName() . '=' . $x . '">
											<img src="view/pics/boxopen5.png">
										</a>';
					}
					else if ($this->game->getTheBoard()->countAroundX($x, $this->game->getTheBoard()->getMineIndex()) == 6)
					{
						$returnString = $returnString . 
										'<a href="' . $this->addFlag() . '&' . $this->getBoxQueryStringName() . '=' . $x . '">
											<img src="view/pics/boxopen6.png">
										</a>';
					}
					else if ($this->game->getTheBoard()->countAroundX($x, $this->game->getTheBoard()->getMineIndex()) == 7)
					{
						$returnString = $returnString . 
										'<a href="' . $this->addFlag() . '&' . $this->getBoxQueryStringName() . '=' . $x . '">
											<img src="view/pics/boxopen7.png">
										</a>';
					}
					else
					{
						$returnString = $returnString . 
										'<a href="' . $this->addFlag() . '&' . $this->getBoxQueryStringName() . '=' . $x . '">
											<img src="view/pics/boxopen8.png">
										</a>';
					}
				}
				// If box is not open and is flagged.
				else if ($this->game->getTheBoard()->getBoxByID($x)->getFlag())
				{
					$returnString = $returnString . 
									'<a href="' . $this->addFlag() . '&' . $this->getBoxQueryStringName() . '=' . $x . '">
										<img src="view/pics/boxflag.png">
									</a>';
				}
				// Displays a empty box.
				else {
					$returnString = $returnString . 
									'<a title="' . $this->getTitleInfoAtBox() . '" href="' . $this->addFlag() . '&' . $this->getBoxQueryStringName() . '=' . $x . '">
										<img class="' . $this->getImageHover() . '" src="view/pics/box.png">
									</a>';
				}
			}
		}
		
		return $returnString;
	}

	// Ger en tidssträng.
	public function getTimeString()
	{
		$fullTime = $this->game->getTheTimer()->getGameTime();

		$mins = floor($fullTime / 60);
		$secs = $fullTime % 60;

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
}