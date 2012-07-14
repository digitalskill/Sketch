<?php
function memberid(){
	loadHelper("session");
	return sessionGet("memberid");
}
function memberGet(){
	loadHelper("session");
	if(memberid()){
	    $mem = getData("sketch_page","content","page_id='".memberid()."'","","1");
	    if($mem->rowCount() > 0){
		    $mem->advance();
			$details = contentToArray($mem->content);
		    return $details;
	    }
	}
	return false;
}
function memberSet($details){
	loadHelper("database");
	loadHelper("session");
	if(memberid()){
		$data = $details;					// Each Table Field
		$data['content'] = $details;
		$allDetails = memberGet();
		$data['content']['password'] = (isset($details['password']))? secureit($details['password']) : $allDetails['password'];
		$data['content'] = array_merge($allDetails,$data['content']);
		foreach($data['content'] as $k => $v){
			if(!is_array($v)){
				$data['content'][$k] = str_replace(array('"'),array(';#;'),stripslashes($v));
			}else{
				$data['content'][$k] = $v;
			}
		}
		$data['content'] = serialize($data['content']);
		$data['page_id'] = memberid();
		return ACTIVERECORD::keeprecord(updateDB("sketch_page",$data,"page_id"));
	}
	return false;
}
function memberAdd($details){
	global $_POST;
	$exists = false;
	$mem = getData("sketch_page","content","WHERE page_type='member' AND content LIKE '%".str_replace("'","",sketch("db")->quote($details['email']))."%'","","1");
	if($mem->rowCount() > 0){
		$mem->advance();
		$result = unserialize($mem->content);
		$memDetails = array();
		foreach($result as $k => $v){
			if(!is_array($v)){
				$memDetails[$k] = str_replace(array(';#;'),array('"'),stripslashes($v));
			}else{
				$memDetails[$k] = $v;
			}
		}
		if($memDetails['email']==$details['email']){
			$exists = true;	
		}
	}
	if(!$exists){
		$data['content'] = $details;
		foreach($data['content'] as $k => $v){
			if(!is_array($v)){
				$data['content'][$k] = str_replace(array('"'),array(';#;'),stripslashes($v));
				if($k=="password"){
					$data['content'][$k] = secureit(trim($v));
				}
			}else{
				$data['content'][$k] = $v;
			}
		}
		if(!isset($details['nickname']) || trim($details['nickname'])==""){
			$details['nickname'] = $details['firstname'];
		}
		$data['content'] = serialize($data['content']);
		$data['edit']    = $data['content'];
		$data['pagefile']=$details['pagefile'];
		$data['page_updated'] = date("Y-m-d");
		$data['updated_by'] = $details['nickname'];
		$data['page_title'] = $details['nickname'];
		$data['page_status'] = 'hidden';
		$data['page_type'] = "member"; 
		$r = ACTIVERECORD::keeprecord(insertDB("sketch_page",$data));
		if($r){
			$pid 	= lastInsertId();
			$SQL 	= "SELECT * FROM ".getSettings('prefix')."sketch_menu WHERE sketch_menu_id IN (SELECT sketch_menu_id FROM ".getSettings('prefix')."sketch_menu WHERE page_id=".intval($details['menu_under']).")";
			$m_q 	= ACTIVERECORD::keeprecord($SQL);
			$Raw 	= stripslashes(trim($details['nickname']."-".$pid));
			$RemoveChars  = array("([\40])","([^a-zA-Z0-9-])","(-{2,})");
			$ReplaceWith 	= array("-","", "-");
			$guid = preg_replace($RemoveChars, $ReplaceWith, $Raw);
			if($m_q->advance()){
				$subpath 	= ltrim(sketch("main_site_url").$m_q->menu_guid,"/"); 
				$guid  		= rtrim($subpath,"/")."/".$guid;
				$menu_id 	= $m_q->sketch_menu_id;
			}
			// Get menu order
			$SQL = "SELECT count(page_id) num FROM ".getSettings('prefix')."sketch_menu WHERE menu_under IN (SELECT sketch_menu_id FROM ".getSettings('prefix')."sketch_menu WHERE page_id=".intval(sketch("sketch_menu_id")).")";
			$newOrder_q = ACTIVERECORD::keeprecord($SQL);
			$newOrder_q->advance();
			$newOrder = intval($newOrder_q->num) + 2;
			$newOrder_q->free();
			// Insert item into database at new location
			$SQL = "INSERT INTO ".getSettings('prefix')."sketch_menu (menu_name,menu_order,page_id,menu_show,sketch_settings_id,menu_under,menu_guid) ".
						 "VALUES (".sketch("db")->quote($details['nickname']).",".intval($newOrder).",".intval($pid).",0,1,".intval(sketch("sketch_menu_id")).",".sketch("db")->quote($guid).")";
			$r = ACTIVERECORD::keeprecord($SQL);
			if($r){
				return true;
			}
		}
	}
	return false;
}
function memberloggin($details){
	loadHelper("database");
	loadHelper("session");
	$mem = getData("sketch_page","page_id,content","WHERE (content LIKE '%".str_replace("'","",sketch("db")->quote($details['email']))."%' AND content LIKE '%".str_replace("'","",sketch("db")->quote(secureit($details['password'])))."%' AND page_type='member') ","","1");
	if($mem->rowCount() > 0){
		$mem->advance();
		$result = unserialize($mem->content);
		$memDetails = array();
		foreach($result as $k => $v){
			$memDetails[$k] = str_replace(array(';#;'),array('"'),stripslashes($v));
		}
		if($memDetails['email']==$details['email'] && secureit($memDetails['password'],true)==$details['password']){
			sessionAdd("memberid",$mem->page_id);
			return true;	
		}
	}
	return false;
}
function memberGetByChr($chr){
        $found = false;
	loadHelper("database");
	loadHelper("session");
	$mem = getData("sketch_page","page_id,content","WHERE page_type='member'");
	if($mem->rowCount() > 0){
		while($mem->advance()){
			$result = unserialize($mem->content);
			$memDetails = array();
			foreach($result as $k => $v){
				if(!is_array($v)){
					$memDetails[$k] = str_replace(array(';#;'),array('"'),stripslashes($v));
				}else{
					$memDetails[$k] = $v;		
				}
			}
			if(md5($memDetails['email'].$memDetails['password'].date("y-m-d"))==$chr){
				sessionAdd("member_check",$mem->page_id);
                                $found = true;
                                ?>
                <h3>Password Reset for<br /><?php echo $memDetails['nickname']; ?></h3>
                <form method="post" action="<?php echo urlPath(sketch("menu_guid")); ?>" class="required">
     				<input type="hidden" name="resetpassword"  value="resetpassword" />
    				<input type="hidden" class="required"  value="<?php sessionAdd("token",md5(rand()),false); echo sessionGet('token'); ?>" name="token"/>
                	 <div class="row">
    				<label>Password</label><input type="password" name="password" value="" class="input required" /></div>
                      <div class="row">
					<label>&nbsp;</label><input type="submit" class="button" value="Reset Password"/></div>
                </form><?php
			}
		}
        }
        if(!$found){
		?><div class="error-message">Cannot Find Details<br />
                  You must complete the password recovery process on the same day.<br />
                  <a href="<?php echo urlPath(sketch("menu_guid")); ?>?recover">Start the recovery process</a>
                </div><?php
		return false;	
	}
}
function updatePassword($pass){
	loadHelper("database");
	loadHelper("session");
	sessionAdd("memberid",sessionGet("member_check"));
	$memberDetails = memberGet();
	$memberDetails['password'] = $pass;
	memberSet($memberDetails);
	sessionRemove("memberid");
	return "<div class='error-message' style='color:#fff;'>Your Password has been updated</div>";
}
function membercheck($details){
	loadHelper("database");
	loadHelper("session");
	$mem = getData("sketch_page","page_id,content","WHERE (content LIKE '%".str_replace("'","",sketch("db")->quote($details['email']))."%' AND content LIKE '%".str_replace("'","",sketch("db")->quote($details['nickname']))."%') ","","1");	
	if($mem->rowCount() > 0){
		$mem->advance();
		$result = unserialize($mem->content);
		$memDetails = array();
		foreach($result as $k => $v){
			if(!is_array($v)){
				$memDetails[$k] = str_replace(array(';#;'),array('"'),stripslashes($v));
			}else{
				$memDetails[$k] = $v;		
			}
		}
		if($memDetails['email']==$details['email'] && $memDetails['nickname']==$details['nickname']){
			sessionAdd("memberid",$mem->page_id);
			return true;	
		}
	}
	return false;
}