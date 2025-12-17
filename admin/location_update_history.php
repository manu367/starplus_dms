<?php
require_once("../config/config.php");
$docid = $_REQUEST['pk'];
?>
<div class="row">
	<div class="col-sm-12 table-responsive">
    	<table  width="99%" id="myTable" class="table-striped table-bordered table-hover" align="center">
        	<thead>
            	<tr>
              		<th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
                    <th><a href="#" name="name" title="asc" ></a>Update On</th>
                    <th><a href="#" name="name" title="asc" ></a>Update By</th>
            	</tr>
          	</thead>
          	<tbody>
            <?php
			$sno=0;
			$sql=mysqli_query($link1,"SELECT userid,update_on FROM daily_activities WHERE ref_no LIKE '".$docid."' AND activity_type='LOCATION' order by id desc");
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
				  $username = mysqli_fetch_assoc(mysqli_query($link1, "SELECT name,oth_empid FROM admin_users WHERE username LIKE '".$row['userid']."'"));
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              
              <td><?php echo $row['update_on'];?></td>
              <td><?= $username['name']." | ".$row['userid']." | ".$username['oth_empid'];?></td>
            </tr>
            <?php }?>
        	</tbody>
    	</table> 
	</div>
</div>