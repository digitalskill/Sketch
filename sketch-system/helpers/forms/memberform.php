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
    <label>First name</label>
    <input type="text" class="required" value="<?php echo sketch("firstname");?>" name="firstname">
    <label>Last name</label>
    <input type="text" class="required" value="<?php echo sketch("lastname");?>" name="lastname">
    <label>Nickname</label>
    <input type="text" class="required" value="<?php echo sketch("nickname");?>" name="nickname">
    <label>Password</label>
    <input type="text" class="password" value="<?php echo secureit(sketch("password"),true);?>" name="password">
    <label>Email</label>
    <input type="text" class="required email" value="<?php echo sketch("email");?>" name="email">
    <label>Address</label>
    <input type="text" class="required" value="<?php echo sketch("address");?>" name="address">
    <label>Postcode</label>
    <input type="text" class="required integer" value="<?php echo sketch("postcode");?>" name="postcode">
    <label>City</label>
    <input type="text" class="required" value="<?php echo sketch("city");?>" name="city">
    <label>Country</label>
    <input type="text" class="required" value="<?php echo sketch("country");?>" name="country">
  </div>
  </div>
    </li>
</ul>