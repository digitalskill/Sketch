<?php if(isset($_GET['n']) && isset($_GET['group']) && isset($_GET['memid']) && adminCheck()){
		$mem = getData("sketch_page","content","page_id='".intval($_GET['memid'])."'","","1");
		$mem->advance();
		$details = contentToArray($mem->content);
		$details['group'] = htmlentities(trim($_GET['group']));
		foreach($details as $k => $v){
			if(!is_array($v)){
				$details[$k] = str_replace(array('"'),array(';#;'),stripslashes($v));
			}else{
				$details[$k] = $v;
			}
		}
		$data = array();
		$data['content'] = serialize($details);
		$data['page_id'] = intval($_GET['memid']);
		setData("sketch_page",$data);
		?>
        <script type="text/javascript">
			$$(".memgroup").unspin();
		</script>
        <?php
		exit();
}
if(!isset($_REQUEST['n'])){?>
<ul style="float:left;width:100%">
    <li><div class="content-column">
	    <div class="title">Member Forms</div>
	    <div class="big-font">Select Forms for members</div>
	</div>
    </li>
</ul>
<ul style="float:left;width:70%;" class="accordian">
    <li>
      <a class="accord-title button"><span class="icons downarrow"></span>Member Settings</a>
  <div class="accord-body">
      <div class="accord-container">
    <label>Email Registrations from</label>
	<input type="text" class="required email" name="emailto" value="<?php echo $this->e('emailto'); ?>" />

    <label>Email Registrations To: (used to alert for new signups)</label>
	<input type="text" class="" name="emailrto" value="<?php echo $this->e('emailrto'); ?>" />
   
    <label>Login form</label>
	<select name="loginform" class="bgClass:'select_bg'">
	    <option value="" <?php if ($this->e('loginform') == "") { ?>selected="selected"<?php } ?>>None</option><?php
foreach (getDirectory(sketch("abspath") . sketch("themepath") . "views" . sketch("slash") . "forms" . sketch("slash")) as $key => $value) {
?><option value="<?php echo $value; ?>" <?php if ($this->e('loginform') == $value) {
 ?>selected="selected"<?php } ?>><?php echo str_replace(array("_", ".php"), array(" ", ""), $value); ?></option><?php
	}
	if(getSettings("version") > 2){
		$r = getData("template","*","template_type='form'");
		while($r->advance()){
			?><option value="<?php echo $r->template_name; ?>" <?php if ( $this->e("loginform") == $r->template_name ) { ?>selected="selected"<?php } ?>><?php echo "FORM: ". $r->template_name; ?></option><?php
		}
	}
?></select>
   
    <label>Details form</label>
	<select name="detailform" class="bgClass:'select_bg'">
	    <option value="" <?php if ($this->e('detailform') == "") { ?>selected="selected"<?php } ?>>None</option><?php
	    foreach (getDirectory(sketch("abspath") . sketch("themepath") . "views" . sketch("slash") . "forms" . sketch("slash")) as $key => $value) {
			?><option value="<?php echo $value; ?>" <?php if ($this->e('detailform') == $value) { ?>selected="selected"<?php } ?>><?php echo str_replace(array("_", ".php"), array(" ", ""), $value); ?></option><?php
	    }
	if(getSettings("version") > 2){
		$r = getData("template","*","template_type='form'");
		while($r->advance()){
			?><option value="<?php echo $r->template_name; ?>" <?php if ( $this->e("detailform") == $r->template_name ) { ?>selected="selected"<?php } ?>><?php echo "FORM: ". $r->template_name; ?></option><?php
		}
	}
?></select>
    <label>Reset Password form</label>
	<select name="resetform" class="bgClass:'select_bg'">
	    <option value="" <?php if ($this->e('resetform') == "") {
 ?>selected="selected"<?php } ?>>None</option><?php
	    foreach (getDirectory(sketch("abspath") . sketch("themepath") . "views" . sketch("slash") . "forms" . sketch("slash")) as $key => $value) {
?><option value="<?php echo $value; ?>" <?php if ($this->e('resetform') == $value) { ?>selected="selected"<?php } ?>><?php echo str_replace(array("_", ".php"), array(" ", ""), $value); ?></option><?php
	    }
	if(getSettings("version") > 2){
		$r = getData("template","*","template_type='form'");
		while($r->advance()){
			?><option value="<?php echo $r->template_name; ?>" <?php if ( $this->e("resetform") == $r->template_name ) { ?>selected="selected"<?php } ?>><?php echo "FORM: ". $r->template_name; ?></option><?php
		}
	}
?>
</select>
    <label>On Register Redirect</label>
	<select name="successreg" class="bgClass:'select_bg'">
	    <option value="">None</option>
			<?php adminFilter("menu",array("select"=>true,"id"=>$this->e("successreg"),"type"=>"menu_guid")); ?>
	</select>
    <label>On Login Redirect</label>
	<select name="redirect" class="bgClass:'select_bg'">
	    <option value="">None</option>
		<?php adminFilter("menu",array("select"=>true,"id"=>$this->e("redirect"),"type"=>"menu_guid")); ?>
	</select>
    <label>Member page (Select the page to show the login,details and register forms)</label>
	<select name="memberpage" class="required bgClass:'select_bg'">
	    <option value="">None</option>
		<?php adminFilter("menu",array("select"=>true,"id"=>$this->e("memberpage"),"type"=>"menu_guid")); ?>
	</select>
    </div>
    </div>
    </li>
    <li>
     <a class="accord-title button"><span class="icons downarrow"></span>Members</a>
  	<div class="accord-body">
      <div class="accord-container">
       <label>Search member records (enter text and press the Enter key)</label>
      <input type="text" name="membersearch" id="memsearch" />
      <script type="text/javascript">
	  	function setMemsearch(){
			$('memberlistform').unspin();
			$("memsearch").addEvent("keypress",function(event){
				if(event.key=="enter"){
						$('memberlistform').spin();
						$('memberlistform').set("load",{url:'','method':'post'});
						$('memberlistform').load('<?php echo urlPath("admin/ajax_plugin_member?page_id=1&noform=t&preview="); ?>&noform=t&n=0&name='+this.value);
				}
			});
		}
		setMemsearch.delay(500);
	  </script>
      <div id="memberlistform">
     
      <?php } ?>
      
      <?php
	  	$limit = "";
		$pagelimit = 25;				// Make this the page output desired
		if($pagelimit > 0){
			$limit = " 0,".$pagelimit;
		}
		$startfrom = (intval(@$_REQUEST['n']) - 1) * $pagelimit;
		$startfrom = ($startfrom < 0)? 0 : $startfrom;
		if($startfrom){
			$limit = " ".$startfrom.",".$pagelimit;	
		}
		$xtra = "";
	  	if(isset($_GET['name']) && trim($_GET['name']) != ''){
			$name = trim(htmlentities($_GET['name']));
			$xtra = " AND content LIKE '%".$name."%' ";
			$limit = "";
		}
	  	$members = getData("sketch_page,sketch_menu","*","(page_type='member' || page_type='unapproved')".$xtra,"page_type DESC",$limit);
		while($members->advance()){
			$c = contentToArray($members->content);
			?><div id="<?php echo 'm'.$members->page_id; ?>" style="clear:both;width:96%;float:left;">
            
            
            	<div style="float:right;width:40%">
                <div style="float:left;width:33%">
                <select name="menu_sel" class="bgClass:'select_bg'" onchange="$('g<?php echo $members->page_id; ?>').value = $('g<?php echo $members->page_id; ?>').value +','+this.value; $('g<?php echo $members->page_id; ?>').fireEvent('keypress'); ">
                    <option value="">Groups</option>
                    <?php
                    $found = false;
                    $memdata = getData("sketch_page,sketch_menu","menu_class","page_status='member' GROUP BY menu_class");
                    while($memdata->advance()){
                        if($memdata->menu_class != ''){
                            $found=true
                        ?><option value="<?php echo $memdata->menu_class; ?>"><?php echo $memdata->menu_class; ?></option><?php	
                        }
                    }
                    ?>
				</select>
                </div>
                
                <input type='text' style="width:60%;float:right;" id="g<?php echo $members->page_id; ?>" name='memgroup' class='memgroup' rel='<?php echo $members->page_id; ?>' value='<?php echo @$c['group']; ?>' /></div>
            	<a class="button" style="float:left" href='<?php echo urlPath($members->menu_guid); ?>'><span class="icons user"></span><?php echo $c['email']; ?></a>
            <?php if($members->page_type!='member'){?>
            	<a style="float:left" class="button ajaxlink output:'<?php echo 'm'.$members->page_id; ?>'" href='<?php echo urlPath($members->menu_guid); ?>?approvemember=<?php echo $members->page_id; ?>'><span class="icons check"></span>Approve</a> 
			<?php } ?>
            	<a style="float:left" class="button negative ajaxlink output:'<?php echo 'm'.$members->page_id; ?>'" href='<?php echo urlPath($members->menu_guid); ?>?unapprovemember=<?php echo $members->page_id; ?>'><span class="icons cross"></span>Ban this member</a>
			</div><?php	
		}
	  ?>
     
      <?php
		$SQL = end(explode("FROM",$members->query));
		list($SQL,) = explode("limit",strtolower($SQL));
		$rowC = ACTIVERECORD::keeprecord("SELECT count(sketch_menu_id) as recordAmount FROM " .$SQL);
		$rowC->advance();
		?>
		<ul class="page-navi" style="clear:both;">
		<?php
		$curr = intval(@$_POST['n']) > 1 ? intval($_POST['n']): 1; 
		for($j=0;$j<($rowC->recordAmount/$pagelimit);$j++){ ?>
			 <li style="float:left;clear:none;"><a href="<?php echo urlPath("admin/ajax_plugin_member?page_id=1&preview="); ?>&noform=t&n=<?php echo $j+1; ?>" class="button <?php if($j+1==$curr){?>current<?php } ?> ajaxlink output:'memberlistform'" ><?php echo $j+1; ?></a></li><?php
		}
		if(intval($rowC->recordAmount) > ($startfrom + $pagelimit)){ ?>
			<li style="float:left;clear:none;"><a href="<?php echo urlPath("admin/ajax_plugin_member?page_id=1&preview="); ?>&noform=t&n=<?php echo $curr+1; ?>" class="button ajaxlink output:'memberlistform'">&raquo;</a></li>
		<?php } 
		?>
        
        </ul>
        <script type="text/javascript">
			function domemberupdate(){
				$('memberlistform').unspin();
				$$(".memgroup").addEvent("keypress",function(event){
					if(event){
						if(event.key=='enter'){
							$(this).set('load',{'url':'','method':'post'});
							$(this).spin();
							$(this).load('<?php echo urlPath("admin/ajax_plugin_member?page_id=1&preview="); ?>&n=0&memid=' + $(this).get("rel") + '&group=' + this.value);	
						}
					}else{
						$(this).set('load',{'url':'','method':'post'});
						$(this).spin();
						$(this).load('<?php echo urlPath("admin/ajax_plugin_member?page_id=1&preview="); ?>&n=0&memid=' + $(this).get("rel") + '&group=' + this.value);	
					}
				});
			}
			domemberupdate.delay(500);
		</script>
        <?php if(!isset($_REQUEST['n'])){?>
         </div>
     </div>
    </div>
    </li>
</ul>
<ul style="float:right;width:28%;margin-top:-10px">
    <li>
	<div class="instruction-box">
	    <h4>Members plugin instructions</h4>
	    <p><strong style="font-size:12px;">Email registrations from : </strong><br />This is the email address that is used to contact members that register.</p>
	    <p><strong style="font-size:12px;">Select the login form :  </strong><br />This is the form that is used to login. Leave blank to use sketch's login form.</p>
	    <p><strong style="font-size:12px;">Select the details form : </strong><br />This is the form that is used to Register new members.<br/>Leave blank to use sketch's login form.<br/>sketch members also use this form to update their details.</p>
	    <p><strong style="font-size:12px;">Select the password reset form : </strong><br />This is the form that is used to reset member passwords for members.</p>
	    <p><strong style="font-size:12px;">Select the register redirect form : </strong><br />This is where to take members once registered.<br/>Leave blank for them to be shown a thank you message and login form.</p>
	    <p><strong style="font-size:12px;">Select the Login redirect : </strong><br />You can select either a product page, blog or membership landing page. Leave blank to take members to the home page.</p>
	</div>
    </li>
</ul>
<?php } ?>
<script type="text/javascript">
	accordRefresh.delay(500);
	function doAjaxlinks(){
		$$(".ajaxlink").each(function(item,index){
			new Ajaxlinks(item); 
		});	
	}
	doAjaxlinks.delay(500);
	</script>