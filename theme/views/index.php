<!doctype html>
<!--[if lt IE 7]> <html class="ie6 oldie no-js"> <![endif]-->
<!--[if IE 7]>    <html class="ie7 oldie no-js"> <![endif]-->
<!--[if IE 8]>    <html class="ie8 oldie no-js"> <![endif]-->
<!--[if IE 9 ]>   <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<head>
<meta charset="UTF-8">
<meta name="viewport" content="">
<title>
<?php 
// - SKETCH TO OUTPUT PAGE TITLE
	echo strip_tags(sketch("page_title")); 
// - END OUTOPUT OF PAGE TITLE
?>
</title>
<?php 
// LOAD STYLES AND SCRIPTS IN DREAMWEAVER 
if(false){ ?>
<link href="styles/boilerplate.css" rel="stylesheet" type="text/css">
<link href="styles/grid.css" rel="stylesheet" type="text/css">
<link href="styles/main.css" rel="stylesheet" type="text/css">
<link href="styles/stickyfooter.css" rel="stylesheet" type="text/css">
<?php } 
// END LOADING STYLES AND SCRIPTS FOR DREAMWEAVER

// - SKETCH LOADING OF HANDLERS
	plugins('meta'); 		// META TAGS 	
	getStylePath(); 		// CSS Styles 
	getScriptPath(); 		// mootools 	
	plugins('script'); 		// Load Script based plugins 
// - END SKETCH LOADING OF HANDLERS
?>
</head>
<body class="<?php echo sketch("page_type"); ?>">
<?php 
// - SKETCH  GET START PLUGINS AND SCRIPTS
	plugins("start"); 
// - END SKETCH LOADING 'START' PLUGINS
?>
<div class="wrapper">
<div class="gridContainer clearfix">
  	<div id="topNav">
    	<div id="logo"><a href="<?php echo urlPath(); ?>"><img src="/sketch-images/logo.png" alt="Sketch" /></a></div>
    	<div id="menu" class="menu noclass">
      <?php 
		// - SKETCH GET MENU
		plugin("menu"); 
		// - END GET MENU
		// FOR DREAMWEAVER - DO NOT REMOVE
		if(false){ 
			?>
      <?php
			include "../../sketch-system/helpers/outputs/menu.php";
			?>
      <?php
		}
		?>
    	</div><!-- end id menu		//-->
  	</div><!-- end id topNav 		//-->
		<?php 
  		// - SKETCH BANNER PLUGIN
  		plugin("banner"); 
		// - END GET SKETCH BANNER
		
		// FOR DREAMWEAVER - DO NOT REMOVE
		if(false){ 
			?>
      <?php
			include "../../sketch-system/helpers/outputs/banner.php";
			?>
      <?php
		}
		?>
		<div id="content">
  		<?php
              switch (sketch("page_type")){
                  case "blogl":
                  case "blog":
                  case "news":
                  case "product":
                  case "any":
                  case "checkout":
                  case "productl":
				  case "article":
				  case "listing":
                     ?>
                  	<div id="postwrap">
					<?php 
						echo sketch("page_type")=="checkout" ? '<div class="post">' : '';
						plugins("center");
						echo sketch("page_type")=="checkout" ? '</div>' : '';
					?>
                    
                    </div>
                  	<div id="sidebar">
					<?php 
                        filter('shoppingcart'); 
                        plugins("sidebar"); 
                    	?>
                  </div>
                  <?php
				break;
				case "landing":
                    ?>
                  <div id="landing">
                    <div class="intro">
                    <?php plugins("center"); ?>
                    </div>
                  </div>

                  <div id="boxes" class="about">
                    <?php plugins('sidebar'); ?>
                  </div>
  				<?php
				break;
				case "galleryl":
                    ?>
  					<div id="gallerynav">
					<?php 
                         sidenav(array('class'=>'gallerynav','id'=>"",'doTop'=>false,"page_id"=>sketch("page_id")));  
                    ?>
  					</div>
                    <div id="gallerygrid">
					<?php
						 plugins("center"); 
					?>
                    </div>
                    <?php
                    break;
                    case "gallery":
						 plugins("center");
                    break;
                    default:
                    ?>
  					<div id="sidenav">
						<?php sidenav();  ?>
                    </div> <!-- 		end id sidenav				//-->
					<div id="rightcontent">
                    	<div class="tab-content">
                    	<?php plugins("center"); ?>
                    	</div> <!-- 	end tab-content class 		//-->
                    </div> <!-- 		end id rightcontent 		//-->
  					<div id="boxes" class="about">
    				<?php plugins('sidebar'); ?>
  					</div><!-- 			end id boxes				//-->
  					<?php
                    break;
              } ?>
	</div>
</div>
<div class="push"></div>
</div>
<div class="footer">
<div id="footer">
  <div id="footerLeft">
    <p class="copyright">&copy; Sketch Development 2010-2012 | Sketch Version <?php echo getSettings("version");?><br />Theme from <a href="http://elemisfreebies.com/04/14/delphic-free-html-template/">elemisfreebies</a></p>
  </div><!--							end id footerLeft		//-->
  <div id="footerRight">
<?php 
  	// - SKETCH GET SOCIAL ICONS
	filter("twitter",array("links"=>true));
	// - END SKETCH GET SOCIAL ICONS
?>
  </div> <!-- 							end id footerRight 		//-->
</div>	 <!--							end if footer			//-->							
</div>
<?php 
	// - SKETCH GET END PLUGINS
	plugins("end"); 
	// - END SKETCH LOADING 'END' PLUGINS
?>
</body>
</html>