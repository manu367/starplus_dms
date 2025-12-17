<?php
require_once("dbconnect_cansaledms.php");
require_once("../includes/common_function.php");
require_once("../includes/globalvariables.php");
///////////////////
$inv_no = base64_decode($_REQUEST['id']);
$inv_date = base64_decode($_REQUEST['docdate']);
$from_location = base64_decode($_REQUEST['fcode']);
$to_location = base64_decode($_REQUEST['tcode']);


$po_sql="SELECT * FROM sale_uploader WHERE from_location='".$from_location."' AND to_location='".$to_location."' AND doc_no='".$inv_no."' AND doc_date='".$inv_date."' AND status='Dispatched' AND receive_by=''";
$po_res=mysqli_query($link1,$po_sql)or die("er1".mysqli_error($link1));
$po_row=mysqli_fetch_assoc($po_res);
@extract($_POST);
if($_POST) {
	if($_POST['Submit'] == "Receive") {
	mysqli_query($link1,"UPDATE sale_uploader SET status='Received',receive_date='".$datetime."', receive_by='".base64_decode($_REQUEST['userid'])."', receive_rmk='".$rcv_remark."' WHERE from_location='".$from_location."' AND to_location='".$to_location."' AND doc_no='".$inv_no."' AND doc_date='".$inv_date."' AND status='Dispatched' AND receive_by=''");
 	////// insert in activity table////
	dailyActivity($_REQUEST['userid'],$inv_no,$po_row['sale_type']." SALE","RECEIVE",$ip,$link1,"");
	$msg = "Stock is successfully received against document no. <b>".$inv_no."</b>";
	$chkflag = "success";	    	
	$chkmsg = "Success";	
	///// move to parent page
	header("Location:receiveStockList.php?&userid=".$_REQUEST['userid']."&msg=".$msg."&chkflag=".$chkflag."&chkmsg=".$chkmsg);
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
<link rel="shortcut icon" href="../img/titleimg.png" type="image/png">
<script src="../js/jquery.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<script src="../js/jquery.validate.js"></script>
<script type="text/javascript">
$(document).ready(function(){
        //$("#frm1").validate();
		$("#frm1").validate({
			submitHandler: function (form) {
				if(!this.wasSent){
					this.wasSent = true;
					$(':submit', form).val('Please wait...')
								  .attr('disabled', 'disabled')
								  .addClass('disabled');
					//spinner.show();		  
					form.submit();
				} else {
					return false;
				}
			}
		});
    });
var fontSize = 4;
function zoomIn() {
	fontSize += 2;
	document.getElementById("itemdet").style.fontSize = fontSize + "px";
}
function zoomOut() {
	fontSize -= 2;
	document.getElementById("itemdet").style.fontSize = fontSize + "px";
}
</script>
</head>
<body>
	<div class="container-fluid">
    	<div class="row content">
    		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
       			<h2 align="center"><i class="fa fa-level-down"></i> Receive Stock</h2>
                <div class="form-group"  id="page-wrap" style="margin-left:10px;">
                    <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                    	<button title="Back" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='receiveStockList.php?&userid=<?=$_REQUEST['userid']?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button>
                    	<div class="panel-group">
                        	<div class="panel panel-info table-responsive">
                            	<div class="panel-heading">Party Information</div>
                            	<div class="panel-body">
                                <table class="table table-bordered" width="100%" style="font-size:9px;">
                                    <tbody>
                                    	<tr>
                                            <td width="40%"><label class="control-label">Invoice No.</label></td>
                                            <td width="60%"><?=$inv_no;?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Invoice Date</label></td>
                                            <td><?php echo $po_row['doc_date'];?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Sale Type</label></td>
                                            <td><?php echo $po_row['sale_type'];?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">From Location</label></td>
                                            <td><?php echo $po_row['from_location_name'];?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">From Location Code</label></td>
                                            <td><?php echo $po_row['from_location'];?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">To Location</label></td>
                                            <td><?php echo $po_row['to_location_name'];?></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">To Location Code</label></td>
                                            <td><?php echo $po_row['to_location'];?></td>
                                        </tr>
    
                                        <tr>
                                            <td><label class="control-label">Status</label></td>
                                            <td><?php echo $po_row['status'];?></td>
                                        </tr>                                        
                                        <tr>
                                            <td><label class="control-label">Remark</label></td>
                                            <td><?php echo $po_row['entry_rmk'];?></td>
                                        </tr>
                                    </tbody>
                                </table>
                           	  </div><!--close panel body-->
                        	</div><!--close panel-->
                        	<div class="panel panel-info table-responsive">
                            	<div class="panel-heading">Items Information</div>
                                <div class="panel-body">
                                    <input type="button" value="ZOOM IN +" onClick="zoomIn()"/>&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="button" value="ZOOM OUT -" onClick="zoomOut()"/>
                                    <table id="itemdet" class="table table-bordered" width="100%" style="font-size:4px">
                                        <thead>
                                            <tr class="<?=$tableheadcolor?>">
                                                <th width="5%">S.No.</th>
                                                <th width="30%">Serial No.</th>
                                                <th width="25%">Product Code</th>
                                                <th width="40%">Product Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $i=1;
                                        $podata_sql="SELECT prod_code,prod_name,serial_no1 FROM sale_uploader WHERE from_location='".$from_location."' AND to_location='".$to_location."' AND doc_no='".$inv_no."' AND doc_date='".$inv_date."' AND status='Dispatched' AND receive_by=''";
                                        $podata_res=mysqli_query($link1,$podata_sql);
                                        while($podata_row=mysqli_fetch_assoc($podata_res)){
                                         
                                        ?>
                                            <tr>
                                                <td><?=$i?></td>
                                                <td><?=$podata_row['serial_no1']?></td>
                                                <td><?=$podata_row['prod_code']?></td>
                                                <td><?=$podata_row['prod_name']?></td>
                                            </tr>
                                        <?php
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <div class="col-md-12"><label class="col-md-4 control-label">Remark<span class="red_small">*</span></label>
                                        <div class="col-md-6">
                                            <textarea name="rcv_remark" class="form-control required addressfield" id="rcv_remark" required></textarea>
                                        </div>
                                   	</div>
                                </div>
                              	<div class="form-group">
                                	<div class="col-md-12"><label class="col-md-4 control-label">&nbsp;</label>
                                        <div class="col-md-6" align="center">
                                            <button class="btn btn-success" id="save" type="submit" name="Submit" value="Receive" <?php if(isset($_POST['Submit'])){ if($_POST['Submit']=='Receive'){?>disabled<?php }}?>><i class="fa fa-level-down fa-lg"></i>&nbsp;&nbsp;Receive</button>&nbsp;&nbsp;
                                      		<button title="Back" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='receiveStockList.php?&userid=<?=$_REQUEST['userid']?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button>
                                        </div>
                                   	</div>
                                </div>
                                
                          		</div><!--close panel body-->
                        	</div><!--close panel-->
                    	</div><!--close panel group-->
                     </form>   
                </div>
                <!--End form group--> 
            </div>
            <!--End col-sm-9--> 
        </div>
        <!--End row content--> 
    </div>
    <!--End container fluid-->
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
</body>
</html>
