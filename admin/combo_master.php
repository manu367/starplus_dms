<?php
////// Function ID ///////
$fun_id = array("a"=>array(53));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

if(isset($_REQUEST['status'])){$selstatus=$_REQUEST['status'];}else{$selstatus="";}
if(isset($_REQUEST['bom_model'])){$selbmodel=$_REQUEST['bom_model'];}else{$selbmodel="";}
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
 <script type="text/javascript" language="javascript" >
$(document).ready(function() {
	var dataTable = $('#combo-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"order": [[ 1, "asc" ]],
		"ajax":{
			url :"../pagination/combo-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>", "status": "<?=$selstatus?>", "bom_model": "<?=$selbmodel?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".combo-grid-error").html("");
				$("#combo-grid").append('<tbody class="combo-grid-error"><tr><th colspan="<?php if($apply_app=="Y"){echo "7";}else{ echo "6";}?>">No data found in the server</th></tr></tbody>');
				$("#combo-grid_processing").css("display","none");
				
			}
		}
	} );
});
$(document).ready(function() {
	$('.selectpicker').selectpicker({
      liveSearch: true
	});
});
</script>
<script src="../js/bootstrap-select.js"></script>
<link href="../css/bootstrap-select.css" rel="stylesheet"/>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-cubes"></i> Combo Master</h2>
      <?php if(isset($_REQUEST['msg'])){?>
        <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
        <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Combo Model</label>	  
			<div class="col-md-6" align="left">
				 <select name="bom_model" id="bom_model" class="form-control selectpicker show-tick custom-select">
                 	<option value="">All</option>
                    <?php
					$res = mysqli_query($link1,"SELECT DISTINCT(bom_modelcode) AS bom_model, bom_modelname FROM combo_master WHERE status='1' ORDER BY bom_modelname");
					while($row = mysqli_fetch_assoc($res)){
					?>
                    <option value="<?=$row['bom_model']?>"<?php if($selbmodel==$row['bom_model']){ echo "selected";}?>><?php echo $row['bom_modelname']." (".$row['bom_model'].")";?></option>
                    <?php
					}
					?>
                 </select>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Status</label>
			<div class="col-md-6" align="left">
			 	<select name="status" id="status" class="form-control">
                    <option value=""<?php if($selstatus==''){ echo "selected";}?>>All</option>
                    <option value="1"<?php if($selstatus==1){ echo "selected";}?>>Active</option>
                    <option value="2"<?php if($selstatus==2){ echo "selected";}?>>Deactive</option>
                    <?php if($apply_app=="Y"){?><option value="3"<?php if($selstatus==3){ echo "selected";}?>>Pending For Approval</option><?php }?>
                </select>
            </div>
          </div>
	    </div><!--close form group-->
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="icn" id="icn" type="hidden" value="<?=$_REQUEST['icn']?>"/>
               <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
            </div>
          </div>
<!-- EXCEL  -->
<div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
               <?php
			   ?>
               <a href="excelexport.php?rname=<?=base64_encode("combomaster")?>&rheader=<?=base64_encode("Combo Master")?>&status=<?=base64_encode($_GET['status'])?>&bom_model=<?=base64_encode($_GET['bom_model'])?>" title="Export combo details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export combo details in excel"></i></a>
               <?php
				?>
            </div>
          </div>
<!-- EXCEL -->

	    </div><!--close form group-->
	  </form>
      <form class="form-horizontal" role="form">
        <div style="display:inline-block;float:right"><button title="Upload Combo" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='comboUpload.php?op=Upload<?=$pagenav?>'"><i class="fa fa-upload fa-lg"></i>&nbsp;&nbsp;Upload Combo</button>&nbsp;&nbsp;&nbsp;&nbsp;</div>
        <div style="display:inline-block;float:right"><button title="Add New Combo" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='add_combo.php?op=Add<?=$pagenav?>'"><i class="fa fa-plus-circle fa-lg"></i>&nbsp;&nbsp;Add New Combo</button></div>
        <!--<div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/>-->
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="combo-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>Combo Model Name</th>
              <th>Combo Model Code</th>
              <th>Combo Model HSN</th>
              <th>Status</th>
              <th>View/Edit</th>
              <?php if($apply_app=="Y"){?>
              <th>Approval</th>
              <?php }?>
              <th>Print</th>
            </tr>
          </thead>
          </table>
          <!--</div>-->
      <!--</div>-->
      </form>
    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>