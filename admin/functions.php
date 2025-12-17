<?php
require_once("../config/config.php");
$url =  "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
$getid=base64_decode($_REQUEST['id']);
////// final submit form ////
@extract($_POST);
if($_POST){
	if($_POST['Submit']=='Update'){
   		///// Update Location details if needed
    	$sql="update asc_master set pwd='".$pswd."',name='".$locationname."',shop_name='".$locationname."',contact_person='".$contact_person."',landline='".$landline."',email='".$email."',phone='".$phone."',addrs='".$comm_address."',disp_addrs='".$dd_address."',landmark='".$landmark."',pincode='".$pincode."',vat_no='".$tin_no."',pan_no='".$pan_no."',cst_no='".$cst_no."',st_no='".$st_no."',proprietor_type='".$proprietor."',tdsper='".$tdsper."',account_holder='".$accountholder."',account_no='".$accountno."',bank_name='".$bankname."',bank_city='".$bankcity."',ifsc_code='".$ifsccode."',status='".$status."',update_date='".$datetime."', gstin_no='$_POST[gst_no]'  where  sno='".$refid."'";
    	mysqli_query($link1,$sql)or die("ER1".mysqli_error($link1));
   		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$loccode,"LOCATION","UPDATE",$ip,$link1,"");
		////// return message
		$msg="You have successfully updated details of location ".$loccode;
	
	}else if($_POST['Submit2']=='Update'){
		///// Update Location details if needed
    	$sql="update asc_master set update_date='".$datetime."', turnover ='".$turnover."'  , vision = '".$vision."'  where  sno='".$refid."'";
    	mysqli_query($link1,$sql)or die("ER1".mysqli_error($link1));
		dailyActivity($_SESSION['userid'],$loccode,"LOCATION","UPDATE",$ip,$link1,"");
		////// return message
		$msg="You have successfully updated other details of location ".$loccode;
	}else if($_POST['Submit3']=='Update'){
		///// Insert in document attach detail by picking each data row one by one
		foreach($document_name as $k=>$val){
			////////////////upload file
			$filename = "fileupload".$k;
			$file_name = $_FILES[$filename]["name"];
			//$file_basename = substr($file_name, 0, strripos($file_name, '.')); // get file extention
			$file_ext = substr($file_name, strripos($file_name, '.')); // get file name
			//////upload image
			if ($_FILES[$filename]["error"] > 0){
				$code=$_FILES[$filename]["error"];
			}
			else{
				// Rename file
				$newfilename = $loccode."_".$todayt.$now.$file_ext;
				move_uploaded_file($_FILES[$filename]["tmp_name"],"../doc_attach/location/".$newfilename);
				$file="../doc_attach/location/".$newfilename;
				//chmod ($file, 0755);
			}
			$sql_inst = "INSERT INTO document_attachment set ref_no='".$loccode."', ref_type='LOCATION MASTER',document_name='".ucwords($document_name[$k])."', document_path='".$file."', updatedate='".$datetime."'";
			$res_inst = mysqli_query($link1,$sql_inst);
		}
		dailyActivity($_SESSION['userid'],$loccode,"LOCATION","UPDATE",$ip,$link1,"");
		////// return message
		$msg="You have successfully attached the document of location ".$loccode;
	}else{
		////// return message
		$msg="Something went wrong. Please try again.";
	}    
	///// move to parent page
    //header("Location:asp_details.php?msg=".$msg."".$pagenav);
	//exit;
}
if($_GET['delete_prodesc_id']!=''){
	$flag = true;
        $sql2 ="SELECT document_path,ref_no FROM document_attachment where id= '".$_GET['delete_prodesc_id']."'";
        $res2 = mysqli_query($link1,$sql2) or die(mysqli_error());
		$image_res1=mysqli_fetch_assoc($res2); 
          if($image_res1["ref_no"]){
        unlink($image_res1['document_path']);
        $req_res1 = mysqli_query($link1, "delete from document_attachment where id='".$_GET['delete_prodesc_id']."'");
		//// check if query is not executed
		if (!$req_res1) {
			$flag = false;
			$err_msg = "Error Code1: " . mysqli_error($link1);
		} 
		///// check both master and data query are successfully executed
        if ($flag) {
            $msg = "Document is deleted";
        } else {
            $msg = "Document could not be deleted " . $err_msg . ". Please try again.";
        }
        ////// insert in activity table////
        $flag = dailyActivity($_SESSION['userid'], $image_res1["ref_no"], "DOCUMENT DELETE", "DELETE", $ip, $link1, "");
		  }
}
////// get details of selected location////
$res_locdet=mysqli_query($link1,"SELECT * FROM asc_master where sno='".$getid."'")or die(mysqli_error($link1));
$row_locdet=mysqli_fetch_array($res_locdet);
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
 <script>
	$(document).ready(function(){
        $("#frm1").validate();
		$("#frm2").validate();
		$("#frm3").validate();
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
	  //alert(name);
	  var splitval=name.split("~");
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{loctype:splitval[1]},
		success:function(data){
	    $('#parentdiv').html(data);
	    }
	  });
   
 }
 function HandleBrowseClick(ind){
    var fileinput = document.getElementById("browse"+ind);
    fileinput.click();
}
function Handlechange(ind){
	var fileinput = document.getElementById("browse"+ind);
	var textinput = document.getElementById("filename"+ind);
	textinput.value = fileinput.value;
}
///// add new row for document attachment
  $(document).ready(function() {
	$("#add_row3").click(function() {		
		var numi = document.getElementById('rowno3');
		var itm = "document_name[" + numi.value+"]";
		var preno=document.getElementById('rowno3').value;
		var num = (document.getElementById("rowno3").value -1)+2;
		numi.value = num;
		if ((document.getElementById(itm).value != "") || ($("#addr_doc" + numi.value + ":visible").length == 0)) {
			var r = '<tr id="addr_doc'+num+'"><td width="30%"><div style="display:inline-block;float:right"><input type="text" class="form-control entername required cp" name="document_name['+num+']"  id="document_name['+num+']" value="" style="width:270px;"></div><div style="display:inline-block;float:left;"><i class="fa fa-close fa-lg" onClick="fun_remove3('+num+');"></i></div></td><td width="70%"><div style="display:inline-block; float:left"><input type="file" id="browse'+num+'" name="fileupload'+num+'" style="display: none" onChange="Handlechange('+num+');" accept="image/*"/><input type="text" id="filename'+num+'" readonly="true" style="width:300px;" class="form-control"/></div><div style="display:inline-block; float:left">&nbsp;&nbsp;<input type="button" value="Click to upload attachment" id="fakeBrowse'+num+'" onclick="HandleBrowseClick('+num+');" class="btn btn-warning"/></div></td></tr>';
			$('#itemsTable3').append(r);
		}
	});
});
function fun_remove3(con){
	var c = document.getElementById('addr_doc' + con);
	c.parentNode.removeChild(c);
	con--;
	document.getElementById('rowno3').value = con;
}
function confirmDel(store){
var where_to= confirm("Are you sure to delete this document?");
if (where_to== true)
 {
  //alert(window.location.href)
  var url="<?php echo $url ?>";
  window.location=url+store;
}
else
 {
return false;
  }
}
  </script>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-bank"></i> View/Edit Location</h2>
      <h4 align="center">
          <?=$row_locdet['name']."  (".$row_locdet['asc_code'].")";?>
          <?php 
		  if(isset($_POST['Submit']) || isset($_POST['Submit2']) || isset($_POST['Submit3'])){
				if($_POST['Submit']=='Update' || $_POST['Submit2']=='Update' || $_POST['Submit3']=='Update'){ ?>
          <br/>
          <span style="color:#FF0000"><?php echo $msg; ?></span>
          <?php }
		  }
		   ?>
        </h4>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
      	<ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-id-card"></i> General Details</a></li>
            <li><a data-toggle="tab" href="#menu3"><i class="fa fa-upload"></i> Upload Document</a></li>
			<li><a data-toggle="tab" href="#menu2"><i class="fa fa-puzzle-piece"></i> Other Details</a></li>
            <!--<li><a data-toggle="tab" href="#menu4"><i class="fa fa-users"></i> Manpower</a></li>-->
          </ul>
           <div class="tab-content">
            <div id="home" class="tab-pane fade in active"><br/>
          <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Region/Circle <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="circle" id="circle" class="form-control required" required disabled>
                  <option value="">--Please Select--</option>
                  <option value="EAST"<?php if($row_locdet['circle']=="EAST"){echo "selected";}?>>EAST</option>
                  <option value="NORTH"<?php if($row_locdet['circle']=="NORTH"){echo "selected";}?>>NORTH</option>
                  <option value="SOUTH"<?php if($row_locdet['circle']=="SOUTH"){echo "selected";}?>>SOUTH</option>
                  <option value="WEST"<?php if($row_locdet['circle']=="WEST"){echo "selected";}?>>WEST</option>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label"></label>
              <div class="col-md-6">
              
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">State <span class="red_small">*</span></label>
              <div class="col-md-6" id="statediv">
                 <select name='locationstate' id='locationstate' class='form-control required' onchange='get_citydiv();' required disabled><option value=''>--Please Select--</option>
                 <?php 
				 $state_query="select distinct(state) from state_master where zone='".$row_locdet['circle']."' order by state";
				 $state_res=mysqli_query($link1,$state_query);
				 while($row_res = mysqli_fetch_array($state_res)){?>
                   <option value="<?=$row_res['state']?>"<?php if($row_locdet['state']==$row_res['state']){ echo "selected";}?>><?=$row_res['state']?></option>
	             <?php }?>
                 </select>              
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">City <span class="red_small">*</span></label>
              <div class="col-md-6" id="citydiv">
                 <select  name='locationcity' id='locationcity' class='form-control required' required disabled><option value=''>--Please Select--</option>
                 <?php 
			     $city_query="SELECT distinct city FROM district_master where state='".$row_locdet['state']."' order by city";
				 $city_res=mysqli_query($link1,$city_query);
				 while($row_city = mysqli_fetch_array($city_res)){?>
                   <option value="<?=$row_city['city']?>"<?php if($row_locdet['city']==$row_city['city']){ echo "selected";}?>><?=$row_city['city']?></option>
	             <?php }?>
                   <option value='Others'<?php if($row_locdet['city']=="Others"){ echo "selected";}?>>Others</option>
               </select>  
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Location Type <span class="red_small">*</span></label>
              <div class="col-md-6">
                <select name="locationtype" id="locationtype" class="form-control required" required disabled>
                  <option value="">--Please Select--</option>
                  <?php
				///// check only one id of HO is in system  
				//$checkhoid=mysqli_num_rows(mysqli_query($link1,"select sno from asc_master where id_type='HO' or user_level='1'"));
				//if($checkhoid>0){$typelist=" and locationtype!='HO'";}else{$typelist="";}
				$typelist="";
				$type_query="SELECT * FROM location_type where status='A' $typelist order by seq_id";
				$check_type=mysqli_query($link1,$type_query);
				while($br_type = mysqli_fetch_array($check_type)){
				?>
                <option value="<?=$br_type['locationtype']."~".$br_type['seq_id']?>"<?php if($row_locdet['user_level']==$br_type['seq_id']){ echo "selected";}?>><?php echo $br_type['locationname']?></option>
                <?php }?>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Location Name <span class="red_small">*</span></label>
              <div class="col-md-6">
               <input type="text" name="locationname" id="locationname" required class="form-control required" value="<?=$row_locdet['name']?>">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Contact Person <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input name="contact_person" type="text" class="form-control required" required id="contact_person" value="<?=$row_locdet['contact_person']?>">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Contact Number</label>
              <div class="col-md-6">
              <input name="phone" type="text" class="digits form-control"  id="phone" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" value="<?=$row_locdet['phone']?>">
              </div>
            </div>
          </div>		 
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Email <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input name="email" type="email" class="email required form-control" id="email" required onBlur="return checkEmail(this.value,'email');" value="<?=$row_locdet['email']?>">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Landline Number </label>
              <div class="col-md-6">
              <input name="landline" type="text" class="form-control" id="landline" maxlength="12" onKeyPress="return onlyNumbers(this.value);" value="<?=$row_locdet['landline']?>">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Landmark </label>
              <div class="col-md-6">
                <input type="text" name="landmark" id="landmark" class="form-control" value="<?=$row_locdet['landmark']?>">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Pincode <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input type="text" name="pincode" maxlength="6"  required class="digits form-control" onBlur="return pincodeV(this);" onKeyPress="return onlyNumbers(this.value);" id="pincode" value="<?=$row_locdet['pincode']?>">
              </div>
            </div>
          </div>
		  <div class="form-group" style="background-color:#CCFFFF">
            <div class="col-md-6"><label class="col-md-6 control-label">Service Tax No. </label>
              <div class="col-md-6">
                <input name="st_no" type="text" class="form-control"  id="st_no" value="<?=$row_locdet['st_no']?>">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">LST / CST Number</label>
              <div class="col-md-6">
              <input type="text" name="cst_no" class="form-control"  id="cst_no" value="<?=$row_locdet['cst_no']?>">
              </div>
            </div>
          </div>
		  <div class="form-group" style="background-color:#CCFFFF">
            <div class="col-md-6"><label class="col-md-6 control-label">PAN Number <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input name="pan_no" type="text" class="form-control required" required id="pan_no" value="<?=$row_locdet['pan_no']?>">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">GST Number</label>
              <div class="col-md-6">
              <input type="text" name="gst_no" id="gst_no" class="form-control" value="<?=$row_locdet['gstin_no']?>">
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Proprietor Type <span class="red_small">*</span></label>
              <div class="col-md-6">
                <select name="proprietor" class="form-control required" required id="proprietor">
                   <option value="">--Please Select--</option>
                   <option value="OWNED"<?php if($row_locdet['proprietor_type']=="OWNED"){echo "selected";}?>>OWNED</option>
                   <option value="PARTNERSHIP"<?php if($row_locdet['proprietor_type']=="PARTNERSHIP"){echo "selected";}?>>PARTNERSHIP</option>
                   <option value="NOPAN"<?php if($row_locdet['proprietor_type']=="NOPAN"){echo "selected";}?>>NO PAN NUMBER</option>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">TDS %</label>
              <div class="col-md-6">
              <select name="tdsper" class="form-control" id="tdsper">
                   <option value="">--Please Select--</option>
                   <option value="1.00"<?php if($row_locdet['tdsper']=="1.00"){echo "selected";}?>>1%</option>
                   <option value="2.00"<?php if($row_locdet['tdsper']=="2.00"){echo "selected";}?>>2%</option>
                   <option value="10.00"<?php if($row_locdet['tdsper']=="10.00"){echo "selected";}?>>10%</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group" style="background-color:#FFFFCC">
            <div class="col-md-6"><label class="col-md-6 control-label">Bank Account Holder Name </label>
              <div class="col-md-6">
                <input name="accountholder" type="text" class="form-control" id="accountholder" value="<?=$row_locdet['account_holder']?>">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Bank Account No. </label>
              <div class="col-md-6">
              <input type="text" name="accountno" id="accountno" class="form-control" value="<?=$row_locdet['account_no']?>">
              </div>
            </div>
          </div>
          <div class="form-group" style="background-color:#FFFFCC">
            <div class="col-md-6"><label class="col-md-6 control-label">Bank Name </label>
              <div class="col-md-6">
                <input name="bankname" type="text" class="form-control" id="bankname" value="<?=$row_locdet['bank_name']?>">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Bank City </label>
              <div class="col-md-6">
              <input type="text" name="bankcity" id="bankcity" class="form-control" value="<?=$row_locdet['bank_city']?>">
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6" style="background-color:#FFFFCC"><label class="col-md-6 control-label">IFSC </label>
              <div class="col-md-6">
              	<input name="ifsccode" type="text" class="form-control" id="ifsccode" value="<?=$row_locdet['ifsc_code']?>">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Status </label>
              <div class="col-md-6">
                <select name="status" id="status" class="form-control">
                  <option value="active"<?php if($row_locdet['status']=="active"){ echo "selected";}?>>Active</option>
                  <option value="deactive"<?php if($row_locdet['status']=="deactive"){ echo "selected";}?>>De-Active</option>
                </select>
              </div>
            </div>
          </div>		  
          
          <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Billing Address <span class="red_small">*</span></label>
              <div class="col-md-6">
                <textarea name="comm_address" id="comm_address" class="form-control required" required onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?=$row_locdet['addrs']?></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Delivery/Shipping Address <span class="red_small">*</span></label>
              <div class="col-md-6">
               <textarea name="dd_address" id="dd_address" class="form-control required" required onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?=$row_locdet['disp_addrs']?></textarea>
              </div>
            </div>
          </div>
		 <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Remark </label>
              <div class="col-md-6">
              		<textarea name="remark" id="remark" class="form-control" style="resize:vertical"><?=$row_locdet['remark']?></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Parent Location <span class="red_small">*</span></label>
              <div class="col-md-6" id="parentdiv">
                 <select name="parentid" id="parentid" required class="form-control required">
                    <option value="">--Please Select--</option>
					<?php
					$parent_query = "SELECT uid,name,city,state FROM asc_master where user_level= '$row_locdet[user_level] ' ";
    $parent_res = mysqli_query($link1, $parent_query);
	
    while ($row_parent = mysqli_fetch_array($parent_res)) {
	?>
        <option value="<?=$row_parent['uid']?>"<?php if($row_locdet['user_level']==$row_locdet['user_level']){ echo "selected";}?>><?php echo $row_parent['name']. "," . $row_parent['city'] . "," . $row_parent['state']?></option>
                <?php }?>
                 </select>
              </div>
            </div>
          </div>		  
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Update" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
              <input name="refid" id="refid" type="hidden" value="<?=$row_locdet['sno']?>"/>
              <input name="loccode" id="loccode" type="hidden" value="<?=$row_locdet['asc_code']?>"/>
              <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='asp_details.php?<?=$pagenav?>'">
            </div>
          </div>
    </form>
    </div>
            <div id="menu2" class="tab-pane fade"> <br/>
              <form  name="frm2" id="frm2" class="form-horizontal" action="" method="post">
              	<div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label">Turnover</label>
                      <div class="col-md-6">
                         <input type="text" name="turnover" class="form-control number" id="turnover" value="<?php if(!empty($row_locdet['turnover'])){ echo $row_locdet['turnover'];}?>"/>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label">Vision</label>
                      <div class="col-md-6">
                        <textarea name="vision" id="vision" class="form-control addressfield" onContextMenu="return false" style="resize:vertical;height:200px;"><?=$row_locdet['vision']?></textarea>
                      </div>
                    </div>
                  </div>
                   <div class="form-group">
           			<div class="col-md-12" align="center">
                    <input type="submit" class="btn <?=$btncolor?>" name="Submit2" id="save2" value="Update" title="" <?php if($_POST['Submit2']=='Update'){?>disabled<?php }?>>
                    <input name="refid" id="refid" type="hidden" value="<?=$row_locdet['sno']?>"/>
                    <input name="loccode" id="loccode" type="hidden" value="<?=$row_locdet['asc_code']?>"/>
                    <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='asp_details.php?<?=$pagenav?>'">
                    </div>
                   </div> 
              </form>
            </div>
            <div id="menu3" class="tab-pane fade"><br/>
            	<form  name="frm3" id="frm3" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                	<div class="col-sm-12">
                	<table class="table table-bordered" width="100%" id="itemsTable3">
                    	<thead>
                        	<tr class="<?=$tableheadcolor?>" >
                            	<th width="30%">Document Name</th>
                            	<th width="70%">Attachment</th>
                        	</tr>
                    	</thead>
                    	<tbody>
                        	<tr id="addr_doc0">
                        		<td><input type="text" class="form-control entername required cp" name="document_name[0]"  id="document_name[0]" value=""></td>
                            	<td>
                                	<div style="display:inline-block; float:left">
                                    <input type="file" class="required" id="browse0" name="fileupload0" style="display: none" onChange="Handlechange(0);" accept=".xlsx,.xls,image/*,.doc, .docx,.ppt, .pptx,.txt,.pdf"/>
                                    <input type="text" id="filename0" readonly style="width:300px;" class="form-control required"/>
                                    </div><div style="display:inline-block; float:left">&nbsp;&nbsp;
                                    <input type="button" value="Click to upload attachment" id="fakeBrowse0" onClick="HandleBrowseClick(0);" class="btn btn-warning"/>
                            		</div>
                            	</td>
                        	</tr>
                    	</tbody>
                	</table>   
                	</div>
                </div>
                <div class="form-group">
           			<div class="col-sm-4" style="display:inline-block; float:left">
           			<a id="add_row3" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add More Attachment</a><input type="hidden" name="rowno3" id="rowno3" value="0"/></div>
            		<div class="col-md-8" style="display:inline-block; float:right" align="left">
                    <input type="submit" class="btn <?=$btncolor?>" name="Submit3" id="save3" value="Update" title="" <?php if($_POST['Submit3']=='Update'){?>disabled<?php }?>>
                    <input name="refid" id="refid" type="hidden" value="<?=$row_locdet['sno']?>"/>
                    <input name="loccode" id="loccode" type="hidden" value="<?=$row_locdet['asc_code']?>"/>
                    <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='asp_details.php?<?=$pagenav?>'">
            		</div>
          		</div>
              	</form>
                <form id="frm2" name="frm2" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
               <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
            <thead>
              <tr class="<?=$tableheadcolor?>" >
                  <th style="font-size:13px;" colspan="4">Uploaded Document</th>                
                </tr>
            </thead>
            <tbody>
              <?php
			  	  $res4 = mysqli_query($link1,"select * from document_attachment where ref_no='".$row_locdet['asc_code']."' order by document_name");
                  while ($pro_desc_img=  mysqli_fetch_assoc($res4)){ 
                     
                  if($pro_desc_img['document_path'] !="") {  
              ?>              
              <tr> 
              <td class="col-md-2" align="left"><?=$pro_desc_img['document_name'];?></td>                                   
              <td class="col-md-6" align="center"><?php /*?><img src="<?=$pro_desc_img['document_path'];?>" alt="" width="100" height="200" class="img-responsive" /><?php */?><a href="<?=$pro_desc_img['document_path'];?>" target="_blank"><i class="fa fa-download fa-lg" title="view document"></i></a></td>
              <td class="col-md-2" align="center"><a onClick="confirmDel('&delete_prodesc_id=<?=$pro_desc_img['id']?>')" href="#" title='delete'><i class="fa fa-trash fa-lg" title="delete document"></i></a></td>                  
              </tr>
                  <?php }} ?>
            </tbody>
          </table>
            </form>
            </div>
            <div id="menu4" class="tab-pane fade"> <br/>
              <form  name="frm4" id="frm4" class="form-horizontal" action="" method="post">
              	<div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label"></label>
                      <div class="col-md-6">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label"></label>
                      <div class="col-md-6">
                      </div>
                    </div>
                  </div>
                   <div class="form-group">
           			<div class="col-md-12" align="center">
                    <input type="submit" class="btn <?=$btncolor?>" name="Submit4" id="save4" value="Update" title="" <?php if($_POST['Submit4']=='Update'){?>disabled<?php }?>>
                    <input name="refid" id="refid" type="hidden" value="<?=$row_locdet['sno']?>"/>
                    <input name="loccode" id="loccode" type="hidden" value="<?=$row_locdet['asc_code']?>"/>
                    <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='asp_details.php?<?=$pagenav?>'">
                    </div>
                   </div> 
              </form>
            </div>
          </div>
      </div><!--End form group-->
    </div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>