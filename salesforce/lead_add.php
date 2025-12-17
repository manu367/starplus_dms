<?php
require_once("../config/config.php");
////// final submit form ////
@extract($_POST);
if($_POST['Submit']=='Save'){
	if($_FILES['visiting_card']['name']){
		///check directory
		$dirct = "../doc_attach/lead/".date("Y-m");
		if (!is_dir($dirct)) {
			mkdir($dirct, 0777, 'R');
		}
		///// attachment 
		if ($_FILES["visiting_card"]["size"]>2097152){
			$msgg="File size should be less than or equal to 2 mb";
			header("Location:lead_add.php?msg=$msgg&sts=fail&page=lead");
		}
		else{ 
			$file_name = $_FILES['visiting_card']['name'];
			$file_tmp =$_FILES['visiting_card']['tmp_name'];
			$up=move_uploaded_file($file_tmp,$dirct."/".time().$file_name);
			$path1=$dirct."/".time().$file_name;
			$img_name1 = time().$file_name;
		}
	}	
	$ref=mysqli_query($link1,"select max(lid) as cnt from sf_lead_master order by lid desc");
	$row = mysqli_fetch_assoc($ref);
	$result=$row[cnt]+1;
	$pad=str_pad($result,3,"0",STR_PAD_LEFT);  
	$reference="LD".$pad;
		
	mysqli_query($link1,"insert into sf_status_history set party_id='".$party_id."', status_id='7', trans_type='add_lead', trans_no='".$reference."',update_by='".$_SESSION['userid']."'");
		
	$expl_prod = explode("~",$prod_code);
		
	mysqli_query($link1,"insert into sf_lead_master set partyid='".$party_id."', party_address='".cleanData($party_add)."', intial_remark='".$remark."', priority='".$priority."', vcard_url='".$path1."', reference='".$reference."', type='Lead', category='', tdate='".$today."', status='7', ip='".$ip."', sales_executive='".$designation."', dept_id='".$dept."', party_state='".$locationstate."', party_city='".$locationcity."',party_contact='".$contact_no."',party_email='".$email."', party_country='".$circle."', lead_source='".$source."', create_location='".$_SESSION['mapped_location']."', create_by='".$_SESSION['userid']."',productcode='".$expl_prod[0]."', productname='".$expl_prod[1]."',type_of_lead='".$type_of_lead."'");		
	if(mysqli_insert_id($link1)>0){	
		// include "mail_leavequest.php";
			//$phone =mysqli_fetch_array(mysqli_query($link1,"select phone,empname from hrms_employe_master where empid = '".$dept."' "));
		
		if($phone['phone']){
			$sms_msg="Dear ".$phone['empname'].", Lead is created  ".$reference." ";
			//$res = file_get_contents("http://sms.foxxglove.com/api/mt/SendSMS?user=cancrm&password=123456&senderid=CANCRM&channel=Trans&DCS=0&flashsms=0&number=".$phone['phone']."&text=".urlencode($sms_msg));
		}		             
		dailyActivity($_SESSION['userid'],$reference,"LEAD","ADD",$ip,$link1,"");
		if($email){
			include "lead_followup.php";
		}
		$msgg="Lead added successfully with reference ".$reference." ".$err_msg;
		header("Location:lead_list.php?msg=$msgg&sts=success&page=lead".$pagenav);
	}
	else{
		$msgg="Request could not be processed";
		header("Location:lead_list.php?msg=$msgg&sts=fail&page=lead".$pagenav);
	}
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
        $("#frm1").validate();
    });
 </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
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
 </script>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-child"></i> Add New Lead</h2><br/><br/>
      <?php if($_REQUEST['msg']!=''){?>
			<div align="center"><h4><span <?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";}?>>
			<?php echo $_REQUEST['msg'];?>
			</span>
            </h4>
            </div>
        <?php }?>
        
          
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" > 
          <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
          <div style="display:inline-block;float:right">
        <button title="Upload" type="button" class="btn btn-success" style="float:right;" onClick="window.location.href='uploadLead.php?<?=$pagenav?>'"><span>Upload  Lead</span></button>&nbsp;&nbsp;&nbsp;&nbsp;</div> <br/><br/>
         
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Type Of Lead <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="type_of_lead" class="form-control" id="type_of_lead" required>
               		<option value="">Select Type</option>
                    <option value="Buy">Buy</option>
                    <option value="Sales/To be vendor">Sales/To be vendor</option>
                    <option value="Partner(ASP/Dealer/Distributor)">Partner(ASP/Dealer/Distributor)</option>
                    <option value="General">General</option>
				</select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">&nbsp;</label>
              <div class="col-md-6">
