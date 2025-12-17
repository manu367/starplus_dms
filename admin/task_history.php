<?php
require_once("../config/config.php");
$docid = $_REQUEST['pk'];
?>
<div class="row">
	<div class="col-sm-12 table-responsive">
    	<div class="panel panel-info table-responsive">
      <div class="panel-heading">Task Perform History</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
            <tr class="<?=$tableheadcolor?>">
                <th width="21%">Remark </th>
                <th width="15%">Status</th>
                <th width="15%">Update Date</th>
                <th width="31%">Address</th>
                <th width="18%">Image</th>
              </tr>
            </thead>
            <tbody>
            <?php
			$sql_taskhist = "SELECT * FROM save_task_report where id='".$docid."' order by updatedate"; 
			$res_taskhist = mysqli_query($link1,$sql_taskhist);
			if(mysqli_num_rows($res_taskhist)>0){
			while($row_taskhist = mysqli_fetch_assoc($res_taskhist)){
			?>
            <tr>
              <td><?=$row_taskhist['remark'];?></td>
              <td><?=$row_taskhist['status'];?></td>
              <td><?=$row_taskhist['updatedate'];?></td>
              <td><?=$row_taskhist['address'];?></td>
              <td><?php if ($row_taskhist['image_name'] != '') { ?><img src="../API/taskImages/<?=$row_taskhist['image_name']; ?>" alt="" id="image<?=$i?>" style="width: 100%;"/><?php }else{ echo "Not clicked";} ?></td>
              </tr>
            <?php
			}
			}else{
			  ?>
              <tr>
              <td colspan="5" align="center">No record found</td>
              </tr>
             <?php
			}
			 ?> 
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	</div>
</div>