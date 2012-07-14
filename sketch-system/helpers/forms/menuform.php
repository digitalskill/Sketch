<?php
global $sketch, $_POST;
$this->allMenus = array();
if ($sketch->superUser()) {
    $record = getData("sketch_menu", "*", "", "ORDER BY menu_name");
} else {
    $record = getData("sketch_menu", "*", "menu_under <> 25 AND sketch_menu_id <> 25", "ORDER BY menu_name");
}
while ($record->advance()) {
    $this->datalookup[$record->sketch_menu_id] = $record->result;
    $this->allMenus[$record->sketch_menu_id] = $record->menu_name;
}
$record->free();
if (isset($_POST['getItem'])) {
?>
    <form class="required ajax:true output:'load-box' showSave:false" method="post" action="<?php echo urlPath("admin"); ?>/admin_plugin_<?php echo $this->settings['name']; ?>" id="specificMenu">
        <input type="hidden" name="page_id" value="<?php echo $this->page_id; ?>" />
        <input type="hidden" name="preview" value="" />
	<?php $this->getMenuSettings($_POST['getItem']); ?>
</form>
<script type="text/javascript">
    function setupMenuForm(){
	new Validate("specificMenu");
	new accord($('specificMenu'));
    }
    setupMenuForm.delay(500);
</script>
<?php } else { ?>
<form class="form">
    <div id="menuform" style="position:relative;height:100%;">
<?php echo $this->buildAdminMenu(0); ?>
        </div>
    </form>
    <div style="clear:both;">&nbsp;</div>
    <div id="menuajaxzone" class="hide"></div>
    <script type="text/javascript">
        var lastMenuItem = "";
        function setupSorts(){
    	$$(".expander").each(function(item,index){
    	    $(item).addEvent("click",function(event){
		new Event(event).stop();
    		$(this).getParent("ul").getElements("li").each(function(it,ind){
    		    $(it).removeClass("hover");
    		});
    		$(this).getParent("li").addClass("hover");
    	    });
    	});
    	$$(".menu-lister").each(function(item){
    	    new Sorter(item);
    	});
    	$$('.ajaxlink').each(function(item,index){
    	    $(item).addEvent("click",function(){
    		if(lastMenuItem != this && !$("menuajaxzone").hasClass("hide")){

    		}else{
    		    $("menuajaxzone").toggleClass("hide");
    		}
    		lastMenuItem = this;
    	    });
    	    new Ajaxlinks(item);
    	});
        }
        setupSorts.delay(500);
    </script>
<?php
}