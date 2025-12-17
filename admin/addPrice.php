<?php
////// Function ID ///////
$fun_id = array("a"=>array(54));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
if($_POST['Submit']=="Save"){

foreach($_POST['state'] as $k=>$val)
	{ 
foreach($_POST['loc'] as $k1=>$val1)	
{
	/// get location id 
   $loc_type=mysqli_query($link1,"select seq_id from location_type where locationtype='".$_REQUEST['loc'][$k1]."'");
   $seq_no=mysqli_fetch_assoc($loc_type);
    ///ref no for price master   
   $ref=$_POST['state'][$k]."~".$_POST['loc'][$k1]."~".$_POST['product_code'];
    ///insert in price master 
	$get_id=mysqli_fetch_assoc(mysqli_query($link1,"select id from price_master where state='".$_POST['state'][$k]."' and location_type='".$val1."' and product_code='$_POST[product_code]'"));
   if($get_id['id']){
	  mysqli_query($link1,"update price_master set mrp='$_POST[mrp]',price='$_POST[price]',combo_price='$_POST[combo_price]',update_date='$datetime',update_by='$_SESSION[userid]' where state='".$_POST['state'][$k]."' and location_type='".$val1."' and product_code='$_POST[product_code]'")or die("ER4".mysqli_error($link1));
	  $id=$get_id['id'];
	  ////// insert in activity table////
	dailyActivity($_SESSION['userid'],$_POST['product_code'],"PRICE","UPDATE",$ip,$link1,"");
   }else{
      mysqli_query($link1,"insert into price_master set state='".$_POST['state'][$k]."' ,location_type='".$val1."',location_seq='".$seq_no['seq_id']."',product_code='$_POST[product_code]',mrp='$_POST[mrp]',price='$_POST[price]',combo_price='$_POST[combo_price]',status='active',create_date='$datetime',create_by='$_SESSION[userid]' ")or die("ER4".mysqli_error($link1));
	  $id=mysqli_insert_id($link1);
	  ////// insert in activity table////
	dailyActivity($_SESSION['userid'],$_POST['product_code'],"PRICE","ADD",$ip,$link1,"");
   }
   $id=mysqli_insert_id($link1);
    if(($id)>0)
		{
     $sql=(mysqli_query($link1,"insert into price_history set type_id='$id',ref_no='$ref',mrp='$_POST[mrp]',price='$_POST[price]',combo_price='$_POST[combo_price]',action='ADD',modify_date='$datetime' ")or die("ER4".mysqli_error($link1)));
	//return message
	$msg="You have successfully created a new Price";
		}
	else{
	////// return message
	$msg="Something went wrong. Please try again.";
   }
}
}
	///// move to parent page
   header("Location:price_master.php?msg=".$msg."".$pagenav);
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
        $("#frm1").validate();
    });
	
	/////// Script For Check All / Uncheck All Master Rights//////////////
function check_all(field){
    for (i = 0; i < field.length; i++){
		
         field[i].checked = true ;
	}
}
function uncheck_all(field){
    for (i = 0; i < field.length; i++){
		
         field[i].checked = false ;
	}
}

/////// Script For Check All / Uncheck All Location //////////////
function check_all1(field1){
    for (i = 0; i <field1.length; i++){
		
         field1[i].checked = true ;
	}
}
function uncheck_all1(field1){
    for (i = 0; i < field1.length; i++){
         field1[i].checked = false ;
	}
}
 </script>
 
 
<style>
.red_small{
	color:red;
}
</style>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-inr"></i>&nbsp;&nbsp;Add New Price</h2><br/><br/>
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
			<div class="col-md-6"><label class="col-md-4 control-label">Product Price<span class="red_small">*</span></label>
              <div class="col-md-6">
                  <input type="text" name="price" id="price" class="form-control" onKeyPress="return onlyFloatNum(event);" value=""  placeholder="0.00" required/>
              </div>
            </div>
           </div>
		  
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-4 control-label">Product Mrp<span class="red_small">*</span></label>
              <div class="col-md-6">
               <input type="text" name="mrp" id="mrp" class="form-control" value="" onKeyPress="return onlyFloatNum(event);" placeholder="0.00"  required />
               </div>
            </div>
			<div class="col-md-6"><label class="col-md-4 control-label">Price in combo<span class="red_small">*</span></label>
              <div class="col-md-6">
                  <input type="text" name="combo_price" id="combo_price" class="form-control" value="" onKeyPress="return onlyFloatNum(event);" placeholder="0.00"  required />
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
				   $res_loctype=mysqli_query($link1,"select * from location_type "); 
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
          <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='price_master.php?<?=$pagenav?>'">
          </div>
		</div>
    
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
