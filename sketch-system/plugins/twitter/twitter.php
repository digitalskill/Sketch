<?php
class TWITTER extends PLUGIN {
	public $twitterAPI;
	function TWITTER($args) {
		global $sketch;$_GET;
		$settings = array("location"=>"twitter","php"=>1,"menuName"=>"Social Links","global"=>1,"css"=>1,"pluginsection"=>"Assets","adminclass"=>"updateForm:false showReEdit:false showPreview:false showPublish:false");	// [ OPTIONAL - pageEdit | js | css | php | global | location | admin | topnav ]
		$settings['content'] = array("screen_name"=>"sketchdevelopme","amount"=>5,"last_fetched"=>0);	
		$this->months		 = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');														
		$this->start($settings,$args);
	}
	function update($old,$new){ 			// [ REQUIRED ]
			return $new;  		
	}
	function display($args=''){				// [ REQUIRED ]
		if(isset($args['links'])){
			?>
            <div id="socials">
                <ul>
                  <li><a href="<?php echo urlPath("newsfeed"); ?>"><img src="sketch-images/icon-rss.png" alt="" /></a></li>
                  <?php if($this->e('screen_name')!=''){?>
                  <li><a href="https://twitter.com/#!/<?php echo $this->e('screen_name'); ?>"><img src="sketch-images/icon-twitter.png" alt="" /></a></li>
                  <?php } ?>
                   <?php if($this->e('dribble_name')!=''){?>
                  <li><a href="http://dribbble.com/<?php echo $this->e('dribble_name'); ?>"><img src="sketch-images/icon-dribble.png" alt="" /></a></li> 
                  <?php } ?>
                   <?php if($this->e('tumblr_name')!=''){?>
                  <li><a href="#"><img src="sketch-images/icon-tumblr.png" alt="" /></a></li>
                   <?php } ?>
                   <?php if($this->e('flicker_name')!=''){?>
                  <li><a href="#"><img src="sketch-images/icon-flickr.png" alt="" /></a></li>
                   <?php } ?>
                   <?php if($this->e('facebook_name')!=''){?>
                  <li><a href="http://www.facebook.com/<?php echo $this->e('facebook_name'); ?>"><img src="sketch-images/icon-facebook.png" alt="" /></a></li>
                   <?php } ?>
                </ul>
              </div>
      <?php
		}else{
			if(isset($args['timeline'])){
				$this->url = str_replace("#",$this->e("screen_name"),$args['url']);
				$this->get_tweets($args['timeline']);
			}else{
				$this->url = "http://api.twitter.com/1/statuses/user_timeline.json?include_entities=true&include_rts=true&screen_name=".$this->e("screen_name")."&count=".intval($this->e("amount"));
				$this->get_tweets();
			}
		}
	}
	function get_tweets($ar = "PastTweats"){
        $endtime = date('U', strtotime("-30 minutes"));
        if(intval($this->e("last_fetched".$ar)) < intval($endtime)){
			$arrayTwitter = json_decode($this->fetch());
            $this->settings['edit'] =  $this->settings['content']; 
			if(!is_array($this->settings['edit'])){
				$this->settings['edit'] = array();
			}
            $this->settings['edit'][$ar]   					= isset($arrayTwitter->results)? $arrayTwitter->results  : $arrayTwitter;
            $this->settings['edit']['last_fetched'.$ar]    	= date("U");
			$arrayTwitter = $this->settings['edit'][$ar];
			$values = serialize($this->settings['edit']);
			$SQL   = "UPDATE " . $this->prefix . "plugin SET content=". sketch( "db" )->quote( $values ) .", edit=" . sketch( "db" )->quote( $values ) . " WHERE plugin_id='" . intval( $this->settings[ 'plugin_id' ] ) . "'";
			$r     = ACTIVERECORD::keeprecord( $SQL );
        }else{
            $arrayTwitter = $this->e($ar);	
        }
		$cleanTweets = array();	
		foreach($arrayTwitter as $key => $value){
			if(isset($value->retweeted_status)){
				$value = $value->retweeted_status;	
			}
			if(isset($value->user)){
				$cleanTweets[$key] = array("date"=>$this->tweet_date($value->created_at),"text"=>$this->makeClickableLinks($value->text),"img"=>$value->user->profile_image_url,"screen_name"=>$value->user->screen_name,"id_str"=>$value->id_str);
			}else{
				$cleanTweets[$key] = array("date"=>$this->tweet_date($value->created_at),"text"=>$this->makeClickableLinks($value->text),"img"=>$value->profile_image_url,"screen_name"=>$value->from_user,"id_str"=>$value->id_str);
			}
		}
		foreach($cleanTweets as $key => $value){
			 ?>
            <div class="tweet <?php echo @$value['id']; ?>">
            	<img width="40" height="40" src="<?php echo $value['img']; ?>" alt="" title="" style="float:left;margin-right:10px">
                <div class="text" style="word-wrap:break-word;width:70%;float:left;">
                	<span class="username">
                    <a rel="external" target="_blank" href="http://twitter.com/#!/<?php echo str_replace(" ","%20",$value['screen_name']);?>">@<?php echo $value['screen_name'];?></a>:</span>
                <?php echo str_replace("HASHBANG;","#!",$value['text']); ?>
                
                	<div class="time">
                    <a rel="external" target="_blank" href="http://twitter.com/#!/<?php echo str_replace(" ","%20",$value['screen_name']);?>/status/<?php echo $value['id_str']; ?>"><?php echo $value['date']; ?></a>
                    </div>
                </div>
                	<div class="top-border" style="clear:both;">&nbsp;</div>
                </div>
            <?php 
		 }
	}
	function makeClickableLinks($text,$chr="http://") {
		$posLink = stripos($text,$chr); 							// Find first http://
		$posEnd = stripos($text," ",$posLink); 						// Find next space or end
		if($posEnd===false){
			$posEnd = strlen($text);	
		}
		$link = substr($text,$posLink,$posEnd-$posLink);
		if($posLink !==false){
			$finallink = array();
			$replace   = array();
			$replace[] = " ;1; ";
			if($chr == "#"){
				$finallink[] = "<a href='http://twitter.com/search?q=".trim(trim($link,"#"),":")."' rel='external'>".$link." </a>";
			}else{
				$finallink[] = "<a href='".($chr=="@"? str_replace(" ","%20",'http://twitter.com/HASHBANG;/'.trim(trim($link,"@"),":")) : str_replace(" ","%20",trim(trim($link,"@"),":")))."' rel='external'>".$link." </a>";
			}	
			$text = str_replace($link," ;1; ",$text);		
			// Replace any second links
			$posLink = stripos($text,$chr,$posLink); 				// Find second http://
			if($posLink !==false){
				$posEnd = stripos($text," ",$posLink); 				// Find next space or end
				if($posEnd===false){
					$posEnd = strlen($text);	
				}
				$link = substr($text,$posLink,$posEnd-$posLink);
				$replace[] = " ;2; ";
				if($chr == "#"){
					$finallink[] = "<a href='http://twitter.com/search?q=".trim(trim($link,"#"),":")."' rel='external'>".$link." </a>";
				}else{
					$finallink[] = "<a href='".($chr=="@"? str_replace(" ","%20",'http://twitter.com/HASHBANG;/'.trim(trim($link,"@"),":")) : str_replace(" ","%20",trim(trim($link,"@"),":")))."' rel='external'>".$link." </a>";
				}
				$text =  str_replace($link," ;2; ",$text);
			}
			$text =  str_replace($replace,$finallink,$text);
		}
		if($chr=="http://"){
			$text = $this->makeClickableLinks($text,"@");
		}
		if($chr=="@"){
			$text = $this->makeClickableLinks($text,"#");
		}
		return $text;	
	}
	function tweet_date($str){
		$d = explode(' ', str_replace(",","",$str));
		if(strpos($d[5],"+") !==false){
			$temp = $d;
			$d[0] = $temp[0];
			$d[1] = $temp[2];
			$d[2] = $temp[1];
			$d[3] = $temp[4];
			$d[4] = $temp[5];
			$d[5] = $temp[3];	
		}
		list($dago,$month,$year,$hago,$mago,$sago) = explode(":",date("d:M:Y:H:i:s", strtotime($str)));
		if($month != date("M") || $year != date("Y")){
			$str = date("d-M-Y",strtotime($str));
		}else{
			$str = (date("d")-$dago) > 0 ? (date("d")-$dago) ." Day".(date("d")-$dago > 1 ? "s" : "")." ago" : 
					((date("H")-$hago) > 0 ? (date("H")-$hago) ." Hour".(date("H")-$hago > 1 ? "s" : ""). " ago": ((date("i")-$mago) > 0 ? (date("i")-$mago) ." Minute".(date("i")-$mago > 1 ? "s" : "")." ago" 
					: date("s")-$sago." Second".(date("s")-$sago > 1 ? "s" : "")." ago"));
		}
		return $str;
	}
	function fetch(){
		$target = parse_url($this->url);
		$data = '';
		$fp = fsockopen($target['host'], 80, $error_num, $error_str, 8); 
		if (is_resource($fp)){
			fputs($fp, "GET {$this->url} HTTP/1.0\r\n");
			fputs($fp, "Host: {$target['host']}\r\n");
			fputs($fp, "User-Agent: sketch PHP/" . phpversion() . "\r\n\r\n");
		    $headers = TRUE;
		    while ( ! feof($fp)){
		        $line = fgets($fp, 4096);

		        if ($headers === FALSE){
		            $data .= $line;
		        }
		        elseif (trim($line) == ''){
		            $headers = FALSE;
		        }
			}
		    fclose($fp); 
		}
		return $data;
	}
	function preview(){						// [ REQUIRED ]
		$this->display();
	}
	function form(){ 						// [ REQUIRED ] 
		@include(loadForm("tweetform",false));
	}	
}