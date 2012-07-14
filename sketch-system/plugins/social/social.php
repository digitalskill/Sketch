<?php
helper("twitter");
class SOCIAL extends PLUGIN {
	public $twitterAPI;
	function SOCIAL($args) {
		global $sketch;$_GET;
		$settings = array("location"=>"meta","php"=>1,"menuName"=>"Social","global"=>1,"pluginsection"=>"pageedit","adminclass"=>"updateForm:false showReEdit:false showPreview:false showPublish:false");	// [ OPTIONAL - pageEdit | js | css | php | global | location | admin | topnav ]
		$settings['content'] = array("TWITTER_CONSUMER_KEY"=>"Byx0y6Ji64zp3Auz2ipYsQ"									// Sketch Twitter Consumer key
									,"TWITTER_CONSUMER_SECRET"=>"nmMoJhmwRKhpoj4NgsYshv4utYborzghZTQPBVLD74Q"			// Sketch Twitter Consumer Secret																
									,"oauth_token"=>"" 																	// Twitter Token
									,"oauth_token_secret"=>""															// Twitter Secret
									,"PastTweats"=>array()
									,"APPID"  => "395686310487492"
									,"Secret" => "012866c16b926ad7e7d7f1ec3fd8edea"
									,"linked_API_Key" => "wd7neeteaq1a"
									,"linked_Secret_Key" => "LLwPwojpiV0T8jC5"
									);															
		$this->start($settings,$args);
		// Save the new settings
		if(isset($_GET['_api'])){
			helper("oauth");
			$linked 	= new linkedin();
			$linked->init($this->e('litoken_secret'),$this->e('lioauth_token'),$_GET['oauth_verifier']);
			$liResult	= $linked->get_access_token();
			$this->settings['content']['oauth_verifier'] = $_GET['oauth_verifier'];
			$this->settings['content']['litoken_secret'] = $liResult['secret'];
			$this->settings['content']['lioauth_token']  = $liResult['token'];
			$SQL = "UPDATE ".$this->prefix."plugin SET content=".sketch("db")->quote(serialize($this->settings['content'])).",edit=".sketch("db")->quote(serialize($this->settings['content']))." WHERE plugin_id='".intval($this->settings['plugin_id'])."'";
			$r = ACTIVERECORD::keeprecord($SQL);
			if($r){
				header("location: http://".$sketch->urlPath($sketch->menu_guid));	
			}
		}else{
			// Save Twitter Settings
			if(isset($_GET['oauth_token']) && $sketch->adminCheck()===true && isset($_GET['oauth_verifier'])){
				$twitterObj = new EpiTwitter($this->e("TWITTER_CONSUMER_KEY"),$this->e("TWITTER_CONSUMER_SECRET"));
				$twitterObj->setToken($_GET['oauth_token']);
				$token = $twitterObj->getAccessToken(array("oauth_verifier"=>$_GET['oauth_verifier']));
				$this->settings['content']['oauth_token']		 = $token->oauth_token;
				$this->settings['content']['oauth_token_secret'] = $token->oauth_token_secret;
				$SQL = "UPDATE ".$this->prefix."plugin SET content=".sketch("db")->quote(serialize($this->settings['content'])).",edit=".sketch("db")->quote(serialize($this->settings['content']))." WHERE plugin_id='".intval($this->settings['plugin_id'])."'";
				$r = ACTIVERECORD::keeprecord($SQL);
				if($r){
					header("location: http://".$sketch->urlPath($sketch->menu_guid));	
				}
			}
		}
	}
	function startTwitterAPI(){
		$canCall = false;
		if($this->e("oauth_token","")=="" || $this->e("oauth_token_secret")==""){
			$this->twitterAPI = new EpiTwitter($this->e("TWITTER_CONSUMER_KEY"),$this->e("TWITTER_CONSUMER_SECRET"));
		}else{
			$this->twitterAPI = new EpiTwitter($this->e("TWITTER_CONSUMER_KEY"),$this->e("TWITTER_CONSUMER_SECRET"),$this->e("oauth_token"),$this->e("oauth_token_secret"));
			$canCall = true;
		}
		return $canCall;
	}
	function update($old,$new){ 			// [ REQUIRED ]
			if(isset($new['liconn']) && $new['liconn']=='connect'){
				helper("oauth");
				$linked 	= new linkedin();
				$linked->init();
				$liResult 	= $linked->get_request_token();
				$new['lioauth_token'] = $liResult['token'];
				$new['litoken_secret'] = $liResult['secret'];
				?><script type="text/javascript">
						window.location = '<?php echo $liResult['url']; ?>';
					</script>	
			     <?php	
			}
			if(isset($new['mynewlinkedin']) && trim($new['mynewlinkedin']) != ''){
				helper("oauth");
				$linked 	= new linkedin();
				$linked->init($this->e('litoken_secret'),$this->e('lioauth_token'),$this->e('oauth_verifier')); 
				$r = $linked->post_status($new['mynewlinkedin'],$new['lititle'],$new['liurl'],$new['liimage']);
				?><script type="text/javascript">
						if($('messageresult')){
							$('messageresult').set("html","Text added to Linkedin:<?php echo $r; ?>");
							$("messageresult").addClass('notice');
						}
						try{
							console.log("Linkedin updated");
						}catch(e){}
                        </script>
			<?php
			}
			foreach($new['tag_id'] as $key => $value){
				$data = array();
				$data['tag_name'] 		= $new['tag_name'][$key];
				$data['tag_content'] 	= $new['tag_content'][$key];
				$data['page_id'] 		= sketch("page_id");
				if(intval($value) > 0){
					setData("tag",$data,"WHERE tag_id=".$value);	
				}else{
					if($data['tag_content'] != ""){
						addData("tag",$data);
					}
				}
			}
			if(isset($new['mynewtweat']) && $new['mynewtweat'] !=""){
				if($this->startTwitterAPI()){
					$result = $this->twitterAPI->post_statusesUpdate(array("status"=>$new['mynewtweat']));
					$check = $result->getDataAsArray();
					if(isset($check['error'])){
						?><script type="text/javascript">
							if($('messageresult')){
								$('messageresult').set("html","<?php echo $check['error']; ?>");
								}
                           try{
                           	console.log("Cannot update twitter");
                           }catch(e){}</script><?php 	
					}else{
						?><script type="text/javascript">
						if($('messageresult')){
							$('messageresult').set("html","Text added to twitter:<?php echo $new['mynewtweat']; ?>");
                        	$("mynewtweat").value="";
							$("messageresult").addClass('notice');
						}
						try{
							console.log("Twitter updated");
						}catch(e){}
                        </script><?php
					}
				}else{ ?>
					<script type="text/javascript">if($('messageresult')){
						$('messageresult').set("html","Your message failed to get to Twitter");$("messageresult").addClass('alert');
                        }</script>
				<?php }
			}
			if(isset($new['message']) && trim($new['message']) != ""){
				helper("facebook");
				$facebook = new Facebook(array(
						'appId' => $this->e("APPID"),
						'secret' => $this->e("Secret")
				));
				try{
				$facebook->api("/me/feed", 'post', array(
						'access_token' => $this->e('access_token'),
						'message' => stripslashes(trim($new['message'])),
						'link'    => stripslashes(trim($new['messagelink'])),
						'name'    => stripslashes(trim($new['messagename'])),
						'description'=> stripslashes(trim($new['messagedescription']))
						));
						?><script>if($('messageresult')){	$('load-box').getElement("form").reset();$('messageresult').addClass("notice");$('messageresult').set("html","Message Posted to facebook");}</script><?php
				} catch (FacebookApiException $e) {
				?><script>if($('messageresult')){
					$('load-box').getElement("form").reset();
					$('messageresult').addClass("notice");
					$('messageresult').set("html","Message Failed to get to Facebook");}
                	try{
						console.log("Cannot post message");
					}catch(e){
						
					}
                </script><?php
				}
			}
			return array_merge($old,$new);  		// Combine old and new details so that keys are not lost
	}
	function display($args=''){				// [ REQUIRED ]
		if(isset($_GET['state']) && isset($_GET['code'])){
			helper("facebook");
			$facebook = new Facebook(array(
					'appId' => $this->e("APPID"),
					'secret' => $this->e("Secret")
			));
			$user = $facebook->getUser();
			if($user){
				$acctoken = $facebook->getAccessToken();
				$data['plugin_id'] = $this->settings[ 'plugin_id' ];
				$data['edit'] 	   = serialize(array_merge($this->settings['content'],array("access_token"=>$acctoken)));
				$data['content']   = $data['edit'];
				setData("plugin",$data);
				header("location: ".urlPath(sketch("menu_guid")));	
			}
		}
		if(isset($args['update'])){
			$this->update($args,$args);
			return false;
		}
		if(isset($args['facebook'])){
			helper("facebook");
			$facebook = new Facebook(array(
					'appId' => $this->e("APPID"),
					'secret' => $this->e("Secret")
			));
			?>
            <div id="fb-root"></div>
			<script src="http://connect.facebook.net/en_US/all.js#appId=272125772803798&amp;xfbml=1"></script>
            <fb:like href="<?php echo $args['facebook']['url']; ?>" <?php if(isset($args['facebook']['send'])){ echo 'send="true"'; } ?> layout="button_count" width="450" <?php if(isset($args['facebook']['faces'])){ echo 'show_faces="true"'; } ?> action="like" font="arial"></fb:like>
            <?php
		}else{
			if(isset($args['twitter'])){
				$this->startTwitterAPI();			// Connect to Twitter
				$twitterInfo= $this->twitterAPI->get_accountVerify_credentials(); // get User account info;
				if(isset($args['twitter']['follow'])){?>
                	<a href="http://twitter.com/<?php echo $twitterInfo->screen_name; ?>" data-show-count="false" class="twitter-follow-button">Follow @<?php echo $twitterInfo->screen_name; ?> </a>
				<?php }
				if(isset($args['twitter']['share'])){?>
                	<a href="http://twitter.com/share" class="twitter-share-button"><?php echo $args['twitter']['share']; ?></a>
                <?php } ?>
                <script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>
                <?php
			}else{
				$r = getData("sketch_page,tag","*","tag.page_id=".sketch("page_id"));
				$found = false;
				while($r->advance()){
					if(strpos($r->tag_name,"og:")!==false){
						$found = true;
						?><meta property="<?php echo $r->tag_name; ?>" content="<?php echo $r->tag_content; ?>"/><?php
					}
				}
				if(!$found){
					?><meta property="og:title" content="<?php echo sketch("page_title"); ?>"/><?php
					?><meta property="og:description" content="<?php echo sketch("page_description")!=""? sketch("page_description") : strip_tags(sketch("leadparagraph")); ?>"/><?php
					if(sketch("page_image") != ""){
						?><meta property="og:image" content="<?php echo urlPath(sketch("page_image")); ?>"/><?php
					}
				}
			}
		}
	}
	function preview(){						// [ REQUIRED ]
		
	}
	function form(){ 						// [ REQUIRED ] 
		global $sketch; 
		$this->startTwitterAPI();			// Connect to Twitter
		$twitterInfo= $this->twitterAPI->get_accountVerify_credentials(); // get User account info;
		@include(loadForm("socialform",false));
	}	
}