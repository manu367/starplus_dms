<?php

require_once("../config/config.php");

$toloctiondet=explode("~",getLocationDetails($_REQUEST['stock_to'],"state,id_type,disp_addrs,city",$link1));

$frmloctiondet=explode("~",getLocationDetails($_REQUEST['stock_from'],"state,city",$link1));


$statusarray=$_REQUEST['stock_to'];
$product =$_REQUEST['prod_code'];


@extract($_POST);
////// we hit save button

 if ($_POST['upd']=='Save'){


     /// transcation parameter /////////////////////
	 
	            mysqli_autocommit($link1, false);
                $flag = true;
                $err_msg = "";
				
				///// main for loop start with no. of location count //////////////////
				for($i = 0 ; $i<$_POST['location_count'] ;$i++){
				
				 $stock_to   = explode(",",$_POST['parentcode']);  /////// no.of stcck to store in it //////////////////
				 $product_code = explode(",",$_POST['productcode']); ///// no of productcode store in it////////////////////////
				
				
	 //// Make System generated Invoice no.//////		
            $res_cnt = mysqli_query($link1, "select inv_str,inv_counter , stn_counter , stn_str from document_counter where location_code='" .$partycode . "'");
            if (mysqli_num_rows($res_cnt)) {
                $row_cnt = mysqli_fetch_array($res_cnt);
                
               
                ///// Insert Master Data
               
				if($_POST['doc_type'] == 'DC'){
				$doctype =  "Delivery Challan";
				$invcnt = $row_cnt['stn_counter'] + 1;
                $pad = str_pad($invcnt, 4, 0, STR_PAD_LEFT);
                $invno = $row_cnt['stn_str'] . $pad;
				
			$result = mysqli_query($link1, "update document_counter set stn_counter=stn_counter+1,update_by='" . $_SESSION['userid'] . "',updatedate='" . $datetime . "' where location_code='" . $partycode. "'");
                //// check if query is not executed
                       if (!$result) {
                          $flag = false;
                          $err_msg = "Error Code2:";
                        }
				
				}else {
				$doctype =  "INVOICE";
				$invcnt = $row_cnt['inv_counter'] + 1;
                $pad = str_pad($invcnt, 4, 0, STR_PAD_LEFT);
                $invno = $row_cnt['inv_str'] . $pad;
				
				$result = mysqli_query($link1, "update document_counter set inv_counter=inv_counter+1,update_by='" . $_SESSION['userid'] . "',updatedate='" . $datetime . "' where location_code='" . $partycode. "'");
                //// check if query is not executed
					 if (!$result) {
						$flag = false;
						$err_msg = "Error Code2:";
					 }
  
					 
				
				}


	///// Insert in item data  on basis of product count//////////////////////////////////
      $value = "";
	  $totalvalue = "";
	  for($j =0 ; $j<$_POST['productcount'] ; $j++)				
	  {   
         ///// fetch product info ///////////////////////////////////////////
		$prodcat=explode("~",getProductDetails($product_code[$j],"productcategory,hsn_code",$link1));
		
		 ///// fetch state of stock to details ///////////		  
		  $toloctiondet=explode("~",getLocationDetails($stock_to[$i],"state,id_type,addrs",$link1)); 
		  
		  ///// fetch price of product code //////////////////////
		   $price_det = mysqli_fetch_array(mysqli_query($link1,"SELECT price,mrp from price_master where product_code='" .$product_code[$j]. "' and state='" .$toloctiondet[0]. "' and location_type='" .$toloctiondet[1]. "'"));
		   
		   $value = ($qty[$i][$j]) * $price_det['price']; //// sum of qty and price /////////////////////
		   $totalvalue = $value; ///// total value /////////////////
		
		if($_POST['doc_type'] == 'INV'){
		    
		   ///// tax calculation ///////////
		   $gst = mysqli_fetch_assoc(mysqli_query($link1, "select sgst,cgst,igst from tax_hsn_master where hsn_code='".$prodcat[1]."'"));
		 
		  if($_POST['fromstate'] == $toloctiondet[0]){
		   $sgst_per = $gst['sgst'];
           $cgst_per = $gst['cgst'];
           $igst_per = '0';
		   $igst_amt =0;
		   $sgst_amt = ($value * $sgst_per)/100;
		   $cgst_amt =  ($value * $cgst_per)/100;
		   $totalvalue = ($value+$sgst_amt+$cgst_amt);
		 
		    }
			else {
		    $sgst_per = 0;
            $cgst_per = 0;
            $igst_per = $gst['igst'];
			$cgst_amt = 0;
			$sgst_amt =0;
			$igst_amt = ($value * $igst_per)/100;
			$totalvalue = ($value+$igst_amt);
		
		      }
           }
		   
		   
			/////////// insert data in billing model data //////////////////////////////

		     $query2 = "insert into billing_model_data set from_location='".$partycode. "',challan_no='" . $invno . "', prod_code='" .$product_code[$j]. "',prod_cat='" . $prodcat[0] . "', qty='" .$qty[$i][$j]. "', mrp='" .$price_det['mrp']. "', price='" .$price_det['price']. "', value='".$value."',sgst_per='" .$sgst_per. "',sgst_amt='" .$sgst_amt."',cgst_per='".$cgst_per."',cgst_amt='".$cgst_amt. "',igst_per='".$igst_per."',igst_amt='".$igst_amt."', totalvalue='" .$totalvalue. "',sale_date='" . $today . "',entry_date='" . $today . "' ";
						
                       $result2 = mysqli_query($link1, $query2);
                        //// check if query is not executed
                        if (!$result2) {
                            $flag = false;
                            $err_msg = "Error Code4:";
                        }
		   ////// hold the PO qty in stock ////
		   $subtotal+=$value;
		   $grandtotal+=$totalvalue;
		   $totalcgst_amt+=$cgst_amt;
		   $totalsgst_amt+=$sgst_amt;
		   $totaligst_amt+=$igst_amt;
		 
		 //////////   inventory check ///////////////////////////////////////////////////////////////////////////////////////////////// 
	      $qtycheck = mysqli_query($link1 , "select okqty from stock_status where partcode = '".$product_code[$j]."' and asc_code = '".$partycode."'  and okqty >= '".$qty[$i][$j]."' ");
		if(mysqli_num_rows($qtycheck)>0) {	
	    $check = mysqli_query($link1, " update stock_status set okqty = okqty-'".$qty[$i][$j]."'  where partcode = '".$product_code[$j]."' and asc_code = '".$partycode."' ");
	      
             //// check if query is not executed
               if (!$check) {
			       $flag = false;
                   $err_msg = "Error Code4:";
               }
	     
	    //////   add stock in okqty of to location /////////////////////////////////
	  $qtycheck_toloc = mysqli_query($link1 , "select okqty from stock_status where partcode = '".$product_code[$j]."' and asc_code = '".$stock_to[$i]."'   ");
	  if(mysqli_num_rows($qtycheck_toloc)>0){
	        $query="UPDATE stock_status set  okqty = okqty+'".$qty[$i][$j]."' where asc_code='".$stock_to[$i]."' and partcode='".$product_code[$j]."'";
	        $result=mysqli_query($link1,$query);
		   }
	  else {
	        $query="insert into stock_status set  okqty = okqty+'".$qty[$i][$j]."' , qty = qty+'".$qty[$i][$j]."' , asc_code='".$stock_to[$i]."' , partcode='".$product_code[$j]."'";
	        $result=mysqli_query($link1,$query);
	
	     }	   
			//// check if query is not executed
			if (!$result) {
				 $flag = false;
				 echo "Error details: " . mysqli_error($link1) . ".";
			}
	   
	   /////// entry in stock ledegr
	   $flag=stockLedger($invno,$today,$product_code[$j], $partycode,$stock_to[$i],$stock_to[$i],"OUT","OK","Stn Distribution",$qty[$i][$j],$price_det['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag); 
		
		 $flag=stockLedger($invno,$today,$product_code[$j], $partycode,$stock_to[$i],$stock_to[$i],"IN","OK","Stn Distribution",$qty[$i][$j],$price_det['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag); 
		 
		 }
		 
		 ///////////////////////////////////////////////////////////////////////////////////////////////////////

  
	}/// close for loop
    
    $tax_amount = $totalcgst_amt+$totalsgst_amt+$totaligst_amt; ///// total tax amount//////////////////////
	
    $from_addr =explode("~",getLocationDetails($partycode,"addrs",$link1));  ////// fetch address of from location /////////////////////////
	
	///// Insert Master Data
    $query1 = "INSERT INTO billing_master set from_location='".$partycode . "',to_location='".$stock_to[$i]."',challan_no='" . $invno . "',sale_date='" . $today . "',entry_date='" . $today . "',entry_time='" . $currtime . "',type='STN Distribution',document_type='".$doctype."',status='Received',entry_by='" . $_SESSION['userid'] . "',basic_cost='" . $subtotal . "',tax_cost='" .$tax_amount . "',total_sgst_amt='" . $totalsgst_amt . "',total_cgst_amt='" . $totalcgst_amt . "',total_igst_amt='" . $totaligst_amt . "',total_cost='" .$grandtotal . "',bill_from='".$partycode. "',bill_topty='".$stock_to[$i]. "',from_addrs='" .$from_addr[0] . "',disp_addrs='" .$from_addr[0]. "',to_addrs='" .$toloctiondet[2]. "',deliv_addrs='" . $toloctiondet[2] . "',billing_rmk='STN Distribution' ";	
				 	
                $result = mysqli_query($link1, $query1);
                //// check if query is not executed
                if (!$result) {
                    $flag = false;
                    $err_msg = "Error Code1:";
                }
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$invno,"STN Distribution","PFA",$ip,$link1,$flag);
	
	$challan_no.=",".$invno;
	}  //// if cindition ends ////////////////////////////////////////////
	
 } ///// main for loop ends ////////////////////////////////////	


	///// check both master and data query are successfully executed

	if ($flag) {

        mysqli_commit($link1);

        $msg = "STN Distribution is successfully placed with ref. no.".$challan_no;

    } else {

		mysqli_rollback($link1);

		$msg = "Request could not be processed. Please try again.";

	} 

    mysqli_close($link1);

	///// move to parent page

    header("location:stndistribution_list.php?msg=".$msg."".$pagenav);

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



 <script type="text/javascript">

	$(document).ready(function(){
	
		$("#frm1").validate();
	
	});

</script>
<script type="text/javascript" src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>

<script type="text/javascript" src="../js/common_js.js"></script>

<script type="text/javascript" src="../js/ajax.js"></script>

<!-- Include multiselect -->
<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>

<script type="text/javascript">

 $(document).ready(function(){

    $("#frm2").validate();

});
 
///// multiselect  for distribution from ///////////

	$(document).ready(function() {
		$('#stock_to').multiselect({
				includeSelectAllOption: true,
				buttonWidth:"200"
		});
	});

  $(document).ready(function() {
	$('#prod_code').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200"
	});
});

 function checkStock(value,prodcode,locindex,prodindex){
 
   var prodcode = prodcode;
   var enterqty = value;
   var loc_index = locindex;
   var prod_index = prodindex;
   var locationCode = document.getElementById("partycode").value ;
    var stocktype="okqty";
	
	 $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{locstk:prodcode,loccode:locationCode,stktype:stocktype,qty:enterqty,locnindex:loc_index,prodtindex:prod_index},

		success:function(data){
		//alert(data);
		var getdata=data.split("~");
		if(parseInt(getdata[0]) < parseInt(getdata[5]) ) {
		 document.getElementById("err_msg["+getdata[6]+"]["+getdata[7]+"]").innerHTML ="Enter qty is more than Avl qty";
	     document.getElementById("qty["+getdata[6]+"]["+getdata[7]+"]").value = '';
          document.getElementById("upd").disabled = true;
		 }
		 else {
		 document.getElementById("upd").disabled = false;
		 document.getElementById("err_msg["+getdata[6]+"]["+getdata[7]+"]").innerHTML  = '';
 
		   }

	    }

	  });
	
	
 
 }
 
