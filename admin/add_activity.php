<?php
require_once("../config/config.php");
if($_SESSION['userid']=="admin" || $_SESSION['utype']=="1"){
	
}else{
	$teamself = getTeamMembers($_SESSION['userid'],$link1);
	if($teamself){
		$teamself = $teamself.",'".$_SESSION['userid']."'"; 
	}else{
		$teamself = "'".$_SESSION['userid']."'"; 
	}
}
if($_REQUEST['user_id']){
	$team = getTeamMembers($_REQUEST['user_id'],$link1);
	if($team){
		$team = $team.",'".$_REQUEST['user_id']."'"; 
	}else{
		$team = "'".$_REQUEST['user_id']."'"; 
	}
}
?>
<table class="table table-bordered" width="100%">
    <tbody>
      <tr>
        <td width="35%"><strong>User Name <span class="red_small">*</span></strong></td>
        <td width="65%">
        	<select name="username" class="form-control required selectpicker" id="username" required data-live-search="true">
            	<option value="">--Please select--</option>
                <?php
				if($_SESSION["userid"]=="admin" || $_SESSION['utype']=="1"){
					$sql = mysqli_query($link1, "SELECT username,name,oth_empid FROM admin_users where 1 AND status='Active' order by name");
				}else{
					$sql = mysqli_query($link1, "SELECT username,name,oth_empid FROM admin_users where 1 AND username IN (".$teamself.") AND status='Active' order by name");
				}
				while ($row = mysqli_fetch_assoc($sql)) {
				?>
				<option value="<?= $row['username']; ?>" <?php if ($_REQUEST['username'] == $row['username']) { echo "selected";}?>><?= $row['name']." | ".$row['username']." ".$row['oth_empid'];?></option>
				<?php } ?>
            </select></td>
      </tr>
      <tr>
        <td width="35%"><strong>Activity Type <span class="red_small">*</span></strong></td>
        <td width="65%">
        	<select name="activitytype" class="form-control required" id="activitytype" required>
                <option value="BTL Activity">BTL Activity</option>
            </select></td>
      </tr>
      <tr>
        <td><strong>Party/Customer Name <span class="red_small">*</span></strong></td>
        <td><input name="partyname" id="partyname" class="form-control required addressfield" type="text" required/></td>
      </tr>
      <tr>
        <td><strong>Contact No. <span class="red_small">*</span></strong></td>
        <td><input name="contactno" id="contactno" class="form-control required digits" minlength="10" maxlength="12" type="text" required/></td>
      </tr>
		
		 <tr>
        <td width="35%"><strong>Activity Action <span class="red_small">*</span></strong></td>
        <td width="65%">
        	<select name="activityaction" class="form-control required" id="activityaction" required>
                <option value="Secondary Sale">Secondary Sale</option>
				 <option value="Primary Sale">Primary Sale</option>
				 <option value="Service Discussion">Service Discussion</option>
				 <option value="Other">Other</option>
            </select></td>
      </tr>
		
      <tr>
        <td><strong>Remark</strong></td>
        <td><textarea name="remark" id="remark" class="form-control addressfield" style="resize:vertical"></textarea></td>
      </tr>
      <tr>
        <td><strong>Attachment</strong></td>
        <td><input type="file" class="form-control" name="attachment" id="attachment" accept="image/*,.pdf,.xlsx,.xls"/><input name="latlong" id="latlong" type="hidden"/></td>
      </tr>
    </tbody>
</table>
<script>
var ltlg = document.getElementById("latlong");

function getLocation() {
  if (navigator.geolocation) {
	navigator.geolocation.getCurrentPosition(showPosition);
  } else { 
	ltlg.value = "Not Found,Not Found";
  }
}

function showPosition(position) {
  ltlg.value = btoa(position.coords.latitude + "," + position.coords.longitude);
}
function checkLocation2(){
	if(document.getElementById("latlong").value){
		document.getElementById("err_msg").innerHTML ="";
		return true;
	}else{
		//document.getElementById("err_msg").innerHTML ="<span class='text-danger'>Please allow location to mark your activity. It is mandatory</span>";
		//return false;
	}
}
</script>