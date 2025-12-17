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
                    <th><a href="#" name="name" title="asc" ></a>Invoice To</th>
                    <th><a href="#" name="name" title="asc" ></a>Invoice From</th>
                    <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Invoice No.</th>
                    <th data-hide="phone,tablet"><a href="#" name="date" title="asc" class="not-sort"></a>Invoice Date</th>
                    <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>PO No.</th>
                    <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>
                    <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Print</th>
                    <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>View</th>
            	</tr>
          	</thead>
          	<tbody>
            <?php
			$sno=0;
			$sql=mysqli_query($link1,"Select * from billing_master where from_location ='".$docid."' order by id desc");
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td><?php echo str_replace("~",",",getLocationDetails($row['to_location'],"name,city",$link1));?></td>
              <td><?php echo str_replace("~",",",getLocationDetails($row['from_location'],"name,city",$link1));?></td>
              <td><?php echo $row['challan_no'];?></td>
              <td><?php echo $row['sale_date'];?></td>
              <td><?php echo $row['po_no'];?></td>
              <td <?php if($row['status']=="PFA"){ echo "class='red_small'";}?>><?php echo $row['status'];?></td>
              <td align="center"><a href='../print/print_invoice.php?rb=view&id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>' target="_blank"  title='Print Invoice'><i class="fa fa-print fa-lg" title="Print Invoice"></i></a><?php if($row['imei_attach']){ ?>  &nbsp;&nbsp;<a href='../print/print_imei.php?rb=view&id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>' target="_blank"  title='Print IMEI'><i class="fa fa-print fa-lg" title="Print IMEI"></i></a><?php }?></td>
              <td align="center"><a href='#' onClick="checkInvInfo('<?=$row['challan_no']?>');" title='Invoice Details'><i class="fa fa-eye fa-lg" title="Invoice Details"></i></a></td>
            </tr>
            <?php }?>
        	</tbody>
    	</table> 
	</div>
</div>