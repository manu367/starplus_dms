  <!-- Trigger the modal with a button -->
  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal2" data-backdrop="true" <?php if($_REQUEST['po_from']==""){ ?>disabled<?php } ?> onClick="startValidation();">Add New Customer</button>
  <!-- Modal -->
  <div class="modal fade" id="myModal2" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">×</button>
          <h4 class="modal-title">Add New Customer</h4>
        </div>
        <div class="modal-body">
          <form  name="form1" id="form1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">State <span class="red_small">*</span></label>
              <div class="col-md-6">
                <select name='locationstate' id='locationstate' class='form-control required' onchange='get_citydiv();' required><option value=''>--Please Select--</option>
				 <?php 
                 $state_query="select distinct(state) from state_master where 1 order by state";
                 $state_res=mysqli_query($link1,$state_query);
                 while($row_res = mysqli_fetch_array($state_res)){
                 ?>
                    <option value="<?=$row_res['state']?>"><?php echo $row_res['state'];?></option>
	             <?php }?>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">City <span class="red_small">*</span></label>
              <div class="col-md-6" id="citydiv">
               <select name="locationcity" id="locationcity" class="form-control required" required>
               <option value=''>--Please Select-</option>
               </select>  
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Customer Name <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input name="customer_name" type="text" class="form-control required" required id="customer_name">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Contact Number</label>
              <div class="col-md-6">
              <input name="phone" type="text" class="digits form-control required" required  id="phone" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();">
              </div>
            </div>
          </div>		 
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Email <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input name="email" type="email" class="email required form-control" id="email" required onBlur="return checkEmail(this.value,'email');">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Pincode <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input type="text" name="pincode" maxlength="6"  required class="digits form-control" onBlur="return pincodeV(this);" onKeyPress="return onlyNumbers(this.value);" id="pincode">
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">GSTIN</label>
              <div class="col-md-6">
                 <input type="text" name="gstin" id="gstin" class="alphanumeric form-control" minlength="15" maxlength="15">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">&nbsp;</label>
              <div class="col-md-6">
                
              </div>
            </div>
          </div>
          <div class="form-group">
           <div class="col-md-12"><label class="col-md-3 control-label">Address <span class="red_small">*</span></label>
              <div class="col-md-9">
                <textarea name="comm_address" id="comm_address" class="form-control required" required onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"></textarea>
              </div>
            </div>
           
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Save" title="" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>
              <input type="hidden" name="mappwith" id="mappwith" value="<?=$_REQUEST['po_from']?>"/>
            </div>
          </div>
    </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>