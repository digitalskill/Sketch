<?php
class PAGEEDIT extends PLUGIN
{
  function PAGEEDIT( $args )
  {
    $settings = array(
      "location" => "center",
      "global" => 1,
      "menuName" => "Page content",
      "topnav" => 1,
      "pluginsection" => "pageedit"
    );
    $this->start( $settings, $args );
  }
  function update( $old, $new )
  {
    return $new;
  }
  function display( )
  {
    global $sketch;
    $sketch->content = str_replace( array(
      "href='/",
      'href="/'
    ), array(
      "href='http://" . $sketch->urlPath() . "/",
      'href="http://' . $sketch->urlPath() . "/"
    ), $sketch->content );
    if ( is_file( sketch( "abspath" ) . sketch( "themepath" ) . "views" . sketch( "slash" ) . sketch( "pageoutput" ) ) ) {
      include( sketch( "abspath" ) . sketch( "themepath" ) . "views" . sketch( "slash" ) . sketch( "pageoutput" ) );
    }else{
		if(sketch( "pageoutput" ) != ''){
			filter("templates",array("show"=>true,"template_type"=>"output","template_name"=>trim(sketch( "pageoutput" ))));
		}else{
		  switch ( sketch( "page_type" ) ) {
		  case "member":
			$this->memberOutput();
			break;
		  case "blog":
		  $this->blogOutput();
		  break;
		  case "product":
			$this->productOutput();
			break;
		  case "gallery":
			$this->galleryOutput();
			break;
		  case "news":
		  $this->newsOutput();
		  break;
		  case "casestudies":
		  default:
			$this->generalOutput();
			break;
		  } //sketch( "page_type" )
		}
    }
  }
  function generalOutput(){
     @include(loadView("general",false,true));
  }
  function newsOutput(){
       @include(loadView("news",false,true));
  }
  function productOutput(){
    @include(loadView("product",false,true));
  }
  function blogOutput(){ 
	@include(loadView("blog",false,true));
  }
  function galleryOutput(){
	@include(loadView("gallery",false,true));
  }
  function memberOutput(){
    helper("member");
    if(adminCheck() || memberid()==sketch("page_id")){
		// Get members details
		$r = getData("sketch_page","*","page_id=".sketch("page_id"));	
		$r->advance();
		$c = contentToArray($r->content);
      	foreach($c as $key => $value){
        	if(stripos($key,"page")===false && stripos($key,"password")===false && strpos($key,"_")===false){
          		echo ucwords(str_replace("name"," name",$key)) . " : ". ucwords(sketch($key))."<br />";
        	}else{
				echo ucwords(str_replace("name"," name",$key)) . " : ". secureit(sketch($key),true)."<br />";
			}
      	}
    }else{
      echo "<h3>Please login to view your member information</h3>";
    }
  }
  function preview( )
  {
    global $sketch;
    $sketch->edit = str_replace( array(
      "href='/",
      'href="/'
    ), array(
       "href='http://" . $sketch->urlPath() . "/",
      'href="http://' . $sketch->urlPath() . "/"
    ), $sketch->edit );
    if ( is_file( sketch( "abspath" ) . sketch( "themepath" ) . "views" . sketch( "slash" ) . sketch( "pageoutput" ) ) ) {
      include( sketch( "abspath" ) . sketch( "themepath" ) . "views" . sketch( "slash" ) . sketch( "pageoutput" ) );
    } //is_file( sketch( "abspath" ) . sketch( "themepath" ) . "views" . sketch( "slash" ) . sketch( "pageoutput" ) )
    else {
      $this->display();
    }
?><script type="text/javascript">
    try{SetUpCufon()}catch(e){}
   </script><?php
  }
  function doUpdate( )
  {
    global $_POST;
	$data                = $_POST;
    if ( isset( $_POST[ 'page_date' ] ) ) {
      list( $d, $m, $y ) = explode( "-", @$_POST[ 'page_date' ] );
    } else {
      list( $d, $m, $y ) = explode( "-", date( "d-m-Y" ) );
    }
	
	if ( isset( $_POST[ 'page_expiry' ] )  && strpos( $_POST[ 'page_expiry' ],"-") !==false) {
      	list( $xd, $xm, $xy ) = explode( "-", @$_POST[ 'page_expiry' ] );
		$data[ 'page_expiry' ] = (!is_numeric($xd))? "NULL" : $xy . "-" . $xm . "-" . $xd;
	}else{
		$data[ 'page_expiry' ] = "NULL";	
	}
    
    $data[ 'page_date' ] = $y . "-" . $m . "-" . $d;
    $data[ 'page_id' ]   = $this->page_id;
    if(isset($_POST['password'])){
      $_POST['password'] = secureit($_POST['password']);
    }
	
	$r = getData("sketch_page","*","page_id='".$this->page_id."'");
	$row = array_keys((array)$r->advance());
	foreach($row as $key => $value){
		if(isset($_POST[$value]) && $value != "edit"){
			unset($_POST[$value]);
		}
	}
	
    foreach ( $_POST as $key => $value ) {
		if(!is_array($value)){
     		 $_POST[ $key ] = str_replace(array("?",'"'),array("_##-",';#;'), trim(stripslashes($value)) );
		}
    } //$_POST as $key => $value
    $data[ 'edit' ] = serialize( $_POST );
    $r              = ACTIVERECORD::keeprecord( updateDB( "sketch_page", $data ) );
?>
        <script type="text/javascript">
          $(document.body).removeEvents("updatepreview");
          $(document.body).removeEvents("getlive");
          $(document.body).addEvent("updatepreview",function(){
            $('<?php echo $this->settings[ 'name' ];?>').load("<?php echo urlPath( "admin/ajax" );?>_plugin_<?php
              echo $this->settings[ 'name' ]; ?>?page_id=<?php echo $this->page_id; ?>&preview=edit");
          });
          $(document.body).addEvent("getlive",function(){
            $('<?php echo $this->settings[ 'name' ]; ?>').load("<?php echo urlPath( "admin/ajax" ); ?>_plugin_<?php
              echo $this->settings[ 'name' ]; ?>?page_id=<?php echo $this->page_id; ?>&preview=preview");
          });
	</script>
<?php
  }
  function approve(){
    global $_SESSION;
    $SQL = "UPDATE " . $this->prefix . "sketch_page SET content=edit, page_updated=now(), updated_by=" . sketch( "db" )->quote( @$_SESSION[ 'admin' ][ 'user_login' ] ) . " " . "WHERE page_id=" . $this->page_id;
    $r   = ACTIVERECORD::keeprecord( $SQL );
    if ( $r ) {
      $this->preview();
    } //$r
  }
  function showForm(){
    $this->settings[ 'content' ] = @$this->settings[ 'edit' ];
?>
    	<form class="required ajax:true" method="post" action="<?php echo urlPath( "admin" ); ?>/admin_plugin_<?php echo $this->settings[ 'name' ]; ?>" id="form_<?php echo $this->settings[ 'name' ]; ?>">
	<input type="hidden" name="page_id" value="<?php echo $this->page_id; ?>" />
       	<input type="hidden" name="plugin_id" value="<?php echo @$this->settings[ 'plugin_id' ]; ?>" />
       	<ul class="form">
          <li><?php $this->getPageDetails(); ?></li>
        </ul>
<?php
      $this->form();
?>
    		<div class="clear">&nbsp;</div>
    	</form><?php
  }
  function form(){ 
  	@include(loadForm("pageeditform",false));
   if ( is_file( sketch( "abspath" ) . sketch( "themepath" ) . "views" . sketch( "slash" ) . "forms" . sketch( "slash" ) . sketch( "pageform" ) ) ) {
      include( sketch( "abspath" ) . sketch( "themepath" ) . "views" . sketch( "slash" ) . "forms" . sketch( "slash" ) . sketch( "pageform" ) );
    }else{
		if(sketch( "pageform" ) != ''){
			filter("templates",array("show"=>true,"template_type"=>"form","template_name"=>trim(sketch( "pageform" ))));
		}else{
			switch ( sketch( "page_type" ) ) {
			  case "member":
				$this->memberForm();
				break;
			  case "blog":
			  $this->blogForm();
			  break;
			  case "product":
				$this->productForm();
				break;
			  case "news":
			  case "casestudies":
				$this->newsForm();
				break;
			  case "gallery":
			  $this->galleryForm();
			  break;
			  default:
				$this->generalForm();
				break;
			}
		}
	}
    ?>
           <script type="text/javascript">
			function setupPageE(){
                new accord('load-box');
				$$(".popup").each(function(item,index){
					 new Popup(item,{'id':index});
				});
			}
			setupPageE.delay(500);
		</script>
 <?php
  }
  function generalForm( ){
    @include(loadForm("generalform",false));
  }
  function newsForm( ){
     @include(loadForm("newsform",false));
  }
  function productForm( ){
    @include(loadForm("productform",false));
  }
  function memberForm(){
    @include(loadForm("memberform",false));
  }
  function blogForm(){
     @include(loadForm("adminpostform",false));
  }
  function galleryForm(){
      @include(loadForm("admingalleryform",false));
  }
}