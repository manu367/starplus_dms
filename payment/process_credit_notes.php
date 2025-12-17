<?php
require_once("../config/config.php");
//$_SESSION["messageIdent"]="";
////// initialize filter values
if(isset($_REQUEST['location_code'])){$location_code=$_REQUEST['location_code'];}else{$location_code="";}
if(isset($_REQUEST['status'])){$status=$_REQUEST['status'];}else{$status="";}

if(isset($_REQUEST['fromDate'])){$fromDate=$_REQUEST['fromDate'];}else{$fromDate="";}
if(isset($_REQUEST['toDate'])){$toDate=$_REQUEST['toDate'];}else{$toDate="";}

//////////// get operational rights
//$get_opr_rgts = getOprRights($_SESSION['userid'],$_REQUEST['pid'],$link1);
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/jquery.js"></script>
 <script src="../js/bootstrap.min.js"></script>
 <script type="text/javascript" src="../js/moment.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <!-- datatable plugin-->
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript" language="javascript" >
 
  $(document).ready(function() {
                $('#fromDate').datepicker({
                    format: "yyyy-mm-dd",
                    //startDate: "<?= $row['sale_date'] ?>",
                    endDate: "<?= $today ?>",
                    todayHighlight: true
                  
                });
                $('#toDate').datepicker({
                    format: "yyyy-mm-dd",
                    //startDate: "<?= $row['sale_date'] ?>",
                    endDate: "<?= $today ?>",
                    todayHighlight: true
                   
                });
            });


 
 
$(document).ready(function() {
	var dataTable = $('#creditnotelist-grid').DataTable( {
		"scrollX" :true,
		"processing": true,
		"serverSide": true,
		"order":  [[0,"desc"]],
		"ajax":{
			url :"../pagination/creditnotelist-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "icn": "<?=$_REQUEST['icn']?>", "location_code": "<?=$location_code?>","from_date": "<?=$fromDate?>","to_date": "<?=$toDate?>","status":"<?=$status?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".creditnotelist-grid-error").html("");
				$("#creditnotelist-grid").append('<tbody class="creditnotelist-grid-error"><tr><th colspan="10">No data found in the server</th></tr></tbody>');
				$("#creditnotelist-grid_processing").css("display","none");
				
			}
		}
	} );
} );


</script>

 <link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
   include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa <?=$fa_icon?>"></i> Credit Notes Status</h2>
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
          <div class="col-md-6"><label class="col-md-5 control-label">From Date</label>	  
              <div class="col-md-5" align="left">
                <div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="fromDate" value="<?php if($fromDate){echo $fromDate ;} else {echo $today ;} ?>" id="fromDate" ></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                    </div>
                   </div>
               </div>
            <div class="col-md-6"><label class="col-md-5 control-label">To Date</label>	  
              <div class="col-md-5" align="left">
               <div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="toDate" value="<?php if($toDate){echo $toDate ;} else {echo $today ;} ?>" id="toDate" ></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                 </div>
              </div>
           </div>
         </div><!--close form group-->
         <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">Location</label>
			<div class="col-md-5" align="left">
              <select name="location_code" id="location_code" class="form-control">
                <option value="all" selected="selected">All</option>
                <?php 
						$sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y' AND id_type IN ('HO','BR')";
						$res_chl=mysqli_query($link1,$sql_chl);
						while($result_chl=mysqli_fetch_array($res_chl))
						{
							$party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state from asc_master where asc_code='$result_chl[location_id]'"));
						?>
										<option value="<?=$result_chl['location_id']?>" <?php if($result_chl['location_id']==$_REQUEST['location_code'])echo "selected";?> >
										  <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
										</option>
										<?php
						}
						?>
              </select>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Status</label>
			<div class="col-md-5" align="left">
                 <select name="status" id="status" class="form-control">
                     <option value=""<?php if($_REQUEST['status']=='all'){ echo "selected";}?>>All</option>
                     <option value="Pending For Approval"<?php if($_REQUEST['status']=='Pending For Approval'){ echo "selected";}?>>Pending For Approval</option>
                     <option value="Approved"<?php if($_REQUEST['status']=='Approved'){ echo "selected";}?>>Approved</option>
                     <option value="Rejected"<?php if($_REQUEST['status']=='Rejected'){ echo "selected";}?>>Rejected</option>
                     <option value="Cancelled"<?php if($_REQUEST['status']=='Cancelled'){ echo "selected";}?>>Cancelled</option>
           </select>
            </div>
          </div>
	    </div><!--close form group-->
        
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
			<div class="col-md-5" align="left">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               	<input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                <input name="icn" id="icn" type="hidden" value="<?=$_REQUEST['icn']?>"/>
               	<input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>
			<div class="col-md-5" align="left">
                
            </div>
          </div>
	    </div><!--close form group-->
         
      
	  </form>
      <form class="form-horizontal" role="form">
    <div style="display:inline-block;float:right">
       <button title="Add New" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='cr_notes_itemwise.php?op=add<?=$pagenav?>'"><span>Itemwise Credit Note</span></button>
      &nbsp;&nbsp;&nbsp;&nbsp;</div>
  <br/> <br/>
		<!--<div style="display:inline-block;float:right">
		<button title="Add New" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='cr_notesnew.php?op=add<?=$pagenav?>'"><span>Amountwise Credit Note</span></button></div>-->

      
        <div class="form-group" id="page-wrap" style="margin-left:10px;"><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="creditnotelist-grid" class="table-striped " align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>Tally Sync</th>
              <th>Customer Name</th>
              <th>Location Name</th>
              <th>Entry Date</th>
              <th>System Ref. No.</th>             
              <th>Amount</th>
              <th>Status</th>
              <th>Print</th>
              <th>View</th>
            </tr>
          </thead>
          </table>
        </div>
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
