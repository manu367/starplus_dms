<?php
include("../config/config.php");
//$arrstatus = getFullStatus("",$link1);
$docid=($_REQUEST['rid']);
?>
<div class="panel-group">
    <div class="panel panel-success table-responsive">
        <div class="panel-heading"><i class="fa fa-rupee fa-lg"></i>&nbsp;&nbsp;Receive Payment Against Invoice</div>
         <div class="panel-body">
          	<div class="form-group">
                 <div class="col-md-3"><strong>Invoice Amount:</strong></div>
                 <div class="col-md-3"><?=$_REQUEST['invamt']?></div>
            </div>
            <div class="form-group">
                 <div class="col-md-12">
                 	<table class="table table-bordered" id="itemsTable1" width="100%" border="0" cellspacing="0" cellpadding="0">
                      <thead>
                      <tr class="<?=$tableheadcolor?>">
                        <th>Payment Mode</th>
                        <th>Receive Amount</th>
                        <th>Ref. No.</th>
                        <th>Remark</th>
                      </tr>
                      </thead>
                      <tbody>
                      <tr id='addr0'>
                        <td>
                        	<select name="payMode[0]" id="payMode[0]" class="form-control" required>
                                <option value="">Please Select</option>
                                <?php
									$sql_mod = "SELECT mode FROM payment_mode WHERE status = 'A' ORDER BY mode";
									$res_mod = mysqli_query($link1,$sql_mod) or die(mysqli_error($link1));
									while($row_mod = mysqli_fetch_array($res_mod)){
								?>
								<option value="<?=$row_mod['mode']?>"><?=$row_mod['mode']?></option>
								<?php 
									}
								?>
                            </select>
                        </td>
                        <td><input type="number" name="recAmount[0]" class="form-control" id="recAmount[0]" value="" onkeyup="checkReceiveAmt();" required/></td>
                        <td><input name="ref_no[0]" id="ref_no[0]" type="text" class="form-control" pattern="[0-9a-zA-Z )(_.\/-]*$"/></td>
                        <td><textarea name="remark[0]" class="form-control addressfield" id="remark[0]"></textarea></td>
                      </tr>
                      </tbody>
                      <tfoot id='productfooter' style="z-index:-9999;">
                      <tr class="0">
                        <td colspan="4" style="font-size:13px;"><a id="add_row" onclick="addNewRow();" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add More</a><input type="hidden" name="rowno" id="rowno" value="0"/></td>
                      </tr>
                    </tfoot>
                    </table>
                 </div>
            </div>
            <div class="form-group">
                 <div class="col-md-3"><strong>Total Receive Amount:</strong></div>
                 <div class="col-md-3"><input type="number" name="totRecAmount" class="form-control" id="totRecAmount" value="0.00" readonly="readonly"/></div>
            </div>
        </div><!--close panel body-->
    </div><!--close panel-->    
    </div><!--close panel group-->
