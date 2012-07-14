<?php
class MODEL{
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
}