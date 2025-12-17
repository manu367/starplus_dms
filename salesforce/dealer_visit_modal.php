<?php
require_once("../config/config.php");
?>
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home"><i class='fa fa-users fa-lg'></i>&nbsp;Existing Dealer</a></li>
    <li><a data-toggle="tab" href="#menu1"><i class='fa fa-user-plus fa-lg'></i>&nbsp;New Dealer</a></li>
  </ul>
  <div class="tab-content">
    <div id="home" class="tab-pane fade in active">
    	<br/>
        <div class="row">
        <form  name="frm2"  id="frm2"  class="form-horizontal" action="" method="post">
    	<div class="form-group">
        	<div class="col-sm-12" style="float:none">
            	<label class="col-md-5 control-label">Dealer<span class="red_small">*</span></label>	  
				<div class="col-md-5" align="left">
			    	<select name="from_location" id="from_location" required class="form-control selectpicker required" data-live-search="true">
                        <option value="" selected="selected">Please Select </option>
                        <?php 
                        $sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y'";
                        $res_chl=mysqli_query($link1,$sql_chl);
                        while($result_chl=mysqli_fetch_array($res_chl)){
                              $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));
                              if($party_det[id_type]!='HO'){
                              ?>
                        <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if(isset($_REQUEST['from_location']) && $result_chl['location_id']==$_REQUEST['from_location'])echo "selected";?> >
                           <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                        </option>
                        <?php
                              }
                        }
                        ?>
                     </select>
          		</div>
          	</div>
		</div>
        <div class="form-group">
        	<div class="col-sm-12" style="float:none">
            	<label class="col-md-5 control-label">Remark</label>
				<div class="col-md-5" align="left">
			    	<textarea  name="remark" id="remark" class="form-control"></textarea>
          		</div>
          	</div>
		</div>
        <div class="form-group">
        	<div class="col-sm-12" id="errmsg2" align="center">
			   <input name="task_id" type="hidden" value="<?=$_REQUEST["taskid"]?>"/>
               <input name="latlong" type="hidden" value="<?=$_REQUEST["latlong"]?>"/>
            </div>
		</div>
        <div class="form-group">
        	<div class="col-sm-12" align="center">
               <input id='btnSave2' name='btnSave2' title="Save Details" type="submit" class="btn btn-primary" value="Submit"/>
               &nbsp;
               <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
		</div>
        </form>
        </div>
    </div>
    <div id="menu1" class="tab-pane fade">
    	<br/>
        <form  name="frm4" id="frm4"  class="form-horizontal" action="" method="post">
    	<div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Region/Circle <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="circle" id="circle" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <option value="EAST">EAST</option>
                  <option value="NORTH">NORTH</option>
                  <option value="SOUTH">SOUTH</option>
                  <option value="WEST">WEST</option>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label"> </label>
              <div class="col-md-6">
               
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">State <span class="red_small">*</span></label>
              <div class="col-md-6" id="statediv">
                 <select name="locationstate" id="locationstate" class="form-control required" required>
                  <option value=''>--Please Select--</option>
                
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
            <div class="col-md-6"><label class="col-md-6 control-label">Location Type  <span class="red_small">*</span></label>
              <div class="col-md-6">
                <select name="locationtype" id="locationtype" class="form-control required" required onChange="return getParentLocation(this.value);">
                  <option value="">--Please Select--</option>
                  <?php
				///// check only one id of HO is in system  
				$checkhoid=mysqli_num_rows(mysqli_query($link1,"select sno from asc_master where id_type='HO' or user_level='1'"));
				if($checkhoid>0){$typelist=" and locationtype!='HO'";}else{$typelist="";}
				$type_query="SELECT * FROM location_type where status='A' $typelist and seq_id >  '".$_SESSION['user_level']."' order by seq_id";
				$check_type=mysqli_query($link1,$type_query);
				while($br_type = mysqli_fetch_array($check_type)){
				?>
                <option value="<?=$br_type['locationtype']."~".$br_type['seq_id']?>"<?php if($_REQUEST[locationtype]==$br_type[locationtype]."~".$br_type['seq_id']){ echo "selected";}?>><?php echo $br_type['locationname']?></option>
                <?php }?>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Organization Name <span class="red_small">*</span></label>
              <div class="col-md-6">
               <input type="text" name="locationname" id="locationname" required class="form-control mastername required">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Contact Person <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input name="contact_person" type="text" class="form-control required" required id="contact_person"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Contact Number<span class="red_small">*</span></label>
              <div class="col-md-6">
              <input name="phone" type="text" class="digits form-control"  id="phone" required maxlength="12" minlength="10"/>
              </div>
            </div>
          </div>		 
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Email <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input name="email" type="email" class="email required form-control" id="email" required />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Landline Number </label>
              <div class="col-md-6">
              <input name="landline" type="text" class="form-control digits" id="landline" maxlength="15">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Landmark </label>
              <div class="col-md-6">
                <input type="text" name="landmark" id="landmark" class="form-control">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Pincode <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input type="text" name="pincode" maxlength="6" minlength="6" required class="digits form-control" id="pincode">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Service Tax No. </label>
              <div class="col-md-6">
                <input name="st_no" type="text" class="form-control alphanumeric"  id="st_no">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">LST / CST Number</label>
              <div class="col-md-6">
              <input type="text" name="cst_no" class="form-control alphanumeric" id="cst_no">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">PAN Number <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input name="pan_no" type="text" class="form-control required alphanumeric" minlength="10" maxlength="10" required id="pan_no">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">GST Number</label>
              <div class="col-md-6">
              <input type="text" name="gst_no" id="gst_no" class="form-control alphanumeric" minlength="15" maxlength="15">
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Proprietor Type <span class="red_small">*</span></label>
              <div class="col-md-6">
                <select name="proprietor" class="form-control required" required id="proprietor">
                   <option value="">--Please Select--</option>
                   <option value="OWNED">OWNED</option>
                   <option value="PARTNERSHIP">PARTNERSHIP</option>
                   <option value="NOPAN">NO PAN NUMBER</option>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">TDS %</label>
              <div class="col-md-6">
              <select name="tdsper" class="form-control" id="tdsper">
                   <option value="">--Please Select--</option>
                   <option value="1.00">1%</option>
                   <option value="2.00">2%</option>
                   <option value="10.00">10%</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Bank Account Holder Name </label>
              <div class="col-md-6">
                <input name="accountholder" type="text" class="form-control mastername" id="accountholder">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Bank Account No. </label>
              <div class="col-md-6">
              <input type="text" name="accountno" id="accountno" class="form-control alphanumeric" >
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Bank Name </label>
              <div class="col-md-6">
                <input name="bankname" type="text" class="form-control mastername" id="bankname">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Bank City </label>
              <div class="col-md-6">
              <input type="text" name="bankcity" id="bankcity" class="form-control">
              </div>
            </div>
          </div>
		  <!--<div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">CC Day </label>
              <div class="col-md-6">
                <input type="text" name="cc_day" id="cc_day" class="form-control"   value="0" onKeyPress="return onlyNumbers(this.value);">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">CC Limit</label>
              <div class="col-md-6">
              <input type="text" name="cc_limit" id="cc_limit" class="form-control" value="0.00" onKeyPress="return onlyFloat(this.value);">
              </div>
            </div>
          </div>-->
          <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Billing Address <span class="red_small">*</span></label>
              <div class="col-md-6">
                <textarea name="comm_address" id="comm_address" class="form-control required addressfield" required style="resize:vertical"></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Delivery/Shipping Address <span class="red_small">*</span></label>
              <div class="col-md-6">
               <textarea name="dd_address" id="dd_address" class="form-control required addressfield" required style="resize:vertical"></textarea>
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Unique Document Code <span class="red_small">*</span><br/><span style="font-size:11px;color:#FF0000">(Please enter a 3 character code)</span></label>
              <div class="col-md-3">
                <input type="text" name="prefixdocstr" id="prefixdocstr" class="form-control" style="width:120px;" value="<?=$fy?>" readonly>
              </div>
              <div class="col-md-3">
                <input type="text" name="docstr" id="docstr" class="required form-control character" required style="width:95px;text-transform:uppercase" maxlength="3" minlength="3" onBlur="checkDupliDoccode(this.value);">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Parent Location <span class="red_small">*</span></label>
              <div class="col-md-6" id="parentdiv">
                 <select name="parentid" id="parentid" required class="form-control required">
                    <option value="">--Please Select--</option>
                    <option value="NONE">NONE</option>
                 </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Remark </label>
              <div class="col-md-6">
                <textarea name="remark" id="remark" class="form-control addressfield" style="resize:vertical"></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">IFSC Code </label>
               <div class="col-md-6">
               <input name="ifsccode" type="text" class="alphanumeric form-control" id="ifsccode" maxlength="15">
              </div>
            </div>
          </div>
        <div class="form-group">
        	<div class="col-sm-12" style="float:none" align="center">
               <input name="task_id" type="hidden" value="<?=$_REQUEST["taskid"]?>"/>
               <input name="latlong" type="hidden" value="<?=$_REQUEST["latlong"]?>"/>
               <input id='btnSave4' name='btnSave4' title="Save Details" type="submit" class="btn btn-primary" value="Submit"/>&nbsp;
               <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
		</div>
        </form>
    </div>
  </div>