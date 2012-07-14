<?php

// EXAMPLE OF USE
/*
	<?php helper("googleapi");
		echo GOOGLEAPI::currency("NZD","AUD",24.30);
	?>
*/
class GOOGLEAPI{
	function GOOGLEAPI($call,$data){
			$this->call = $call;
			$this->data = $data; 
		   $this->calls['TRANSLATE'] ="http://ajax.googleapis.com/ajax/services/language/translate";
		   $this->calls['TRANSLATE_DETECTLANG'] ="http://ajax.googleapis.com/ajax/services/language/detect";
			//2.    Feeds
		   $this->calls['FEED'] ="http://ajax.googleapis.com/ajax/services/feed/load";
		   $this->calls['FEED_FIND'] ="http://ajax.googleapis.com/ajax/services/feed/find";
		   $this->calls['FEED_LOOKUP'] ="http://ajax.googleapis.com/ajax/services/feed/lookup";

			//3.1   Blogs
		   $this->calls['BLOGS'] 	="http://ajax.googleapis.com/ajax/services/search/blogs";

			//3.2   Books
		   $this->calls['BOOKS'] 	="http://ajax.googleapis.com/ajax/services/search/books";

			//3.3 Images
		   $this->calls['IMAGES'] 	="http://ajax.googleapis.com/ajax/services/search/images";

			//3.4 Local
		   $this->calls['SEARCH'] 	="http://ajax.googleapis.com/ajax/services/search/local";

			//3.5 News
		   $this->calls['NEWS'] 	="http://ajax.googleapis.com/ajax/services/search/news";

			//3.6 Patents
		   $this->calls['PATENTS'] 	="http://ajax.googleapis.com/ajax/services/search/patent";

			//3.7 Videos
		   $this->calls['VIDEO'] 	="http://ajax.googleapis.com/ajax/services/search/video";

			//3.8 Web
		   $this->calls['WEB']		="http://ajax.googleapis.com/ajax/services/search/web";

			//4.    Google Suggest
		   $this->calls['SUGGEST'] 	="http://google.com/complete/search";

			//5.    Google Weather
		   $this->calls['WEATHER'] 	="http://www.google.com/ig/api";

			//6. Google Calculator
		   $this->calls['CALCULATOR'] ="http://www.google.com/ig/calculator";

			//7. Google Dictionary
		   $this->calls['DICTIONARY'] ="http://www.google.com/dictionary/json";
	}
	function returnCall(){
		$opts = array(
		  'http'=>array(
			'method'=>"GET",
			'header'=>"Accept-language: en\r\n"
		  )
		);
		$context = stream_context_create($opts);
		if(isset($this->calls[strtoupper($this->call)])){
			// Open the file using the HTTP headers set above
			return file_get_contents($this->calls[strtoupper($this->call)]."?hl=en&q=".urlencode($this->data), false, $context);
		}else{
			return false;	
		}
	}
	function error(){
		
	}
	static function currency($from,$to,$amount){
		$data = $amount.$from."=?".$to;
		$call = "CALCULATOR";	
		$gapi = new GOOGLEAPI($call,$data);
		$rawdata = $gapi->returnCall();
		$data = explode('"', $rawdata);
		$data = explode(' ', $data['3']);
		$var = $data['0'];
		return number_format(floatval($var),2,'.',',');
	}
	static function call($call,$data){
		$gapi = new GOOGLEAPI($call,$data);
		return $gapi->returnCall();
	}
}