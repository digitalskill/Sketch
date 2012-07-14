<ul class="form">
    <li><?php $this->getPageDetails(); ?></li>
</ul>
<ul class="form" id="seoaccord" style="float:left;width:70%;">
    <li>
	<a class="accord-title button"><span class="icons downarrow"></span>Robots</a>
	<div class="accord-body">
	    <div class="accord-container">
		<p>Robots is used by search engines to check if it can index this page and follow links from it</p>
		<input type="text" name="page_robots" id="page_robots" class="required" value="<?php echo $this->e('page_robots'); ?>"/>
	    </div>
	</div>
    </li>
    <li>
	<a class="accord-title button"><span class="icons downarrow"></span>Description</a>
	<div class="accord-body">
	    <div class="accord-container">
		<p>A page description should be a short summary of the page (250 characters)</p>
		<textarea name="page_description" id="page_description" class="maxValue:250" style="height:100px"><?php echo $this->e('page_description', ''); ?></textarea>
	    </div>
	</div>
    </li>
    <li>
	<a class='accord-title button'><span class="icons downarrow"></span>Keywords</a>
	<div class="accord-body">
	    <div class="accord-container">
		<p>Page keywords are to reinforce the content words that define your site and pages. (250 characters max)</p>
		<textarea name="page_keywords" class="maxValue:250" id="page_keywords" style="height:100px;"><?php echo $this->e('page_keywords'); ?></textarea>
	   
	<?php
	$tc = explode(" ", strtolower(str_replace(array('&nbsp;', "&", "'", '"', ",", '.', ';', '_', '{', '}', '[', ']', '*', '(', ')', ':', '@', '!', '#', '$', '%', '^', '|'), '', strip_tags(trim(stripslashes($sketch->content))))));
	$tagCloud = array();
	foreach ($tc as $key => $value) {
	    if (strlen($value) > 5 && trim($value) != '') {
		if (!isset($tagCloud[$value])) {
		    $tagCloud[$value] = 0;
		}
		$tagCloud[$value]++;
	    }
	}

	function cmp($a, $b) {
	    return ($a == $b) ? 0 : (($a > $b) ? -1 : 1);
	}

	uasort($tagCloud, 'cmp');
	$string = '';
	foreach ($tagCloud as $key => $value) {
	    $string .= ( ($string == '') ? "" : ", ") . $key . "(" . $value . ")";
	}
	?>
	<p>sketch has scanned your page content. Theses words have been found, the number <em>()</em> indicates how often it appears.</p>
	<textarea style="height:100px;"><?php echo $string; ?></textarea>
	 </div>
	</div>
    </li>
    <li>
	<a class="accord-title button"><span class="icons downarrow"></span>Page Icons / RSS / Apple / Fav Icons</a>
	 <div class="accord-body">
	 <div class="accord-container">
	<label for='page_canonical'>Canonical</label>
	<input type="text" name="page_canonical"  value="<?php echo $this->e('page_canonical'); ?>"/>
	<label for='favicon'>Favicon</label>
	<input type="text" name="favicon" value="<?php echo $this->e('favicon'); ?>"/>
  
	<label for='appleicon'>Apple I-sketch Icon</label>
	<input type="text" name="appleicon" value="<?php echo $this->e('appleicon'); ?>"/>
  
	<label for='rssfeed'>RSS Feed HTML</label>
	<input type="text" name="rssfeed" value="<?php echo $this->e('rssfeed'); ?>"/>
  
	<label for='rsstext'>RSS Description Text</label>
	<textarea name="rsstext" id="rsstext" style="height:80px;"><?php echo $this->e('rsstext'); ?></textarea>
	 </div>
	 </div>
    </li>
</ul>
<script type="text/javascript">
    function setAccord(){
	new accord($('seoaccord'));
    }
    setAccord.delay(500);
</script>