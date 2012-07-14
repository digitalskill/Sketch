<?php
class INDEX extends CONTROLLER{
  function INDEX($page){
    if(isset($_GET['page_id'])){
		$r = getData("sketch_menu","menu_guid","page_id=".intval($_GET['page_id']));
		if($r->advance()){
			header("Location: ". urlPath($r->menu_guid));
			exit();
		}
    }
	
	// PAGE APPOVAL / REMOVAL WORKING
	if(adminCheck() && isset($_POST['ajax']) && isset($_POST['deletepage'])){
		$data = array();
		$data['page_id'] = intval($_POST['deletepage']);
		removeData("tag",$data);
		removeData("sketch_page",$data);
		die("DELETED");
	}
	
	if(adminCheck() && isset($_POST['ajax']) && isset($_POST['approvepage'])){
		$data = array();
		$data['page_id'] = intval($_POST['approvepage']);
		$data['page_status'] ='published';
		setData("sketch_page",$data);
		die("APPROVED");
	}
	
	
    parent::__construct("index");
		plugin("templates");
    	list($page,) = explode(".",$page);
		if(sketch("nativePage")==false || !sketch("db") || isset($_REQUEST['single'])){
		  list($url,) = explode("?",end(explode("/",$page)));
		  $this->loadView($url);
		}else{
		  helper("minifyhtml");
		  if(sketch("page_type")=="product"){
			helper("shoppingcart");
			helper("member");
		  }
		  ob_start("compress_page");
		  	$templates = sketch("plugins");
		  	if(isset($templates['templates'])){
		  		filter("templates",array("show"=>true,"template_type"=>"page","template_name"=>trim(sketch("pagefile"))));
			}else{
		  		$this->loadView(trim(sketch("pagefile")));
			}
		  ob_end_flush();
		}
  	}
}