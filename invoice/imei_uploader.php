<?php
require_once("../config/config.php");
$invoice=base64_decode($_REQUEST['id']);

$sql1=mysqli_query($link1,"select * from billing_master where challan_no='".$invoice."'");
$srow=mysqli_fetch_assoc($sql1);

////// final submit form ////
@extract($_POST);

if($_POST['submit']=="Save"){
  $flag=true;
  $count=0;
  $tqty=0;
	//////insert into upload file data////////////
	mysqli_query($link1,"delete from temp_imei_upload where flag='' and update_by='".$_SESSION['userid']."' and browserid='".$browserid."'");
	
	foreach($imei as $k => $val) {
	
	  $ex=explode("~", $imei[$k]);
		
	   if($ex[0]!='')
	   { 	   
	    $qty=mysqli_query($link1,"select qty, prod_code from billing_model_data where  challan_no='".$invoice."' and prod_code='".$ex[1]."'");
		if(mysqli_num_rows($qty)>0)
		{
	   while($qrow=mysqli_fetch_assoc($qty))
	   {
	    $count=$count+1;	
	   	$tqty=$tqty+$qrow['qty'];
	   $chck=mysqli_query($link1, "select imei1, imei2, prod_code from billing_imei_data where id='".$imei[$k]."'");
	   $crow=mysqli_fetch_assoc($chck);
	
	   $sql=mysqli_query($link1,"select * from temp_imei_upload where prod_code='".$qrow['prod_code']."' and inv_no='".$invoice."' ");
	   if($qrow['qty']>mysqli_num_rows($sql))
	   {	 
		$sql="INSERT INTO temp_imei_upload set prod_code='".$crow['prod_code']."',imei1='".$crow['imei1']."',imei2='".$crow['imei2']."',inv_no='".$invoice."',inv_date='".$srow['entry_date']."',update_by='".$_SESSION['userid']."',browserid='".$browserid."'";
		   mysqli_query($link1,$sql);		  
	   }	  
  
	   }
		}
	}
	 }
	 
 $qqty=mysqli_query($link1,"select sum(qty) as s from billing_model_data where  challan_no='".$invoice."'");
  $trow=mysqli_fetch_assoc($qqty);

 	    $qtycount=mysqli_query($link1, "select count(id) as total from temp_imei_upload where inv_no='".$invoice."'");
	   $trow1=mysqli_fetch_assoc($qtycount);
	  
	//// check excel file is completely uploaded///
	if($trow1['total']==$trow['s']){	    
		header("Location:showtempimeidata.php?msg=sucess&f_name=$f_name&file_id=$file_id&bdate=$invdate&inv_no=$invoice".$pagenav);
		exit;
    }
	else{
	    ////////////delete all un-valid data from temp table////////////////
	   // mysqli_query($link1,"delete from temp_imei_upload where flag='' and update_by='".$_SESSION['userid']."' and browserid='".$browserid."'");
		$msg="Upload all IMEI for the invoice no". " ".$invoice;	
	 header("Location:retailbillinglist.php?msg=".$msg."".$pagenav);
     exit;
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
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 
 <script>
	$(document).ready(function(){
        $("#frm1").validate();
    });
$(document).ready(function () {
    var charReg = /^\s*[a-zA-Z0-9,\s]+\s*$/;
    $('.keyup-char').keyup(function () {
        $('span.error-keyup-1').hide();
        var inputVal = $(this).val();

        if (!charReg.test(inputVal)) {
            $(this).parent().find(".warning").show();
			$("#save").prop("disabled",true);
        } else {
            $(this).parent().find(".warning").hide();
			$("#save").prop("disabled",false);
        }
    });
});
 /////////// function to get city on the basis of state
 function checkDupliCcode(val){
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{pcode:val},
		success:function(data){
	      //// if string found then alert
		  if(data>0){
			  //alert("Duplicate product code");
			  $("#dupli").show();
			  $("#save").prop("disabled",true);
			  //$('#docstr').val('');
		  }else{
			  $("#dupli").hide();
			  $("#save").prop("disabled",false);
		  }
	    }
	  });
   
 }
 </script>
 
 
 

<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>

<!--  jquery for multiselect-->
<script src="jquery_tree/lib/jquery-1.11.3.min.js"></script>
 <script src="jquery_tree/lib/jquery-ui.min.js"></script>
 <script src="jquery_tree/src/jquery.tree-multiselect.js"></script>
 <style>
.red_small{
	color:red;
}
.warning,.warning2 {
    color:#d2232a;
    -webkit-border-radius: 12px; 
    border-radius: 12px;
    background-color:#ffdd97;
    padding:5px;
    width:100%;
    display:none;
}


</style>
 
