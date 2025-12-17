<?php
require_once("../includes/config.php");
$getid=base64_decode($_REQUEST['id']);

////// Function ID ///////
$fun_id = array("u"=>array(), "a"=>array(), "d"=>array(306)); // User:, Admin:24: //d=Dealer
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}



////// get details of selected city////
$rs=mysqli_query($link1,"select * from ev_customer_master where id='$getid'")or die(mysqli_error($link1));
$row2=mysqli_fetch_array($rs);

$today=date("Y-m-d");
@extract($_POST);
////// if we hit process button
if($_POST){
if($_REQUEST['add']){
////// pick max no. of Customer
     // insert all details of location //
  $sql="Update ev_customer_master set customer_name='".$name."',status='".$locationstatus."',pincode = '".$pincode."' , address1 = '".$address."' , update_by='".$_SESSION['userid']."',update_date='".$today."' , gst_no = '".$gstin."', veh_no='".$veh_no."' ,color = '".$color."' , serial_no  = '".$ch_no."' , eng_no = '".$eng_no."'   , title = '".$titleval."' , alt_mobile = '".$alt_mobile."',landmark='".$landmark."',adhaar_no='".$adhaar_no."' ,  mobile = '".$phone."',emailid='".$email."' , stateid = '".$locationstate."' , cityid = '".$locationcity."' ,customer_type = '".$customer_type."'    where customer_id='".$customercode."'";
   $result=mysqli_query($link1,$sql);
   //// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error Code0.1:";
    }
	
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
				$newfilename = $newcustomercode."_".$today.$now.$file_ext;
				move_uploaded_file($_FILES[$filename]["tmp_name"],"customer_doc/".$newfilename);
				$file="customer_doc/".$newfilename;
				//chmod ($file, 0755);
			} 
                        if($document_name[$k]  != '' && $document_desc[$k] != '') {
			$sql_inst = "INSERT INTO document_attachment set ref_no='".$customercode."', ref_type='Customer Document',document_name='".ucwords($document_name[$k])."', document_path='".$file."', document_desc='".ucwords($document_desc[$k])."' , updatedate='".$datetime."'";
			$res_inst = mysqli_query($link1,$sql_inst);
			 //// check if query is not executed
				if (!$res_inst) {
					 $flag = false;
					 $err_msg = "Error Code0.11:";
				}
                           }
		}
   ////// insert in activity table////
   $flag=dailyActivity($_SESSION['userid'],$customercode,"CUSTOMER","EDIT",$ip,$link1,$flag);
   $msg = "Customer is Successfully created with ref. no.".$customercode;
   
		header("location:customer_details.php?msg=".$msg."".$pagenav);
		exit;
}
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title>Edit Customer</title>
 <script src="../js_new/jquery.js"></script>
 <script language="JavaScript" src="../js/ajax.js"></script>
 <link href="../css_new/font-awesome.min.css" rel="stylesheet">
 <link href="../css_new/abc.css" rel="stylesheet">
 <script src="../js_new/bootstrap.min.js"></script>
 <link href="../css_new/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css_new/bootstrap.min.css">
 <link rel="stylesheet" href="../css_new/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js_new/jquery.dataTables.min.js"></script>
 <script>
	$(document).ready(function(){
		$('#myTable').dataTable();
	});
	$(document).ready(function(){
        $("#form1").validate();
    });
	
	/////////////////////////////// getting city ///////////////////////////////////////////////////////////////////
