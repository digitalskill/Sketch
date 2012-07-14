<?php
helper("session");
helper("member");
function getProducts($items=""){
	// Select All products from the Database That they have ordered
	$items = is_array($items)? $items : (array)sessionGet("cart");
	$where = "WHERE page_type='product' AND (";
	foreach($items as $key => $value){
		if(intval($value) > 0 && intval($key) > 1){
			$where .= (($where=="WHERE page_type='product' AND (")?  "" : " OR " ). "page_id=".intval($key);	
		}else{
			unset($items[$key]);
		}
	}
	if(is_array(sessionGet("cart"))){
		sessionAdd("cart",$items);
	}
	return ($where=="WHERE page_type='product' AND (")? false : getData("sketch_page","*",$where.")","page_id");
}
function addProduct($item="",$quantity=1){
	global $_REQUEST,$_POST;
	if(isset($_REQUEST['quantity']) && isset($_REQUEST['product']) && !is_array($_POST['product']) && $item==""){
		$items = (array)sessionGet("cart");
		$colors = (array)sessionGet("color");
		$sizes = (array)sessionGet("sizes");
		$items[$_REQUEST['product']] = $_REQUEST['quantity'];
		$colors[$_REQUEST['product']] = $_REQUEST['color'];
		$sizes[$_REQUEST['product']] = $_REQUEST['size'];
		sessionAdd("cart",$items);
		sessionAdd("color",$colors);
		sessionAdd("sizes",$sizes);
		return true;
	}else{
	    if(isset($_POST['product']) && is_array($_POST['product'])){
		$items = (array)sessionGet("cart");
		$colors = (array)sessionGet("color");
		$sizes = (array)sessionGet("sizes");
		foreach($_POST['product'] as $key => $value){
		    $items[$value] = $_POST['quantity'][$key];
		    sessionAdd("cart",$items);
		}
		return false; // already on shopping page to bulk update
	    }else{
		if($item != ""){
			$items = (array)sessionGet("cart");
			$items[$item] = $quantity;
			sessionAdd("cart",$items);
			return true;
		}
	    }
	}
	return false;
}

function saveProductSize($id,$size){
	$sizes = (array)sessionGet("sizes");
	$sizes[$id] = $size;
	sessionAdd("sizes",$sizes);	
}

function saveProductColour($id,$size){
	$sizes = (array)sessionGet("color");
	$sizes[$id] = $size;
	sessionAdd("color",$sizes);	
}

function getProductSize($id){
	$sizes = (array)sessionGet("sizes");
	if(isset($sizes[$id])){
		return $sizes[$id];
	}else{
		return false;	
	}
}

function getProductColour($id){
	$sizes = (array)sessionGet("color");
	if(isset($sizes[$id])){
		return $sizes[$id];
	}else{
		return false;	
	}
}

function saveDeliveryInfo($details){
	sessionAdd("delivery",$details);
}

function getDeliveryInfo($delivery=""){
	if($delivery==""){
		$delivery = sessionGet("delivery"); 
	}
	foreach((array)$delivery as $key=>$value){
		$delivery[$key] = htmlentities(stripslashes($value));
	}
	return $delivery;
}

function clearShoppingCart(){
	helper("session");
	sessionRemove("cart");
	sessionRemove("delivery");
	sessionRemove("itembreakdown");
}

function getItemAmount($item){
	$r =  (array)sessionGet("cart");
	return intval((isset($r[$item]))? $r[$item] : 0);
}
function removeFromWishlist($item=""){
	global $_POST;
	$details = memberGet();
	if($item=="" && isset($_POST['nowish']) && is_numeric($_POST['nowish'])){
		$item = intval($_POST['nowish']);	
	}
	if(isset($details['wishlist'][$item])){
		unset($details['wishlist'][$item]);
		memberSet($details);	
	}
}

function addtoWishList($item=""){
	// Add item to members wish list
	$details = memberGet();
	if($item=="" && isset($_POST['wish']) && is_numeric($_POST['wish'])){
		$item = intval($_POST['wish']);	
	}
	if($item != ""){
		if(!isset($details['wishlist']) || !is_array($details['wishlist'])){
			$details['wishlist'] = array();	
		}
		$details['wishlist'][$item] = $item;	 // Push new item into array
	memberSet($details);
	}
}
addtoWishList();
removeFromWishlist();