<style>
      * {
        font-family: sans-serif;
      }
    </style>
    <link rel="stylesheet" href="jquery_tree/dist/jquery.tree-multiselect.min.css">
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-tablet"></i>&nbsp;&nbsp;Upload IMEI                                                
 </h2><br/><br/>
     
       
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
     
          <form name="update_permission" id="update_permission" class="form-horizontal" action="" method="post">
          <div class="form-group">
                                                <div class="col-md-12" >
                                             <div class="col-md-6">   
                                           <label class="col-md-6 control-label">Model</label> 
                                           </div>
                                           <div class="col-md-6">
                                           <label class="col-md-6 control-label">Required Quantity</label>   </div>    
                                              </div>  </div>
          
          <?php 
		  $bill=mysqli_query($link1, "select * from billing_model_data where challan_no='".$invoice."' order by id");
	  while($res=mysqli_fetch_assoc($bill)){
	  ?>  <div class="form-group">
                                                <div class="col-md-12" >
                                                 <div class="col-md-6">
                                            <input type="text" name="model" id="model<?=$res['prod_code']?>" value="<?php echo str_replace("~",",",getProductDetails($res['prod_code'],"productname,productcolor",$link1));?>" class="form-control" readonly />     
                                          </div>
                                           <div class="col-md-6">
                                            <input type="text" name="qty" id="qty<?=$res['prod_code']?>" value="<?php echo $res['qty'];?>" class="form-control" readonly />   </div>    
                                              </div>  </div><?php  } ?>
                                                
          							 											
                                                <div class="form-group" style="height:360px;overflow-y:scroll;">
                                                <div class="col-md-12" >
												<?php $sno=0;?>
                                                 <select id="test-select" multiple="multiple" name="imei[]" class="imei[<?php echo $sno;?>]" onchange= "check_product();" required >
                                                 <?php
												
												  $sql=mysqli_query($link1,"select * from billing_imei_data where owner_code='".$srow['from_location']."' and prod_code in(select prod_code from billing_model_data where challan_no='".$invoice."' order by id)");
												   while($row=mysqli_fetch_assoc($sql)){
				
										 //$chek_owner=mysqli_fetch_assoc(mysqli_query($link1,"select owner_code,doc_no from billing_imei_data where imei1='".$row['imei1']."' order by id desc"));
				 
				  //$chek_rcvin=mysqli_fetch_assoc(mysqli_query($link1,"select status from billing_master where challan_no='".$chek_owner['doc_no']."'"));
				  /*if($chek_rcvin['status']==""){
				  $chek_rcvin2=mysqli_fetch_assoc(mysqli_query($link1,"select status from opening_stock_master where doc_no='".$chek_owner['doc_no']."'"));
					  $checkstatus=$chek_rcvin2['status'];
				  }else{
					 $checkstatus=$chek_rcvin1['status'];
				  }*/
			      //if($chek_owner['owner_code']==$row['owner_code'] && $checkstatus=="Received"){
				  //$sno=$sno+1;
				  //$locdet=explode("~",getLocationDetails($row['owner_code'],"name,city,state,id_type",$link1));
	              //$proddet=str_replace("~",",",getProductDetails($row['prod_code'],"productname,productcolor",$link1));?>
											
      <option value="<?php echo $row['id']."~".$row['prod_code'];?>" data-section="<?php echo str_replace("~",",",getProductDetails($row['prod_code'],"productname,productcolor",$link1));?>"><?php echo $row['imei1'];?></option>
    
     
      <?php //}
	  $sno=$sno+1; } ?>
     </select>
    
        
                             </div>
                                              </div>
                                                
                                                 <div class="form-actions" align="center">
												<div class="row">
													<div class="col-md-12">
														<button class="btn btn-primary" type="submit" name="submit" value="Save" id="save"  >
                                                      
															<i class="fa fa-save"></i>
															Submit
														</button>
                                                         &nbsp;&nbsp;&nbsp;
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='retailbillinglist.php?<?=$pagenav?>'">
                                                        <!--<a class="btn btn-default" name="cancel" value="Cancel" onClick="reset_form();">
															Cancel
														</a>-->
													</div>
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

 <script type="text/javascript">
	var jq = $.noConflict();
     jq("#test-select").treeMultiselect({ enableSelectAll: true, sortable: true });
    </script>
    
    <script type="text/javascript" language="javascript">
///// function to get price of product
//function check_product(){
	/*
	if(document.getElementById('save').disabled==true)
	{
	 document.getElementById('save').disabled=false;
	}
var fld = document.getElementById('test-select');
//var values = [];
var partcode= Array();
for (var i = 0; i < fld.options.length; i++) {
  if (fld.options[i].selected) {
    var getval=(fld.options[i].value).split("~");
	partcode.push(getval[1]);
	//values.push(fld.options[i].value);
	//alert('  you selected' + fld.options[i].value); 
  }
}
partcode.sort();
var current = null;
    var cnt = 0;
    for (var i = 0; i < partcode.length; i++) {
        if (partcode[i] != current) {
            if (cnt > 0) {
				var billqty=document.getElementById("qty"+current).value;
				if(parseInt(billqty)==parseInt(cnt)){
					//alert("match");
					
					
				}else{
					alert("Quantity mismatch");
					document.getElementById('save').disabled=true;
					
					
					return false;
					
				}
                //document.write(current + ' comes --> ' + cnt + ' times<br>');
            }
            current = partcode[i];
            cnt = 1;
        } else {
            cnt++;
        }
    }
    if (cnt > 0) {
		var billqty=document.getElementById("qty"+current).value;
		if(parseInt(billqty)==parseInt(cnt)){
			//alert("match");
			
			
		}else{
			alert("Quantity mismatch");
			document.getElementById('save').disabled=true;
			
			return false;
		}
        //document.write(current + ' comes --> ' + cnt + ' times');
    }
	
*/
//}
</script>
</body>
</html>