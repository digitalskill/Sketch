<?php
class SEO extends PLUGIN {
	function SEO($args) {
		$settings = array("location"=>"meta","global"=>1,"php"=>1,"adminclass"=>"showReEdit:false showPreview:false showPublish:false","pluginsection"=>"pageedit","menuName"=>"SEO settings");	// [ OPTIONAL - pageEdit | js | css | php | global | location | admin | class ]
		$settings['content'] = array("favicon"=>"","appleicon"=>'');
		$this->start($settings,$args);
	}
	function update($old,$new){ 		// [ REQUIRED ]
		return $new;
	}
	function doUpdate(){						// [ OVERRIDE ] 
		global 	$_POST,$_SESSION;
		$values = array("favicon"=>$_POST['favicon'],"appleicon"=>$_POST['appleicon']);
		$SQL 	= "UPDATE ".$this->prefix."plugin SET content=".sketch("db")->quote(serialize($values)).", edit=".sketch("db")->quote(serialize($values))." WHERE plugin_id='".intval($this->settings['plugin_id'])."'";
		startTransaction();
		$r = ACTIVERECORD::keeprecord($SQL);
		$r->free();
		$r 	= ACTIVERECORD::keeprecord(updateDB("sketch_page",$_POST,$this->page_id));
		$r->free();
		commitTransaction();
		?>
        <script type="text/javascript">
			document.title = "<?php echo stripslashes($_POST['page_title']); ?>";
		</script>
        <?php
	}
	function display(){                             // [ REQUIRED ] 		// outputs to the page
          ?><meta name="description" content="<?php	echo $this->e('page_description'); ?>" /><?php
        ?><meta name="keywords" content="<?php		echo $this->e('page_keywords'); ?>" /><?php 
        ?><meta name="robots" content="<?php		echo $this->e('page_robots','INDEX,FOLLOW'); ?>" /><?php 
        if($this->e('canonical','')!=''){
        	?><link rel="canonical" href="<?php 	echo $this->e('page_canonical'); ?>" /><?php
       	}else{
			?><link rel="canonical" href="<?php 	echo urlPath(sketch("menu_guid")) ?>" /><?php
		}
        if($this->e('favicon')!= ''){
        	?><link rel="icon" href="<?php 		echo str_replace("index.php/","",urlPath($this->e('favicon'))); ?>" type="image/x-icon" /><?php
        	?><link rel="shortcut icon" href="<?php	echo str_replace("index.php/","",urlPath($this->e('favicon'))); ?>" type="image/x-icon" /><?php
        }
        if($this->e('appleicon')!= ''){
        	?><link rel="apple-touch-icon" href="<?php echo str_replace("index.php/","",urlPath($this->e('appleicon'))); ?>"/><?php
        } 
        if($this->e('rssfeed','')!=''){
        	?><link rel="alternate" type="application/rss+xml" title="<?php echo $this->e('rsstext'); ?>" href="<?php echo $this->e('rssfeed'); ?>" /><?php 
        }
	}
	function showForm(){ 	// [ OVERRIDE ]
		$this->settings['content'] = $this->settings['edit'];
	?>
    	<form class="required ajax:true" method="post" action="<?php echo urlPath("admin"); ?>/admin_plugin_<?php echo $this->settings['name']; ?>" id="form_<?php echo $this->settings['name']; ?>">
		<input type="hidden" name="page_id" value="<?php 		echo $this->page_id; 	?>" />
       	<input type="hidden" name="plugin_id" value="<?php 		echo $this->settings['plugin_id']; ?>" />
		<?php $this->form(); ?>
    		<div style="clear:both;">&nbsp;</div>
    	</form><?php
	}
	function form(){ 			// [ REQUIRED ] 
		global $sketch;
		@include(loadForm("seoform",false));
	}
}