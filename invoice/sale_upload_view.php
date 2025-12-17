<?php
require_once("../config/config.php");
$docno = base64_decode($_REQUEST['id']);
////// get details
$res_salupd = mysqli_query($link1,"SELECT * FROM sale_uploader WHERE doc_no='".$docno."'");
$row_salupd = mysqli_fetch_assoc($res_salupd);
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
  <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
$(document).ready(function(){
    $('#subloc-grid').dataTable();
});
</script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
		<?php 
    	include("../includes/leftnav2.php");
    	?>
		<div class="<?=$screenwidth?> tab-pane fade in active" id="home">
        	<h2 align="center"><i class="fa fa-cubes"></i> Uploaded <?=$row_salupd['sale_type']?> Sale View</h2>
            <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-info">
                        <div class="panel-heading"><i class="fa fa-building" aria-hidden="true"></i> Basic Details</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-md-6"><label class="col-md-5">From Location</label>
                                    <div class="col-md-7">
                                        <?=str_replace("~"," , ",getAnyDetails($row_salupd["from_location"],"name,city,state,asc_code","asc_code","asc_master",$link1))?>
                                    </div>
                                </div>
                                <div class="col-md-6"><label class="col-md-4">To Location</label>
                                    <div class="col-md-7">
                                        <?=str_replace("~"," , ",getAnyDetails($row_salupd["to_location"],"name,city,state,asc_code","asc_code","asc_master",$link1))?>
                                        <br/>
                                        <?=$row_salupd["to_location_sapcode"]?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 alert-warning"><label class="col-md-5">Document No.</label>
                                    <div class="col-md-7">
                                        <?=$row_salupd["doc_no"]?>
                                    </div>
                                </div>
                                <div class="col-md-6"><label class="col-md-4">Document Date</label>
                                    <div class="col-md-7">
                                        <?=$row_salupd["doc_date"]?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6"><label class="col-md-5">Entry By</label>
                                    <div class="col-md-7">
                                        <?=getAnyDetails($row_salupd["entry_by"],"name","username","admin_users",$link1)." ".$row_salupd["entry_by"]?>
                                    </div>
                                </div>
                                <div class="col-md-6"><label class="col-md-4">Entry Date</label>
                                    <div class="col-md-7">
                                        <?=$row_salupd["entry_date"]?>
                                    </div>
                                </div>
                            </div>
                            <?php if($row_salupd["receive_by"]){?>
                            <div class="form-group">
                                <div class="col-md-6"><label class="col-md-5">Receive By</label>
                                    <div class="col-md-7">
                                        <?=getAnyDetails($row_salupd["receive_by"],"name","username","admin_users",$link1)." ".$row_salupd["receive_by"]?>
                                    </div>
                                </div>
                                <div class="col-md-6"><label class="col-md-4">Receive Date</label>
                                    <div class="col-md-7">
                                        <?=$row_salupd["receive_date"]?>
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
            	<?php 
				$arr_modelws = array();
				$imeiquery="SELECT * FROM sale_uploader WHERE doc_no='".$docno."' ORDER BY prod_code";
				$imeiresult=mysqli_query($link1,$imeiquery);
				while($imeirow=mysqli_fetch_array($imeiresult)){
					$arr_modelws[$imeirow['prod_code']][] = $imeirow['serial_no1'];
				}
				foreach($arr_modelws as $prodcode => $serialarr){
					$product_name = explode("~", getProductDetails($prodcode, "productname,productcolor,sap_code", $link1));
				?>
                <div class="col-sm-6">
                    <div class="panel panel-success">
                        <div class="panel-heading"><i class="fa fa-cube" aria-hidden="true"></i> <?php echo "<b>Model Name: </b>".$product_name[0]." / ".$prodcode."  SAP Code -: ".$product_name[2];?></div>
                        <div class="panel-body">
                        	<?php for($i=0; $i<count($serialarr); $i++){ ?>
                            <div class="form-group">
                				<div class="col-sm-2"><?=($i+1)?></div>
                                <div class="col-sm-4"><?=$serialarr[$i]?></div>
                            </div>
                            <?php }?>
                        </div>
                    </div>
                </div>
                <?php }?>
            </div>
            <div class="form-group">
                <div class="col-md-12" align="center">
                    <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='sale_uploader.php?<?=$pagenav?>'">
                </div>
            </div>
			</form>
    	</div><!--close tab pane-->
	</div><!--close row content-->
</div><!--close container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>