function get_citydiv(){
 
	  var name=$('#locationstate').val();	 
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{state:name},
		success:function(data){		
	    $('#citydivnew').html(data);
	    }
	  });
   
 }
	
	
 </script>
 <script language="javascript" type="text/javascript">
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
			var r = '<tr id="addr_doc'+num+'"><td width="30%"><div style="display:inline-block;float:right"><select class="form-control " name="document_name['+num+']"  id="document_name['+num+']" style="width:300px;" onchange="checkDuplicate(' + num + ',this.value);"><option value="">Please Select</option><?php $res = mysqli_query($link1 , "select id, doc_name from ev_document_list");while($row = mysqli_fetch_array($res)){?><option  value="<?=$row['doc_name']?>"><?=$row['doc_name']?></option><?php }?></select></div><div style="display:inline-block;float:left;"><i class="fa fa-close fa-lg" onClick="fun_remove3('+num+');"></i></div></td><td width="30%"><div style="display:inline-block;float:right"><input type="text" class="form-control entername  cp" name="document_desc['+num+']"  id="document_desc['+num+']" value="" style="width:300px;"></div></td><td width="70%"><div style="display:inline-block; float:left"><input type="file" id="browse'+num+'" name="fileupload'+num+'" style="display: none" onChange="Handlechange('+num+');" accept="image/*"/><input type="text" id="filename'+num+'" readonly="true" style="width:300px;" class="form-control"/></div><div style="display:inline-block; float:left">&nbsp;&nbsp;<input type="button" value="Click to upload attachment" id="fakeBrowse'+num+'" onclick="HandleBrowseClick('+num+');" class="btn btn-warning"/></div></td></tr>';
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
<style>
.red_small{
	color:red;
}
</style>
<script type="text/javascript" src="../js_new/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center">Edit Customer Details</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="form1" class="form-horizontal" action="" method="post" id="form1" onSubmit="return chk_data()"  enctype="multipart/form-data">

     <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Contact No<span class="red_small">*</span></label>
              <div class="col-md-7">
               <input name="phone" id = "phone" type="text" class="digits form-control required" size="40" value="<?=$row2['mobile']?>"  maxlength="10"  minlength = "10" />
              
                
          
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Alternate Contact No.</label>
              <div class="col-md-7">
                
               <input name="alt_mobile" id = "alt_mobile" type="text" class="digits form-control" size="40" value="<?=$row2['alt_mobile']?>" minlength = "10"  maxlength="10" />
      
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Title<span class="red_small">*</span></label>
              <div class="col-md-7">
              
               <select name="titleval" id="titleval" required class="form-control selectpicker required" data-live-search="true" >
                                          <option value ="">Please Select</option>
                                           <option value="Mr" <?php if($row2['title']=='Mr'){ echo "selected";}?>>Mr</option>
                                            <option value="Mrs" <?php if($row2['title']=='Mrs'){ echo "selected";}?>>Mrs</option> 
                                             <option value="Miss" <?php if($row2['title']=='Miss'){ echo "selected";}?>>Miss</option> 
                                             <option value="M/s" <?php if($row2['title']=='M/s'){ echo "selected";}?>>M/s</option> 
                                        </select>
                
          
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-4 control-label">Customer Name</label>
              <div class="col-md-7">
                
         <input name="name"  id= "name"  type="text" class="form-control" size="40" value="<?=$row2['customer_name']?>" required />
      
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Email:</label>
              <div class="col-md-7">
                 <input name="email"  id="email" type="text" class="email form-control" size="40" value="<?=$row2['emailid']?>" onBlur="return asc_email(this.value);"/>
                 
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-4 control-label">Country<span class="red_small">*</span></label>
              <div class="col-md-7">
                <input name="country" id="country" type="text" readonly value="<?=getAnyDetails($row2['country'],"countryname","countryid","country_master",$link1);?>" class="form-control" size="40" required  /> 
             
       
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">State<span class="red_small">*</span></label>
              <div class="col-md-7" >
               <select name="locationstate" id="locationstate" class="form-control" required  onChange="get_citydiv();get_distdiv();">
                    <option value="">--None--</option>
                               
                      <?php
						$circlequery="select distinct(state) , stateid from state_master order by state";
						$circleresult=mysqli_query($link1,$circlequery) or die(mysqli_error($link1));
						while($circlearr=mysqli_fetch_array($circleresult)){
						?>
						  <option data-tokens="<?php echo $circlearr['state'];?>" value="<?=$circlearr['stateid']?>" <?php if($row2['stateid'] == $circlearr['stateid']){ echo "selected";}?>><?=ucwords($circlearr['state'])?></option>
						<?php 
						}
						?>
                  </select>
               
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-4 control-label">City<span class="red_small">*</span></label>
            <div class="col-md-7" id="citydivnew">
              
               <select name="locationcity" id="locationcity" class="form-control required" required>
                <option value=''>--Please Select-</option>
			      <?php
						$circlequery="select distinct(cityid) , city from city_master order by city";
						$circleresult=mysqli_query($link1,$circlequery) or die(mysqli_error($link1));
						while($circlearr=mysqli_fetch_array($circleresult)){
						?>
						  <option data-tokens="<?php echo $circlearr['cityid'];?>" value="<?=$circlearr['cityid']?>" <?php if($row2['cityid'] === $circlearr['cityid']) { echo "selected";} ?>><?=ucwords($circlearr['city'])?></option>
						<?php 
						}
						?>
               </select> 
              </div>
            </div>         
          </div> 
		  
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Pincode</label>
              <div class="col-md-7" >
			  <input name="pincode" id="pincode"  class="digits form-control" value="<?=$row2['pincode']?>" >
               
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-4 control-label">Landmark</label>
              <div class="col-md-7">
                <input name="landmark" id="landmark" type="text" class="form-control" value="<?=$row2['landmark']?>"/> 
              </div>
            </div>   
          </div>
         <!-- <div class="form-group">
           <div class="col-md-6"><label class="col-md-5 control-label"><strong>Vehicle  Type<span class="red_small">*</span></strong></label>
            <div class="col-md-7">
               <input type="text" name="cat" id="cat" class="form-control" value="<?=$row2['type']?>" readonly />
                </div>
                </div>
            <div class="col-md-6"><label class="col-md-4 control-label"><strong>Customer Status<span class="red_small">*</span></strong></label>
            <div class="col-md-7">
		     
                </div>
                </div>
				</div>
                <div class="form-group">
						<div class="col-md-6"><label class="col-md-5 control-label">Color</label>

                      <div class="col-md-6">

                    <select name="color" id="color" class="form-control required"  required/>
                    <option value="">Please Select</option>
                    <option value="Black" <?php if($row2['color']=='Black'){ echo "selected";}?>>Black</option>
                    <option value="White" <?php if($row2['color']=='White'){ echo "selected";}?>>White</option>
                    <option value="Grey" <?php if($row2['color']=='Grey'){ echo "selected";}?>>Grey</option>
                    </select>
                      </div>
                    </div>

                    <div class="col-md-6"><label class="col-md-4 control-label">Motor No. <span class="red_small">*</span></label>

                      <div class="col-md-6">

                       <input name="eng_no" id="eng_no" type="text" value="<?=$row2['eng_no']?>" maxlength="15" class="form-control required" required/>

                      </div>

                    </div>

                  </div>
                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-5 control-label">Vehicle Registration Number <span class="red_small">*</span></label>

                      <div class="col-md-6">
					  <input name="veh_no" id="veh_no" type="text"  value="<?=$row2['veh_no']?>" class="form-control required"  maxlength="15" required/>

                
                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-4 control-label">Chassis Number/VIN<span class="red_small">*</span></label>

                      <div class="col-md-6">

                   <input name="ch_no" id="ch_no" type="text"  value="<?=$row2['serial_no']?>" class="form-control required" maxlength="15"  required/>
                      </div>

                    </div>

                  </div>  -->
			<div class="form-group">
           <div class="col-md-6"><label class="col-md-5 control-label"><strong>GSTIN</strong></label>
            <div class="col-md-7">
               <input type="text" name="gstin" id="gstin" class="alphanumeric form-control" minlength="15" maxlength="15" value="<?=$row2['gst_no']?>"  />
                </div>
                </div>
            <div class="col-md-6"><label class="col-md-4 control-label"><strong>Adhaar No.</strong></label>
            <div class="col-md-7">
		        <input name="adhaar_no" id="adhaar_no" type="text" class="form-control digits" minlength="12" maxlength="12" value="<?=$row2['adhaar_no']?>"/> 
                </div>
                </div></div>
	<div class="form-group">
           <div class="col-md-6"><label class="col-md-5 control-label"><strong>Address<span class="red_small">*</span></strong></label>
            <div class="col-md-7">
               <textarea name="address" id="address" class="form-control required" required onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);" onContextMenu="return false" style="resize:vertical"><?=$row2['address1']?></textarea>
                </div>
                </div>
            <div class="col-md-6"><label class="col-md-4 control-label"><strong>Status</strong></label>
            <div class="col-md-7">
		        <select name="locationstatus" id="locationstatus" class="form-control">
                    <option value="Active"<?php if($row2['status']=='Active'){ echo "selected";}?>>Active</option>
                    <option value="deactive"<?php if($row2['status']=='deactive'){ echo "selected";}?>>Deactive</option>
                </select>
                </div>
                </div></div>
			  <div class="form-group">
           <div class="col-md-6"><label class="col-md-5 control-label"><strong>Customer Type</strong></label>
            <div class="col-md-7">
                 <input name="customer_type" id="customer_type" type="text" class="form-control"  value="<?=$row2['customer_type']?>"/> 
                </div>
                </div>
            <div class="col-md-6"><label class="col-md-4 control-label"><strong></strong></label>
            <div class="col-md-7">
		        
                </div>
                </div></div>
                

    
					<h4 align="center">Document Details</h4>
					<!-- Items Detail content -->
					<div class="form-group">
						<table id="dt_basic6" class="table table-bordered table-hover" width="80%">
							<thead>
								<tr class="<?=$tableheadcolor?>" >
                                    <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a><strong>S No.</strong> </th>
                                    <th data-hide="phone"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Document Name</strong></th>
                                    <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Document Description</strong></th>
                                    <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Upload Date</strong></th>
                                    <th data-hide="phone"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Download</strong></th>
								</tr>
							</thead>
                           
                	<tbody>
                  	<?php  
						$sno=0;
								$tsql=mysqli_query($link1,"select * from document_attachment where  ref_no='".$row2['customer_id']."' order by id desc");
									while($trow=mysqli_fetch_assoc($tsql))
										{
										$sno=$sno+1;
									 ?>	
								
                 	<tr title="" class="even pointer">
                    <td><?php echo $sno; ?></td>
					<td style=" text-align:right;"><?php echo $trow['document_name'];?></td>
					<td style=" text-align:right;"><?php echo $trow['document_desc'];?></td>
                    <td style=" text-align:center;"><?php echo dt_format($trow['updatedate']);?></td>
                    <td style=" text-align:center;"><img src="<?php echo $trow['document_path'];?>" width="50" height="50"/></td>               	
                  </tr>
                 <?php }?>
 				</tbody>
      		</table>
    	</div>
									<!-- end Items Detail content -->
									<div class="form-group">
                	<div class="col-sm-12">
                	<table class="table table-bordered" width="100%" id="itemsTable3">
                    	<thead>
                        	<tr class="<?=$tableheadcolor?>" >
                            	<th width="25%">Document Name</th>
								<th width="20%">Description</th>
                            	<th width="50%">Attachment</th>
                        	</tr>
                    	</thead>
                    	<tbody>
                        	<tr id="addr_doc0">
                        		<td><select class="form-control " name="document_name[0]"  id="document_name[0]" onChange="checkDuplicate(0, this.value);">
								<option value="">Please Select</option>
								      <?php 
								          $res = mysqli_query($link1 , "select id, doc_name from ev_document_list");
										   while($row = mysqli_fetch_array($res)){?>
										      <option  value="<?=$row['doc_name']?>"><?=$row['doc_name']?></option>
										   
										    <?php   }
								             ?>
								</select></td>
								<td><input type="text" class="form-control entername  cp" name="document_desc[0]"  id="document_desc[0]" value=""></td>
                            	<td>
                                	<div style="display:inline-block; float:left">
                                    <input type="file"  id="browse0" name="fileupload0" style="display: none" onChange="Handlechange(0);" accept=".xlsx,.xls,image/*,.doc, .docx,.ppt, .pptx,.txt,.pdf"/>
                                    <input type="text" id="filename0" readonly style="width:300px;" class="form-control "/>
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
                    <input type="submit" class="btn <?=$btncolor?>" name="add" id="add" value="EDIT" title="Add Customer" <?php if($_POST['add']=='EDIT'){?>disabled<?php }?>>
                    <input name="refid" id="refid" type="hidden" value="<?=$getid?>"/>
