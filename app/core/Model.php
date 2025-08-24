<?php

abstract class Model
{
	protected $db; // mysqli

	public function __construct()
	{
		$this->db = Database::getInstance();
	}
}

