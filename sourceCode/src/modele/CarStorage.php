<?php

interface CarStorage{

	public function read($id);

	public function readAll();

	public function create(Car $car);

	public function delete($id);

}

?>