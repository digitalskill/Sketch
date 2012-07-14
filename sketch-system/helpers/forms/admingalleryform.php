<ul style="float:right;width:28%;">
    <li>
        <div class="instruction-box">
	 <h4>Instructions for editing a gallery Item</h4>
        <p><strong style="font-size:12px;">Enter in the image heading : </strong><br />The image heading will be used as the title of the image</p>
        <p><strong style="font-size:12px;">Select the Gallery image : </strong><br />The gallery image will be displayed on the page.</p>
        <p><strong style="font-size:12px;">Fill in the fields on the form :  </strong><br />When happy with the content - click on Save</p>
        <p><strong style="font-size:12px;">Preview : </strong><br />Preview the new content<br />This is done by clicking on "preview" when it becomes available.
        </p>
        <p><strong style="font-size:12px;">Publish : </strong><br />Only click publish when happy with the image information.</p>
        <p><strong>Published Content previewing : </strong><br />
                Need to have others approve this page and view content before it becomes live?<br />
                Provide them with this link: <a href="<?php echo urlPath( sketch( "menu_guid" ) );?>?checking"><?php echo urlPath( sketch( "menu_guid" ) ); ?>?checking</a>.
        </p>
        </div>
    </li>
</ul>
<ul class="form accordian" style="float:left;clear:left;width:70%">
<li>
<a class="accord-title button"><span class="icons downarrow"></span>Page images</a>
<div class="accord-body">
    <div class="accord-container">
    	<label>Image Title</label>
        <input type="text" name="page_heading" value="<?php echo sketch("page_heading"); ?>" />
        <label>Page Images</label>
        <textarea class="doTiny:true tinySettings:1" name="edit" id="edit"><?php echo sketch( "edit" ); ?></textarea>
</div>
</div>
</li>
</ul>