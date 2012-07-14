<ul style="float:right;width:38%;">
    <li>
	<label>Instructions for creating a new page</label>
	<div class="instruction-box">
	    <p><strong style="font-size:12px;">Enter in the menu name : </strong>This name must be unique to the site.</p>
	    <p><strong style="font-size:12px;">Select where the page resides in the menu : </strong><br />
		Select the parent of this page. E.g If this page is a product - then it may go under a products page.</p>
	    <p><strong style="font-size:12px;">Select the page type : </strong><br />
		Choose if this is to be a general web page for your site, or a special page like a product or case study page</p>
	    <p><strong style="font-size:12px;">Select if the page is public : </strong><br />Public pages are viewable by everyone,
		while hidden pages are only for administrators.
	    </p>
	    <p><strong style="font-size:12px;">On Menu:</strong><br />Select Yes or No to update the sites menu.</p>
	    <p><strong style="font-size:12px;">Page Template : </strong><br />Select the template that best suits this page.<br />
		If the page is a news or case studies page, there may be a special template available to <br />make the page display content correctly.
	    </p>
	    <p><strong style="font-size:12px;">Page Form : </strong><br />Select the form that matches the pages content.
	    </p>
	    <p><strong style="font-size:12px;">Page Output : </strong><br />Select the ouput format that matches the pages content.</p>
	</div>
    </li>
</ul>
<ul class="form accordian" style="float:left;width:60%">
    <li><label></label></li>
    <li><a class="accord-title button"><span class="icons downarrow"></span>Create single page</a>
	<div class="accord-body">
	    <div class="accord-container">
		<label>Menu Name</label>
		<input type="text" name="menu_name" value="" />
                <label>Page Under</label>
		<select name="menu_under" class="bgClass:'select_bg'">
		    <option value="">None (top Level page)</option>
			<?php adminFilter("menu",array("select"=>true,"id"=>0)); ?>
		</select>
		<label>Page type</label>
		<select name="page_type" class="bgClass:'select_bg'">
		    <option value="general">General</option>
            <option value="landing">Landing page</option>
		    <option value="news">News</option>
		    <option value="gallery">Gallery</option>
		    <option value="casestudies">Case-studies</option>
		    <option value="product">Product</option>
		    <option value="newsletter">Newsletter</option>
		    <option value="blog">Blog</option>
		    <option value="member">Member</option>
            <option value="any">Any</option>
            <option value="checkout">Checkout page</option>
          		<option value="article"    >Article listing</option>
          		<option value="newsl"      >Newsletter Listing</option>
          		<option value="blogl"      >Blog Listing</option>
          		<option value="productl"   >Product Listing</option>
                <option value="galleryl"   >Gallery Listing</option>
                <option value="pagel"      >Page Grouping</option>
		</select>
		<label>Publish status</label>
		<select name="page_status" class="bgClass:'select_bg'">
		    <option value="published">Publish</option>
		    <option value="hidden">Hidden</option>
		    <option value="member">Members only</option>
		</select>
		<label>On menu</label>
		<select name="menu_show" class="bgClass:'select_bg'">
		    <option value="1">Yes</option>
		    <option value="0">No</option>
		</select>
		<label>Template</label>
		<select name="pagefile" class="bgClass:'select_bg'">
		    <option value="">None (standard)</option>
<?php
		foreach (getDirectory(sketch("abspath") . sketch("themepath") . "views" . sketch("slash")) as $key => $value) {
		    if (strpos($value, "template") !== false) {
?>
    		    <option value="<?php
			echo $value;
?>"><?php
			echo $value;
?></option>
		    <?php
		    } //strpos( $value, "template" ) !== false
		} //getDirectory( sketch( "abspath" ) . sketch( "themepath" ) . "views" . sketch( "slash" ) ) as $key => $value
		    ?>
		</select>
		<label>Page form</label>
		<select name="pageform" class="bgClass:'select_bg'">
		    <option value="">None (standard)</option>
