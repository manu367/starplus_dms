<?php
require_once("../config/config.php");
$docid = $_REQUEST['doc_id'];
$po_sql = "SELECT * FROM delivery_address_master WHERE id='".$docid."'";
$po_res = mysqli_query($link1,$po_sql);
$row1 = mysqli_fetch_assoc($po_res);
?>
<div class="panel panel-success table-responsive">
  <div class="panel-heading">Location Ship To Details</div>
  <div class="panel-body">
  	<div class="form-group">
        <div class="col-sm-6"><strong>Main Location</strong></div>
        <div class="col-sm-6"><?=$row1["location_code"]?></div>
    </div>
    <div class="form-group">
        <div class="col-sm-6"><strong>Party Name <span class="red_small">*</span></strong></div>
        <div class="col-sm-6"><input name="party_name" type="text" class="form-control required" required id="party_name" value="<?=$row1["party_name"]?>" disabled="disabled"></div>
    </div>
    <div class="form-group">
        <div class="col-sm-6"><strong>Ship To/ Address <span class="red_small">*</span></strong></div>
        <div class="col-sm-6"><textarea name="ship_to" id="ship_to" class="form-control addressfield required" required style="resize:vertical" disabled="disabled"><?=$row1["address"]?></textarea></div>
    </div>
    <div class="form-group">
        <div class="col-sm-6"><strong>State <span class="red_small">*</span></strong></div>
        <div class="col-sm-6">
<select name='locationstate' id='locationstate' class='form-control required' onchange='get_citydiv();' required disabled="disabled">
<option value=''>--Select State--</option>
<?php
$state_query = "select distinct(state) from state_master where 1 order by state";
$state_res = mysqli_query($link1, $state_query);
while ($row_res = mysqli_fetch_array($state_res)) {
?>
<option value="<?=$row_res['state']?>"<?php if($row_res['state']==$row1['state']){ echo "selected";}?>><?php echo $row_res['state'];?></option>
<?php }?>
</select></div>
    </div>
    <div class="form-group">
        <div class="col-sm-6"><strong>City <span class="red_small">*</span></strong></div>
        <div class="col-sm-6" id="citydiv">
        <select  name='locationcity' id='locationcity' class='form-control required' required disabled="disabled">
                	<option value=''>--Please Select--</option>
    				<?php
                    $city_query = "SELECT distinct city FROM district_master where state='".$row1['state']."' order by city";
					$city_res = mysqli_query($link1, $city_query);
					while ($row_city = mysqli_fetch_array($city_res)) {
					?>
					<option value="<?=$row_city['city']?>"<?php if($row_city['city']==$row1['city']){ echo "selected";}?>><?php echo $row_city['city'];?></option>
					<?php
                    }
					?>
    				<option value='Others'<?php if($sel_result['city']=="Others"){ echo "selected";}?>>Others</option>
                </select></div>
    </div>
    <div class="form-group">
        <div class="col-sm-6"><strong>Pincode <span class="red_small">*</span></strong></div>
        <div class="col-sm-6"><input type="text" name="pincode" minlength="6" maxlength="6" required class="digits form-control" id="pincode" value="<?=$row1["pincode"]?>"></div>
    </div>
    <div class="form-group">
        <div class="col-sm-6"><strong>Landmark</strong></div>
        <div class="col-sm-6"><input type="text" name="landmark" id="landmark" class="form-control addressfield" value="<?=$row1["landmark"]?>"></div>
    </div>
    <div class="form-group">
        <div class="col-sm-6"><strong>GSTIN</strong></div>
        <div class="col-sm-6"><input type="text" name="gst_no" id="gst_no" class="form-control alphanumeric" minlength="15" maxlength="15" value="<?=$row1["gstin"]?>"></div>
    </div>
    <div class="form-group">
        <div class="col-sm-6"><strong>Contact Person <span class="red_small">*</span></strong></div>
        <div class="col-sm-6"><input name="contact_person" type="text" class="form-control required" required id="contact_person" value="<?=$row1["contact_person"]?>"></div>
    </div>
    <div class="form-group">
        <div class="col-sm-6"><strong>Contact No. <span class="red_small">*</span></strong></div>
        <div class="col-sm-6"><input name="contact_no" type="text" class="digits form-control" id="contact_no" required minlength="10" maxlength="10" value="<?=$row1["contact_no"]?>"></div>
    </div>
    <div class="form-group">
        <div class="col-sm-6"><strong>Email <span class="red_small">*</span></strong></div>
        <div class="col-sm-6"><input name="email" type="email" class="email required form-control" id="email" required value="<?=$row1["email_id"]?>"></div>
    </div>
    <div class="form-group">
        <div class="col-sm-6"><strong>Status <span class="red_small">*</span></strong></div>
        <div class="col-sm-6">
        	<select name="status" id="status" class="form-control">
                <option value="Active"<?php if($row1['status']=="Active"){ echo "selected";}?>>Active</option>
                <option value="Deactive"<?php if($row1['status']=="Deactive"){ echo "selected";}?>>De-Active</option>
           	</select><input name="ref_no" id="ref_no" type="hidden" value="<?=base64_encode($docid);?>"/>
    	</div>
    </div> 
  </div><!--close panel body-->
</div><!--close panel-->