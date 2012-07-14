<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Database Error</title>
</head>
<body>
<div style="border:1px solid #e2e2e2;padding:10px;margin:auto;width:80%;">
	<h3>Your request cannot be completed due to the following:</h3>
	<p>
	<?php 
	if(function_exists("sketch")){
		echo sketch("errorMessage"); 
	}else{
		echo $sketch->errorMessage;	
	}
	?>
    </p>
</div>
</body>
</html>