<?php
require_once("../config/config.php");
$docid = $_REQUEST['pk'];
$indx = $_REQUEST['indx_no'];
$valamt = $_REQUEST['val_amt'];
$valqty = $_REQUEST['val_qty'];
$prd_Dtl_val = $_REQUEST['prd_Dtl'];

$productname = mysqli_fetch_assoc(mysqli_query($link1, "select productname from product_master where productcode='" . $docid . "'"));

?>

  <div class="row">
	<div class="col-sm-12 table-responsive">
    	<div class="panel panel-info table-responsive">
      <div class="panel-heading heading1">Available Scheme For Selected Model <strong><?=$productname["productname"]." - ".$docid?></strong></div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
            <tr class="<?=$tableheadcolor?>">
                <th width="5%">Select </th>
                <th width="13%">Scheme No.</th>
                <th width="15%">Scheme Period</th>
                <th width="25%">Scheme Name</th>
                <th width="12%">Applicable On</th>
                <th width="11%">Min. Criteria</th>
                <th width="10%">Scheme Given</th>
                <th width="9%">Offer</th>
              </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$sql_schm = "SELECT * FROM scheme_master where productcode='".$docid."' and from_date <= '".$today."' and to_date >= '".$today."' and status='Active'"; 
			$res_schm = mysqli_query($link1,$sql_schm);
			if(mysqli_num_rows($res_schm)>0){
			while($row_schm = mysqli_fetch_assoc($res_schm)){
				/////check scheme applicable
				if(($row_schm["scheme_based_type"]=="Total Qty" && $valqty >= $row_schm["scheme_based_on"]) || ($row_schm["scheme_based_type"]=="Total Amount" && $valamt >= $row_schm["scheme_based_on"])){
			?>
            <tr>
              <td align="center">
              	<input name="scheme_applicable" id="schm<?=$indx?>" type="radio" value="<?=$row_schm["scheme_code"]?>" <?php  if($_REQUEST['scheme_applicable'] == $row_schm['scheme_code']){ echo "checked"; } ?> />
              </td>
              <td><?=$row_schm["scheme_code"]?></td>
              <td><?=$row_schm['from_date']." - ".$row_schm['to_date'];?></td>
              <td><?=$row_schm['scheme_name'];?></td>
              <td><?=$row_schm['scheme_based_type'];?></td>
              <td align="right"><?=$row_schm['scheme_based_on'];?></td>
              <td><?=$row_schm['scheme_given_type'];?></td>
              <td align="right"><?=$row_schm['scheme_given'];?></td>
              </tr>
            <?php
			$i++;
				}
			}
			}else{
			  ?>
              <tr>
              <td colspan="8" align="center">No scheme found</td>
              </tr>
             <?php
			}
			 ?>
             <input type="hidden" name="valAmt" id="valAmt" value="<?=$valamt?>" />
             <input type="hidden" name="valINDNo" id="valINDNo" value="<?=$indx?>" />
             <input type="hidden" name="valPrd" id="valPrd" value="<?=$docid?>" />
             <input type="hidden" name="prdDtlVal" id="prdDtlVal" value="<?=$prd_Dtl_val?>" />
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	</div>
  </div>
  
