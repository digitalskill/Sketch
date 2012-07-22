<?php
function compress_page($buffer){
	$search = array("/ +/" => " ",
		"/<!�\{(.*?)\}�>|<!�(.*?)�>|[\t]|<!�|�>|\/\/ <!�|\/\/ �>|<!\[CDATA\[|\/\/ \]\]>|\]\]>|\/\/\]\]>|\/\/<!\[CDATA\[/" => "",
		//'/<!--(.*)-->/Uis'=>"",
		'/>\s+\</'=>'><',
		'/\s(![^\r\n])\s+(![^\r\n])/'=>' ',
		'/&(?![A-Za-z0-9#]{1,7};)/'=>'&amp;'
	);
	$path = urlPath();
	if(!getSettings("htaccess")){
		$path = str_replace("index.php","",$path);
		$buffer = str_replace(
							array("index.php/sketch-images/","index.php/sketch-files",'"sketch-images',"'sketch-images",'"sketch-files',"'sketch-files"),
							array($path."/sketch-images",$path."/sketch-files",'"'.$path.'/sketch-images',"'".$path."/sketch-images",'"'.$path.'/sketch-files',"'".$path."/sketch-files"),
							$buffer);
	}
	return str_replace(array(';###;','href="/',"href='/","src='/",'src="/'),array('&&','href="'.$path,"href='".$path,"src='".$path,'src="'.$path),preg_replace(array_keys($search), array_values($search),str_replace("&&",';###;',$buffer)));
}