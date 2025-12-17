<?php
require_once("../config/config.php");
$docid=base64_decode($_REQUEST['id']);
?> 
      <h4 align="center">Lead ID: <?php echo $docid;?></h4>
   <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">   
   <div class="panel-group">
  <div class="panel panel-info table-responsive">
      <div class="panel-heading">Status Change Info</div>
      <div class="panel-body">
          <table class="table table-bordered" width="100%"> 
            <thead>
              <tr>
                <th width="20%">Party Name</th>
                <th width="30%">Status</th>
                <th width="20%">Type</th>
                <th width="30%">Update On</th>
              </tr>
            </thead>
            <tbody>
            <?php
			$res_poapp=mysqli_query($link1,"SELECT * FROM sf_status_history where trans_no='".$docid."' ORDER BY id ASC")or die("ERR1".mysqli_error($link1)); 
			while($row_poapp=mysqli_fetch_assoc($res_poapp)){
			?>
              <tr>
                <td><?php echo $row_poapp['party_id'];?></td>
                <td><?php echo get_status($row_poapp['status_id'],$link1);?></td>
                <td><?php echo $row_poapp['trans_type']?></td>
                <td><?php echo $row_poapp['updatedate']?></td>
              </tr>
              <?php }?>
            </tbody>
          </table>
          
      </div><!--close panel body-->
    </div><!--close panel-->
  </div><!--close panel group-->
  </form>