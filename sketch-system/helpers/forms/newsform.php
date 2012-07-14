<ul style="float:right;width:28%;margin-top:-10px;">
    <li>
        <div class="instruction-box">
	    <h4>Instructions for editing a page</h4>
        <p><strong style="font-size:12px;">Enter in the page heading : </strong><br />Page headings are used when a summary of the page is displayed.</p>
        <p><strong style="font-size:12px;">Enter the page summary : </strong><br />Page summaries are vital for news, newsletter or casestudy pages.<br />
           This information is used in RSS feeds or on pages that list summaries for pages.</p>
        <p><strong style="font-size:12px;">Fill in the fields on the form :  </strong><br />When happy with the content - click on Save</p>
        <p><strong style="font-size:12px;">Preview : </strong><br />Preview the new content<br />This is done by clicking on "preview" when it becomes available.
        </p>
        <p><strong style="font-size:12px;">Publish : </strong><br />Only click publish when happy with the content.</p>
        <p><strong>Published Content previewing : </strong><br />
                Need to have others approve this page and view content before it becomes live?<br />
                Provide them with this link: <a href="<?php echo urlPath( sketch( "menu_guid" ) );?>?checking"><?php echo urlPath( sketch( "menu_guid" ) ); ?>?checking</a>.
        </p>
        </div>
    </li>
</ul>
<ul class="form accordian" style="float:left;clear:left;width:70%">
<li>
<a class="accord-title button"><span class="icons downarrow"></span>Page Main Content</a>
<div class="accord-body">
    <div class="accord-container">
    <label>Main heading</label>
    <input type="text" name="page_heading" value="<?php echo sketch( "page_heading" ); ?>">
    <label>Main content</label>
    <textarea name="edit" class="doTiny:true tinySettings:1" id="edit" style="height:300px;width:97%"><?php echo htmlentities(sketch( "edit" )); ?></textarea>
</div>
</div>
</li>
</ul>