</script>



</head>

<body onKeyPress="return keyPressed(event);">

<div class="container-fluid">

  <div class="row content">

	<?php 

    include("../includes/leftnav2.php");

    ?>

    <div class="col-sm-9">

      <h2 align="center"><i class="fa fa-bars"></i> STN Distribution </h2><br/>

      <div   class="form-group" id="page-wrap" style="margin-left:10px;">

          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">

          <div class="form-group">

            <div class="col-md-10"><label class="col-md-5 control-label">Distribution  From <span style="color:#F00">*</span></label>

              <div class="col-md-7">

                 <select name="stock_from" id="stock_from" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">

                    <option value="" selected="selected">Please Select </option>

                    <?php 

					$sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y'";

					$res_chl=mysqli_query($link1,$sql_chl);

					while($result_chl=mysqli_fetch_array($res_chl)){

	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));

	                  

                          ?>

                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['stock_from'])echo "selected";?> >

                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>

                    </option>

                    <?php

						
					}

                    ?>
					
					
					

                 </select>

              </div>

            </div>

          </div>

          <div class="form-group">

            <div class="col-md-10"><label class="col-md-5 control-label">Distribution To <span style="color:#F00">*</span></label>

              <div class="col-md-7">

                 <select name="stock_to[]" id="stock_to" required class="form-control required" multiple="multiple"  onChange="document.frm1.submit();" >

                 

                     <?php 
					  $sql_parent = "select mapped_code,uid from mapped_master where uid='" . $_REQUEST['stock_from'] . "' and status='Y'";

					$res_parent=mysqli_query($link1,$sql_parent);

					while($result_parent=mysqli_fetch_array($res_parent)){

	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='" . $result_parent['mapped_code'] . "'"));
						   if ($party_det[id_type] != 'HO') {

                          ?>

                    <option data-tokens="<?=$party_det['name']." | ".$result_parent['uid']?>" value="<?=$result_parent['mapped_code']?>" 
					
					 <?php for($i=0; $i<count($statusarray); $i++){ if($statusarray[$i]==$result_parent['mapped_code']){ echo "selected";}}?> >

                       <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_parent['mapped_code']?>

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
            <div class="col-md-10"><label class="col-md-5 control-label">Product <span class="red_small">*</span></label>
              <div class="col-md-7">
                <select name="prod_code[]" id="prod_code" class="form-control required " required  multiple="multiple" onChange="document.frm1.submit();" >

                    <?php 

				$model_query="select pm.productcode,pm.productname,pm.productcolor from product_master pm , stock_status  st where pm.status='active' and st.okqty >0 and pm.productcode = st.partcode and asc_code = '".$_REQUEST['stock_from']."'  ";

			        $check1=mysqli_query($link1,$model_query);

			        while($br = mysqli_fetch_array($check1)){?>
                    <option data-tokens="<?php echo $br['productname'];?>" value="<?php echo $br['productcode'];?>" 
					<?php for($i=0; $i<count($product); $i++){ if($product[$i]==$br['productcode']){ echo "selected";}}?>><?php echo $br['productname'].' | '.$br['productcolor'].' | '.$br['productcode'];?></option>

                    <?php }?>

                  </select>
              </div>
            </div>
          </div>
		  
		  
		  <div class="form-group">
            <div class="col-md-10"><label class="col-md-5 control-label">Document Type</label>
              <div class="col-md-7">
                 <select name="doc_type" id="doc_type" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();" >
				 <option value="DC" <?php if($_REQUEST['doc_type'] == "DC") { echo "selected"; }?>>Delivery Challan</option>
                  <option value="INV" <?php if($_REQUEST['doc_type'] == "INV") { echo "selected";}?>>Invoice</option>
                  
                 </select>
              </div>
            </div>
          </div>
		  
		  <div class="form-group">
            <div class="col-md-10"><label class="col-md-5 control-label"></label>
              <div class="col-md-7">
                <input  type="submit" class="btn <?=$btncolor?>" name="Submit" id="Submit" value="Apply" title="Apply"  > 
				
              </div>
            </div>
          </div>
		  
		  
		  
         </form>

<?php if($_REQUEST['Submit']) {?>
         <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">

          <div>
          <div style="float:right; width:60%">
          <table width="100%" id="itemsTable1" class="table table-bordered ">
                    
              <tr class="<?=$tableheadcolor?>" >
                
		    <?php 
			$locstr = "";	
			$arr_stockto = $_REQUEST['stock_to'];
			$arr_prodcodenew = $_REQUEST['prod_code'];
			for($i=0; $i<count($arr_stockto); $i++){			
			?>
                <td width="10px;"><?=getLocationDetails($arr_stockto[$i],"name",$link1);?></td>				
		   <?php 
		   
		     if($locstr){
					$locstr.=",".$arr_stockto[$i];
				}else{
					$locstr.= $arr_stockto[$i];
				}
		   }?>            
           </tr>
 
		
		</table>
				
		<table  width="100%" id="itemsTable1" class="table table-bordered">
			 <?php 
			  for($m=0; $m<count($arr_prodcodenew); $m++){ ?>
			  <tr>
			  <?php 
			  for($k=0; $k<count($arr_stockto); $k++){ ?>
				<td><input type="text" name="qty[<?=$k?>][<?=$m?>]" id="qty[<?=$k?>][<?=$m?>]" class="digits form-control required"   width="40px;"  onBlur="checkStock(this.value,'<?=$arr_prodcodenew[$m]?>','<?=$k?>','<?=$m?>');"  placeholder="Enter Qty"  maxlength="4" required>
				<span id="err_msg[<?=$k?>][<?=$m?>]" class="red_small" ></span>
				</td>
				<?php }?>
			  </tr>
			  <?php }?>
       </table>

		</div>
		
		
		
	  <div  style="float:left;width:40%">
	 <td>
	 <table width="40%"  class="table table-bordered ">	
	 <tr class="<?=$tableheadcolor?>">
	 <td>Product List</td>
	 </tr>
	 </table>
	 </td>
	 
	   <?php 
	        $prodstr = "";
			$arr_prodcode = $_REQUEST['prod_code'];
			
			for($j=0; $j<count($arr_prodcode); $j++){			
			?>
		  <td>
		  <table width="40%"  class="table table-bordered ">	
			  <tr class="<?=$tableheadcolor?>" >
			    <td><?=getProductDetails($arr_prodcode[$j],"productname",$link1)." ( ".$arr_prodcode[$j]." ) "?></td>
              </tr>
			   </table>
			<td>
		  <?php 
		        if($prodstr){
					$prodstr.=",".$arr_prodcode[$j];
				}else{
					$prodstr.= $arr_prodcode[$j];
				}
		  
		  
		  }?>
		
		 
          </div>
       </div>
	   
	   <div class="form-group">

            <div class="col-md-12" align="center">

              <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Save" title="Save STN">
<input type="hidden" name="productcode" id="productcode" value="<?=$prodstr?>"/>
                <input type="hidden" name="partycode" id="partycode" value="<?=$_REQUEST['stock_from']?>"/>
				
				<input type="hidden" name="fromstate" id="fromstate" value="<?=$frmloctiondet[0]?>">
				<input type="hidden" name="parentcode" id="parentcode" value="<?=$locstr?>"/>
				
				
				<input type="hidden" name="location_count" id="location_count" value="<?=$i?>"/>

                <input type="hidden" name="productcount" id="productcount" value="<?=$j?>"/>
                <input type="hidden"  name="doc_type" id="doc_type" value="<?=$_REQUEST['doc_type']?>">
             

            </div>

          </div>
	   
	   
      </form>

<?php }?>
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

