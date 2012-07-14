<ul style="float:right;width:28%;margin-top:-10px">
    <li>
            <div class="instruction-box">
		<h4>Instructions for editing a Product</h4>
            <p><strong style="font-size:12px;">Enter in the Product name : </strong><br />Page headings are used when a summary of the page is displayed.</p>
            <p><strong style="font-size:12px;">Select the product summary : </strong><br />The summary is used when listing several products together</p>
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
<a class="accord-title button"><span class="icons downarrow"></span>Product Main Content</a>
<div class="accord-body">
    <div class="accord-container">
        <label>Main heading</label>
        <input type="text" name="page_heading" value="<?php echo sketch( "page_heading" ); ?>">
        <label>Lead Paragraph</label>
        <textarea name="leadparagraph" style="height:100px;width:95%"><?php echo sketch( "leadparagraph" ); ?></textarea>
        <label>Main content</label>
        <textarea name="edit" class="doTiny:true tinySettings:1" id="edit" style="height:300px;width:95%"><?php echo sketch( "edit" ); ?></textarea>
    </div>
</div>
<a class="accord-title button"><span class="icons downarrow"></span>Product Information</a>
<div class="accord-body">
    <div class="accord-container">
        <label>Product In Stock</label>
        <input type="text" name="product_stock" class="integer" value="<?php echo sketch( "product_stock" ); ?>">
        <label>Product Weight</label>
        <input type="text" name="product_weight" class="decimal" value="<?php echo sketch( "product_weight" ); ?>">
        <label>Product Price</label>
        <input type="text" name="product_price" class="decimal" value="<?php echo sketch( "product_price" ); ?>">
         <label>Product File</label>
        <select name="product_file" class="bgClass:'select_bg'">
          <option value="">None</option>
          <?php $allF = getFiles();
           foreach($allF as $key => $value){
             foreach($value as $k => $v){
          ?>
          <option value="<?php echo str_replace("/index.php","",urlPath($k)); ?>" <?php if ( sketch( "product_file" ) == str_replace("/index.php","",urlPath($k))) { ?>selected="selected"<?php } ?>><?php echo $k ; ?></option>
          <?php
             }
          } ?>
        </select>
        <label>Product Color (Comma seperated list: red,blue,green )</label>
        <input type="text" name="product_color" value="<?php echo sketch( "product_color" ); ?>">
        <label>Product Size (comma seperated list: xl,l,m,s)</label>
        <input type="text" name="product_size" value="<?php echo sketch( "product_size" ); ?>">
        <label>Product Code</label>
        <input type="text" name="product_code"  value="<?php echo sketch( "product_code" ); ?>">
        <label>Product SKU</label>
        <input type="text" name="product_sku"  value="<?php echo sketch( "product_sku" ); ?>">
</div></div>
</li>
</ul>