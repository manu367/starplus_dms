<?php
require_once("dbconnect_cansaledms.php");
require_once("../includes/common_function.php");
require_once("../includes/globalvariables.php");
//$uid = base64_decode($_REQUEST['userid']);
///////////////
$filter_str = "";
if($_REQUEST['srch'] !=''){
	$filter_str	.= " AND (system_ref_no = '".$_REQUEST['srch']."' OR location_code = '".$_REQUEST['srch']."')";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=siteTitle?></title>
<link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
<script src="../js/jquery.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<script>
function openModel(docid){
	$.get('rewardburninfo.php?id=' + docid, function(html){
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
       			<h2 align="center"><i class="fa fa-history"></i> Redeemed Points List</h2>
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
                                <button title="Back" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='redeemptionpage.php?&usercode=<?=$_REQUEST['usercode']?>'"><i class="fa fa-reply fa-lg"></i>&nbsp;&nbsp;Back</button>
                                </div>
                                <div style="width:55%; float:left; display:inline-block">
                                    <input name="srch" id="srch" class="form-control" value="<?=$_REQUEST['srch']?>" placeholder="Enter Ref. No. here to search"/>
                                </div>
                                <div style="width:20%; float:right; display:inline-block">
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
                    $sql = mysqli_query($link1,"SELECT * FROM reward_redemption_master WHERE entry_by='".$_REQUEST['usercode']."' ".$filter_str." ORDER BY id DESC LIMIT 0,10");
                    if(mysqli_num_rows($sql)>0){
                    while($row = mysqli_fetch_assoc($sql)){
                        $sno=$sno+1;
                    ?>
                        <div class="form-group" style="padding-top: 8px; padding-bottom: 8px; border:ridge; margin-right:-5px">
                            <div class="col-md-12">
                                <div style="width:30%; float:left; display:inline-block">
                                    <strong>Ref. No.</strong>
                                </div>
                                <div  style="width:70%; float:left; display:inline-block">
                                    <span style="color:#428bca"><?php echo $row['system_ref_no'];?></span>
                                </div>
                                <div style="width:30%; float:left; display:inline-block">
                                    <strong>Location Code</strong>
                                </div>
                                <div  style="width:70%; float:left; display:inline-block">
                                    <?php echo $row['location_code'];?>&nbsp;
                                </div>
                                <div style="width:30%; float:left; display:inline-block">
                                    <strong>Address</strong>
                                </div>
                                <div  style="width:70%; float:left; display:inline-block">
                                    <?php echo $row['delivery_address'];?>&nbsp;
                                </div>
                                <div style="width:30%; float:left; display:inline-block">
                                    <strong>Status</strong>
                                </div>
                                <div  style="width:70%; float:left; display:inline-block">
                                    <?php echo $row['status'];?>&nbsp;
                                </div>
                                <div style="width:30%; float:left; display:inline-block">
                                    <strong>Redeem Point</strong>
                                </div>
                                <div  style="width:70%; float:left; display:inline-block">
                                    <?php echo $row['total_redeem_reward'];?>&nbsp;
                                </div>
                                <div style="width:30%; float:left; display:inline-block">
                                    <strong>Entry Date</strong>
                                </div>
                                <div  style="width:70%; float:left; display:inline-block">
                                    <?php echo $row['entry_date'];?>
                                </div>
                                <div style="width:30%; float:left; display:inline-block">
                                    <strong>&nbsp;</strong>
                                </div>
                                <div style="width:70%; float:left; display:inline-block">
                                    <button title="view product details" type="button" class="btn<?=$btncolor?>" onClick="openModel('<?=base64_encode($row['system_ref_no']);?>');"><i class="fa fa-eye fa-sm"></i>&nbsp;&nbsp;View</button>
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
                	<h2 class="modal-title" align="center"><i class='fa fa-pencil-square-o'></i>&nbsp; &nbsp;Redeem Point Info</h2>
      				<button type="button" class="close" data-dismiss="modal">&times;</button>
      				
    			</div>
    			<div class="modal-body modal-bodyTH">
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