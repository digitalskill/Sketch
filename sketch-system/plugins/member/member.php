<?php
helper("member");
helper("session");
class MEMBER extends PLUGIN {
	function MEMBER($args) {
		$settings = array("location"=>"member","php"=>1,"menuName"=>"Member","adminclass"=>"updateForm:false showReEdit:false showPreview:false showPublish:false","pluginsection"=>"Assets");	// [ OPTIONAL - pageEdit | js | css | php | global | location | admin | menuName ]
		$settings['content'] = array("detailform"=>"","loginform"=>"","redirect"=>"");
		$this->start($settings,$args);
	}
	function update($old,$new){ 						// [ REQUIRED ]
		return $new;
	}
	function display(){ 							// [ REQUIRED ]
          global $_POST, $_GET;
          if(isset($_POST['login']) || isset($_GET['recover']) || isset($_GET['chr']) ||  isset($_POST['update']) || isset($_POST['register']) || isset($_POST['reset']) || isset($_POST['resetpassword'])){
              $this->filter();
          }else{
            if($this->e('memberpage')==sketch("page_id")){
              $_POST = memberGet();
              if(memberid() || isset($_GET['register'])){
				 if(isset($_GET['purchase']) && memberid()){
					 ?>
                     <a style="float:right;" href="<?php echo urlPath("sketch-admin"); ?>" class="button"><span class="icons lock"></span>Logout</a>
                     <a style="float:right;" href="<?php echo urlPath(sketch("menu_guid")); ?>" class="button"><span class="icons user"></span>View Your Details</a>
					 	
					 <?php
					 $this->purchaseHistory();
				 }else{
					if(memberid()){
						?>
                        <a style="float:right;" href="<?php echo urlPath("sketch-admin"); ?>" class="button"><span class="icons lock"></span>Logout</a>
                        <a style="float:right;" href="<?php echo urlPath(sketch("menu_guid")); ?>?purchase" class="button"><span class="icons book"></span>View Purchase history</a>
							
						<?php
					}
                	$this->detailForm();
				 }
              }else{
                 $this->loginForm();
              }//else if memberid
            }// if this->e('memberpage')
          }// isset($_POST['login'])...
		  ?><div style="clear:both"></div><?php
	}
	function purchaseHistory(){
		$details = "purchaseform";
		filter("templates",array("show"=>true,"template_type"=>"form","template_name"=>$details,"data"=>$this->settings[ 'content' ]));
	}
	function detailForm(){
		$details = $this->e("detailform")=="" ? "detailform" : $this->e("detailform");
		filter("templates",array("show"=>true,"template_type"=>"form","template_name"=>$details,"data"=>$this->settings[ 'content' ]));
	}
	function loginForm(){
		$details = $this->e("loginform")=="" ? "loginform" : $this->e("loginform");
		filter("templates",array("show"=>true,"template_type"=>"form","template_name"=>$details,"data"=>$this->settings[ 'content' ]));
	}
	function filter($args=""){
		global $_POST,$GET;
		loadHelper("validate");
		if(isset($_GET['chr'])){
		  memberGetByChr(trim($_GET['chr']));
		}else{
		  if(isset($_GET['recover'])){
				$details = $this->e('resetform')=="" ? "resetform" : $this->e('resetform');
				filter("templates",array("show"=>true,"template_type"=>"form","template_name"=>$details,"data"=>$this->settings[ 'content' ]));
          	}else{
            	if(isset($_POST['token']) && isset($_SESSION['token']) &&  $_POST['token'] == $_SESSION['token']){
                        unset($_SESSION['token']);
                        if(isset($_POST['login'])){
                                if(is_file(loadForm($this->e('loginform'),false))){
                                  $val = VALIDATE::loadForm($this->e("loginform"));
                                }else{
                                   $val = VALIDATE::loadForm("loginform");
                                }
                                if($val->processForm($_POST)){
                                        if(memberloggin($_POST)){
												if ( isset( $_POST[ 'remember' ] ) ) {
													setcookie( "llock", md5( $_SERVER[ 'HTTP_USER_AGENT' ] ), time() + 60 * 60 * 24 * 30 * 3, "/" );
													setcookie( "lemail", trim($_POST['email']), time() + 60 * 60 * 24 * 30 * 3, "/" );
													setcookie( "lpw", secureit($_POST['password']), time() + 60 * 60 * 24 * 30 * 3, "/" );
												}
												$r = getData("sketch_menu","menu_guid","sketch_menu_id=".intval($this->e("redirect")),"",1);
												if($r->rowCount() > 0){
													$r->advance();
                                                	header("location: ".urlPath($r->menu_guid));
												}else{
													header("location: ".urlPath());	
												}
                                        }else{
                                                $_POST['error'] =  "<div class='alert'>Login Failed - Please Try Again</div>";
                                                $this->loginForm();
                                        }
                                }else{
                                        $_POST['error'] =  "<div class='notice'>".$val->getError()."</div>";
                                        $this->loginForm();
                                }
                        }// login
                        if(isset($_POST['update'])){
                          if(is_file(loadForm($this->e('detailform'),false))){
                           $val = VALIDATE::loadForm($this->e("detailform"));
                          }else{
                           $val = VALIDATE::loadForm("detailform");
                          }
                          if($val->processForm($_POST)){
                             if(!memberSet($_POST)){
                              $_POST['error'] = "<div class='error-message alert'>Update Failed - Please Try Again</div>";
                              $this->detailForm();
                             }else{
                              $_POST['error'] = "<div class='error-message success'>Success - Your Details have been updated</div>";
							  $this->detailForm();
                             }
                           }else{
                              $_POST['error'] =  "<div class='error-message notice'>".$val->getError()."</div>";
                              $this->detailForm();
                           }
                        }// update
                        if(isset($_POST['register'])){
                                if(is_file(loadForm($this->e('detailform'),false))){
                                  $val = VALIDATE::loadForm($this->e("detailform"));
                                }else{
                                  $val = VALIDATE::loadForm("detailform");
                                }
                                if($val->processForm($_POST)){
                                        if(!memberAdd($_POST)){
                                                $_POST['error'] = "<div class='error-message'>Registration Failed - That Email Address is already in use.<br />You can login to the site <a href='".urlPath($this->e('successreg'))."'>on this page.</a></div>";
                                                $this->detailForm();
                                        }else{
                                                helper("email");
                                                $data = $_POST;
                                                unset($data['token']);
                                                unset($data['menu_under']);
                                                unset($data['register']);
                                                $file = sketch("abspath")."sketch-system".sketch("slash")."helpers".sketch("slash")."email".sketch("slash")."html.html";
                                               
											    $emailSuccess = email($this->e("emailto"),$this->e("emailrto"),"Website Sign up for approval",$data,$file);
											    $emailSuccess = email($this->e("emailto"),htmlentities($_POST['email']),"Website Registration Details",$data,$file);
												
												$r = getData("sketch_menu","menu_guid","sketch_menu_id=".intval($this->e("successreg")),"",1);
												if($r->rowCount() > 0){
													$r->advance();
                                                	header("location: ".urlPath($r->menu_guid));
												}else{
													header("location: ".urlPath());
												}
                                        }
                                }else{
                                        $_POST['error'] =  "<div class='error-message'>".$val->getError()."</div>";
                                        $this->detailForm();
                                }
                        }// register
                        if(isset($_POST['reset'])){
                                if(is_file(loadForm($this->e('resetform'),false))){
                                   $val = VALIDATE::loadForm($this->e("resetform"));
                                }else{
                                  $val = VALIDATE::loadForm("resetform");
                                }
                                if($val->processForm($_POST)){
                                        if(!membercheck($_POST)){
                                                $_POST['error'] = "<div class='error-message'>Reset Failed - Cannot locate your details<br />Please try again</div>";
                                        }else{
                                                helper("email");
                                                helper("session");
                                                $memDetails = memberGet();
                                                $data['Information']= "You or someone else asked for a password reset for your account.<br />If you did not attempt to reset your password, you can ignore this email.";
                                                $data['email']	  =  $memDetails['email'];
                                                $data['nickname']   = $memDetails['nickname'];
                                                $data['Reset link'] = "Click this link or copy and paste it into your browser to reset the password<br /><a href='".urlPath(sketch("menu_guid"))."?chr=".md5($memDetails['email'].$memDetails['password'].date("y-m-d"))."'>".urlPath(sketch("menu_guid"))."?chr=".md5($memDetails['email'].$memDetails['password'].date("y-m-d"))."</a>";
                                                $file = sketch("abspath")."sketch-system".sketch("slash")."helpers".sketch("slash")."email".sketch("slash")."html.html";
                                                $emailSuccess = email($this->e("emailto"),$memDetails['email'],"Membership Password reset",$data,$file);
                                                $_POST['error'] =  "<div class='error-message'>A password reset has been sent to your email address</div>";
                                                sessionRemove("memberid");
                                        }
                                }else{
                                        $_POST['error'] =  "<div class='error-message'>".$val->getError()."</div>";
                                }
                        }// post reset
                        if(isset($_POST['resetpassword']) && trim($_POST['password']) != ''){
							echo updatePassword($_POST['password']);
							$details = $this->e("loginform")=="" ? "loginform" : $this->e("loginform");
							filter("templates",array("show"=>true,"template_type"=>"form","template_name"=>$details,"data"=>$this->settings[ 'content' ]));
                        }// reset password
                    }
                }
             }
        }
	function preview(){
		$this->display();
	}
	function form(){					// [ REQUIRED ]
		$details = $this->e("memberadminform")=="" ? "memberadminform" : $this->e("memberadminform");
		adminfilter("templates",array("show"=>true,"template_type"=>"form","template_name"=>$details,"data"=>$this->settings[ 'content' ]));
	}
}