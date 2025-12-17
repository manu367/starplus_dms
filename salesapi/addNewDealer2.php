<?php
require_once("dbconnect_cansaledms.php");
require_once("../includes/common_function.php");
require_once("../includes/globalvariables.php");
function isExist($link1, $fy, $ranstr){

	$indb = mysqli_num_rows(mysqli_query($link1,"SELECT id from document_counter where financial_year='".$fy."' and doc_code='".$ranstr."'"));
	if($indb > 0){
		return true;
	}else{
		return false;
	}
}
function generateRandomString($length,$fy,$link1){

	$x = true;
	while($x){
		$ranstr = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789"), 0, $length);
		$x = isExist($link1, $fy, $ranstr);
	}
	return $ranstr;
}
////// final submit form ////
@extract($_POST);
if($_POST['Submit']=='Save'){
}
?>

<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
<script src="../js/bootstrap-select.min.js"></script>
 <script>
	$(document).ready(function(){
        //$("#frm1").validate();
		$("#frm1").validate({
			submitHandler: function (form) {
				if(!this.wasSent){
					this.wasSent = true;
					$(':submit', form).val('Please wait...')
								  .attr('disabled', 'disabled')
								  .addClass('disabled');
					//spinner.show();		  
					form.submit();
				} else {
					return false;
				}
			}
		});
    });
 </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
 <script language="javascript" type="text/javascript">
  /////////// function to get state on the basis of circle
  $(document).ready(function(){
	$('#circle').change(function(){
	  var name=$('#circle').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{circle:name},
		success:function(data){
	    $('#statediv').html(data);
	    }
	  });
    });
/*	$('#frm1').on('submit',function(e){
		console.log('ffg');
		
	});*/
  });
 /////////// function to get city on the basis of state
 function get_citydiv(){
	  var name=$('#locationstate').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{state:name},
		success:function(data){
	    $('#citydiv').html(data);
	    }
	  });
   
 }
 /////////// function to get Parent Location on the basis of location Type
 function getParentLocation(){
	  var name=$('#locationtype').val();
	  var ptystate=$('#locationstate').val();
	  var splitval=name.split("~");
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{loctype:splitval[1],locstate:ptystate,loctypstr:splitval[0]},
		success:function(data){
	    $('#parentdiv').html(data);
		makeSelect();
	    }
	  });
   
 }
 function makeSelect(){
  $('.selectpicker').selectpicker({
	liveSearch: true,
	showSubtext: true
  });
 }
 /////////// function to get city on the basis of state
 function checkDupliDoccode(val){
	  var fyr=$('#prefixdocstr').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{fcyear:fyr,doccode:val},
		success:function(data){
	      //// if string found then alert
		  if(data>0){
			  alert("Duplicate document code");
			  $('#docstr').val('');
		  }
	    }
	  });
   
 }
  </script>
