<?php

class Box {

	private $mine;
	private $open;
	private $flag;
	
	public function __construct()
	{
		$this->mine = false;
		$this->open = false;
		$this->flag = false;
	}

	public function changeMine()
	{
		$this->mine = true;
	}

	public function changeOpen()
	{
		$this->open = true;
	}

	public function changeFlag()
	{
		$this->flag = !$this->flag;
	}
	
	public function getMine()
	{
		return $this->mine;
	}

	public function getOpen()
	{
		return $this->open;
	}

	public function getFlag()
	{
		return $this->flag;
	}
}