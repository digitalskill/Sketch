<ul class="form" style="clear:both;">
    <li style="float:right; width:29%">
	<label>Amounts</label>
	<input type="text" name="amount" value="<?php echo $this->e("amount"); ?>" class="integer" id="fontamount"/>
	<input type="button" name="update" value="update" id="update" style="height:40px;width:99%;margin-top:5px;"/>
    </li>
    <li id="clonefonts" style="float:left;width:70%">
	<?php for ($i = 1; $i < (intval($this->e("amount")) + 1); $i++) {
	?>
    	<ul class="form" style="clear:both; width:100%">
    	    <li style="float:left; width:29%;margin-right:1%">
    		<label>Class (h1,p,.classname)</label>
    		<input type="text" name="class<?php echo $i; ?>" class="class" value="<?php echo $this->e("class" . $i); ?>"/>
    	    </li>
             <li style="float:left; width:29%;margin-right:1%">
    		<label>Extras</label>
    		<input type="text" name="extras<?php echo $i; ?>" class="class" value="<?php echo $this->e("extras" . $i); ?>"/>
    	    </li>
    	    <li style="float:right; clear:none; width:38%;">
    		<label>Cufon Font</label>
    		<input type="text" name="family<?php echo $i; ?>" class="family" value="<?php echo $this->e("family" . $i); ?>"/>
    	    </li>
    	</ul>
	<?php } ?>
    </li>
</ul>
<script type="text/javascript">
    function setupClones(){
	$('update').addEvent("click",function(){
	    if($('clonefonts').getElements("ul").length < parseInt($('fontamount').value)){
		for(var i=$('clonefonts').getElements("ul").length;i<$('fontamount').value;i++){
		    var newrow = $('clonefonts').getElement("ul").clone();
		    $(newrow).inject($('clonefonts'),'bottom');
		    $(newrow).getElements("input").each(function(item,index){
			$(item).set("value","");
			if($(item).hasClass("class")){
			    $(item).set("name","class"+(i+1));
			}
			if($(item).hasClass("family")){
			    $(item).set("name","family"+(i+1));
			}
		    });
		}
	    }else{
		if($('clonefonts').getElements("ul").length > parseInt($('fontamount').value)){
		    for(var i=$('clonefonts').getElements("ul").length;i>$('fontamount').value;i--){
			$('clonefonts').getElements("ul").getLast().destroy();
		    }
		}
	    }
	});
    }
    setupClones.delay(500);
</script>