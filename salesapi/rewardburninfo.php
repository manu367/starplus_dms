<?php
require_once("dbconnect_cansaledms.php");
$docid=base64_decode($_REQUEST['id']);

$sql_data = "SELECT * FROM reward_redemption_data WHERE system_ref_no='".$docid."'";
$res_data = mysqli_query($link1,$sql_data);

?>
<table class="table table-bordered" width="100%" id="itemsTable3">
    <thead>
    	<tr class="<?=$tableheadcolor?>">
    		<th width="5%">S.No.</th>
            <th width="10%">Product</th>
            <th width="10%">Qty</th>
            <th width="10%">Redeem Point</th>
        </tr>
    </thead>
    <tbody>
      <?php
	  $i=1;
	  while($row_data = mysqli_fetch_assoc($res_data)){
	  ?>	
      <tr>
        <td><?=$i?></td>
        <td><?=$row_data['partcode']?></td>
        <td><?=$row_data['qty']?></td>
        <td><?=$row_data['redeem_point']?></td>
      </tr>
      <?php
	  $i++; 
	  }
	  ?>
    </tbody>
</table>