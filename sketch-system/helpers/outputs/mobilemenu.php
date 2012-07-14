<?php
$menuunder = intval(sketch("menu_under"));
if($menuunder==0){
    $menuunder = "0 OR menu_under IS NULL";
}
helper("member");
$sqlExtra = " AND page_status <> 'hidden' AND page_status <> 'member' ";
if (memberid ()) {
    $sqlExtra = " AND page_status='member' AND page_status <> 'hidden' ";
}
$record = getData("sketch_menu,sketch_page", getSettings("prefix") .
		    "sketch_menu.*", "sketch_settings_id=" . sketch("siteid") .
		    " AND menu_under <> 25 AND sketch_menu_id <> 25 AND menu_show=1 AND menu_mobile=1 AND page_type='general' AND menu_under=".$menuunder." OR menu_under=".sketch("sketch_menu_id")." ".
		    $sqlExtra, "ORDER BY menu_under, menu_order, sketch_menu_id");
$currentm = 0;
$count = 0;
echo '<ul id="m0" title="'. $_SERVER['HTTP_HOST'] .'" selected="true">';
while($record->advance()){  
    if($currentm != intval($record->menu_under) && $count > 0){
	echo '</ul><ul id="m'.$record->menu_under.'" title="'.$record->menu_name.'">';
	$currentm = intval($record->menu_under);
    }
    echo '<li><a href="'.urlPath($record->menu_guid).'?m">'.$record->menu_name.'</a></li>';
    $count++;
}
echo '</ul>';