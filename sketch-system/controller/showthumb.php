<?php
class SHOWTHUMB extends CONTROLLER{
  function SHOWTHUMB(){
    global $_SESSION;
    $path = str_replace(array("\\","//"),"/",$_SESSION['path']);
    $path = str_replace("/index.php","",urlPath($path))."/";
    if(isset($_SESSION['mediatype']) && ($_SESSION['mediatype']=="gif" || $_SESSION['mediatype']=="jpg" || $_SESSION['mediatype']=="png")){  //JPG, gif or png ?>
      <img src="<?php echo $path . $_SESSION[$_REQUEST['filename']]; ?>"/>
<?php }else{ ?>
      <a href="<?php echo $path . $_SESSION[$_REQUEST['filename']]; ?>"><?php echo $_SESSION[$_REQUEST['filename']]; ?></a>
<?php }  
   }
}