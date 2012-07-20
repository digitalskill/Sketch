<?php
class INDEX extends CONTROLLER{
  function INDEX($page){
	parent::__construct("index");
    if(isset($_GET['page_id'])){
		$r = getData("sketch_menu","menu_guid","page_id=".intval($_GET['page_id']));
		if($r->advance()){
			header("Location: ". urlPath($r->menu_guid));
			exit();
		}
    }
	
	if(sketch("page_status")=='member' && sketch("menu_class") != "" && !adminCheck()){
		$allowed = false;
		helper("member");
		$details = memberGet();
		if(!isset($details['group'])){
			$details['group'] = '';	
		}
		$groups = explode(',',$details['group']);
		foreach((array)$groups as $key => $value){
			if(stripos(sketch("menu_class"),$value) !== false){
				$allowed = true;
			}
		}
		if(!$allowed){
			header("location: ".urlPath());
			die();	
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
	
	if(adminCheck() && isset($_POST['ajax']) && isset($_POST['approvemember'])){
		$data = array();
		$data['page_id'] = intval($_POST['approvemember']);
		$data['page_type'] ='member';
		setData("sketch_page",$data);
		die("APPROVED");
	}
	
	if(adminCheck() && isset($_POST['ajax']) && isset($_POST['unapprovemember'])){
		$data = array();
		$data['page_id'] = intval($_POST['unapprovemember']);
		$data['page_type'] ='unapproved';
		setData("sketch_page",$data);
		die("APPROVED");
	}
	
	if(adminCheck() && isset($_POST['ajax']) && isset($_POST['approvepage'])){
		$data = array();
		$data['page_id'] = intval($_POST['approvepage']);
		$data['page_status'] ='published';
		setData("sketch_page",$data);
		die("APPROVED");
	}

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
		  $this->loadView(trim(sketch("pagefile")));
		  ob_end_flush();
		}
  	}
}