&nbsp;
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Party Name(Customer) <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input type="text" autocomplete="off" id="basic_autocomplete_field" name="party_id" class="form-control entername required" required/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Sales Executive <span class="red_small">*</span></label>
              <div class="col-md-6">
               <select name="designation" class="form-control" id="designation" required>
               		<option value="">Select Sales Executive</option>
					<?php 
                    //$childNode = getChildNod($_SESSION["uid"]);
                    if($childNode){
                        $userList = $childNode.",'".$_SESSION["userid"]."'"; 
                    }else{
                        $userList = "'".$_SESSION["userid"]."'"; 
                    }
                    $sales=mysqli_query($link1,"select username,name from admin_users where username in (".$userList.") and status='active' order by name asc");
                    while($srow=mysqli_fetch_assoc($sales))
                    {
                    ?>
                    <option value="<?php echo $srow['username'];?>"><?php echo $srow['name'];?></option>
					<?php }?>
				</select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Circle <span class="red_small">*</span></label>
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
            <div class="col-md-6"><label class="col-md-6 control-label">State <span class="red_small">*</span></label>
              <div class="col-md-6" id="statediv">
                <select name="locationstate" id="locationstate" class="form-control required" required>
                  <option value=''>--Please Select--</option>
                
                </select>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">City <span class="red_small">*</span></label>
              <div class="col-md-6" id="citydiv">
                <select name="locationcity" id="locationcity" class="form-control required" required>
               <option value=''>--Please Select-</option>
               </select> 
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Party Address <span class="red_small">*</span></label>
              <div class="col-md-6">
               <textarea name="party_add" id="party_add" class="form-control addressfield required" required></textarea>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Contact No. <span class="red_small">*</span></label>
              <div class="col-md-6">
                <input name="contact_no" id="contact_no" class="form-control digits required" minlength="10" maxlength="12" required type="text"/>      
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Email</label>
              <div class="col-md-6">
              	<input name="email" id="email" class="form-control email" type="email"/>
              </div>
            </div>
          </div>	
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Priority <span class="red_small">*</span></label>
              <div class="col-md-6">
                <select name="priority" class="form-control" id="priority" required>
                	<option value="">Select Priority</option>                             
                    <option value="Cold">Cold</option>
                    <option value="Warm">Warm</option>
                    <option value="Hot">Hot</option>
                </select>      
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Remark</label>
              <div class="col-md-6">
              <textarea name="remark" id="remark" class="form-control addressfield"></textarea>
              </div>
            </div>
          </div>		 
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Transfer To </label>
              <div class="col-md-6">
                <select name="dept" class="form-control" id="dept">
              		<option value="">Select</option>
              		<?php 
					//$dept=mysqli_query($link1,"select empname,empid from hrms_employe_master where status='active'");
					$dept=mysqli_query($link1,"select name,username from admin_users where status='active' AND utype='7'");
					while($drow=mysqli_fetch_assoc($dept)){?>
            		<option value="<?php echo $drow['username'];?>"><?php echo ucwords($drow['name']); ?></option>
            		<?php } ?>
             	</select> 
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Attach Visiting Card(Allowed jpg, png, gif, jpeg and Upload upto 2 MB)  <span class="red_small">*</span></label>
              <div class="col-md-6">
              <input type="file" class="form-control" name="visiting_card" id="visiting_card" accept="image/*"/>
              <div id="image-holder"></div>
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Lead Source </label>
              <div class="col-md-6">
                <select name="source" class="form-control" id="source" required>
                	<option value="">Select Source</option>
                    <?php $source=mysqli_query($link1,"select * from sf_source_master");while($srow=mysqli_fetch_assoc($source)){?>
                    <option value="<?php echo $srow['id'];?>"><?php echo ucwords($srow['source']); ?></option>
                    <?php } ?>
                </select> 
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Product </label>
              <div class="col-md-6">
              	<select name="prod_code" id="prod_code" class="form-control selectpicker" required data-live-search="true">
                    <option value="">--None--</option>
                    <?php 
					$model_query="select productcode,productname,model_name,productcolor from product_master where status='active'";
			        $check1=mysqli_query($link1,$model_query);
			        while($br = mysqli_fetch_array($check1)){?>
                    <option value="<?php echo $br['productcode']."~".$br['productname'];?>"><?=$br['productname']." | ".$br['model_name']." | ".$br['productcode']?></option>
                    <?php }?>
                  </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn btn-primary" name="Submit" id="save" value="Save" title="" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='lead_list.php?<?=$pagenav?>'">
            </div>
          </div>
    </form>
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