<?php
		foreach (getDirectory(sketch("abspath") . sketch("themepath") . "views" . sketch("slash") . "forms" . sketch("slash")) as $key => $value) {
		    if (strpos($value, "page") !== false) {
?>
    		    <option value="<?php
			echo $value;
?>"><?php
			echo $value;
?></option>
		    <?php
		    } //strpos( $value, "page" ) !== false
		} //getDirectory( sketch( "abspath" ) . sketch( "themepath" ) . "views" . sketch( "slash" ) . "forms" . sketch( "slash" ) ) as $key => $value
		    ?>
		</select>
		<label>Page output template</label>
		<select name="pageoutput" class="bgClass:'select_bg'">
		    <option value="">None (standard)</option>
		    <?php
		    foreach (getDirectory(sketch("abspath") . sketch("themepath") . "views" . sketch("slash")) as $key => $value) {
			if (strpos($value, "page") !== false) {
		    ?>
			    <option value="<?php
			    echo $value;
		    ?>"><?php
			    echo $value;
		    ?></option>
		    <?php
			} //strpos( $value, "page" ) !== false
		    } //getDirectory( sketch( "abspath" ) . sketch( "themepath" ) . "views" . sketch( "slash" ) ) as $key => $value
		    ?>
		</select>
	    </div>
	</div>
    </li>
    <li id="addnewrow"><a class="accord-title button"><span class="icons downarrow"></span>Create multiple pages</a>
	<div class="accord-body" id="newpagecontainer">
	    <div class="accord-container">
		<div class="clone" style="float:right;clear:both;width:100%">
		    <div style="float:left;width:25%"><label>Menu Name</label>
			<input type="text" class="newMenus" name="menu_name_multi[]" value="" />
		    </div>
		    <div style="float:left;width:25%">
			<label>Page type</label>
			<select name="page_type_multi[]" class="bgClass:'select_bg'">
			    <option value="general" selected="selected">General</option>
			    <option value="news">News</option>
			    <option value="gallery">Gallery</option>
			    <option value="casestudies">Case-studies</option>
			    <option value="product">Product</option>
			    <option value="newsletter">Newsletter</option>
			    <option value="blog">Blog</option>
                <option value="staff">Staff</option>
                <option value="listing">Listing</option>
			    <option value="member">Member</option>
                <option value="any"        >Any</option>
          		<option value="article"    >Article listing</option>
          		<option value="newsl"      >Newsletter Listing</option>
          		<option value="blogl"      >Blog Listing</option>
          		<option value="productl"   >product Listing</option>
			</select>
		    </div>
		    <div style="float:left;width:24%">
			<label>Publish status</label>
			<select name="page_status_multi[]" class="bgClass:'select_bg'">
			    <option value="published" selected="selected">Publish</option>
			    <option value="hidden">Hidden</option>
			    <option value="member">Members only</option>
			</select>
		    </div>
		    <div style="float:left;width:20%">
			<label>Page under</label>
			<select name="menu_under_multi[]" class="bgClass:'select_bg' pageunder">
			    <option value="">None (top Level page)</option>
				<?php adminFilter("menu",array("select"=>true,"id"=>0)); ?>
			</select>
		    </div>
		    <div style="width:5%;float:left;">
			<label>&nbsp;</label>
			<button type="button" class="button negative hide" style="width:100%;height:32px;margin-top:2px"><span class="icons cross"></span></button>
		    </div>
		</div>
		<div style="float:right;clear:both;width:100%" id="addnewadmin">
		    <a class="button clear"><span class="icons plus"></span>Add Page</a>
		</div>
	    </div>
	</div>
    </li>
</ul>
<script type="text/javascript">
    accordRefresh.delay(500);
    function setupClones(){
	var canProcess = true;
	$('addnewadmin').getElement(".button").addEvent("click",function(){
	    $$(".newMenus").each(function(item,index){
		if($(item).value.clean()==""){
		    $(item).getParent(".clone").highlight();
		    canProcess =  false;
		}
	    });
	    if(canProcess==false){
		return false;
	    }
	    $('newpagecontainer').setStyle("height",$('newpagecontainer').getSize().y + 36);
	    $('addnewrow').getElements(".clone").getLast().getElement("input").addEvent("keypress",function(event){ new Event(event).stop(); return false;});
	    var newrow = $('addnewrow').getElements(".clone").getLast().clone();
	    $(newrow).getElements("label").destroy();
	    $(newrow).inject($('addnewadmin'),'before');
	    $(newrow).getElements("select").each(function(item,index){
		$(item).addEvent("change",function(){
		    $(this).getParent("div").getElement(".span").set("html",$(this).getSelected().get("html"));
			
		    if($(this).hasClass("pageunder")){
			var found = false;
			$('addnewrow').getElements("input[type=text]").each(function(item,index){
			    if($(item).value==this.value && $(item).value.clean() != ''){
				found = true;
				$(this).getParent(".clone").setStyle("width",($(item).getParent(".clone").getStyle("width").toInt()-5 + "%"));
			    }
			},this);
			if(!found){
			    $(this).getParent(".clone").setStyle("width","100%");
			}
		    }
		});
	    });
	    $(newrow).getElement("button").removeClass("hide");
	    $(newrow).getElement("button").addEvent("click",function(){
		$(this).getParent(".clone").destroy();
	    });
	    new Element('option', {'value': $(newrow).getElement("input").get("value"), 'text':$(newrow).getElement("input").get("value")}).inject($(newrow).getElement(".pageunder"));
	    $(newrow).getElements("input").each(function(item,index){
		$(item).set("value","");
	    });
	    $(newrow).getElement("input").addEvent("keypress",function(event){
		if(event.key.toString()=="enter"){
		    $('addnewadmin').getElement(".button").fireEvent("click");
		}
	    });
	    try{$(newrow).getElement("input").focus();}catch(e){}
	});
	$("newpagecontainer").getElement("input").addEvent("keypress",function(event){
	    if(event.key.toString()=="enter"){
		$('addnewadmin').getElement(".button").fireEvent("click");
	    }
	});
    }
    setupClones.delay(500);
</script>