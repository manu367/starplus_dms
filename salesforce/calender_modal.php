<?php
require_once("../config/config.php");
$docid=$_REQUEST['id'];
//// assigned task details
$tskasn_sql="SELECT * FROM pjp_data WHERE id='".$docid."'";
$tskasn_res=mysqli_query($link1,$tskasn_sql);
$tskasn_row=mysqli_fetch_assoc($tskasn_res);
?>
   <div class="panel-group">
    <div class="panel panel-success table-responsive">
        <div class="panel-heading">Task Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Task id</label></td>
                <td width="30%"><?php echo $tskasn_row['document_no'];?></td>
                <td width="20%"><label class="control-label">Task Name</label></td>
                <td width="30%"><?php echo $tskasn_row['task'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Visit Area</label></td>
                <td><?php echo $tskasn_row['visit_area'];?></td>
                <td><label class="control-label">Plan Date</label></td>
                <td><?php echo $tskasn_row['plan_date'];?></td>
              </tr>
              <tr>
                <td colspan="4"><?php if($tskasn_row["task"]=="Dealer Visit"){ 
					$res_dev = mysqli_query($link1,"SELECT * FROM deviation_request WHERE pjp_id='".$tskasn_row["id"]."' ORDER BY id DESC");
					if(mysqli_num_rows($res_dev)>0){
						$row_dev = mysqli_fetch_assoc($res_dev);
						echo "<span style='color:red'>Your Deviation Request is ".$row_dev["app_status"]."</span>";	
					}
				}?>
                <input name="latlong" id="latlong" type="hidden"/><div align="center" id="err_msg"></div></td>
              </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
  </div><!--close panel group-->