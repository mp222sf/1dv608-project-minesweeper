<?php 

class Sessions {
	private static $sessGame = 'gameSession';
	private static $sessResult = 'gameResult';

	// Sätter MineSweeperGame-session.
	public function setSessionGame($msg)
	{
		$_SESSION[self::$sessGame] = $msg;
	}

	// Sätter Result-session.
	public function setSessionResult($nr)
	{
		$_SESSION[self::$sessResult] = $nr;
	}

	// Raderar alla sessioner.
	public function deleteSessions()
	{
		session_destroy();
	}

	// Hämtar MineSweeperGame-session.
	public function getSessionGame()
	{
		if(isset($_SESSION[self::$sessGame]))
		{
			return $_SESSION[self::$sessGame]; 
		}
		return null;
	}

	// Hämtar Result-session.
	public function getSessionResult()
	{
		if(isset($_SESSION[self::$sessResult]))
		{
			return $_SESSION[self::$sessResult]; 
		}
		return null;
	}
}