<ul style="float:right;width:28%;">
    <li>
            <div class="instruction-box">
		<h4>Instructions for setting up and editing members</h4>
            <p><strong style="font-size:12px;">Enter in the Members Details.</strong></p>
            <p><strong style="font-size:12px;">Fill in the fields on the form :  </strong><br />When happy with the membership information - click on Save</p>
            <p><strong style="font-size:12px;">Preview : </strong><br />Preview the new content<br />This is done by clicking on "preview" when it becomes available.</p>
            <p><strong style="font-size:12px;">Publish : </strong><br />Only click publish when you want to create the new member.</p>

            </div>
    </li>
</ul>
<ul style="float:left;clear:left;width:70%" class="accordian form">
<li>
  <a class="accord-title button"><span class="icons downarrow"></span>Member Details</a>
  <div class="accord-body">
      <div class="accord-container">
   		<?php 
		helper("member");
		$_SESSION['memberid'] = sketch("page_id");
		$r = getData("sketch_page,sketch_menu","*","sketch_page.page_id='".sketch("page_id")."'");
		$r->advance();
		$_POST = contentToArray($r->content);
		include(loadForm("detailform",false)); 
		?>
  </div>
  </div>
    </li>
</ul>