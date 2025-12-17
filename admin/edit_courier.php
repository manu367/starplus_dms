<?php
////// Function ID ///////
$fun_id = array("a"=>array(30));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$sno=base64_decode($_REQUEST['id']);

if($_REQUEST['Submit']=="new")
{
$name="";
$c_p="";
$code="";
$add="";
$con="";
$email="";
$locationcity="";
$locationstate="";
$rate="";
$tat="";
$vehicletype="";
$button="Save";
}
if($_REQUEST[Submit]=="edit")
{
	
$res=mysqli_query($link1,"select * from diesl_master where sno='".$sno."' ")or die(mysqli_error($link1));
if($row=mysqli_fetch_array($res))
{
$name="$row[couriername]";
$c_p="$row[contact_person]";
$code="$row[couriercode]";
$add="$row[addrs]";
$locationcity="$row[city]";
$email="$row[email]";
$locationstate="$row[state]";
$status="$row[status]";
$con="$row[phone]";
$rate="$row[rate]";
$tat="$row[tat]";
$vehicletype="$row[vehicletype]";
$weight="$row[weight]";
$button="Update";
}

}
if($_REQUEST[Submit]=="Save")
{
	$res_max=mysqli_query($link1,"select count(sno) as nos from diesl_master");
	$row_max=mysqli_fetch_assoc($res_max);
	$maxno=$row_max['nos']+1;
	$couriercode="CSLOGIX".str_pad($maxno,3,0,STR_PAD_LEFT);
$res=mysqli_query($link1,"insert into diesl_master set couriername='$_REQUEST[name]',contact_person='$_REQUEST[c_p]',couriercode='$couriercode',addrs='$_REQUEST[address]',city='$_REQUEST[locationcity]',email='$_REQUEST[email]',state='$_REQUEST[locationstate]',status='$_REQUEST[status]',rate='$_REQUEST[rate]',tat='$_REQUEST[tat]',
vehicletype='$_REQUEST[vehicletype]',weight='$_REQUEST[weight]',phone='$_REQUEST[phone]'
")or die("ER1".mysqli_error($link1));
////// insert in activity table////
	dailyActivity($_SESSION['userid'],$_REQUEST[name],"COURIER","ADD",$ip,$link1,"");
	
$msg="You have successfully add details of courier ";
	///// move to parent page
    header("Location:courier_master.php?msg=".$msg."".$pagenav);
}


if($_REQUEST[Submit]=="Update")
{
	
$res=mysqli_query($link1,"update diesl_master set couriername='$_REQUEST[name]',contact_person='$_REQUEST[c_p]',addrs='$_REQUEST[address]',city='$_REQUEST[locationcity]',email='$_REQUEST[email]',state='$_REQUEST[locationstate]',status='$_REQUEST[status]',rate='$_REQUEST[rate]',tat='$_REQUEST[tat]',vehicletype='$_REQUEST[vehicletype]',weight='$_REQUEST[weight]',phone='$_REQUEST[phone]' where sno='$sno'
")or die(mysqli_error($link1));
////// insert in activity table////
	dailyActivity($_SESSION['userid'],$_REQUEST[name],"COURIER","UPDATE",$ip,$link1,"");
$msg="You have successfully updated details of courier ";
	///// move to parent page
    header("Location:courier_master.php?msg=".$msg."".$pagenav);


}

?>
<style>
.red_small{
	color:red;
}
</style>
 <script>
	
	$(document).ready(function(){
        $("#frm1").validate();
    });
	
	/////////// function to get city on the basis of state
 function get_citydiv(){
	  var name=$('#locationstate').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{state:name},
		success:function(data){
			//alert(data);
	    $('#citydiv').html(data);
	    }
	  });
   
 }
 </script>
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
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script src="../js/frmvalidate.js"></script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
 
 
 
 <body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
	 
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-truck"></i>&nbsp;&nbsp;Add/Edit Courier</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
	  
          <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
		<?php if(($_REQUEST[Submit]=="new")||($_REQUEST[Submit]=="edit")){ ?>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Courier Name<span class="red_small">*</span></label>
              <div class="col-md-5">
               <input name="name" type="text" id="name"  value="<?=$name?>" class="form-control required" required/> 
          
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Contact Person<span class="red_small">*</span></label>
              <div class="col-md-5">
                <input name="c_p" type="text" id="c_p"  value="<?=$c_p?>" class="form-control required"  required/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Contact No.<span class="red_small">*</span></label>
              <div class="col-md-5">
               <input name="phone" type="text" id="phone"  value="<?=$con?>" class="form-control required" onKeyPress="return isNumber(event);" maxlength="10" required />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Email<span class="red_small">*</span></label>
              <div class="col-md-5">
               <input name="email" type="email" id="email"  value="<?=$email?>"  class="form-control required" onBlur="return asc_email(this.value);" required/>
              </div>
            </div>
          </div>
          <div class="form-group">
		  <div class="col-md-6"><label class="col-md-5 control-label">State<span class="red_small">*</span></label>
              
			  <div class="col-md-5" id="statediv">
                 <select name="locationstate" id="locationstate" onchange='get_citydiv();' class="form-control required" required>
                    <option value="">--Please Select--</option>
                                                <?php $state =mysqli_query($link1,"select * from state_master order by state asc");
												while($courier_state=mysqli_fetch_assoc($state)){?>
                            				<option value="<?php echo $courier_state['state'];?>"<?php if($courier_state['state']==$row['state']){ echo "selected";} ?>><?php echo $courier_state['state'];?></option>
                                            <?php }?>
                </select>              
              </div>
            </div>
            
            <div class="col-md-6"><label class="col-md-5 control-label">City<span class="red_small">*</span></label>
              
			  <div class="col-md-5" id="citydiv">
              
               
			   <?php if($_REQUEST[Submit]=="new")
                     { ?>
				  <select name="locationcity" id="locationcity" class="form-control required" required>
					<option value=''>--Please Select-</option>
					</select>
					<?php }
                 else 
				 {?>
	<select name="locationcity" id="locationcity" class="form-control required" required>
        <option value="<?php echo $row['city'];?>"><?php echo $row['city'];?></option>
	            <?php   }?>

               </select>  
              </div>
            </div>
            
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Address<span class="red_small">*</span></label>
              <div class="col-md-5">
              <textarea name="address"  id="address" class="form-control required" required><?=$add?></textarea>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Status<span class="red_small">*</span></label>
              <div class="col-md-5">
                <select name="status" id="status" class="form-control required" required>
          <option value="" >Please Select</option>
          <option value="Active"<?php if($status=='Active'){ echo "selected";} ?>>Active</option>
          <option value="Deactive"<?php if($status=='Deactive'){ echo "selected";} ?>>Deactive</option>
        </select> 
              </div>
            </div> 
          </div>	
          
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Rate</label>
              <div class="col-md-5">
               <input name="rate" type="text" id="rate"  value="<?=$rate?>" class="form-control"/> 
          
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">TAT</label>
              <div class="col-md-5">
                <input name="tat" type="text" id="tat"  value="<?=$tat?>" class="form-control"/>
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Vehicle Type</label>
              <div class="col-md-5">
               <input name="vehicletype" type="text" id="vehicletype"  value="<?=$vehicletype?>" class="form-control"/> 
          
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Weight</label>
              <div class="col-md-5">
                <input name="weight" type="text" id="weight"  value="<?=$weight?>" class="form-control" />
              </div>
            </div>
          </div>






          <div class="form-group">
            <div class="col-md-12" align="center">
              
            <input type="submit" class="btn btn-primary" name="Submit" id="" value="<?php echo $button;?>" >
        <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='courier_master.php?<?=$pagenav?>'">
      
            </div>
			
          </div>
		  
<?php } ?>
    </form>
      </div>

    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>

</html>
 