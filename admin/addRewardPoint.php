<?php
require_once("../config/config.php");
if($_POST['Submit']=="Save"){
	foreach($_POST['state'] as $k=>$val)
	{ 
		foreach($_POST['loc'] as $k1=>$val1)	
		{
    		///insert in price master 
			$get_id = mysqli_fetch_assoc(mysqli_query($link1,"SELECT id FROM reward_points_master WHERE state='".$_POST['state'][$k]."' AND id_type='".$val1."' AND partcode='".$_POST['product_code']."'"));
   			if($get_id['id']){
	  			mysqli_query($link1,"UPDATE reward_points_master SET reward_point='".$_POST['reward_point']."',parent_party_reward='".$_POST['parent_reward_point']."', update_on='".$datetime."', update_by='".$_SESSION['userid']."' WHERE state='".$_POST['state'][$k]."' AND id_type='".$val1."' AND partcode='".$_POST['product_code']."'")or die("ER4".mysqli_error($link1));
	  			$id = $get_id['id'];
	  			////// insert in activity table////
				dailyActivity($_SESSION['userid'],$_POST['product_code'],"REWARD POINT","UPDATE",$ip,$link1,"");
   			}else{
      			mysqli_query($link1,"INSERT INTO reward_points_master SET state='".$_POST['state'][$k]."', id_type='".$val1."', partcode='".$_POST['product_code']."', reward_point='".$_POST['reward_point']."',parent_party_reward='".$_POST['parent_reward_point']."', status='A', create_on='".$datetime."', create_by='".$_SESSION['userid']."' ")or die("ER4".mysqli_error($link1));
	  			$id = mysqli_insert_id($link1);
	  			////// insert in activity table////
				dailyActivity($_SESSION['userid'],$_POST['product_code'],"REWARD POINT","ADD",$ip,$link1,"");
   			}
    		if(($id)>0)
			{
				//return message
				$msg="You have successfully created a new reward point";
				$cflag="success";
				$cmsg = "Success";
			}
			else{
				////// return message
				$msg="Something went wrong. Please try again.";
				$cflag="danger";
				$cmsg = "Failed";
   			}
		}
	}
	///// move to parent page
   	header("Location:reward_points_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	exit;
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
 
 <script type="text/javascript" src="../js/ajax.js"></script>
 
 <script>
	$(document).ready(function(){
	var spinner = $('#loader');
    $("#frm1").validate({
		submitHandler: function (form) {
			if (!this.wasSent) {
				this.wasSent = true;
				$(':submit', form).val('Please wait...')
						.attr('disabled', 'disabled')
						.addClass('disabled');
				spinner.show();
				form.submit();
			} else {
				return false;
			}
		}
	});
});
	
	/////// Script For Check All / Uncheck All Master Rights//////////////
function checkAll(field){
    for (i = 0; i < field.length; i++){
		
         field[i].checked = true ;
	}
}
function uncheckAll(field){
    for (i = 0; i < field.length; i++){
		
         field[i].checked = false ;
	}
}

</script>
 
 
<style>
.red_small{
	color:red;
}
</style>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<link href="../css/loader.css" rel="stylesheet"/>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-money"></i>&nbsp;&nbsp;Add New Reward</h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post" >
          
		  <div class="form-group">
           <div class="col-md-6"><label class="col-md-4 control-label">Product <span class="red_small">*</span></label>
            <div class="col-md-6">
            <select name="product_code"  class="form-control selectpicker required" data-live-search="true" id="product_code" required>
            <option value="">--Please Select--</option>
             <?php $loc =mysqli_query($link1,"select * from product_master"); while($srow=mysqli_fetch_assoc($loc)){?>
             <option data-tokens="<?=$srow['productname']." | ".$srow['productcode']?>" value="<?php echo $srow['productcode'];?>"><?php echo $srow['productname']." (".$srow['productcode'].")";?></option>
              <?php }?>
               </select>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-4 control-label">Reward Points<span class="red_small">*</span></label>
              <div class="col-md-6">
                  <input type="text" name="reward_point" id="reward_point" class="form-control digits" value=""  placeholder="0" required/>
              </div>
            </div>
           </div>
           
           <div class="form-group">
           <div class="col-md-6"><label class="col-md-4 control-label">&nbsp;</label>
            <div class="col-md-6">
            &nbsp;
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-4 control-label">Parent Reward</label>
              <div class="col-md-6">
                  <input type="text" name="parent_reward_point" id="parent_reward_point" class="form-control digits" value=""  placeholder="0"/>
              </div>
            </div>
           </div>		  
		   
		
		  
		   <div class="table-responsive"> 
              <div class="form-buttons" style="float:right">
                <input name="CheckAll" type="button" class="btn <?=$btncolor?>" onClick="checkAll(document.frm1.state)" value="Check All" />
                <input name="UnCheckAll" type="button" class="btn <?=$btncolor?>" onClick="uncheckAll(document.frm1.state)" value="Uncheck All" /></div> 
          <table id="myTable" class="table table-hover">
            <thead>
                  <tr>
                    <th style="border:none">&nbsp;State <span class="red_small">*</span></th>
                  </tr>
                </thead>
                <tbody>
                 <?php
				  $k=1;
				   $state=mysqli_query($link1,"select * from state_master order by state "); 
				   while($row_state=mysqli_fetch_assoc($state)){
				   	if($k%6==1){   
				  ?>
                  <tr>
                  <?php }?>
                    <td><input style="width:20px" required type='checkbox' name="state[]" id='state' value="<?=$row_state['state']?>"/> <?=$row_state['state']?></td>
                    <?php if($k/6==0){?>
                    </tr>
                  <?php 
				          }
						  $k++;
				   }
				  ?>  
                  
                </tbody>
              </table>
             </div>        
          <div class="table-responsive"> 
              <div class="form-buttons" style="float:right">
                <input name="CheckAll" type="button" class="btn <?=$btncolor?>" onClick="checkAll(document.frm1.loc)" value="Check All" />
                <input name="UnCheckAll" type="button" class="btn <?=$btncolor?>" onClick="uncheckAll(document.frm1.loc)" value="Uncheck All" /></div> 
          <table id="myTable" class="table table-hover">
            <thead>
                  <tr>
                    <th style="border:none">&nbsp;Location Type <span class="red_small">*</span></th>
                  </tr>
                </thead>
                <tbody>
                 <?php
				  $k=1;
				   $res_loctype=mysqli_query($link1,"select * from location_type where status='A'"); 
				   while($row_loctype=mysqli_fetch_assoc($res_loctype)){
				   	if($k%6==1){   
				  ?>
                  <tr>
                  <?php }?>
                    <td><input style="width:20px" type='checkbox' required name="loc[]" id='loc' value="<?=$row_loctype['locationtype']?>"/> <?=$row_loctype['locationname']?></td>
                    <?php if($k/6==0){?>
                    </tr>
                  <?php 
				          }
						  $k++;
				   }
				  ?>  
                  
                </tbody>
              </table>
              </div>        
	   <div class="form-group">
            <div class="col-md-12" align="center">
           <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="" value="Save" >&nbsp;
          <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='reward_points_master.php?<?=$pagenav?>'">
          </div>
		</div>
    
	</form>
      </div>
          </div>
    
  </div>
</div>
<div id="loader"></div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
