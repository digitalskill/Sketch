<?php 
helper("session");
helper("member");
if(isset($_POST['token']) && $_POST['token']==sessionGet("token") && isset($_POST['addpostcoment']) && $_POST['addpostcoment']=="addpostcoment" && memberid()){ 
	sessionRemove('token');
	$data 					= $_POST;
	$data['menu_under'] 	= intval($_POST['replyto']) > 0 ?  intval($_POST['replyto']) : intval(sketch("sketch_menu_id"));
	$data['sketch_settings_id'] = sketch("siteid");
	$data['page_updated']   = date("Y-m-d H:i:s");
	$data['page_date']	    = date("Y-m-d H:i:s");
	$data['page_type']	    = "blog";
	$data['page_status']	= "hidden";
	
	$memDetails		    	= memberGet();
	$_POST['member_id']	    = intval(memberid());
	$data['updated_by']	    = (isset($memDetails['nickname']) && $memDetails['nickname']!= "")? $memDetails['nickname'] : $_SERVER['HTTP_HOST'];
	$serial			    	= $_POST;
	foreach($serial as $key => $value){
	    $serial[$key] 			= htmlentities(strip_tags(trim(stripslashes($value))));
	}
	$data['content']	 		= serialize($serial);
	$data['edit']	    		= $data['content'];
	
	$Raw = stripslashes(trim($data['name'].date("ymdhis")));
	$RemoveChars = array("([\40])", "([^a-zA-Z0-9-])", "(-{2,})");
	$ReplaceWith = array("-", "", "-");
	$guid = preg_replace($RemoveChars, $ReplaceWith, $Raw);
	
	$mrow = explode("?",sketch("menu_guid"));
	$guid = trim($mrow[0],"/"). "/" . $guid;
	$data['menu_guid']	= $guid;
	$data['menu_show']	= 0;
	$data['menu_name']	= htmlentities(trim(strip_tags($_POST['name'])));
	$add 	= addData("sketch_page",$data);
	$data['page_id']		= lastInsertId();
	$addp 	= addData("sketch_menu",$data);
	?>
    <div class="post alert">
  		<h2 class="title">Thank you - your comment will appear once moderated</h2>
    </div>
    <?php
}
$pstatus =  "page_status='published' AND ";
if(adminCheck()){
	$pstatus ="";
}
?>
<div class="post">
  <h2 class="title"><?php echo sketch('page_heading'); ?></h2>
  <div class="meta">
    <div class="top-border"></div>
    Posted on
    <div class="date"><?php echo @date("d F, Y",strtotime(sketch("page_date"))); ?></div>
    <?php
         // Get posting subject
			$r = getData("sketch_page,sketch_menu","*",$pstatus." sketch_menu_id=".sketch("menu_under"));
			$r->advance();
			// GET Comment Count
			$comments = getData("sketch_menu,sketch_page","count(sketch_menu_id) as commentcount",$pstatus." menu_under=".intval(sketch("sketch_menu_id")));
			$comments->advance();	?>
  by <a title=""><?php echo sketch("updated_by"); ?></a> under 
  	 <a href="<?php echo urlPath($r->menu_guid); ?>" title=""><?php echo $r->page_title; ?></a> | <a href="#comments" title=""><?php echo intval($comments->commentcount); ?> Comments</a> </div>
<?php if(sketch("page_image")!=""){?>
<img src="<?php echo urlPath(sketch("page_image")); ?>" alt=""/>
<?php } ?>
<?php echo sketch("content"); ?>
<div class="top-border"></div>
<div class="tags">
  <?php
            // Get posting Tags
			$tagr = getData("tag","*","page_id=".sketch("page_id"));
			if($tagr->rowCount() > 0){?>
  Tags:
  <?php
			$counter = 0;
			while($tagr->advance()){
          		echo $counter > 0? ", " : ""; ?>
  <a href="<?php echo urlPath($r->menu_guid);?>?tag=<?php echo urlencode($tagr->tag_name); ?>" title=""><?php echo $tagr->tag_name; ?></a>
  <?php 
				$counter++;
				}
			}
			?>
