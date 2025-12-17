<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);

$sql_master = "SELECT * FROM activity_master WHERE id='".$docid."'";
$res_master = mysqli_query($link1,$sql_master);
$row_master = mysqli_fetch_assoc($res_master);
?>
<table class="table table-bordered" width="100%">
    <tbody>
      <tr>
        <td width="35%"><strong>Ref No.</strong></td>
        <td width="65%"><?=$row_master['ref_no']?></td>
      </tr>
      <tr>
        <td width="35%"><strong>User Name</strong></td>
        <td width="65%"><?=getAdminDetails($row_master['user_id'],"name",$link1)." (".$row_master['user_id'].")"?></td>
      </tr>
      <tr>
        <td width="35%"><strong>Activity Type</strong></td>
        <td width="65%"><?=$row_master['activity_type']?></td>
      </tr>
      <tr>
        <td><strong>Party/Customer Name</strong></td>
        <td><?=$row_master['party_name']?></td>
      </tr>
      <tr>
        <td><strong>Contact No.</strong></td>
        <td><?=$row_master['party_contact']?></td>
      </tr>
      <tr>
        <td><strong>Intial Remark</strong></td>
        <td><?=$row_master['intial_remark']?></td>
      </tr>
      <tr>
        <td width="35%"><strong>Status <span class="red_small">*</span></strong></td>
        <td width="65%">
        	<select name="status" class="form-control required" id="status" required>
            	<option value="">--Please Select--</option>
                <option value="In-Progress">In-Progress</option>
                <option value="Complete">Complete</option>
            </select></td>
      </tr>
      <tr>
        <td><strong>Remark</strong></td>
        <td><textarea name="remark" id="remark" class="form-control addressfield" style="resize:vertical"></textarea></td>
      </tr>
      <tr>
        <td><strong>Attachment</strong></td>
        <td><input type="file" class="form-control" name="attachment" id="attachment" accept="image/*,.pdf,.xlsx,.xls"/><input name="latlong2" id="latlong2" type="hidden"/><input name="ref_no" id="ref_no" type="hidden" value="<?=base64_encode($row_master['ref_no']);?>"/></td>
      </tr>
    </tbody>
</table>
<script>
var ltlg = document.getElementById("latlong2");

function getLocation2() {
  if (navigator.geolocation) {
	navigator.geolocation.getCurrentPosition(showPosition2);
  } else { 
	ltlg.value = "Not Found,Not Found";
  }
}

function showPosition2(position) {
  ltlg.value = btoa(position.coords.latitude + "," + position.coords.longitude);
}
function checkLocation3(){
	if(document.getElementById("latlong2").value){
		document.getElementById("err_msg").innerHTML ="";
		return true;
	}else{
		//document.getElementById("err_msg").innerHTML ="<span class='text-danger'>Please allow location to mark your activity. It is mandatory</span>";
		//return false;
	}
}
</script>