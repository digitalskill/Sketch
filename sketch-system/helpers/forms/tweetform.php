<ul class="form">
        <li>
        	<label>Twitter screen name</label>
    		<input type="text" name="screen_name" class="" value="<?php echo $this->e("screen_name"); ?>"/>
            <label>Amount of Tweets to Display</label>
            <input type="text" name="amount" class="required integer" value="<?php echo $this->e("amount"); ?>"/>
            <label>Facebook screen name</label>
    		<input type="text" name="facebook_name" class="" value="<?php echo $this->e("facebook_name"); ?>"/>
            <label>Dribble screen name</label>
    		<input type="text" name="dribble_name" class="" value="<?php echo $this->e("dribble_name"); ?>"/>
            <label>Tumblr screen name</label>
    		<input type="text" name="tumblr_name" class="" value="<?php echo $this->e("tumblr_name"); ?>"/>
            <label>Flicker screen name</label>
    		<input type="text" name="flicker_name" class="" value="<?php echo $this->e("flicker_name"); ?>"/>
        </li>
</ul>