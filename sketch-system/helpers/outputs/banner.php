<div id="banner-container"> 
    <div id="leftbtn"><a href="#" id="bannerbackbtn">Prev</a></div>
    <div id="bannerCenter">
      <div id="sliderholder-cycle" class="<?php if($this->e('effect')!=""){ ?>animate<?php } ?> banner-animate <?php if(isset($_POST['panel_id']) || isset($_POST['addbanner'])){?>autoStart:false<?php } ?> nextButton:'bannernextbutton' backButton:'bannerbackbtn' directBtn:'.banner-controlbtn' effect:'<?php echo $this->e('effect'); ?>' effectTime:'<?php echo $this->e('effectTime'); ?>' delay:<?php echo $this->e('delay'); ?>">
        <?php		
        $count=0;
        while($r->advance()){ ?>
        <div class="anime banner-anime <?php if($count>0){?>hide<?php } ?>">
          <?php if(trim($r->panel_link)!=""){ ?>
          <a href="<?php echo $r->panel_link; ?>">
          <?php } ?>
          <div class="<?php echo $this->e("innerclass"); ?>">
            <div class="banner-text">
              <h1 class="banner-heading"><?php echo $r->panel_heading; ?></h1>
              <?php echo $r->panel_content; ?> </div>
            <?php if(trim($r->panel_image)!=''){ ?>
            <div class="banner-image"><img src="<?php echo urlPath($r->panel_image); ?>" alt="" /> </div>
            <?php } ?>
          </div>
          <?php if(trim($r->panel_link)!=""){ ?>
          </a>
          <?php } ?>
        </div>
        <?php
         $count++;
    } // End while ?>
      </div>
      <ul class="slidernav">
        <?php		$r->seek();
                $count = 0;
                while($r->advance()){
                    $count++;
                    $imgsrc = (trim($r->panel_thumbnail) !='')? $r->panel_thumbnail : $r->panel_image;
                    ?>
        <a class="banner-controlbtn" href="#" id="bE<?php echo $r->panel_id; ?>"></a>
        <?php		} ?>
      </ul>
    </div>
    <div id="rightbtn"><a href="" id="bannernextbutton">Next</a></div>
    </div>