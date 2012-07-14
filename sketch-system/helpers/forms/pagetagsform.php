    <ul class="form">
        <li>
    <div class="content-column">
      <div class="big-font">PAGE TAGS</div>
    </div>
  </li>
    </ul>
    <div class="clb"></div>
    <ul>
    	 
    </ul>
    <ul class="form" id="tagaccord">
    	<li class="clonez clear" style="display:none;float:left;margin:5px;padding:0;clear:none;">
        	  <a class="button negative"><span class="icons cross"></span></a>
              <input type="text" class="tagnameclass" name="tag_name[]" style="display:none;" value="<?php echo stripslashes(trim($tag_q->tag_name)); ?>"/>
              <input type="hidden" name="tag_id[]" class="theid" />
	    </li>
        <li>
        	<label>Tags used on this page</label>
        </li>
<?php // GET PAGE TAG DATA
	$tag_q = getData("tag","*","WHERE page_id=".intval($this->page_id)." AND tag_name NOT LIKE 'og%'" );
	while ($tag_q->advance()){ ?>
	   <li style="float:left;margin:5px;padding:0px;clear:none;"><a class="button negative"><span class="icons cross"></span><?php echo stripslashes(trim($tag_q->tag_name)); ?></a><input type="hidden" name="tag_id[]" class="theid" value="<?php echo $tag_q->tag_id; ?>" /></li>
<?php } ?>
    </ul>
    <ul>
    	<li style="float:left;width:30%;margin-right:2%">
        	<label>Add new tag</label>
            <input type="text" value="" />
            <input type="button" class="button addnewtagclick" value="Add New Tag"/>
        </li>
    	<li style="float:left;clear:none;width:30%;margin-right:2%">
       		<label>All Tags In use</label>
	        <?php 
				$r = getData("tag","*","WHERE tag_name NOT LIKE 'og%'");
				$usedTags = array(); 
				while($r->advance()){
					if(!in_array($r->tag_name,$usedTags)){
						$usedTags[] = $r->tag_name;
						?><input type="button" class="button round addnewtag" style="float:left;width:auto;" value="<?php echo $r->tag_name; ?>"/><?php
					}
				}
			?>
        </li>
        <li style="float:left;clear:none;width:30%;">
        	<label>Recommended Tags (by Scanning page content)</label>
        <?php
			global $pod;
        	$tc = explode(" ", strtolower(str_replace(array('&nbsp;', "&", "'", '"', ",", '.', ';', '_', '{', '}', '[', ']', '*', '(', ')', ':', '@', '!', '#', '$', '%', '^', '|'), '', strip_tags(trim(stripslashes($pod->content))))));
			$tagCloud = array();
			foreach ($tc as $key => $value) {
				if (strlen($value) > 5 && trim($value) != '') {
				if (!isset($tagCloud[$value])) {
					$tagCloud[$value] = 0;
				}
				$tagCloud[$value]++;
				}
			}
		
			function cmp($a, $b) {
				return ($a == $b) ? 0 : (($a > $b) ? -1 : 1);
			}
		
			uasort($tagCloud, 'cmp');
			$string = '';
			foreach ($tagCloud as $key => $value) {
				if($value > 1){
					$string .= '<input type="button" class="button round addnewtag" style="float:left;width:auto;" value="'. $key .'"/>';
				}
			}
			echo $string; ?>
        </li>
    </ul>
    <script type="text/javascript">
        function setAccord(){
			$$('.addnewtag').addEvent("click",function(event){
				new Event(event).stop();
				var newrow = $('tagaccord').getElement(".clonez").clone();
				$(newrow).removeClass("clonez");
				$(newrow).removeClass("clear");
				$(newrow).inject($('tagaccord'),'bottom');
				$(newrow).setStyle("display","block");
				$(newrow).getElements(".tagnameclass").each(function(item,index){
					$(item).set("value",$(this).get("value"));
				},this);
				$(newrow).getElements("a").each(function(item,index){
					$(item).set("html",'<span class="icons cross"></span>' + $(this).get("value"));
				},this);
				setupNagClicks();
			});
			$$('.addnewtagclick').addEvent("click",function(event){
				new Event(event).stop();
				if($(this).getParent("li").getElement("input").get("value").clean() != ""){
					var newrow = $('tagaccord').getElement(".clonez").clone();
					$(newrow).removeClass("clonez");
					$(newrow).removeClass("clear");
					$(newrow).inject($('tagaccord'),'bottom');
					$(newrow).setStyle("display","block");
					$(newrow).getElements("a").each(function(item,index){
						$(item).set("html",'<span class="icons cross"></span>' + $(this).getParent("li").getElement("input").get("value"));
					},this);
					$(newrow).getElements(".tagnameclass").each(function(item,index){
						$(item).set("value",$(this).getParent("li").getElement("input").get("value"));
						$(this).getParent("li").getElement("input").set("value","");
					},this);
				}
				setupNagClicks();
			});
			setupNagClicks();
        }
		function setupNagClicks(){
			$('tagaccord').getElements("a.negative").removeEvents("click");
			$('tagaccord').getElements("a.negative").addEvent("click",function(event){
				new Event(event).stop();
				if($(this).getParent("li").getElement(".theid").get("value")==""){
					$(this).getParent("li").destroy();
				}else{
					$(this).getParent("li").setStyle("display","none");
					$(this).getParent("li").getElement(".theid").set("name","removeit[]");
				}
			});	
		}
        setAccord.delay(500);
    </script>