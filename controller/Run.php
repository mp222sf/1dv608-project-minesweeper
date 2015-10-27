<?php 
require_once('view/GameView.php');
require_once('model/MinesweeperGame.php');
require_once('model/Result.php');
require_once('model/ResultDAL.php');
require_once('model/Sessions.php');

class Run {
	private $msg;
	private $gv;
	private $mysqli;
	private $resultDAL;
	private $newResult;
	private $sh;

	public function __construct()
	{
		session_start();
		$this->sh = new Sessions();

		// Skapar en anslutning till en databas som innehåller resultat.
		$this->mysqli = new mysqli(Settings::MYSQL_SERVER, Settings::MYSQL_USERNAME, Settings::MYSQL_PASSWORD, Settings::MYSQL_DATABASE, Settings::MYSQL_PORT);
		if (mysqli_connect_errno())
		{
			throw new Exception(mysqli_connect_error());
			exit();
		}

		// Om en MinesweeperGame-session existerar, används sessionen som MinesweeperGame.
		if ($this->sh->getSessionGame() instanceof MinesweeperGame)
		{
			$this->msg = $this->sh->getSessionGame();
		}

		// Om en Resultat-session existerar, används sessionen som Resultat.
		if ($this->sh->getSessionResult() instanceof Result)
		{
			$this->newResult = $this->sh->getSessionResult();
		}

		$this->resultDAL = new ResultDAL($this->mysqli);
		$this->gv = new GameView($this->msg, $this->newResult, $this->resultDAL);
	}

	public function start()
	{
		// Om ett spel finns.
		if ($this->msg instanceof MinesweeperGame)
		{
			// Användaren klickade på "Gå tillbaka".
			if ($this->gv->didUserPressRestart())
			{
				$this->sh->deleteSessions();
				header('Location: ?');
			}
			// Sparar ett resultat, vid vunnet spel.
			else if($this->gv->didUserPressSubmitScore() && $this->msg->getTheBoard()->checkIfGameWon())
			{
				$name = $this->gv->getInputName();
				$time = $this->msg->getTheTimer()->getGameTime();
				$level = $this->msg->getTheBoard()->getAmountOfMines();
				
				if ($this->newResult == null && strlen($name) > 0)
				{
					$this->newResult = new Result($name, $time, $level, null);
					$this->resultDAL->add($this->newResult);
					$this->sh->setSessionResult($this->newResult);
					header('Location: ?');
				}
			}
			// Spelar spelet.
			else {
				if ($this->gv->getFlagClickQS() == 'true' && $this->gv->getClickQS() != null && $this->msg->getTheBoard()->checkCanPlay())
				{
					$this->msg->flagBox($this->gv->getClickQS());

				}
				else if($this->gv->getClickQS() != null && $this->msg->getTheBoard()->checkCanPlay())
				{
					$this->msg->openBox($this->gv->getClickQS());
				}
			}
		}
		else {
			if ($this->gv->didUserPressStart9x9() || $this->gv->didUserPressStart16x16() || $this->gv->didUserPressStart22x22())
			{
				if ($this->gv->didUserPressStart9x9())
				{
					$this->msg = new MinesweeperGame(9,9,10);
				}
				else if ($this->gv->didUserPressStart16x16())
				{
					$this->msg = new MinesweeperGame(16,16,40);
				}
				else {
					$this->msg = new MinesweeperGame(22,22,99);
				}

				$this->sh->setSessionGame($this->msg);
				header('Location: ?');
			}
		}
	}

	// Hämtar GameView-HTML.
	public function getGameHTML()
	{
		return $this->gv->render();
	}
}