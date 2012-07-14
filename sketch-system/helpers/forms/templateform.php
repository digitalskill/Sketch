<ul class="form">
    <li><?php $this->getPageDetails(); ?></li>
</ul>
<ul style="float:right;width:38%;margin-top:-10px;">
    <li>
        <div class="instruction-box">
	    <h4>Instructions for creating a template</h4>
        <p><strong style="font-size:12px;">Enter in all the page content : </strong><br />A template is blank - you will need to create all the necessary HTML elements for your page.</p>
        <p><strong style="font-size:12px;">Enter in the necessary code : </strong><br />Page code needs to be put in <span style="color:#900; font-weight:bold;">&lt;?php ?&gt;</span> tags.<br />
           You will need to open and close questions in one tag - do not split decision items over several objects.</p>
        <p><strong style="font-size:12px;">What code tags can I use :  </strong><br />Anything that is valid PHP can be used.</p>
        <p><strong style="font-size:12px;">Native Function Quick Referance: </strong><br />
        	<select class="bgClass:'select_bg'" onchange="$('codeResult').set('html',this.value); insertCode(this.value);">
            	 <option value="">Code Snippet</option>
                 <option style="padding:5px;color:#00F;background-color:#CCC;">PAGE DETAILS</option>
                 <option value="echo urlPath( );">Get site URL</option>
    		    <option value="echo sketch( 'content' );">Get page content</option>
    		    <option value="echo sketch( 'page_id' );" >Get page id</option>
    		    <option value="echo sketch( 'sketch_settings_id' );">Get site id</option>
                <option value="echo sketch( 'menu_guid' );">Get Page path</option>
                <option value="echo urlPath( sketch( 'menu_guid' ) );">Get Page URL</option>
    		    <option value="echo loadForm( 'form_name' );">Load a Form (form name needs to be a file or form template)</option>
                <option value="echo loadView( 'view_name' );">Load a view (View needs to a be a file or page Template)</option>
                <option style="padding:5px;color:#00F;background-color:#CCC;">DATABASE QUERIES</option>
                <option value="$r = getData( 'table1,table2 ');<br />while($r->advance()){ $c = contentToArray($r->field_name);<br />echo $c['sub_field'];<br />}">Query Database with Seralised data</option>
                <option value="$r = getData( 'table1,table2','*','WHERE a=b','ORDER BY a');<br />while($r->advance()){<br />echo $r->field_name; <br />}">Query Database (simple example)</option>
                <option value="$r = getData( 'table1,table2','*','WHERE a=b','ORDER BY a');<br />while($r->advance()){<br />echo $r->field_name; <br />}">Query Database (simple example)</option>
                <option style="padding:5px;color:#00F;background-color:#CCC;">MEMBERS</option>
                <option value="helper(' member' ); $r = memberid(); ">Get member id</option>
                <option value="helper(' member' ); $r = memberGet(); ">Get member data as an array</option>
                <option value="helper(' member' ); $r = memberSet( array('Field'=>'new value') ); ">Update member data</option>
                <option style="padding:5px;color:#00F;background-color:#CCC;">SHOPPING CART</option>
                <option value="helper(' member' ); $r = memberid(); ">Get member id</option>
                <option value="helper(' member' ); $r = memberGet(); ">Get member data as an array</option>
                <option value="helper(' member' ); $r = memberSet( array('Field'=>'new value') ); ">Update member data</option>
    		</select>
        </p>
        <p id="codeResult" class="info">
        	Select a code snippit above to view a quick referance
        </p>
        </div>
    </li>
</ul>
<ul class="form" id="templateaccord" style="float:left;width:60%;">
<?php
	$r = getData("template");
	while($r->advance()){
?>
    <li>
	<a class="accord-title button"><span class="icons downarrow"></span>Template <?php echo $r->template_name; ?></a>
	<div class="accord-body">
	    <div class="accord-contianer">
			<label>Template Name</label>
            <input type="hidden" name="template_id[]" value="<?php echo $r->template_id; ?>"  />
            <input type="text" name="template_name[]" value="<?php echo $r->template_name; ?>" />
            <label>Template Type</label>
            <select name="template_type[]" class="bgClass:'select_bg'">
    		    <option value="page" 		<?php if ($r->template_type == "page") { ?>selected="selected"<?php } ?>>Page</option>
    		    <option value="output" 		<?php if ($r->template_type == "output") { ?>selected="selected"<?php } ?>>Output</option>
    		    <option value="form" 		<?php if ($r->template_type == "form") { ?>selected="selected"<?php } ?>>Form</option>
    		    <option value="css" 		<?php if ($r->template_type == "css") { ?>selected="selected"<?php } ?>>CSS</option>
    		    <option value="javascript" 	<?php if ($r->template_type == "javascript") { ?>selected="selected"<?php } ?>>Javascript</option>
                <option value="delete">DELETE</option>
    		</select>
            <label>Template Content</label>
            <textarea name="template_content[]" style="height:500px;overflow:auto;"><?php echo htmlentities( str_replace( array( "phpstart","phpstart","endphp" ), array( "<?php","<?","?>" ), $r->template_content ) ); ?></textarea>
	    </div>
	</div>
    </li>
<?php } ?>
 <li>
	<a class="accord-title button"><span class="icons downarrow"></span>New Template</a>
	<div class="accord-body">
	    <div class="accord-contianer">
			<label>Template Name</label>
            <input type="hidden" name="template_id[]" value="0"  />
            <input type="text" name="template_name[]" value="" />
            <label>Template Type</label>
            <select name="template_type[]" class="bgClass:'select_bg'">
                <option value="">Select</option>
    		    <option value="page">Page (HTML)</option>
    		    <option value="output">Output</option>
    		    <option value="form">Form</option>
    		    <option value="css">CSS</option>
    		    <option value="javascript">Javascript</option>
    		</select>
            <label>Template Content</label>
            <textarea name="template_content[]" style="height:500px;overflow:scroll;"></textarea>
	    </div>
	</div>
    </li>
</ul>
<script type="text/javascript">
    function setAccord(){
		$$("textarea").addEvent("focus",function(event){
			currentTemplateEdit = this;
			currentCarPos =  $(this).getCaretPosition();
		});
		$$("textarea").addEvent("keydown",function(event){
			var Ev = new DOMEvent(event);
			currentCarPos =  $(this).getCaretPosition();
			if(Ev.code==9){
				Ev.stop();
				var cp = $(this).getCaretPosition();
				$(this).insertAtCursor("    ");
				$(this).setCaretPosition(cp + 4);
			}
		});
		
		new accord($('templateaccord'));
    }
	function insertCode(code){
		if($(currentTemplateEdit)){
			$(currentTemplateEdit).setCaretPosition(currentCarPos);
			$(currentTemplateEdit).insertAtCursor("<?php echo '<?php '; ?>"+code+"<?php echo " ?>"; ?>");
		}
	}
	function insertPageTemplate(obj){
		$(obj).set("text",$('pagesample').get("html"));
	}
	var currentTemplateEdit = null;
	var currentCarPos = 0;
    setAccord.delay(500);
</script>