<?php
class TINYSTYLES extends CONTROLLER{
	function TINYSTYLES(){
		parent::__construct("tinystyles");
		header('Content-Type: text/css; charset=utf-8');	// Make the server deliver the style sheet as utf 8
		echo str_replace(array("images/","../"),array("sketch-system/".rtrim(sketch("themepath"),"/")."/views/styles/images/",""),file_get_contents(sketch("abspath").sketch("themepath")."/views/styles/cms.css"));
	}
}