<?php
echo sketch("page_heading")!=""		? "<h1>".sketch("page_heading")."</h1>" 			: "";
echo sketch("leadparagraph")!="" 	? "<p class='lead'>".sketch("leadparagraph")."</p>" : "";
echo sketch("page_image")!= ""		? '<img src="'.str_replace("index.php","",urlPath(sketch("page_image"))) .'" alt="'.htmlentities(strip_tags(sketch("page_heading"))).'" style="max-width:100%"/>' : '';
echo sketch("content");