<?php

class ImageStorageMySQL{
	
	private $bd;

	public function __construct($bd){
		$this->bd = $bd;
	}

	public function read($id){
		$rq = "SELECT name FROM images WHERE carID=:id";
		$stmt = $this->bd->prepare($rq);
		
		$data = array(
			':id' => $id,
		);

		$stmt->execute($data);
		$result = $stmt->fetchAll();

		return $result;
	}

	public function addImage($name, $carID){
		$rq = "INSERT INTO images (name, carID) VALUES (:name, :id)";
		$stmt = $this->bd->prepare($rq);
			
		$data = array(
			':name' => $name,
			':id' => $carID,
		);
		
		$stmt->execute($data);
	}

	public function delete($name){
		$rq = "DELETE FROM images WHERE name=:name";
		$stmt = $this->bd->prepare($rq);
			
		$data = array(
			':name' => $name,
		);
		
		$stmt->execute($data);
	}

	public function deleteAll($carID){
		$rq = "DELETE FROM images WHERE carID=:id";
		$stmt = $this->bd->prepare($rq);
			
		$data = array(
			':id' => $carID,
		);
		
		$stmt->execute($data);
	}

}

?>