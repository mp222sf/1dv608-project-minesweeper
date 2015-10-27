<?php

class ResultDAL {

	private $database;
	
	public function __construct(mysqli $db)
	{
		$this->database = $db;
	}

	// Hämtar de 20 bästa resultaten från vald nivå ($level).
	public function getTop20($level)
	{
		$resultArray = array();
		$sql = 'SELECT * FROM results WHERE level = ? ORDER BY time LIMIT 20';

		$stmt = $this->database->prepare($sql);
		if ($stmt === FALSE) {
			throw new Exception($this->database->error);
		}
		$stmt->bind_param('s', 
			$level);
		$stmt->execute();
	 	
	    $stmt->bind_result($name, $time, $level, $date);

	    while ($stmt->fetch()) {
	    	$userResult = new Result($name, $time, $level, $date);
	    	array_push($resultArray, $userResult);
		}
		return  $resultArray;
	}

	// Lägger till ett resultat i databasen.
	public function add(Result $resObject)
	{
		$stmt = $this->database->prepare("INSERT INTO `highscores`.`results` (
			`name` , `time` , `level` , `date` )
				VALUES (?, ?, ?, ?)");
		if ($stmt === FALSE) {
			throw new Exception($this->database->error);
		}

		$name = $resObject->getName();
		$time = $resObject->getTime();
		$level = $resObject->getLevel();
		$date = $resObject->getDate();
		$stmt->bind_param('sisi', 
			$name, 
			$time, 
			$level, 
			$date);
		$stmt->execute();
	}
}