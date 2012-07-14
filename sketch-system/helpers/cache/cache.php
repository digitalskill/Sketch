<?php
class CACHECLASS{
	private $time 		= 60;							// time in Minutes to keep the cache
	private $id   		= '';
	public 	$cachingObj = false;
	private $cacheDir 	= '';
	private $file		= 'cache.txt';
	function __construct($id,$time=60){
		$this->time = $time;
		$this->id 	= $id;
		$this->cacheDir = sketch("abspath").sketch("themepath")."cache".sketch("slash");
		$this->file = $this->cacheDir.md5($this->id).".txt";
	}
	function __set($item,$value){
		$this->$item = $value;
	}
	public static function cache($id,$time=60){
		return new CACHECLASS($id,$time);
	}
	function start(){
		if(getSettings("cache")==false){
			return false;
		}
		global $_GET;
		if(is_writable(dirname($this->file))){
			if(is_file($this->file)){ 
				if((filemtime($this->file) + ($this->time * 60)) < date("U")){
					$this->cachingObj = true;
					ob_start();	
				}else{
					echo file_get_contents($this->file);
					return true;
				}
			}else{
				$this->cachingObj = true;
				ob_start();	
			}
		}
		return false;
	}
	function end(){
		if($this->cachingObj){
			if(false !== ($f = @fopen($this->file, 'w'))){
	      		fwrite($f, ob_get_flush());
	      		fclose($f);
			}
		}
	}
	function clearCache(){
		if(is_file($this->file)){
			@unlink($this->file);
		}
	}
	function resetCache(){
          if(is_dir($this->cacheDir)){
            $direc = @scandir($this->cacheDir);
            foreach($direc as $key => $file){
              if (!preg_match('~^\.~', $file)) { 			// no hidden files / directories here...
                @unlink($this->cacheDir.$file);
              }
            }
          }
	}
}