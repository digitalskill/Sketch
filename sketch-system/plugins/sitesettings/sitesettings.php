<?php
class SITESETTINGS extends PLUGIN {
	function SITESETTINGS($args) {
		$settings = array("location"=>"none","admin"=>1,"php"=>1,"topnav"=>1,"isSuper"=>true,"adminclass"=>"showReEdit:false showPreview:false showPublish:false","pluginsection"=>"sitesettings","menuName"=>"sketch settings");	// [ OPTIONAL - pageEdit | js | css | php | global | location | admin | class ]
		$this->start($settings,$args);
	}
	function update($old,$new){ 				// [ REQUIRED ]
		return $new;
	}
	function doUpdate(){					// [ OVERRIDE ] 
		global $sketch,$_POST,$site_settings;
		helper("cache");
		$cache = CACHECLASS::cache('unset');
		$cache->resetCache();
		$activated = false;
		foreach($_POST['sketch_settings_id'] as $key => $value){
			if(intval($value) > 0){
				$SQL 	= "UPDATE ".$this->prefix."sketch_settings SET ".
								"main_site_url 		= ".sketch("db")->quote($_POST['main_site_url'][$key]).", ".
								"theme_path 		= ".sketch("db")->quote($_POST['theme_path'][$key]).", ".
								"global_update 		= ".sketch("db")->quote($_POST['global_update'][$key])." ".
								"WHERE sketch_settings_id=".intval($value); 
				$r = ACTIVERECORD::keeprecord($SQL);
				$r->free();
			}else{
				if(trim($_POST['main_site_url'][$key]) != "" && trim($_POST['theme_path'][$key]) != ""){
					startTransaction();
					$SQL 	= "INSERT INTO ".$this->prefix."sketch_settings (main_site_url,theme_path,global_update) VALUES (".
									sketch("db")->quote($_POST['main_site_url'][$key]).", ".
									sketch("db")->quote($_POST['theme_path'][$key]).", ".
									sketch("db")->quote($_POST['global_update'][$key])." )";
					$r = ACTIVERECORD::keeprecord($SQL);
					$activated = true;
					if($r){
					    $pid = lastInsertId();
					    $r->free();
					    $data = array();
					    $data['page_updated']	= date("Y-m-d H:i:s");
					    $data['updated_by']		= @$_SESSION['admin']['user_login'];
					    $data['page_date']		= date("Y-m-d");
					    $data['page_title']		= "index";
					    $data['page_type']		= "general";
					    $data['page_status']	= "published";
					    $data['sketch_settings_id'] = $pid;
					    $r = ACTIVERECORD::keeprecord(insertDB("sketch_page", $data));
					    if($r){
						$data['page_id']    = lastInsertId();
						$r->free();
						$data['menu_guid']  = "/";
						$data['menu_show']  = 1;
						$data['menu_under'] = 0;
						$data['menu_name']  = "Home";
						$r = ACTIVERECORD::keeprecord(insertDB("sketch_menu", $data));
						if($r){
						   commitTransaction();
						}
					    }
					}
					$r->free();
				}
			}
		}
		foreach($_POST['plugin_name'] as $key => $value){
			$data = array();
			$data['name']			= stripslashes(trim(strtolower($value)));
			$data['activated']		= (strtolower($_POST['activated'][$key])=="yes" || intval($_POST['activated'][$key])==1)? 1 : 0 ;
			$data['is_super_admin']		= (strtolower($_POST['is_super_admin'][$key])=="yes" || intval($_POST['is_super_admin'][$key])==1)? 1 : 0 ;
			$data['menu_name']		= $_POST['menu_name'][$key];
			$data['site_order']		= intval(@$_POST['site_order'][$key]);	
			$data['global_location']	= strtolower(trim(stripslashes($_POST['global_location'][$key])));
			$sketch->registerPlugin(array("name"=>$data['name']));
			if(intval($_POST['installed'][$key])==0 && strtolower($data['activated'])==1){
				$sketch->plugins[$data['name']]->install();
				$sketch->plugins[$data['name']]->updateSettings($data);
				$activated = true;
			}else if(intval($_POST['installed'][$key])==1){
				$sketch->plugins[$data['name']]->updateSettings($data);
			}
		}

		foreach($site_settings as $key => $value){
		    if(isset($_POST[$key])){
			if($value===true || $value===false){
			    $site_settings[$key] = ($_POST[$key]=="true" || $_POST[$key]==1)? true : false;
			}else{
			    $site_settings[$key] = trim(stripslashes($_POST[$key]));
			}
		    }
		}
		$db =  @mysql_pconnect($site_settings['hostname'], $site_settings['username'], $site_settings['password']); // Connect to the database
		$r = @mysql_select_db($site_settings['database']);

		if(!$db || !$r){?>
		    <script type='text/javascript'>
			 alert('Database not found - please check your settings');
		     </script><?php

		}else{
		    $string = '<?php '."\r\n".'$site_settings = array();'."\r\n";
		    foreach($site_settings as $key => $value){
			if($value=="true" || $value=="false"){
			    $string .= '$site_settings["'.$key.'"] = '.$value.';'."\r\n";
			}else{
			    $string .= '$site_settings["'.$key.'"] = "'.stripslashes(trim($value)).'";'."\r\n";
			}
			
			if(!isset($site_settings['jquery'])){
				$string .= '$site_settings["jquery"] = '.intval($_POST['jquery']).';'."\r\n";	
			}
			
		    }
		    $string .= 'define("SALT", "'.stripslashes(trim($_POST['salt'])).'");';
		    $f = file_put_contents(sketch ("abspath") ."config.php",$string);
		    if(!$f){
			?><script type='text/javascript'>
			 alert('Cannot save new settings - please make config.php writable');
		     </script><?php
		    }
		}

		if($activated){
		    ?><script type="text/javascript">
			alert("New Plugins Activated - Reloading Interface");
			window.location = window.location;
		    </script><?php
		}else{
		    $this->showForm();
		}
	}
	function display(){			// [ REQUIRED ] 		// outputs to the page 
	}
	function form(){ 			// [ REQUIRED ] 
		global $sketch, $site_settings;
		$SQL  = "SELECT * FROM ".$this->prefix."sketch_settings ORDER BY sketch_settings_id";
		$ps_q = ACTIVERECORD::keeprecord($SQL); ?>
		<ul class="form" style="float:left;width:30%; padding:1%;" id='newsites'>
		<li><div class="content-column">
                      <div class="big-font">WEBSITES:</div>
                    </div>
                    </li>
        	<?php 
        	$count = 0;
        	while($ps_q->advance()){
        		$count++;
        		?>
           <li>
           <a class="accord-title button"><span class="icons downarrow"></span><?php if($count==1){?>Main Website: <?php }else{ ?>Website: <?php } echo $ps_q->main_site_url; ?></a>
           <div class="accord-body">
	       <div class="accord-container">
            <label>Site URL</label>
            <input type="text" 		name="main_site_url[]" class="required" value="<?php echo $ps_q->main_site_url; ?>"/>
            <input type="hidden"	name="sketch_settings_id[]" value="<?php echo $ps_q->sketch_settings_id; ?>" />
            <label>Site Theme</label>
           	<select name="theme_path[]" class="required bgClass:'select_bg'">
            	<?php foreach($sketch->getDirectory($sketch->abspath.sketch("slash")) as $key => $value){?>
                <option value="<?php echo $value; ?>" <?php if(trim($ps_q->theme_path) == trim($value)){?>selected="selected"<?php } ?>><?php echo $value; ?></option>
                <?php }?>
            </select>
            <label>Global update (change the date to force refresh of all scripts and css files - (YYYY-MM-DD H:M:S))</label>
            <input type="text" name="global_update[]" class="required" value="<?php echo $ps_q->global_update; ?>"/>
            </div>
	   </div>
          </li>
          <?php } ?>

	  <li>
	   <a class="accord-title button"><span class="icons downarrow"></span>sketch Configuration</a>
           <div class="accord-body">
	       <div class="accord-container">
	     
		<label>Database Name</label>
		<input type="text" name="database" <?php if(isset($r) && !$r){?>style="color:#CB441A;"<?php } ?> value="<?php echo $site_settings['database']; ?>"/>
	    
	    <label>Database Table Prefix</label>
		<input type="text" name="prefix" value="<?php echo $site_settings['prefix']; ?>" />
	    
	    <label>Database Host (Contact your web host if unsure)</label>
		<input type="text" name="hostname" value="<?php echo $site_settings['hostname']; ?>" />
	    
	    <label>Database Username (Contact your web host if unsure)</label>
		<input type="text" name="username" value="<?php echo $site_settings['username']; ?>" />
	    
	    <label>Database Password (Contact your web host if unsure)</label>
		<input type="text" name="password" value="<?php echo $site_settings['password']; ?>" />
	    
	    <label>Database Type</label>
		<select name="dbtype" class="bgClass:'select_bg'">
		    <option value="mysql">MySQL</option>
		</select>
	    
	    <label>Show PHP Errors</label>
		<select name="show_php_errors" class="bgClass:'select_bg'">
		    <option value="1" <?php if($site_settings['show_php_errors']){?>selected="selected"<?php } ?>>Yes (Use this for testing or production servers only)</option>
		    <option value="0" <?php if(!$site_settings['show_php_errors']){?>selected="selected"<?php } ?>>No</option>
		</select>
	    
	    <label>Compress Output</label>
		<select name="compress" class="bgClass:'select_bg'">
		    <option value="1" <?php if($site_settings['compress']){?>selected="selected"<?php } ?>>Yes</option>
		    <option value="0" <?php if(!$site_settings['compress']){?>selected="selected"<?php } ?>>No</option>
		</select>
	    
	    <label>Put WWW in all urls</label>
		<select name="www" class="bgClass:'select_bg'">
		    <option value="0" <?php if(!$site_settings['www']){?>selected="selected"<?php } ?>>No</option>
		    <option value="1" <?php if($site_settings['www']){?>selected="selected"<?php } ?>>Yes</option>
		</select>
	    
	    <label>Use .htacess (if not sure - please leave as "no")</label>
		<select name="htaccess" class="bgClass:'select_bg'">
		    <option value="1" <?php if($site_settings['htaccess']){?>selected="selected"<?php } ?>>Yes</option>
		    <option value="0" <?php if(!$site_settings['htaccess']){?>selected="selected"<?php } ?>>No</option>
		</select>
	    
	    <label>Multiple Sites (If unsure - leave as site names)</label>
		<select name="directory" class="bgClass:'select_bg'">
		    <option value="1" <?php if($site_settings['directory']){?>selected="selected"<?php } ?>>Websites are accessed by Folder names (REQUIRES HTACCESS)</option>
		    <option value="0" <?php if(!$site_settings['directory']){?>selected="selected"<?php } ?>>Websites are accessed by Domain Names</option>
		</select>
	    
	    <label>Enable Caching</label>
		<select name="cache" class="bgClass:'select_bg'">
		    <option value="1" <?php if($site_settings['cache']){?>selected="selected"<?php } ?>>Yes</option>
		    <option value="0" <?php if(!$site_settings['cache']){?>selected="selected"<?php } ?>>No (select if site is in development)</option>
		</select>
	    
        <label>Enable jQuery no conflict</label>
        <select name="jquery" class="bgClass:'select_bg'">
		    <option value="1" <?php if(isset($site_settings[ 'jquery' ]) && $site_settings[ 'jquery' ]){?>selected="selected"<?php } ?>>Yes</option>
		    <option value="0" <?php if(!isset($site_settings[ 'jquery' ]) || !$site_settings[ 'jquery' ]){?>selected="selected"<?php } ?>>No</option>
		</select>
       
	    <label>Get Mootools from Google</label>
		<select name="googleapi" class="bgClass:'select_bg'">
		    <option value="1" <?php if($site_settings['googleapi']){?>selected="selected"<?php } ?>>Yes</option>
		    <option value="0" <?php if(!$site_settings['googleapi']){?>selected="selected"<?php } ?>>No (select if site is in development)</option>
		</select>
	    
	    <label>Encryption Key</label>
		<input type="text" name="salt" id="theme_path" class="text" value="<?php echo SALT; ?>" size="20"/>
	    
	    <label>Development Folder</label>
		<input type="text" name="ignore" id="theme_path" class="text" value="<?php echo $site_settings['ignore']; ?>" />
	    
	    <label>Path to sketch System Folder (if in site folder - ignore)</label>
		<input type="text" name="PathTosketch" id="theme_path" class="text" value="<?php echo $site_settings['PathTosketch']; ?>" />
	    
	   </div>
	   </div>
	  </li>

           <li>
           <a class="accord-title button"><span class="icons downarrow"></span>Create a new website:</a>
           <div class="accord-body">
	       <div class="accord-container">
            <label>Site URL</label>
            <input type="text" 		name="main_site_url[]" value=""/>
            <input type="hidden"	name="sketch_settings_id[]" value="0" />
            <label>Site Theme</label>
           	<select name="theme_path[]" class="bgClass:'select_bg'">
           		<option value="">None</option>
            	<?php foreach($sketch->getDirectory($sketch->abspath.sketch("slash")) as $key => $value){?>
                <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                <?php }?>
            </select>
            <label>Global update (change the date to force refresh of all scripts and css files - (YYYY-MM-DD H:M:S))</label>
            <input type="text" name="global_update[]" class="" value="<?php echo date("Y-m-d :H:i:s")?>"/>
            </div>
	   </div>
          </li>
       </ul>
        <ul class="form" id="pluginAccord" style="float:left;width:30%; padding:1%;">
        	<li>
            	<div class="content-column">
                	<div class="big-font">INSTALLED PLUGINS:</div>
                </div>
            </li>
			<?php 
				$allPlugins = $sketch->getDirPlugins(); //$sketch->getDirectory($sketch->sketchPath."plugins".sketch("slash"));
				$SQL 		= "SELECT `name` FROM ".$this->prefix."plugin";
				$pl_q 		= ACTIVERECORD::keeprecord($SQL);
				$installed = array();
				while($pl_q->advance()){
					$installed[] = $pl_q->name;
				}
				$pl_q->free();
				foreach($allPlugins as $key => $value){
					if(in_array(trim($value),$installed)){
						$sketch->registerPlugin(array("name"=>$value));?>
                    	<li><a class="accord-title button"><span class="icons downarrow"></span><?php echo $sketch->plugins[$value]->menuName($value) ;?></a>
                        	<div class="accord-body">
				    <div class="accord-container">
                            	<label>Plugin Area</label>
                                <input name="global_location[]" value="<?php echo $sketch->plugins[$value]->settings['global_location']; ?>"/>
                                <label>Page Order</label>
                                <input name="site_order[]" value="<?php echo $sketch->plugins[$value]->settings['site_order']; ?>"/>
                                <label>Menu Name</label>
                                <input name="menu_name[]" value="<?php echo $sketch->plugins[$value]->menuName($value) ;?>"/>
                                <label>Activated</label>
                                <select name="activated[]" class="bgClass:'select_bg'">
		    						<option value="Yes" <?php if($sketch->plugins[$value]->settings['activated']==1){?>selected="selected"<?php } ?>>Yes</option>
		    						<option value="No" <?php if($sketch->plugins[$value]->settings['activated']!=1){?>selected="selected"<?php } ?>>No</option>
								</select>
                                <label>Super Admin Only</label>
                                 <select name="is_super_admin[]" class="bgClass:'select_bg'">
		    						<option value="Yes" <?php if($sketch->plugins[$value]->isSuper()){?>selected="selected"<?php } ?>>Yes</option>
		    						<option value="No" <?php if(!$sketch->plugins[$value]->isSuper()){?>selected="selected"<?php } ?>>No</option>
								</select>
                                <input type="hidden" name="plugin_name[]" value="<?php echo $value; ?>" />
                                <input type="hidden" name="installed[]" value="1" />
                            </div>
			    </div>
                        </li>
					<?php
					}
				}?>
        </ul>
        <ul class="form" id="pluginAccordInstall" style="float:left;width:30%; padding:1%;">
        	<li>
            	<div class="content-column">
                	<div class="big-font">AVAILABLE PLUGINS:</div>
                </div>
            </li>
			<?php 
				foreach($allPlugins as $key => $value){
					if(!in_array(trim($value),$installed)){
						if($sketch->registerPlugin(array("name"=>$value))!==false){?>
                    		<li><a class="accord-title button"><span class="icons downarrow"></span><?php echo $sketch->plugins[$value]->menuName($value) ;?></a>
                        	<div class="accord-body">
				    <div class="accord-container">
                            	<label>Plugin Area</label>
                                <input name="global_location[]" value="<?php echo $sketch->plugins[$value]->settings['location']; ?>"/>
                                <label>Menu Name</label>
                                <input name="menu_name[]" value="<?php echo $sketch->plugins[$value]->menuName($value) ;?>"/>
                                <label>Install?</label>
                                <select name="activated[]" class="bgClass:'select_bg'">
		    						<option value="Yes">Yes</option>
		    						<option value="No" selected="selected">No</option>
								</select>
                                <label>Super Admin Only</label>
                                 <select name="is_super_admin[]" class="bgClass:'select_bg'">
		    						<option value="Yes" <?php if($sketch->plugins[$value]->isSuper()){?>selected="selected"<?php } ?>>Yes</option>
		    						<option value="No" <?php if(!$sketch->plugins[$value]->isSuper()){?>selected="selected"<?php } ?>>No</option>
								</select>
                                <input type="hidden" name="plugin_name[]" value="<?php echo $value; ?>" />
                                <input type="hidden" name="installed[]" value="0" />
                            </div>
				    </div>
                        	</li>
                        <?php } ?>
					<?php
					}
				}
			?>
        </ul>
        <script type="text/javascript">
		function setupSettingAccord(){
			new accord($('newsites'));
			new accord($('pluginAccord'));
			new accord($('pluginAccordInstall'));
			$("load-box").getElements("form").each(function(item,index){
			   new Validate($(item));
			});
		}
		setupSettingAccord.delay(500);
	</script>
<?php }
}