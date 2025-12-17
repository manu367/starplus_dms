<?php
require_once("dbconnect_cansaledms.php");
require_once("../includes/common_function.php");
require_once("../includes/globalvariables.php");
$uid = base64_decode($_REQUEST['userid']);
///////////////
$filter_str = "";
if($_REQUEST['srch'] !=''){
	$filter_str	.= " AND (partcode LIKE '".$_REQUEST['srch']."%' OR state LIKE '".$_REQUEST['srch']."%')";
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
</head>
<body>
	<div class="container-fluid">
    	<div class="row content">
    		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
       			<h2 align="center"><i class="fa fa-money"></i> Reward Points List</h2>
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
                            <div style="width:58%; float:left; display:inline-block">
                                <input name="srch" id="srch" class="form-control" value="<?=$_REQUEST['srch']?>" placeholder="Enter product/state here to search"/>
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
                    $sql = mysqli_query($link1,"SELECT * FROM reward_points_master WHERE status = 'A' ".$filter_str." ORDER BY id DESC LIMIT 0,10");
                    if(mysqli_num_rows($sql)>0){
                    while($row = mysqli_fetch_assoc($sql)){
                        $sno=$sno+1;
                    ?>
                        <div class="form-group" style="padding-top: 8px; padding-bottom: 8px; border:ridge; margin-right:-5px">
                            <div class="col-md-12">
                                <div style="width:30%; float:left; display:inline-block">
                                    <strong>Partcode</strong>
                                </div>
                                <div  style="width:70%; float:left; display:inline-block">
                                    <span style="color:#428bca"><?php echo $row['partcode'];?></span>
                                </div>
                                <div style="width:30%; float:left; display:inline-block">
                                    <strong>Product Name</strong>
                                </div>
                                <div  style="width:70%; float:left; display:inline-block">
                                    <?php echo getAnyDetails($row['partcode'],"productname","productcode","product_master",$link1);?>
                                </div>
                                <div style="width:30%; float:left; display:inline-block">
                                    <strong>State</strong>
                                </div>
                                <div  style="width:70%; float:left; display:inline-block">
                                    <?php echo $row['state'];?>
                                </div>
                                <div style="width:30%; float:left; display:inline-block">
                                    <strong>Location For</strong>
                                </div>
                                <div  style="width:70%; float:left; display:inline-block">
                                    <?php echo getLocationType($row['id_type'],$link1);?>
                                </div>
                                <div style="width:30%; float:left; display:inline-block">
                                    <strong>Reward Points</strong>
                                </div>
                                <div style="width:70%; float:right; display:inline-block">
                                    <span class="text-success"><?php echo $row['reward_point'];?></span>
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
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
</body>
</html>