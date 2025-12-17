<?php
require_once("../config/config.php");
$sno=base64_decode($_REQUEST['id']);
////// get details of selected location////

$res_locdet=mysqli_query($link1,"SELECT * FROM admin_users where name='".$sno."'")or die(mysqli_error($link1));
$row_locdet=mysqli_fetch_array($res_locdet);


////// final submit form ////
@extract($_POST);
   if($_POST['save']=='Update'){
      $count=count($dealer);
      $i=0;
	 
	  mysqli_query($link1,"update fos_mapped set status='' where fos_id='".$row_locdet['username']."'")or die("er0".mysqli_error($link1));
      while($i < $count){
           if($dealer[$i]==''){
              $status='';
           }else{
              $status='Y';
		   }
		   ///// check mapping is already is there
           if(mysqli_num_rows(mysqli_query($link1,"select fos_id,dealer_id from fos_mapped where fos_id='".$row_locdet['username']."' and dealer_id='".$dealer[$i]."'"))>0){
              mysqli_query($link1,"update fos_mapped set status='".$status."',update_date='".$today."' where fos_id='".$row_locdet['username']."' and dealer_id='".$dealer[$i]."' ")or die("ER1".mysqli_error($link1));
           }else{
              mysqli_query($link1,"insert into fos_mapped set fos_id='".$row_locdet['username']."' ,dealer_id='".$dealer[$i]."',status='".$status."',update_date='".$today."'")or die("ER2".mysqli_error($link1));
		   }
           
           $i++;	
	  }///close while loop
      ////// insert in activity table////
	  dailyActivity($_SESSION['userid'],$row_locdet['username'],"MAPPING","UPDATE",$ip,$link1);
	  ////// return message
	  $msg="You have successfully mapped selected dealer  to FOS ".$fos." ";
	  ///// move to parent page
      header("Location:fos_master.php?msg=".$msg."".$pagenav);
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
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
	
});
$(document).ready(function(){
    $("#frm2").validate();
});
$(document).ready(function () {
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});
$(document).ready(function () {
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});
</script>
 <script src="../js/frmvalidate.js"></script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
 <link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-bank"></i> Mapped FOS</h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
          
		 
            
            
         
		 
		 
		
		  
          
		 
		  
          
		  
		  
		  
		  
		 
          
		  
         
		  
		  
		   
		  <div class="table-responsive"> 
              <div class="form-buttons" style="float:right">
                <input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm1.dealer)" value="Check All" />
                <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm1.dealer)" value="Uncheck All" /></div> 
          <table id="myTable" class="table table-hover">
            <thead>
                  <tr>
                    <th style="border:none;padding-left:95px;" >Select Dealer<span class="red_small">*</span></th>
                  </tr>
                </thead>
                <tbody>
                 <?php
				  $k=1;
				 
				   $res_loctype=mysqli_query($link1,"select * from vendor_master");
				   
				   while($row_loctype=mysqli_fetch_assoc($res_loctype)){
				   	if($k%5==1){ ?>
						<tr>
                  <?php }	
                   $state_acc=mysqli_query($link1,"select * from fos_mapped where status='Y' and fos_id='".$row_locdet['username']."' and dealer_id='".$row_loctype['id']."'")or die(mysqli_error($link1));
                   $num1=mysqli_fetch_array($state_acc);
		   				  ?>                  
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input style="width:20px" type='checkbox'  name="dealer[]" id='dealer' value="<?=$row_loctype['id']?>" <?php if ($row_loctype['id'] == $num1['dealer_id']) echo "checked='checked'";?> /> <?=$row_loctype['name']?></td>
                    <?php if($k/5==0){?>
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
              <input type="submit" class="btn btn-primary" name="save" id="save" value="Update" title="" >
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='fos_master.php?<?=$pagenav?>'">
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