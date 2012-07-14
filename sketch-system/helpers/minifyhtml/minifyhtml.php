<?php
function compress_page($buffer){
	$search = array("/ +/" => " ",
		"/<!�\{(.*?)\}�>|<!�(.*?)�>|[\t]|<!�|�>|\/\/ <!�|\/\/ �>|<!\[CDATA\[|\/\/ \]\]>|\]\]>|\/\/\]\]>|\/\/<!\[CDATA\[/" => "",
		//'/<!--(.*)-->/Uis'=>"",
		'/>\s+\</'=>'><',
		'/\s(![^\r\n])\s+(![^\r\n])/'=>' ',
		'/&(?![A-Za-z0-9#]{1,7};)/'=>'&amp;'
	);
	return str_replace(array(';###;','href="/',"href='/","src='/",'src="/'),array('&&','href="'.urlPath(),"href='".urlPath(),"src='".urlPath(),'src="'.urlPath()),preg_replace(array_keys($search), array_values($search),str_replace("&&",';###;',$buffer)));
}