<body>
<div class="container-fluid">
  <div class="row content">
    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
      <h2 align="center"><i class="fa fa-bank"></i> Add New Location</h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Region/Circle <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                 <select name="circle" id="circle" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <option value="EAST">EAST</option>
                  <option value="NORTH">NORTH</option>
                  <option value="SOUTH">SOUTH</option>
                  <option value="WEST">WEST</option>
                </select>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Segment <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              	<select name="segment" id="segment" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <?php
					$seg_sql = "SELECT * FROM segment_master WHERE status='A' ORDER BY segment";
					$seg_res = mysqli_query($link1,$seg_sql);
					while($seg_row = mysqli_fetch_array($seg_res)){
					?>
                	<option value="<?=$seg_row['segment']?>"<?php if($_REQUEST['segment']==$seg_row['segment']){ echo "selected";}?>><?php echo $seg_row['segment']?></option>
                	<?php }?>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">State <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="statediv">
                 <select name="locationstate" id="locationstate" class="form-control required" required>
                  <option value=''>--Please Select--</option>
                
                </select>               
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">City <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="citydiv">
               <select name="locationcity" id="locationcity" class="form-control required" required>
               <option value=''>--Please Select-</option>
               </select>  
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Location Type  <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <select name="locationtype" id="locationtype" class="form-control required" required onChange="return getParentLocation(this.value);">
                  <option value="">--Please Select--</option>
                  <?php
				///// check only one id of HO is in system  
				$checkhoid=mysqli_num_rows(mysqli_query($link1,"select sno from asc_master where id_type='HO' or user_level='1'"));
				if($checkhoid>0){$typelist=" and locationtype!='HO'";}else{$typelist="";}
				$type_query="SELECT * FROM location_type where status='A' $typelist and seq_id =  '5' order by seq_id";
				$check_type=mysqli_query($link1,$type_query);
				while($br_type = mysqli_fetch_array($check_type)){
				?>
                <option value="<?=$br_type['locationtype']."~".$br_type['seq_id']?>"<?php if($_REQUEST[locationtype]==$br_type[locationtype]."~".$br_type['seq_id']){ echo "selected";}?>><?php echo $br_type['locationname']?></option>
                <?php }?>
                </select>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Organization Name <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
               <input type="text" name="locationname" id="locationname" required class="form-control required">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Contact Person <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input name="contact_person" type="text" class="form-control required" required id="contact_person">
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Contact Number<span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <input name="phone" type="text" class="digits form-control"  id="phone" required maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();">
              </div>
            </div>
          </div>		 
		  <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Email</label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input name="email" type="email" class="email form-control" id="email" onBlur="return checkEmail(this.value,'email');">
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Landline Number </label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <input name="landline" type="text" class="form-control" id="landline" maxlength="12" onKeyPress="return onlyNumbers(this.value);">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Area <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input type="text" name="landmark" id="landmark" class="form-control required" required>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Pincode <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input type="text" name="pincode" maxlength="6"  required class="digits form-control" onBlur="return pincodeV(this);" onKeyPress="return onlyNumbers(this.value);" id="pincode">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Service Tax No. </label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input name="st_no" type="text" class="form-control"  id="st_no">
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">LST / CST Number</label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <input type="text" name="cst_no" class="form-control"  id="cst_no">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">PAN Number</label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input name="pan_no" type="text" class="form-control" id="pan_no">
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">GST Number</label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <input type="text" name="gst_no" id="gst_no" class="form-control">
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><!--Proprietor Type <span class="red_small">*</span>--></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <!--<select name="proprietor" class="form-control required" required id="proprietor">
                   <option value="">--Please Select--</option>
                   <option value="OWNED" selected>OWNED</option>
                   <option value="PARTNERSHIP">PARTNERSHIP</option>
                   <option value="NOPAN">NO PAN NUMBER</option>
                </select>-->
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">TDS %</label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
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
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Bank Account Holder Name </label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input name="accountholder" type="text" class="form-control" id="accountholder">
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Bank Account No. </label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <input type="text" name="accountno" id="accountno" class="form-control" >
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Bank Name </label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input name="bankname" type="text" class="form-control" id="bankname">
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Bank City </label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <input type="text" name="bankcity" id="bankcity" class="form-control" >
              </div>
            </div>
          </div>
		  <!--<div class="form-group">
           <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">CC Day </label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <input type="text" name="cc_day" id="cc_day" class="form-control"   value="0" onKeyPress="return onlyNumbers(this.value);">
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">CC Limit</label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
              <input type="text" name="cc_limit" id="cc_limit" class="form-control" value="0.00" onKeyPress="return onlyFloat(this.value);">
              </div>
            </div>
          </div>-->
          <div class="form-group">
           <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Billing Address <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <textarea name="comm_address" id="comm_address" class="form-control required" required onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"></textarea>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Delivery/Shipping Address</label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
               <textarea name="dd_address" id="dd_address" class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"></textarea>
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><!--Unique Document Code <span class="red_small">*</span><br/><span style="font-size:11px;color:#FF0000">(Please enter a 3 character code)</span>--></label>
              <div class="col-md-3">
                <!--<input type="text" name="prefixdocstr" id="prefixdocstr" class="form-control" style="width:120px;" value="<?=$fy?>" readonly>-->
              </div>
              <div class="col-md-3">
                
              </div><!--<input type="text" name="docstr" id="docstr" class="required form-control" required style="width:95px;text-transform:uppercase" maxlength="3" minlength="3" onKeyUp="onlyCharcter(this.value,'docstr');" onBlur="checkDupliDoccode(this.value);">-->
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Under Distributor <span class="red_small">*</span></label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" id="parentdiv">
                 <select name="parentid" id="parentid" required class="form-control required">
                    <option value="">--Please Select--</option>
                    <option value="NONE">NONE</option>
                 </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">Remark </label>
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <textarea name="remark" id="remark" class="form-control addressfield" style="resize:vertical"></textarea>
              </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><label class="col-xs-12 col-sm-12 col-md-12 col-lg-12">IFSC Code </label>
               <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
               <input name="ifsccode" type="text" class="digits form-control" id="ifsccode">
              </div>
            </div>
          </div>
		  
		  <br><br>
		  
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Save" title="" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>
              <?php /*?><input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='asp_details.php?<?=$pagenav?>'"><?php */?>
            </div>
          </div>
    </form>
      </div><!--End form group-->
    </div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->
</body>
</html>