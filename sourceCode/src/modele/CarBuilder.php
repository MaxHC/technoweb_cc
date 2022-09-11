<?php

class CarBuilder{

	private $data, $error;
	public const MARK_REF = "mark";
	public const MODEL_REF = "model";
	public const YEAR_REF = "year";
	public const COLOR_REF = "color";

	public function __construct($data){
		$this->data = $data;
		$this->error = array("mark" => "", "model" => "", "year" => "", "color" => "");
	}

	public function getData(){
		return $this->data;
	}

	public function getError(){
		return $this->error;
	}

	public function createCar($ownerID){
		return new Car(htmlspecialchars($this->data[self::MARK_REF]), htmlspecialchars($this->data[self::MODEL_REF]), htmlspecialchars($this->data[self::YEAR_REF]), htmlspecialchars($this->data[self::COLOR_REF]), $ownerID);
	}

	public function isValid(){
		if($this->data[self::MARK_REF] != null && $this->data[self::MODEL_REF] != null && $this->data[self::COLOR_REF] != null && $this->data[self::YEAR_REF] != null && $this->data[self::YEAR_REF] >= 0 && is_numeric($this->data[self::YEAR_REF])){
			return true;
		} else {
			$this->error["mark"] = $this->data[self::MARK_REF]!=null?"":"Marque vide !";
			$this->error["model"] = $this->data[self::MODEL_REF]!=null?"":"Modele vide !";
			$this->error["year"] = $this->data[self::YEAR_REF]!=null?"":"Année vide !";
			$this->error["color"] = $this->data[self::COLOR_REF]!=null?"":"Couleur vide !";
			
			if($this->error["year"] == ""){
				if(!is_numeric($this->data[self::YEAR_REF])){
					$this->error["year"] = "L'année n'est pas un nombre !";
				} else {
					if($this->data[self::YEAR_REF]>=0){
						$this->error["year"] = "";
					} else {
						$this->error["year"] = "Année negative !";
					}
				}
			}

			return false;
		}
	}

	public function getMarkRef(){
		return self::MARK_REF;
	}

	public function getModelRef(){
		return self::MODEL_REF;
	}

	public function getYearRef(){
		return self::YEAR_REF;
	}

	public function getColorRef(){
		return self::COLOR_REF;
	}

}

?>