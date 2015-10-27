<?php

class Result {
	
	private $name;
	private $time;
	private $level;
	private $date;

	public function __construct($n, $t, $l, $d)
	{
		$this->name = $n;

		if ($l == 10 || $l == '9x9')
		{
			$this->level = '9x9';
		}
		else if ($l == 40 || $l == '16x16')
		{
			$this->level = '16x16';
		}
		else if ($l == 99 || $l == '22x22')
		{
			$this->level = '22x22';
		}
		else {
			throw new Exception('Fel leveltyp angiven.');
		}

		$this->time = $t;

		if (is_int($d))
		{
			$this->date = $d;
		}
		else {
			$this->date = time();
		}
	}

	public function getName()
	{
		return $this->name;
	}

	public function getTime()
	{
		return $this->time;
	}

	public function getLevel()
	{
		return $this->level;
	}

	public function getDate()
	{
		return $this->date;
	}

}