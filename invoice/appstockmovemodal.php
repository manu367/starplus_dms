<?php
require_once("../config/config.php");
/// decode id
$doc_id = base64_decode($_REQUEST['doc_id']);
/// get doc data
$sql_m = "SELECT * FROM stock_movement_master WHERE doc_no = '".$doc_id."'";
$res_m = mysqli_query($link1, $sql_m);
$row = mysqli_fetch_assoc($res_m);
///// main location
$main_location = explode("~",getLocationDetails($row['main_location'],"name,city,state,addrs,pincode,email,phone,gstin_no",$link1));
/// move from party
$billfrom=getLocationDetails($row['from_location'],"name,city,state",$link1);
$explodevalf=explode("~",$billfrom);
if($explodevalf[0]){ $fromparty=$billfrom; }else{ $fromparty=getAnyDetails($row['from_location'],"sub_location_name","sub_location","sub_location_master",$link1);}
/// move to party
$billto=getLocationDetails($row['to_location'],"name,city,state",$link1);
$explodeval=explode("~",$billto);
if($explodeval[0]){ $toparty=$billto; }else{ $toparty=getAnyDetails($row['to_location'],"sub_location_name","sub_location","sub_location_master",$link1);}
?>
<table class="table" border="1" style="border: 1px solid #ddd;">
    <tbody>
        <tr>
            <td width="50%" colspan="2"><strong>Main Location:</strong>&nbsp;&nbsp;<?php echo $main_location[0]." , ".$main_location[1]." , ".$main_location[2];?><br/>
            <strong>Address:</strong>&nbsp;&nbsp;<?=$main_location[3]?><br/>
            <strong>State:</strong>&nbsp;&nbsp;<?=$main_location[2]?><br/>
            <strong>Pincode:</strong>&nbsp;&nbsp;<?=$main_location[4]?><br/>
            <strong>Email:</strong>&nbsp;&nbsp;<?=$main_location[5]?><br/>
            <strong>Contact No.:</strong>&nbsp;&nbsp;<?=$main_location[6]?><br/>
            <strong>GSTIN:</strong>&nbsp;&nbsp;<?=$main_location[7]?><br/></td>
            <td width="50%"  colspan="2"><strong>Document No.:</strong>&nbsp;&nbsp;<?php echo $row['doc_no'];?><br/>
            <strong>Document Date:</strong>&nbsp;&nbsp;<?php echo dt_format($row['entry_date']);?><br/>
            <strong class="text-danger">Move Type:</strong>&nbsp;&nbsp;<?php echo getStockTypeName($row['move_stocktype']);?><br/>
            <strong>Entry By:</strong>&nbsp;&nbsp;<?php echo getAnyDetails($row['entry_by'], "name","username","admin_users", $link1);?><br/>
            <strong>Status:</strong>&nbsp;&nbsp;<?php echo $row['status'];?><br/>
            <strong>Remark:</strong>&nbsp;&nbsp;<?php echo $row['entry_remark'];?><br/>
            <?php if($row['app_by']){?>
            <strong>Approval By:</strong>&nbsp;&nbsp;<?php echo getAnyDetails($row['app_by'], "name","username","admin_users", $link1);?><br/>
            <strong>Approval Date:</strong>&nbsp;&nbsp;<?php echo dt_format($row['app_date']);?><br/>
            <strong>Approval Remark:</strong>&nbsp;&nbsp;<?php echo $row['app_remark'];?><br/><?php }?></td>
</tr>
          <tr>
            <td width="50%" colspan="2"><strong>Move From:</strong>&nbsp;&nbsp;<?php echo str_replace("~",",",$fromparty);?><br/>
            <strong>Address:</strong>&nbsp;&nbsp;<?=$main_location[3]?><br/></td>
            <td width="50%" colspan="2"><strong>Move To:</strong>&nbsp;&nbsp;<?php echo str_replace("~",",",$toparty);?><br/>
            <strong>Address:</strong>&nbsp;&nbsp;<?=$main_location[3]?><br/></td>
          </tr>          
    </tbody>
</table>
<table class="table" border="1" style="border:1px solid #ddd;">
    <thead style="background:#f1f1f1;">
        <tr><td colspan="5"><strong>Item Information</strong></td></tr>
    </thead>
    <tbody>
        <tr class="<?=$tableheadcolor?>">
            <th style="text-align:center" width="5%">#</th>
            <th style="text-align:center" width="45%">Product</th>
            <th style="text-align:center" width="15%">Qty</th>
            <th style="text-align:center" width="15%">Price</th>
            <th style="text-align:center" width="20%">Value</th>
        </tr>
        <?php
            $i=1;
            $sum_qty=0;			
            $sum_subtotal = 0;
            $invdata_sql="SELECT * FROM stock_movement_data WHERE doc_no='".$row["doc_no"]."'";
            $invdata_res=mysqli_query($link1,$invdata_sql);
            while($invdata_row=mysqli_fetch_assoc($invdata_res)){                          
                $partdet = explode("~",getAnyDetails($invdata_row["partcode"],"productname,model_name","productcode","product_master",$link1));
        ?>
          <tr>
            <td><?=$i?></td>
            <td><?=$partdet[0].", ".$partdet[1].' ('.$invdata_row["partcode"].')';?></td>
            <td style="text-align:right"><?=$invdata_row['qty']?></td>
            <td style="text-align:right"><?=$invdata_row['price']?></td>
            <td style="text-align:right"><?=$invdata_row['value']?></td>
        </tr>
        <?php
            $sum_qty+=$invdata_row['qty'];
			$sum_subtotal+=$invdata_row['value'];
            $i++;
        }
        ?>
        <tr>
            
            <td align="right" colspan="2"><strong>Total</strong></td>
            <td style="text-align:right"><strong><?=$sum_qty?></strong></td>
            <td style="text-align:right">&nbsp;</td>
            <td style="text-align:right"><strong><?=$sum_subtotal?></strong></td>
        </tr>
    </tbody>
</table>
<table class="table" border="1" style="border:1px solid #ddd;">
    <thead style="background:#f1f1f1;">
        <tr><td colspan="4"><strong>Approval Action</strong><input type="hidden" name="refno" id="refno" value="<?=base64_encode($row["doc_no"])?>"/></td></tr>
    </thead>
    <tbody>
    	<tr>
            <td width="25%"><strong>Approval Status</strong></td>
            <td width="25%">
            	<select name="app_status" id="app_status" class="form-control required" required>
                	<option value="">--Please Select--</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Rejected</option>
            	</select>
            </td>
            <td width="25%"><strong>Approval Remark</strong></td>
            <td width="25%"><textarea name="app_remark" style="resize:vertical" class="form-control"></textarea></td>
        </tr>
    </tbody>
</table>