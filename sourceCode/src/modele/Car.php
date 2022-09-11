<?php

class Car{

	private $mark, $model, $year, $color, $ownerID;

	function __construct($mark, $model, $year, $color, $ownerID){
		$this->mark = $mark;
		$this->model = $model;
		$this->year = $year;
		$this->color = $color;
		$this->ownerID = $ownerID;
	}

	public function getMark(){
		return $this->mark;
	}

	public function getModel(){
		return $this->model;
	}

	public function getYear(){
		return $this->year;
	}

	public function getColor(){
		return $this->color;
	}

	public function getOwnerID(){
		return $this->ownerID;
	}

}

?>