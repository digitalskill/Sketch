<ul style="float:right;width:50%;">
                <li>
                    <label>Instructions for creating an XML Feed</label>
                    <div class="instruction-box">
                    <p><strong style="font-size:12px;">Enter in the feed location : </strong><br />This is the address of the XML feed</p>
                    <p><strong style="font-size:12px;">Select the values to show : </strong><br />
                       How do I find the values to show? XML feed have extensive markup, If unsure - just enter in "auto" into this box. 
		       sketch will find the values for you and list them below after you click Save.
		       <br /><strong>IMPORTANT:</strong><br />
		       sketch will use the FIRST item input as the channel. Use the word "auto" if unsure what you channel is for the XML Feed.
		    </p>
                    <p><strong style="font-size:12px;">Select the limit</strong><br />
                       XML feeds can be very long, It is suggested that you limit this to 10 or less.</p>
		    <p><strong style="font-size:12px;">Select the amount to skip</strong><br />
                       You can skip over entries in the XML file</p>
                    <p><strong style="font-size:12px;">Select the frequency : </strong><br />When sketch reads the XML file, it can take some time, slowing your site.
			It is suggested that you set this to daily unless you need a "live" xml feed at all times on your site.
                    </p>
		    <div class="">
			<?php if($this->e('url')!= '')
			{
			    ?>
			    <h3>XML values</h3>
			<?php
			    $xml = simplexml_load_file($this->e('url'), "SimpleXMLElement", LIBXML_NOCDATA);
			    if($xml !== false){
				$count = 0;
				$values = $this->objectsIntoArray($xml);
				 foreach(recall($values) as $k => $v){
				     if(trim($v)!= ""){
					echo (($count==0)? "": ",") .$v;
				     }
				     $count ++;
				     if($count%6==0 & $count > 0){
					 echo "<br />";
					 $count=0;
				     }

				 }

			    }
			}
			?>
		    </div>
                    </div>
                </li>

            </ul>

<ul class="form" style="float:left;width:45%">
        <li><div class="content-column">
    	    <div class="title">Feed XML</div>
    	    <div class="big-font">Global XML Feed for site</div>
    	</div></li>
	<li><label>Feed Heading</label>
    	<input name="heading" class="required" value="<?php echo $this->e('heading'); ?>"/>
        <li><label>Feed URL (http://www.sitename.co.nz/feedpath)</label>
    	<input name="url" class="required" value="<?php echo $this->e('url'); ?>"/>
        </li>
        <li><label>Values to Show (Comma seperated list)</label>
    	<input name="valuestoshow" class="required" value="<?php echo $this->e('valuestoshow'); ?>"/>
        </li>
        <li><label>Limit (0=unlimited)</label>
    	<input name="limit" class="required integer" value="<?php echo $this->e('limit', '0'); ?>"/>
        </li>
        <li><label>Get Feed Frequency</label>
    	<select name="frequency" class="bgClass:'select_bg'">
    	    <option value="daily" <?php if ($this->e("frequency") == "daily") {
?>selected="selected"<?php } ?>>Daily (recommended)</option>
	    <option value="visit" <?php if ($this->e("frequency") == "visit") {
?>selected="selected"<?php } ?>>Each Visit </option>
	</select>
    </li>
</ul>