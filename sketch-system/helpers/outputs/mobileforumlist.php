<?php if(!isset($_REQUEST['replace'])){
    ?><ul title="<?php echo $this->e("heading" . $i); ?>"><?php
}
loadHelper("session");
loadHelper("member");
if ($this->e('members' . $i) == "yes" || memberid() || adminCheck()) {
    while ($panel_q_row->advance()) { ?>
	<li class="post" id="post-<?php echo $panel_q_row->page_id; ?>"><?php echo strip_tags(trim(stripslashes($reply_q->page_heading)));  
	list($y, $m, $d) = explode("-", $panel_q_row->page_updated);
	list($h, $mins, $s) = explode(":", end(explode(" ", $d)));
	$day = explode(" ", $d);
	echo "<a href='".urlPath($panel_q_row->menu_guid)."?m&amp;i=".$i."'>";
	echo "<span style='font-size:small'>".@date("d F, Y", mktime(intval($h), intval($mins), intval($s), $m, $day[0], $y)) . " | Posted by " . stripslashes($panel_q_row->updated_by)."</span><br/ >";
	echo $panel_q_row->page_heading."</a></li>";
	$count++;
    } // End WHILE
    $nextpURL = urlPath(sketch("menu_guid"));
    $nextpURL .= "?replace&amp;";
    if ($totalRecords > $pagelimit && $pagelimit != 0) {
	if ($totalRecords > ($startfrom + $pagelimit)) {
	    ?><li><a target="_replace" href="<?php echo $nextpURL . "fl" . $i . "=" . (intval(@$_REQUEST['fl' . $i]) + 1); ?>&amp;m">Next <?php echo $pagelimit; ?></a></li>
    <?php } ?>
<?php
    }
}else{
    ?><li><h2>This is a member only Forum.</h2>Please join or sign in to view</li><?php
}
if(!isset($_REQUEST['replace'])){
    ?></ul><?php
}