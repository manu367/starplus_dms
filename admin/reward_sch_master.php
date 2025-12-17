<?php
////// Function ID ///////
$fun_id = array("u"=>array(152)); // User:, Location:, Admin:22:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
//////////////////////////
$_SESSION['msgscheme'] = "";
$_SESSION['msgeditscheme'] = "";
$_SESSION['messageIdentSMA'] = "";
////// initialize filter values
if(isset($_REQUEST['status'])){$selstatus=$_REQUEST['status'];}else{$selstatus="";}
//////////// get operational rights
//$get_opr_rgts = getOprRights($_SESSION['userid'],$_REQUEST['pid'],$link1);
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="shortcut icon" href="../img/titleimg.png" type="image/png">
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
	var dataTable = $('#account-grid').DataTable( {
		"responsive": true, 
		"processing": true,
		"serverSide": true,
		"order":  [[0,"asc"]],
		"ajax":{
			url :"../pagination/rwdscheme-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>", "status": "<?=$selstatus?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".account-grid-error").html("");
				$("#account-grid").append('<tbody class="account-grid-error"><tr><th colspan="10">No data found in the server</th></tr></tbody>');
				$("#account-grid_processing").css("display","none");
				
			}
		}
	} );
} );
</script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-gift"></i> Scheme Master</h2>
      <?php if(isset($_REQUEST['msg'])){?>
        <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="post">
	  <div class="row">
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-9">Status</label>
        	<select name="status" id="status" class="form-control">
                <option value=""<?php if(isset($_REQUEST['status'])){if($_REQUEST['status']==''){ echo "selected";}}?>>All</option>
                <option value="Active"<?php if(isset($_REQUEST['status'])){if($_REQUEST['status']=="Active"){ echo "selected";}}?>>Active</option>
                <option value="Deactive"<?php if(isset($_REQUEST['status'])){if($_REQUEST['status']=="Deactive"){ echo "selected";}}?>>Deactive</option>
            </select>
        </div>
        <div class="col-sm-3 col-md-3 col-lg-3"><label class="col-md-6">&nbsp;</label><br/>
            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
			<input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
        </div>
      </div>
      <br/>
      <div class="row">
      	<div class="col-sm-6 col-md-6 col-lg-6">
		   <!-- excel export code here-->
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6">
            <button title="Add New Reward Scheme" type="button" class="btn<?=$btncolor?>" style="float:right;margin-bottom:20px" onClick="window.location.href='add_reward_sch.php?op=add<?=$pagenav?>'"><i class="fa fa-plus-circle fa-lg"></i>&nbsp;&nbsp;Add New Reward Scheme</button>
        </div>
      </div>
	  </form>
      <form class="form-horizontal" role="form">
       <table  width="100%" id="account-grid" class="display table-striped" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>Scheme Code</th>
              <th>Scheme Name</th>
              <th>Scheme Description</th>
              <th>Valid From</th>
              <th>Valid To</th>
              <th>Status</th>
              <th>Attachment</th>
              <th>Mapping</th>
              <th>View/Edit</th>
            </tr>
          </thead>
          </table>
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