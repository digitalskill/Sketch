<?php
class USERHELP extends CONTROLLER{
  function USERHELP($page){
    parent::__construct("userhelp");
    	include_once("user-guide.php");
		?>
        	<script type="text/javascript">
				function increaseHelpBar(){
					$('imageSide').morph({'width':415});
					$('load-box').morph("margin-right",472);
					accordRefresh();
					$$(".popup").each(function(item,index){
						 new Popup(item,{'id':index});
					});		
				}
				increaseHelpBar.delay(1000);
			</script>
        <?php
	}
}