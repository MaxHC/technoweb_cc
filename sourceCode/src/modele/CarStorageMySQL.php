<?php

class CarStorageMySQL implements CarStorage{

	public $bd;

	public function __construct($bd){
		$this->bd = $bd;
	}

	public function read($id){
		$rq = "SELECT * FROM cars WHERE id=:id";
		$stmt = $this->bd->prepare($rq);

		$data = array(
			':id' => $id,
		);

		$stmt->execute($data);
		$result = $stmt->fetch();
		if($result){
			return new Car($result["mark"], $result["model"], $result["year"], $result["color"], $result["ownerID"]);
		} else {
			return null;
		}	
	}

	public function readAll(){
		$rq = "SELECT * FROM cars";

		$stmt = $this->bd->prepare($rq);
		$stmt->execute();

		$result = $stmt->fetchAll();
		$carArray = array();
		foreach($result as $car){
			$carArray[$car["id"]] = new Car($car["mark"], $car["model"], $car["year"], $car["color"], $car["ownerID"]);
		}
		return $carArray;
	}

	public function create(Car $car){
		$rq = "INSERT INTO cars (mark, model, year, color, ownerID) VALUES (:mark, :model, :year, :color, :ownerID)";
		$stmt = $this->bd->prepare($rq);

		$data = array(
			':mark' => $car->getMark(),
			':model' => $car->getModel(),
			':year' => $car->getYear(),
			':color' => $car->getColor(),
			':ownerID' => $car->getOwnerID(),
		);

		$stmt->execute($data);
		
		//get the ID of the animal inserted
		$request = "SELECT LAST_INSERT_ID()";
		$result = $this->bd->query($request)->fetch();
		
		return $result[0]; //0 is the ID in array => result([0] => $id)
	}

	public function delete($id){
		$rq = "DELETE FROM cars WHERE id=:id";
		$stmt = $this->bd->prepare($rq);

		$data = array(
			':id' => $id,
		);

		$stmt->execute($data);
	}

	public function modify($car, $id){
		$rq = "UPDATE cars SET mark=:mark, model=:model, year=:year, color=:color WHERE id=:id";
		$stmt = $this->bd->prepare($rq);

		$data = array(
			':mark' => $car->getMark(),
			':model' => $car->getModel(),
			':year' => $car->getYear(),
			':color' => $car->getColor(),
			':id' => $id,
		);

		$stmt->execute($data);
	}

}

?>