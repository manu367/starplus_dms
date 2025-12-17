<?php
require_once("dbconnect_cansaledms.php");
require_once("../includes/common_function.php");
require_once("../includes/globalvariables.php");
$uid = base64_decode($_REQUEST['userid']);
///////////////
$filter_str = "";
if($_REQUEST['srch'] !=''){
	$filter_str	.= " AND (doc_no = '".$_REQUEST['srch']."' OR to_location = '".$_REQUEST['srch']."' OR to_location_name='%".$_REQUEST['srch']."%')";
}
///// get location code user
$owner_code = getAnyDetails($uid,"owner_code","username","admin_users",$link1);
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
<script>
function openModel(docid,fromid,toid,docdt){
	$.get('serialSaleInfo.php?id=' + docid + '&fcode=' + fromid + '&tcode=' + toid + '&docdate=' + docdt, function(html){
		 $('#courierModel .modal-body').html(html);
		 $('#courierModel').modal({
			show: true,
			backdrop:"static"
		});
	 });
	 $("#courierModel #close_btn").html('<button type="button" id="btnCancel" class="btn btn-success" data-dismiss="modal"><i class="fa fa-window-close fa-sm"></i> Close</button>');
}
</script>
</head>
<body>
	<div class="container-fluid">
    	<div class="row content">
    		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
       			<h2 align="center"><i class="fa fa-level-down"></i> Receive Stock</h2>
                <div align="center"><button title="view received invoice" type="button" class="btn btn-info" onClick="window.location.href='receivedStockList.php?userid=<?=$_REQUEST['userid']?>'"><i class="fa fa-cube fa-lg"></i>&nbsp;&nbsp;View Received Invoice</button></div><br/>
                <div class="form-group"  id="page-wrap" style="margin-left:10px;">
                    <?php if($_REQUEST['msg']){?>
                    <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
                    </div>
                    <?php 
                        unset($_POST);
                     }?>
                    <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
                        <div class="row">

                                <div style="width:25%; float:left; display:inline-block">
                                    <button title="Back" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='myStockTask.php?&usercode=<?=$uid?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button>
                                </div>
                                <div style="width:58%; float:left; display:inline-block">
                                    <input name="srch" id="srch" class="form-control" value="<?=$_REQUEST['srch']?>" placeholder="Enter Ref. No. here to search"/>
                                </div>
                                <div style="width:15%; float:left; display:inline-block">
                                &nbsp;
                                    <input name="Submit" type="submit" class="btn <?=$btncolor?>" value="GO"  title="Go!">
                            	</div>	
                        </div>
                    </form>
                    <br/>
                    <form class="form-horizontal" role="form">
                    <?php
					//$accesslocation=getAccessLocation($uid,$link1);
                    $sno = 0;
                    $sql = mysqli_query($link1,"SELECT * FROM sale_uploader WHERE to_location='".$owner_code."' ".$filter_str." AND status='Dispatched' AND receive_by='' GROUP BY doc_no,from_location,to_location,doc_date ORDER BY doc_date DESC LIMIT 0,10");
                    if(mysqli_num_rows($sql)>0){
                    while($row = mysqli_fetch_assoc($sql)){
                        $sno=$sno+1;
                    ?>
                        <div class="form-group" style="padding-top: 8px; padding-bottom: 8px; border:ridge; margin-right:-5px">
                            <div class="col-md-12">
                                <div style="width:35%; float:left; display:inline-block">
                                    <strong>Inv. No.</strong>
                                </div>
                                <div  style="width:65%; float:left; display:inline-block">
                                    <span style="color:#428bca"><?php echo $row['doc_no'];?></span>
                                </div>
                                <div style="width:35%; float:left; display:inline-block">
                                    <strong>Inv. Date</strong>
                                </div>
                                <div  style="width:65%; float:left; display:inline-block">
                                    <span style="color:#428bca"><?php echo $row['doc_date'];?></span>
                                </div>
                                <div style="width:35%; float:left; display:inline-block">
                                    <strong>Location Code</strong>
                                </div>
                                <div  style="width:65%; float:left; display:inline-block">
                                    <?php echo $row['from_location'];?>&nbsp;
                                </div>
                                <div style="width:35%; float:left; display:inline-block">
                                    <strong>Location Name</strong>
                                </div>
                                <div  style="width:65%; float:left; display:inline-block">
                                    <?php echo $row['from_location_name'];?>&nbsp;
                                </div>
                                <div style="width:35%; float:left; display:inline-block">
                                    <strong>Status</strong>
                                </div>
                                <div  style="width:65%; float:left; display:inline-block">
                                    <?php echo $row['status'];?>&nbsp;
                                </div>
                                <div style="width:35%; float:left; display:inline-block">
                                    <strong>Entry Remark</strong>
                                </div>
                                <div  style="width:65%; float:left; display:inline-block">
                                    <?php echo $row['entry_rmk'];?>&nbsp;
                                </div>
                                <div style="width:35%; float:left; display:inline-block">
                                    <strong>Entry Date</strong>
                                </div>
                                <div  style="width:65%; float:left; display:inline-block">
                                    <?php echo $row['entry_date'];?>
                                </div>
                                <div style="width:35%; float:left; display:inline-block">
                                    <button title="view serial details" type="button" class="btn<?=$btncolor?>" onClick="openModel('<?=base64_encode($row['doc_no']);?>','<?=base64_encode($row['from_location']);?>','<?=base64_encode($row['to_location']);?>','<?=base64_encode($row['doc_date']);?>');"><i class="fa fa-eye fa-lg"></i>&nbsp;&nbsp;View</button>
                                </div>
                                <div style="width:65%; float:right; display:inline-block" align="right">
                                    <button title="Receive Stock" type="button" class="btn btn-success" onClick="window.location.href='receiveStockAction.php?id=<?php echo base64_encode($row['doc_no']);?>&fcode=<?php echo base64_encode($row['from_location']);?>&tcode=<?php echo base64_encode($row['to_location']);?>&docdate=<?php echo base64_encode($row['doc_date']);?>&userid=<?=$_REQUEST['userid']?>'"><i class="fa fa-level-down fa-lg"></i>&nbsp;&nbsp;Receive</button>
                                </div>
                            </div>
                        </div>
    
                        <?php 
                        }
                        }else{?>
                        <div class="form-group" style="padding-top: 8px; padding-bottom: 8px; border:ridge">
                            <div class="col-md-12" align="center">
                                 No records found 
                            </div>
                        </div>
                        <?php
                        }
                        ?>
                    </form>
                </div>
                <!--End form group--> 
            </div>
            <!--End col-sm-9--> 
        </div>
        <!--End row content--> 
    </div>
    <!--End container fluid-->
    <!-- Start Modal view -->
    <div class="modal modalTH fade" id="courierModel" role="dialog">
        <form class="form-horizontal" role="form" id="frm2" name="frm2" method="post">	
            <div class="modal-dialog modal-dialogTH modal-lg">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title" align="center"><i class='fa fa-pencil-square-o'></i>&nbsp; &nbsp;Serial Info</h2>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        
                    </div>
                    <div class="modal-body modal-bodyTH" style="overflow:auto">
                        <!-- here dynamic task details will show -->
                    </div>
                    <div class="modal-footer" id="close_btn">
          
                    </div> 
                </div>
            </div>
        </form>        
    </div><!--close Modal view -->
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
</body>
</html>
