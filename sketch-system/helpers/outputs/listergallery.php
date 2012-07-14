<?php
$counter = 0;
while($panel_q->advance()){
	$c = contentToArray($panel_q->result['content']);
	?>
	<li data-id="id-<?php echo $mcount; ?>" class="<?php echo isset($master)? str_replace(" ","-",@htmlentities(@$master->menu_name)) : ""; ?>"> 
		<a href="<?php echo urlPath($panel_q->menu_guid); ?>" title="<?php echo htmlentities($panel_q->menu_name); ?>">
		<img src="<?php echo urlPath($c['page_image']); ?>" alt="<?php echo htmlentities($c['page_heading']); ?>" />
		</a>
	</li>
	<?php 
}