<?php
class CONTROLLER{
	public $name;
	function __construct($name){
		$this->$name = $name;
	}
	function __get($item){
		return $this->$item;
	}
	function __set($item,$value){
		$this->$item = $value;
	}
	function loadView($view,$return=false){
		return loadView($view,$return);
	}
	function loadModel($model){
		loadModel($model);
	}
}