</div>
</div>
<div id="comment-wrapper">
<?php 
	$com = getData("sketch_page,sketch_menu","*",$pstatus." menu_under=".intval(sketch("sketch_menu_id"))); 
	if($com->rowCount() > 0) { ?>
	<h3><a id="comments"></a><?php echo intval($comments->commentcount); ?> Responses to "<?php echo sketch("page_title"); ?>"</h3>
    <div id="comments">
      <ol id="singlecomments" class="commentlist">
      	<?php while($com->advance()){ 
				$c = contentToArray($com->content);
				$mem = getData("sketch_page","*","page_type='member' AND page_id=".intval($c['member_id']));
				$mem->advance();
				$memc = contentToArray($mem->content);	
		?>
        <li class="clearfix" id='comment<?php echo $com->page_id; ?>'>
          <div class="user"><img alt="" src="sketch-images/art/member1.jpg" height="60" width="60" class="avatar" /> 
          					<?php if(memberid()){?>
                            <a class="reply-link" href="#cform" onclick="$('replyto').set('value','<?php echo $com->sketch_menu_id; ?>');$('replyingto').set('html','Replying to a comment'); $('replyingto').highlight();">Reply</a>
                            <?php } ?>
                            </div>
          <div class="message">
            <div class="infor">
              <h3><a href="#"><?php echo $memc['nickname']==""? $memc['firstname']." ".$memc['lastname']: $memc['nickname']; ?></a></h3>
              <span class="date"><?php echo date("d F, Y",strtotime($com->page_date)); ?></span> </div>
           	  <p><?php echo htmlentities(strip_tags(trim($c['textarea']))); ?></p>
              
              <?php if(adminCheck()){?>
              	<div class="info round">
                	<?php if($com->page_status!='published'){?>
                	<a id='approve<?php echo $com->page_id; ?>' class="button ajaxlink output:'approve<?php echo $com->page_id; ?>'" href="<?php echo urlPath(sketch("menu_guid")); ?>?approvepage=<?php echo $com->page_id; ?>"><span class="icons cross"></span>Approve</a>
                	<?php } ?>
                    <a class="button negative ajaxlink output:'comment<?php echo $com->page_id; ?>'" href="<?php echo urlPath(sketch("menu_guid")); ?>?deletepage=<?php echo $com->page_id; ?>"><span class="icons cross"></span>Delete</a>
                </div>
              <?php } ?>
              
          </div>
          <div class="clear"></div>
          <?php getSubReplies($com->sketch_menu_id); ?>
        </li>
        <?php } ?>
      </ol>
    </div>
<?php } 

	function getSubReplies($parent){ 
		$r = getData("sketch_menu,sketch_page","*","menu_under=".intval($parent));
		while($r->advance()){
			$c = contentToArray($r->content);
			$mem = getData("sketch_page","*","page_type='member' AND page_id=".intval($c['member_id']));
			$mem->advance();
			$memc = contentToArray($mem->content);
	?>
		 <ul class="children">
            <li class= "clearfix">
              <div class="user"><img alt="" src="sketch-images/art/member2.jpg" height="60" width="60" class="avatar" />
              <?php if(memberid()){?>
              <a class="reply-link" href="#cform" onclick="$('replyto').set('value','<?php echo $r->sketch_menu_id; ?>'); $('replyingto').set('html','Replying to a comment'); $('replyingto').highlight();">Reply</a>
              <?php } ?>
              </div>
              <div class="message">
                <div class="infor">
                  <h3><a href="#"><?php echo $memc['nickname']==""? $memc['firstname']." ".$memc['lastname']: $memc['nickname']; ?></a></h3>
                  <span class="date"><?php echo date("d F, Y",strtotime($r->page_date)); ?></span> </div>
                  <p><?php echo htmlentities(strip_tags(trim($c['textarea']))); ?></p>
                  <?php if(adminCheck()){?>
              	<div class="info round">
                	<?php if($r->page_status!='published'){?>
                		<a id='approve<?php echo $r->page_id; ?>' class="button ajaxlink output:'approve<?php echo $r->page_id; ?>'" href="<?php echo urlPath(sketch("menu_guid")); ?>?approvepage=<?php echo $r->page_id; ?>"><span class="icons cross"></span>Approve</a>
                	<?php } ?>
                    <a class="button negative ajaxlink output:'comment<?php echo $com->page_id; ?>'" href="<?php echo urlPath(sketch("menu_guid")); ?>?deletepage=<?php echo $r->page_id; ?>"><span class="icons cross"></span>Delete</a>
                </div>
              <?php } ?>
              </div>
              <div class="clear"></div>
              <?php getSubReplies($r->sketch_menu_id); ?>
            </li>
          </ul>	
<?php	}
	}
	if(memberid()){ 
		$details = memberGet();
	?>
		<div id="comment-form" class="comment-form">
        	<a id="cform"></a> 
          <h3 id='replyingto'>Leave a Reply</h3>
           <form action="<?php echo urlPath(sketch("menu_guid")); ?>" method="post" class="required">
            <input type="hidden" name="addpostcoment" value="addpostcoment">
            <input type="hidden" name="token" value="<?php $tok = md5(rand()); sessionSet("token",$tok,false); echo sessionGet("token"); ?>" class="required"/>
            <input type="hidden" name="replyto" id="replyto" value="" />
            <div class="comment-input">
              <p>Hi <?php echo !isset($details['nickname']) || $details['nickname']==''? $details['firstname']." ".$details['lastname'] : $details['nickname']; ?>, as a member you can leave a comment or click reply to give feedback on other members comments.</p>
              <p>We love to hear what our members think - and your comments will appear once moderated.</p>
            </div>
            <div class="comment-textarea">
              <textarea name="textarea" id="textarea"></textarea>
               <button type="submit" name="submit"><span class="icons pen"></span>Leave comment</button>
            </div>
          </form>
          <div class="clear"></div>
        </div>
        <?php } ?>
</div>