<?php
class CssMin{
	public function __construct(){
	
	}
	public static function compress($css) {
    $cssmin = new CssMin();
    return $cssmin->processcss($css);
	}
	
	private function processcss($item){
		return str_replace('; ',';',str_replace(' }','}',str_replace('{ ','{',str_replace(array("\r\n","\r","\n","\t",'  ','    ','    '),"",preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!','',$item)))));
	}
}?>