<input type="hidden" name="customercode" id="customercode" value="<?=$row2['customer_id']?>">
                    <input type="hidden" name="reference" id="reference" value="<?=$data['reference']?>" />
                    <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='customer_details.php?<?=$pagenav?>'">
            		</div>
          		</div>
							
							<!-- end Items Detail -->
         </form>
      </div>

    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
?>
</body>
</html>
<script type="text/javascript">
	///// function for checking duplicate Product value
	function checkDuplicate(fldIndx1, enteredsno) { 
	 document.getElementById("add").disabled = false;
		if (enteredsno != '') {
			var check2 = "document_name[" + fldIndx1 + "]";
			var flag = 1;
			for (var i = 0; i <= fldIndx1; i++) {
				var check1 = "document_name[" + i + "]";
				if (fldIndx1 != i && (document.getElementById(check2).value == document.getElementById(check1).value )){
					if ((document.getElementById(check2).value == document.getElementById(check1).value)) {
						alert("Duplicate Document Selection.");
						document.getElementById(check2).value = '';
						document.getElementById(check2).style.backgroundColor = "#F66";
						flag *= 0;
					}
					else {
						document.getElementById(check2).style.backgroundColor = "#FFFFFF";
						flag *= 1;
						///do nothing
					}
				}
			}//// close for loop
			if (flag == 0) {
				return false;
			} else {
				return true;
			}
		}	
	}
	</script>