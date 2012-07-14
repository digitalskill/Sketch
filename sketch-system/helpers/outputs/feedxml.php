<?php
$myLink = explode("/", str_replace("http://", "", $this->e("url")));
if ($this->e("url") != "") {
    if ($this->e('LastChecked') != date("Y-m-d") || $this->e('frequency') == "visit" || !is_array($this->e("xml"))) {
	$this->settings['content']['LastChecked'] = date("Y-m-d");
	$chk = simplexml_load_file($this->e('url'), "SimpleXMLElement", LIBXML_NOCDATA);
	$this->settings['content']['xml'] = $this->objectsIntoArray($chk);
	$this->settings['edit'] = $this->settings['content'];
	$this->approve();
    }
    if (is_array($this->settings['content']['xml']) && $this->e("valuestoshow")!="") {
	$valueOrder = (array) explode(",", str_replace("auto","channel",$this->e("valuestoshow")));
	if(strpos($this->e("valuestoshow"),"auto")!==false){
	    $count = 0;
	    foreach($this->settings['content']['xml'] as $key => $value){
		if($count==count($this->settings['content']['xml']))
		    $valueOrder[0] = $key;
	    }
	    $count++;
	}
	if($this->e("heading")!= ""){
	    ?><h3><?php echo $this->e("heading"); ?></h3><?php
	}
	sketchOutputXML($this->settings['content']['xml'][$valueOrder[0]],$valueOrder,0,intval($this->e("limit")),false);
    }
}

function sketchOutputXML($array,$valueHunt,$count,$limit,$afterIntro){
    foreach ($array as $key => $value) {
	if(is_array($value)){
	    if($count <= $limit || $limit==0){
		if($afterIntro){
		    $count++;
		    echo '<div class="feed-entry">';
		 }
		 sketchOutputXML($value,$valueHunt,$count,$limit,true);
		 if($afterIntro){
		    echo "</div>";
		 }
	    }
	}else{
	    if($afterIntro && $count > 0 && $count <= $limit){
		if(in_array($key,$valueHunt)){
		    $class="";
		    if($valueHunt[1]==$key){
			$class = "large";
		    }
		    if(end($valueHunt)==$key){
			$class .= "last small";
		    }
		    echo "<p class='".$key." feed-row ".$class."'>".strip_tags($value)."</p>";
		}
	    }
	}
    }
}