<?php
////// Function ID ///////
$fun_id = array("u"=>array(120)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
@extract($_POST);
////// we hit save button
if($_POST){
	if($_POST['upd']=='Save'){   
	//// start transaction
	mysqli_autocommit($link1, false);
	$flag = true;
	$err_msg = "";
	############## Make System generated document no . #####################################
	$res_po = mysqli_query($link1,"SELECT MAX(temp_no) AS no FROM stockconvert_master WHERE location_code='".$partycode."'");
	$row_po = mysqli_fetch_array($res_po);
	$c_nos = $row_po['no']+1;
	$po_no = $partycode."STC".$c_nos; 
	/////
	if($stocktype == 'okqty'){
		 $typestock = "OK";
	}else if ($stocktype == 'missing'){
		$typestock = "MISSING";
	}else if($stocktype == 'damage'){
		$typestock = "DAMAGE";
	}else {
		
	}
	/////
	if($convertstocktypeval == 'okqty'){
		 $contypestock = "OK";
	}else if ($convertstocktypeval == 'missing'){
		$contypestock = "MISSING";
	}else if($convertstocktypeval == 'damage'){
		$contypestock = "DAMAGE";
	}else {
		
	}
    ###################  Insert Master Data
	$query1= "INSERT INTO stockconvert_master SET location_code='".$partycode."',sub_location='".$godown."', temp_no ='".$c_nos."', doc_no='".$po_no."', requested_date='".$today."', entry_date='".$today."', entry_time='".$currtime."', status='Processed', stock_type='".$stocktype." to ".$convertstocktypeval."', create_by = '".$_SESSION['userid']."', entry_by='".$_SESSION['userid']."', ip='".$ip."', remark='".$remark."'";
	$result1 = mysqli_query($link1,$query1);
	//// check if query is not executed
	if (!$result1) {
	     $flag = false;
         $err_msg = "Error details1: " . mysqli_error($link1) . ".";
    }
	$loc_det = explode("~",getLocationDetails($partycode,"state,id_type",$link1));
	#################3 Insert in item data by picking each data row one by one ##############################3
	foreach($prod_code as $k=>$val)
	{   
		############3 checking row value of product and qty should not be blank ###################################
		if($prod_code[$k]!='' && $req_qty[$k]!='' && $req_qty[$k]!=0 ) {
			/////////// insert data
		    $query2 = "INSERT INTO stockconvert_data SET doc_no ='".$po_no."', prod_code='".$val."',to_prod_code='".$to_prod_code[$k]."', qty = '".$req_qty[$k]."', convertstocktype ='".$convertstocktypeval."', stock_type = '".$stocktype."', entry_date = '".$today."'";
		   	$result2 = mysqli_query($link1, $query2);
		   	//// check if query is not executed
		   	if (!$result2) {
	           	$flag = false;
               	$err_msg =  "Error details2: " . mysqli_error($link1) . ".";
           	}
	      	###########3  update inventory in stock status table ############################################################33
		    if(mysqli_num_rows(mysqli_query($link1,"SELECT partcode FROM stock_status WHERE partcode='".$val."' AND asc_code='".$partycode."' AND sub_location='".$godown."' AND $stocktype >0")) >0 ){
				########## deduct stock from main partcode #################################################
				$result_stk = mysqli_query($link1,"UPDATE stock_status SET $stocktype= $stocktype-'".$req_qty[$k]."',updatedate='".$datetime."' WHERE partcode='".$val."' AND asc_code='".$partycode."' AND sub_location='".$godown."'");
				if (!$result_stk) {
					$flag = false;
					$err_msg = "Error details3: " . mysqli_error($link1) . ".";				
				}		
			}
			else{
				$flag = false;
				$err_msg= "Stock is not Available for partcode: ".$val;
				//header("location:stockconvert_list.php?msg=".$err_msg);
				//exit;
			}
			///// update stock in inventory //
			  if(mysqli_num_rows(mysqli_query($link1,"select partcode from stock_status where partcode='".$to_prod_code[$k]."' and asc_code='".$partycode."' and sub_location='".$godown."'"))>0){
				 ///if product is exist in inventory then update its qty 
				 $result3=mysqli_query($link1,"update stock_status set $convertstocktypeval= $convertstocktypeval+'".$req_qty[$k]."',updatedate='".$datetime."' where partcode='".$to_prod_code[$k]."' and asc_code='".$partycode."' AND sub_location='".$godown."'");
			  }
			  else{
				 //// if product is not exist then add in inventory
				$result3=mysqli_query($link1,"insert into stock_status set asc_code='".$partycode."',sub_location='".$godown."',partcode='".$to_prod_code[$k]."',$convertstocktypeval= $convertstocktypeval+'".$req_qty[$k]."',uom='PCS',updatedate='".$datetime."'");
			  }
				//// check if query is not executed
				if (!$result3) {
					$flag = false;
					$err_msg = "Error Code5: ".mysqli_error($link1);
				}
			############ find price & fsp price  of partcode ###################################3##############
			$frompartprice = mysqli_fetch_array(mysqli_query($link1,"SELECT price FROM price_master WHERE product_code = '".$prod_code[$k]."' AND state = '".$loc_det[0]."' AND location_type = '".$loc_det[1]."'"));				  	
			#########3  stockleger entey of from partcode	
			$flag=stockLedger($po_no,$today,$val,$godown,$partycode,$godown ,"OUT",$typestock,"Stock Convert",$req_qty[$k],$frompartprice['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
			#########3  stockleger entey of to partcode	
			$flag=stockLedger($po_no,$today,$to_prod_code[$k],$partycode,$godown,$godown ,"IN",$contypestock,"Stock Convert",$req_qty[$k],$frompartprice['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		} ###########  close if loop of checking row value of product and qty should not be blank
		else{
			$flag = false;
			$err_msg= "Qty Cannot be blank or zero";
			//header("location:stockconvert_list.php?msg=".$err_msg);
			//exit;
		}
	}###################3 close for loop
	###############3 insert in activity table ###############################
	$flag=dailyActivity($_SESSION['userid'],$po_no,"Stock Convert",$stocktype,$ip,$link1,$flag);
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
        $msg = "Stock Convert is successfully placed with document no.".$po_no;
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.".$err_msg;
	} 
    mysqli_close($link1);
	///// move to parent page
	header("location:stockconvert_list.php?msg=".$msg."".$pagenav);
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

 <link rel="stylesheet" href="../css/bootstrap-select.min.css">

 <script src="../js/bootstrap-select.min.js"></script>



 <script type="text/javascript">

$(document).ready(function(){

    $("#frm2").validate();

});



</script>

<script type="text/javascript" src="../js/jquery.validate.js"></script>

<script type="text/javascript" src="../js/common_js.js"></script>

<script type="text/javascript">


/////////// function to get available stock of ho

  function getAvlStk(indx){
      
	  var productCode=document.getElementById("prod_code["+indx+"]").value;

	  var locationCode=$('#location').val();

	  var stocktype= document.getElementById("stocktype").value;
	  
	  var godownid= document.getElementById("godown").value;

	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{locstk:productCode,loccode:locationCode,godown:godownid,stktype:stocktype,indxx:indx},

		success:function(data){
          //alert(data);
			var getdata=data.split("~");

	        document.getElementById("avl_stock["+getdata[1]+"]").value=getdata[0];

	    }

	  });

  }




$(document).ready(function(){
    	$("#add_row").click(function(){
			var numi = document.getElementById("rowno");
			var itm="prod_code["+numi.value+"]";
			var qTy="req_qty["+numi.value+"]";
			var preno=document.getElementById("rowno").value;
			var num = (document.getElementById("rowno").value -1)+ 2;
			if((document.getElementById(itm).value!="" && document.getElementById(qTy).value!="" && document.getElementById(qTy).value!="0") || ($("#addr"+numi.value+":visible").length==0)){
				numi.value = num;				
			var r='<tr id="addr'+num+'"><td><div id="pdtid'+num+'" style="display:inline-block;float:left; width:350px"><select name="prod_code['+num+']" id="prod_code['+num+']" class="form-control selectpicker required" required data-live-search="true" onChange="getAvlStk('+num+');checkDuplicate('+num+',this.value);"><option value="">--None--</option><?php $model_query="select productcode,productname,productcolor from product_master where status='active' order by productcode";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option data-tokens="<?php echo $br['productcode']."|".$br['productname'];?>" value="<?php echo $br['productcode'];?>"><?php echo cleanData($br['productname']).' | '.$br['productcolor'].' | '.$br['productcode'];?></option><?php }?></select></div></td><td><input type="text" class="form-control" name="avl_stock['+num+']" id="avl_stock['+num+']" readonly style="width:100px"></td><td><input type="text" class="form-control digits required" name="req_qty['+num+']" id="req_qty['+num+']" style="width:100px" autocomplete="off" required onkeyup="rowTotal('+num+');"><span id="err_msg['+num+']" name="err_msg['+num+']" class="red_small"></span><div style="display:inline-block;float:right"><i class="fa fa-close fa-lg" onClick="deleteRow('+num+');"></i></div></td><td><div id="tpdtid'+num+'" style="display:inline-block;float:left; width:350px"><select name="to_prod_code['+num+']" id="to_prod_code['+num+']" class="form-control selectpicker" required data-live-search="true"><option value="">--None--</option><?php $model_query="select productcode,productname,productcolor from product_master where status='active' order by productcode";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option data-tokens="<?php echo $br['productcode']."|".$br['productname'];?>" value="<?php echo $br['productcode'];?>"><?php echo cleanData($br['productname']).' | '.$br['productcolor'].' | '.$br['productcode'];?></option><?php }?></select></div></td></tr>';
				$('#itemsTable1').append(r);
				makeSelect();
				//makeCalDeliv(num);            
			}
  		});
	});
	
    function makeSelect(){
  		$('.selectpicker').selectpicker({
			liveSearch: true,
			showSubtext: true
  		});
		
	}


///////////////////////////


////// delete product row///////////

function deleteRow(ind){  

     var id="addr"+ind; 
     var itemid="prod_code"+"["+ind+"]";
	 var qtyid="req_qty"+"["+ind+"]";
	 var toitemid="to_prod_code"+"["+ind+"]";
	 //var toprod_code="prod_code"+"["+ind+"]";

	 // hide fieldset \\

    document.getElementById(id).style.display="none";

	// Reset Value\\

	// Blank the Values \\

	document.getElementById(itemid).value="";

	document.getElementById(qtyid).value="0.00";
	
	document.getElementById(toitemid).value="";

	//document.getElementById(toprod_code).value="";


  rowTotal(ind);

}


/////// calculate line total /////////////

function rowTotal(ind){ 
  var ent_qty="req_qty["+ind+"]";  
  var avl_qty="avl_stock["+ind+"]";
  
 

  ////// check if entered qty is something

  if(document.getElementById(ent_qty).value){ var qty=document.getElementById(ent_qty).value;}else{ var qty=0;}

  //////  check if avl qty is something //////////////////
  
 if(document.getElementById(avl_qty).value){ var avlqty=document.getElementById(avl_qty).value;}else{ var avlqty=0;} 
  

  ////// check entered qty should be available


  if(parseFloat(avlqty) >= parseFloat(qty) ){
  

	  document.getElementById("err_msg["+ind+"]").innerHTML ="";

     calculatetotal();
  }
  else{
	  document.getElementById("err_msg["+ind+"]").innerHTML ="Enter qty  is more than Available Qty";
	  document.getElementById(ent_qty).value = "";

   }
  
}

////// calculate final value of form /////

function calculatetotal(){

    var rowno=(document.getElementById("rowno").value);

	var sum_qty=0;

	

    for(var i=0;i<=rowno;i++){

		var temp_qty="req_qty["+i+"]";

		///// check if line qty is something

        if(document.getElementById(temp_qty).value){ totqty= document.getElementById(temp_qty).value;}else{ totqty=0;}

		sum_qty+=parseFloat(totqty);

	}/// close for loop

    document.getElementById("total_qty").value=sum_qty;
}


</script>

<script type="text/javascript">

            ///// function for checking duplicate Product value

            function checkDuplicate(fldIndx1, enteredsno) {  

			 document.getElementById("upd").disabled = false;

                if (enteredsno != '') {

                    var check2 = "prod_code[" + fldIndx1 + "]";
					

                    var flag = 1;

                    for (var i = 0; i <= fldIndx1; i++) {

                        var check1 = "prod_code[" + i + "]";
						

                        if (fldIndx1 != i && document.getElementById(check2).value != '' && document.getElementById(check1).value != ''){
                            if ((document.getElementById(check2).value == document.getElementById(check1).value)) {

                                alert("Duplicate Product Selection.");

                                document.getElementById(check2).value = '';
								document.getElementById("avl_stock[" + fldIndx1 + "]").value = '';
								document.getElementById("req_qty[" + fldIndx1 + "]").value = '';

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

</head>

<body onKeyPress="return keyPressed(event);">

<div class="container-fluid">

  <div class="row content">

	<?php 

    include("../includes/leftnav2.php");

    ?>

    <div class="col-sm-9">

      <h2 align="center"><i class="fa fa-bars"></i> Stock Convert </h2><br/>

      <div class="form-group" id="page-wrap" style="margin-left:10px;">

          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">

          <div class="form-group">
            <div class="col-md-10"><label class="col-md-5 control-label">Location <span style="color:#F00">*</span></label>
              <div class="col-md-7">
                 <select name="location" id="location" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
                    <?php 

					$sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y' AND id_type IN ('HO','BR')";
					$res_chl=mysqli_query($link1,$sql_chl);
					while($result_chl=mysqli_fetch_array($res_chl)){
	                      $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));

                          ?>

                    <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['location'])echo "selected";?> >

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
            <div class="col-md-10"><label class="col-md-5 control-label">Cost Centre(Godown)<span style="color:#F00">*</span></label>
              <div class="col-md-7">
                 <select name="godown" id="godown" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
                     <?php                                 
                    $smfm_sql = "SELECT asc_code, name, city, state, id_type FROM asc_master WHERE asc_code='".$_REQUEST['location']."'";
                    $smfm_res = mysqli_query($link1,$smfm_sql);
                    while($smfm_row = mysqli_fetch_array($smfm_res)){
                    ?>
                    <option value="<?=$smfm_row['asc_code']?>" <?php if($smfm_row['asc_code']==$_REQUEST['godown'])echo "selected";?>><?=$smfm_row['name']." | ".$smfm_row['city']." | ".$smfm_row['state']." | ".$smfm_row['asc_code']?></option>
                    <?php
                    }
                    ?>
                    <?php                                 
                    $smf_sql = "SELECT sub_location, sub_location_name FROM sub_location_master WHERE main_location='".$_REQUEST['location']."' AND status='Active'";
                    $smf_res = mysqli_query($link1,$smf_sql);
                    while($smf_row = mysqli_fetch_array($smf_res)){
                    ?>
                    <option value="<?=$smf_row['sub_location']?>" <?php if($smf_row['sub_location']==$_REQUEST['godown'])echo "selected";?>><?=$smf_row['sub_location_name']." | ".$smf_row['sub_location']?></option>
                    <?php
                    }
					
                    ?>
                </select>
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="col-md-10"><label class="col-md-5 control-label"> Stock Type <span style="color:#F00">*</span></label>
              <div class="col-md-7">
                 <select name="stocktype" id="stocktype" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
					<option value="okqty" <?php if($_REQUEST['stocktype'] == "okqty"){ echo "selected";} ?>>OK</option>
					<option value="missing" <?php if($_REQUEST['stocktype'] == "missing"){ echo "selected";} ?>>Missing</option>
					<option value="broken" <?php if($_REQUEST['stocktype'] == "broken"){ echo "selected";} ?>>Damage</option>
                 </select>
              </div>
            </div>
          </div>
		  
		  <div class="form-group">
            <div class="col-md-10"><label class="col-md-5 control-label"> Convert As Stock Type <span style="color:#F00">*</span></label>
              <div class="col-md-7">
                 <select name="convertstocktype" id="convertstocktype" required class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">Please Select </option>
					<option value="okqty" <?php if($_REQUEST['convertstocktype'] == "okqty"){ echo "selected";} ?>>OK</option>
					<option value="missing" <?php if($_REQUEST['convertstocktype'] == "missing"){ echo "selected";} ?>>Missing</option>
					<option value="broken" <?php if($_REQUEST['convertstocktype'] == "broken"){ echo "selected";} ?>>Damage</option>
                 </select>
              </div>
            </div>
          </div>
		  

         </form>

         <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">

          <div class="form-group">

          <table width="100%" id="itemsTable1" class="table table-bordered table-hover">

            <thead>

              <tr class="<?=$tableheadcolor?>" >

                <th data-class="expand" class="col-md-1" style="font-size:13px;">From Product Code</th>

                <th data-hide="phone"  class="col-md-1" style="font-size:13px">Avl Qty</th>

                <th data-hide="phone"  class="col-md-1" style="font-size:13px">Qty</th>

                <th data-hide="phone"  class="col-md-1" style="font-size:13px">To Product Code</th>
              </tr>
            </thead>

            <tbody>

              <tr id='addr0'>

                <td class="col-md-1">

                    <div id="pdtid0">

                  <select name="prod_code[0]" id="prod_code[0]" class="form-control selectpicker required"  style="width:200px;" required data-live-search="true" onChange="getAvlStk(0);checkDuplicate(0, this.value);" >

                    <option value="">--None--</option>

                    <?php 

				$model_query="select productcode,productname,productcolor from product_master where status='active' order by productcode";

			        $check1=mysqli_query($link1,$model_query);

			        while($br = mysqli_fetch_array($check1)){?>

                    <option data-tokens="<?php echo $br['productname']."|".$br['productcode'];?>" value="<?php echo $br['productcode'];?>"><?php echo $br['productname'].' | '.$br['productcolor'].' | '.$br['productcode'];?></option>

                    <?php }?>
                  </select>
                    </div>

                    </td>

                <td class="col-md-1"><input type="text" class="form-control" name="avl_stock[0]" id="avl_stock[0]" readonly style="width:100px" autocomplete="off" ></td>

                <td class="col-md-1"><input type="text" class="form-control digits" name="req_qty[0]" id="req_qty[0]"  style="width:100px" autocomplete="off" required onKeyUp="rowTotal(0);"><span id="err_msg[0]" name="err_msg[0]" class="red_small"></span></td>

                
                

                <td class="col-md-1"><div id="tpdtid0">

                  <select name="to_prod_code[0]" id="to_prod_code[0]" class="form-control selectpicker required"  style="width:200px;"  required data-live-search="true">

                    <option value="">--None--</option>

                    <?php 
					$model_query="select productcode,productname,productcolor from product_master where status='active' order by productcode";
			        $check1=mysqli_query($link1,$model_query);
			        while($br = mysqli_fetch_array($check1)){?>
                    <option data-tokens="<?php echo $br['productname']."|".$br['productcode'];?>" value="<?php echo $br['productcode'];?>"><?php echo $br['productname'].' | '.$br['productcolor'].' | '.$br['productcode'];?></option>
                    <?php }?>
                  </select>
                    </div>

                    </td>
              </tr>
            </tbody>

            <tfoot id='productfooter' style="z-index:-9999;">

              <tr class="0">

                <td colspan="10" style="font-size:13px;"><a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a><input type="hidden" name="rowno" id="rowno" value="0"/></td>
              </tr>
            </tfoot>
          </table>

          </div>

          <div class="form-group">

            <div class="col-md-10">

              <label class="col-md-3 control-label">Total Qty</label>

              <div class="col-md-3">

                <input type="text" name="total_qty" id="total_qty" class="form-control" value="0" readonly/>

              </div>

              <label class="col-md-3 control-label">Remark</label>

              <div class="col-md-3">

               <textarea name="remark" id="remark" class="form-control addressfield" style="resize:none"></textarea>

              </div>

            </div>

          </div>

 
          <div class="form-group">

            <div class="col-md-12" align="center">

              <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Save" title="Save">
                <input type="hidden" name="partycode" id="partycode" value="<?=$_REQUEST['location']?>"/>
				<input type="hidden" name="stocktype" id="stocktype" value="<?=$_REQUEST['stocktype']?>"/>
                <input type="hidden" name="godown" id="godown" value="<?=$_REQUEST['godown']?>"/>
				<input type="hidden" name="convertstocktypeval" id="convertstocktypeval" value="<?=$_REQUEST['convertstocktype']?>" >
                  <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='stockconvert_list.php?<?=$pagenav?>'">

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

<?php if($_REQUEST['location']=='' || $_REQUEST['stocktype']=='' || $_REQUEST['convertstocktype']=='' || $_REQUEST['godown']==''){ ?>

<script>

$("#frm2").find("input[type='submit']:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");

</script>

<?php } ?>