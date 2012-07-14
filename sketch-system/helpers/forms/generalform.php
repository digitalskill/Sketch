<ul style="float:right;width:28%;margin-top:-10px">
    <li>
	<div class="instruction-box">
	    <h4>Page Editing instructions</h4>
	    <p><strong style="font-size:12px;">Page Settings : </strong><br />This is where you can adjust page privacy and the page type and template settings.</p>
	    <p><strong style="font-size:12px;">Page Main Content :  </strong><br />This is the main content to appear on the page - edit as needed.</p>
	</div>
    </li>
</ul>
<ul style="float:left;width:70%;clear:left" class="form">
<li>
<a class="accord-title button"><span class="icons downarrow"></span>Page Main Content</a>
<div class="accord-body">
    <div class="accord-container">
        <label>Main heading</label>
        <input type="text" name="page_heading" value="<?php echo sketch( "page_heading" ); ?>">
        <label>Lead Paragraph</label>
        <textarea name="leadparagraph" style="height:100px;width:95%"><?php echo sketch( "leadparagraph" ); ?></textarea>
        <label>Main content</label>
        <textarea name="edit" class="doTiny:true tinySettings:1" id="edit" style="height:300px;width:95%"><?php echo htmlentities(sketch( "edit" )); ?></textarea>
    </div>
</div>
</li>
</ul>