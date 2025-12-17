<?php
require_once("../config/config.php");
$docid = base64_decode($_REQUEST['doc_id']);
$po_sql = "SELECT * FROM purchase_order_master WHERE po_no='".$docid."'";
$po_res = mysqli_query($link1,$po_sql);
$row1 = mysqli_fetch_assoc($po_res);
///// approval data
$appdet = mysqli_fetch_assoc(mysqli_query($link1,"SELECT action_by,action_date FROM approval_activities WHERE ref_no='".$row1["po_no"]."'"));
?>
<div class="panel panel-success table-responsive">
  <div class="panel-heading">PO No. <?=$docid?></div>
  <div class="panel-body">
   <table class="table table-bordered" width="100%">
     <tbody>
        <tr>
          <td width="30%"><strong>PO From</strong></td>
          <td width="70%"><?php echo str_replace("~",",",getLocationDetails($row1['po_from'],"name,city,state,asc_code",$link1)); ?></td>
        </tr>
        <tr>
          <td><strong>PO To</strong></td>
          <td><?php echo str_replace("~",",",getLocationDetails($row1['po_to'],"name,city,state,asc_code",$link1)); ?></td>
        </tr>
        <tr>
          <td><strong>PO Move Location <span class="red_small">*</span></strong></td>
          <td>
          	<select name="po_allocate_to" id="po_allocate_to" class="form-control selectpicker required" required data-live-search="true">
                <option value="" selected="selected">Please Select </option>
                <?php
                $sql_chl="SELECT asc_code, name, city, state, id_type FROM asc_master WHERE id_type IN ('HO','BR') AND status='Active' AND asc_code!='".$row1['po_to']."' ORDER BY name";
                $res_chl=mysqli_query($link1,$sql_chl);
                while($result_chl=mysqli_fetch_array($res_chl)){
                ?>
                <option value="<?=$result_chl['asc_code']?>" <?php if($result_chl['asc_code']==$_REQUEST['po_allocate_to'])echo "selected";?>><?=$result_chl['name']." | ".$result_chl['city']." | ".$result_chl['state']." | ".$result_chl['asc_code']?></option>
                <?php
                }
                ?>
            </select></td>
        </tr>
        <tr>
          <td><strong>PO Date</strong></td>
          <td><?php echo $row1['requested_date']?></td>
        </tr>
        <tr>
          <td><strong>Status</strong></td>
          <td><?php echo $row1['status']?></td>
        </tr>
        <tr>
          <td><strong>Entry By</strong></td>
          <td><?php echo getAdminDetails($row1['create_by'],"name",$link1);?></td>
        </tr>
        <tr>
          <td><strong>Approved By</strong></td>
          <td><?php echo getAdminDetails($appdet['action_by'],"name",$link1)."  on ".$appdet['action_date'];?></td>
        </tr>
        
        <tr>
          <td><strong>Remark</strong></td>
          <td>
            <textarea name="poallocrmk" id="poallocrmk" class="form-control" style="resize:vertical"></textarea>
            <input name="ref_no" id="ref_no" type="hidden" value="<?=base64_encode($docid);?>"/>
            <input name="old_loc" id="old_loc" type="hidden" value="<?=base64_encode($row1['po_to']);?>"/>
          </td>
        </tr>
     </tbody>
   </table>
  </div><!--close panel body-->
</div><!--close panel-->