 <div id="socials">
                <ul>
                  <li><a href="<?php echo urlPath("newsfeed"); ?>"><img src="sketch-images/icon-rss.png" alt="" /></a></li>
                  <?php if($this->e('screen_name')!=''){?>
                  <li><a href="https://twitter.com/#!/<?php echo $this->e('screen_name'); ?>"><img src="sketch-images/icon-twitter.png" alt="" /></a></li>
                  <?php } ?>
                   <?php if($this->e('dribble_name')!=''){?>
                  <li><a href="http://dribbble.com/<?php echo $this->e('dribble_name'); ?>"><img src="sketch-images/icon-dribble.png" alt="" /></a></li> 
                  <?php } ?>
                   <?php if($this->e('tumblr_name')!=''){?>
                  <li><a href="#"><img src="sketch-images/icon-tumblr.png" alt="" /></a></li>
                   <?php } ?>
                   <?php if($this->e('flicker_name')!=''){?>
                  <li><a href="#"><img src="sketch-images/icon-flickr.png" alt="" /></a></li>
                   <?php } ?>
                   <?php if($this->e('facebook_name')!=''){?>
                  <li><a href="http://www.facebook.com/<?php echo $this->e('facebook_name'); ?>"><img src="sketch-images/icon-facebook.png" alt="" /></a></li>
                   <?php } ?>
                </ul>
              </div>