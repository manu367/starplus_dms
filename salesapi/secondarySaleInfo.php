<?php
require_once("dbconnect_cansaledms.php");
$inv_no = base64_decode($_REQUEST['id']);
$inv_date = base64_decode($_REQUEST['docdate']);
$from_location = base64_decode($_REQUEST['fcode']);
$to_location = base64_decode($_REQUEST['tcode']);

$sql_data = "SELECT prod_code,prod_name,serial_no1 FROM sale_uploader WHERE sale_type='SECONDARY' AND from_location='".$from_location."' AND to_location='".$to_location."' AND doc_no='".$inv_no."' AND doc_date='".$inv_date."'";
$res_data = mysqli_query($link1,$sql_data);
?>
<table class="table table-bordered table-responsive" width="100%" id="itemsTable3">
    <thead>
    	<tr class="<?=$tableheadcolor?>">
    		<th width="5%">S.No.</th>
            <th width="30%">Serial No.</th>
            <th width="25%">Product Code</th>
            <th width="40%">Product Name</th>
        </tr>
    </thead>
    <tbody>
      <?php
	  $i=1;
	  while($row_data = mysqli_fetch_assoc($res_data)){
	  ?>	
      <tr>
        <td><?=$i?></td>
        <td><?=$row_data['serial_no1']?></td>
        <td><?=$row_data['prod_code']?></td>
        <td><?=$row_data['prod_name']?></td>
      </tr>
      <?php
	  $i++; 
	  }
	  ?>
    </tbody>
</table>