<?php
require_once("../config/config.php");
$docid = $_REQUEST['pk'];
?>
<div class="row">
	<div class="col-sm-12 table-responsive">
    	<table  width="99%" id="myTable" class="table-striped table-bordered table-hover" align="center">
        	<thead>
            	<tr>
              		<th><a href="#" name="entity_id" title="asc" ></a>S.No</th>
                    <th data-class="expand"><a href="#" name="name" title="asc" ></a>Location Name</th>
                    <th><a href="#" name="name" title="asc" ></a>Location Type</th>
                    <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Product Code</th>
                    <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Product Name</th>
                    <th data-hide="phone,tablet"><a href="#" name="number" title="asc" class="not-sort"></a>Ok Qty</th>
                    <th data-hide="phone,tablet"><a href="#" name="number" title="asc" class="not-sort"></a>Damage Qty</th>
                    <th data-hide="phone,tablet"><a href="#" name="number" title="asc" class="not-sort"></a>Missing Qty</th>
                    <th data-hide="phone,tablet"><a href="#" name="number" title="asc" class="not-sort"></a>Total Qty</th>
                    <th data-hide="phone,tablet"><a href="#" name="number" title="asc" class="not-sort"></a>Current Price</th>
                    <th data-hide="phone,tablet"><a href="#" name="number" title="asc" class="not-sort"></a>Value</th>
            	</tr>
          	</thead>
          	<tbody>
            <?php
			$sno=0;
			$new_loc = "";
			$old_loc = "";
			$sql=mysqli_query($link1,"Select * from stock_status where asc_code='".$docid."'");
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
				  $new_loc = $row['asc_code'];
				  if($old_loc != $new_loc){
				  	$locdet=explode("~",getLocationDetails($row['asc_code'],"name,city,state,id_type",$link1));
				  }
	              $proddet=str_replace("~",",",getProductDetails($row['partcode'],"productname,productcolor",$link1));
				  $price = mysqli_fetch_assoc(mysqli_query($link1,"SELECT price FROM price_master where state='".$locdet[2]."' and location_type='".$locdet[3]."' and product_code='".$row['partcode']."' and status='active'"));
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td><?php echo $locdet[0].",".$locdet[1].",".$row['asc_code'];?></td>
              <td><?=getLocationType($locdet[3],$link1);?></td>
              <td><?php echo $row['partcode'];?></td>
              <td><?php echo $proddet;?></td>
              <td align="right"><?php echo $row['okqty'];?></td>
              <td align="right"><?php echo $row['broken'];?></td>
              <td align="right"><?php echo $row['missing'];?></td>
              <td align="right"><?php echo $total = $row['okqty']+$row['broken']+$row['missing'];?></td>
              <td align="right"><?=number_format($price["price"],'2')?></td>
              <td align="right"><?=number_format($price["price"]*$total,'2')?></td>
            </tr>
            <?php 
			$old_loc = $row['asc_code'];
			}
			?>
        	</tbody>
    	</table> 
	</div>
</div>