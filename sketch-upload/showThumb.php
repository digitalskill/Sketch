<?php
session_start();
error_reporting(0);
		$path = str_replace("\\","/",$_SESSION['path']); 
		if(isset($_SESSION['mediatype']) && ($_SESSION['mediatype']=="gif" || $_SESSION['mediatype']=="jpg" || $_SESSION['mediatype']=="png")){  //JPG, gif or png ?>
			<img src="<?php echo $path . $_SESSION[$_REQUEST['filename']]; ?>" rel="<?php echo str_replace("../cms/","cms/",$path . $_SESSION[$_REQUEST['filename']]); ?>" class="link"/>
            <input name="image[]" value="<?php echo $_SESSION[$_REQUEST['filename']]; ?>" type="hidden" />
<?php 	}else{
			if(isset($_SESSION['mediatype']) && ($_SESSION['mediatype']=="pdf" || $_SESSION['mediatype']=="doc" || $_SESSION['mediatype']=="ppt")){ ?>
				<a href="<?php echo $path . $_SESSION[$_REQUEST['filename']]; ?>" class="link" rel="<?php echo str_replace("../cms/","cms/",$path . $_SESSION[$_REQUEST['filename']]); ?>"><?php echo $_SESSION[$_REQUEST['filename']]; ?></a>
<?php
			}else{
				if(isset($_SESSION['mediatype']) && $_SESSION['mediatype']=="swf"){					// Flash File ?>
				 	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" height="200">
                    <param name="movie" value="images/<?php echo $_SESSION[$_REQUEST['filename']]; ?>" />
                    <param name="quality" value="high" />
                    <embed src="<?php echo $path . $_SESSION[$_REQUEST['filename']]; ?>" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" height="200"></embed>
				</object>    
			<?php
					}else{
						if(isset($_SESSION['mediatype']) && $_SESSION['mediatype']=="flv"){				// FLV file ?>
				 		<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="200" height="160" id="FLVPlayer">
              				<param name="movie" value="uploadfiles/FLVPlayer_Progressive.swf" />
              				<param name="salign" value="lt" />
              				<param name="quality" value="high" />
              				<param name="scale" value="noscale" />
                        	<param name="FlashVars" value="&MM_ComponentVersion=1&skinName=Clear_Skin_2&streamName=<?php echo $path . str_replace(".flv","",$_SESSION[$_REQUEST['filename']]); ?>&autoPlay=false&autoRewind=false" />
              				<embed src="uploadfiles/FLVPlayer_Progressive.swf" flashvars="&MM_ComponentVersion=1&skinName=Clear_Skin_2&streamName=<?php echo $path . str_replace(".flv","",$_SESSION[$_REQUEST['filename']]); ?>&autoPlay=false&autoRewind=false" quality="high" scale="noscale" width="200" height="160" name="FLVPlayer" salign="LT" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" />            
						</object>
			<?php 
						}else{ 											// Error Not Sure what this is ?>
							<a href="<?php echo $path . $_SESSION[$_REQUEST['filename']]; ?>" class="link" rel="<?php echo str_replace("../cms/","cms/",$path . $_SESSION[$_REQUEST['filename']]); ?>"><?php echo $_SESSION[$_REQUEST['filename']]; ?></a>
			<?php		}
					}
				}
			} 
?>