<?php
require_once("../config/config.php");
$docid = $_REQUEST['doc_id'];
$partycode = $_REQUEST['partycode'];
$po_sql = "SELECT * FROM sub_location_master WHERE id='".$docid."'";
$po_res = mysqli_query($link1,$po_sql);
$row1 = mysqli_fetch_assoc($po_res);
?>
<div class="panel panel-success table-responsive">
  <div class="panel-heading">Sub Location Details</div>
  <div class="panel-body">
  	<div class="form-group">
        <div class="col-sm-6"><strong>Main Location</strong></div>
        <div class="col-sm-6"><?=$row1["main_location"]?></div>
    </div>
    <div class="form-group">
        <div class="col-sm-6"><strong>Cost Centre <span class="red_small">*</span></strong></div>
        <div class="col-sm-6"><input type="text" name="cost_centre" class="required mastername form-control cp" id="cost_centre" value="<?=$row1["cost_center"]?>" readonly="readonly" required/></div>
    </div>
    <div class="form-group">
        <div class="col-sm-6"><strong>Sub Location <span class="red_small">*</span></strong></div>
        <div class="col-sm-6"><input type="text" name="sub_location" class="required mastername form-control cp" id="sub_location" value="<?=$row1["sub_location_name"]?>" required/></div>
    </div>
    <div class="form-group">
        <div class="col-sm-6"><strong>Segment<span class="red_small">*</span></strong></div>
        <div class="col-sm-6"><select name="segment" id="segment" class="form-control required" disabled="disabled" required>
            <option value="">--Please Select--</option>
            <?php
            $seg_sql = "SELECT * FROM segment_master WHERE status='A' ORDER BY segment";
            $seg_res = mysqli_query($link1,$seg_sql);
            while($seg_row = mysqli_fetch_array($seg_res)){
            ?>
            <option value="<?=$seg_row['segment']?>"<?php if($row1['sub_location_type']==$seg_row['segment']){ echo "selected";}?>><?php echo $seg_row['segment']?></option>
            <?php }?>
            </select></div>
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
        <div class="col-sm-6"><input name="email" type="email" class="email required form-control" id="email" required value="<?=$row1["contact_email"]?>"></div>
    </div>
    <div class="form-group">
        <div class="col-sm-6"><strong>Remark</strong></div>
        <div class="col-sm-6"><textarea name="remark" id="remark" class="form-control addressfield" style="resize:vertical"><?=$row1["remark"]?></textarea><input name="ref_no" id="ref_no" type="hidden" value="<?=base64_encode($docid);?>"/></div>
    </div>
    <div class="form-group">
        <div class="col-sm-6"><strong>Status <span class="red_small">*</span></strong></div>
        <div class="col-sm-6">
        	<select name="status" id="status" class="form-control">
                <option value="Active"<?php if($row1['status']=="Active"){ echo "selected";}?>>Active</option>
                <option value="Deactive"<?php if($row1['status']=="Deactive"){ echo "selected";}?>>De-Active</option>
           	</select>
    	</div>
    </div> 
  </div><!--close panel body-->
</div><!--close panel-->