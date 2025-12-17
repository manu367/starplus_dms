<?php
require_once("../config/config.php");
$date=date("Y-m-d");
@extract($_GET);
##seleceted date
if($fdate=='' || $tdate=='')
{
	$sql_date='1';
}
else
{
    $sql_date="(expense_date>='".$fdate."' and expense_date<='".$tdate."')";
}


if($location == ''){
$location = '';
}
else {
  $location = "and location_code = '".$location."' ";
}


?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery-1.10.1.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
	
});
</script>
 <link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
<script>
$(document).ready(function () {
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});
$(document).ready(function () {
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});
</script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-inr"></i>&nbsp;Locationwise Expense</h2>
      <?php if($_REQUEST[msg]){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      <?php }?>
	  <form id="frm1" name="frm1" class="form-horizontal" action="" method="get">
	  
	  
	<div class="form-group">

         <div class="col-md-6"><label class="col-md-5 control-label"> From Date</label>	  

			<div class="col-md-5" align="left">

			   <div ><input type="text" class="form-control span2" name="fdate"  id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			 

            </div>

          </div>

		  <div class="col-md-6"><label class="col-md-5 control-label">To Date</label>	  

			<div class="col-md-5" align="left">
             
			 <div><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $date;}?>"style="width:160px;" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--> </div>
			  

			  
            </div>

         </div>

	    </div>

        <div class="form-group">

          <div class="col-md-6"><label class="col-md-5 control-label">Location</label>

            <div class="col-md-5">

            <select name="location" id="location"  class="form-control selectpicker " data-live-search="true" >
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y'";
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

		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  

			<div class="col-md-5" align="left">
			
			 <input name="Submit" type="submit" class="btn <?=$btncolor?>" value="GO"  title="Go!">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>  

              


            </div>

          </div>

	    </div><!--close form group-->
		</form>
		
		<?php if($_REQUEST['Submit']) { ?>
	    <div class="form-group">

          <div class="col-md-6"><label class="col-md-5 control-label"></label>

            <div class="col-md-5">

               

            </div>

          </div>

		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  

			<div class="col-md-5" align="left">

               <a href="excelexport.php?rname=<?=base64_encode("expense_report")?>&rheader=<?=base64_encode("Expense Report")?>&fromdate=<?=base64_encode($_GET['fdate'])?>&todate=<?=base64_encode($_GET['tdate'])?>&loc=<?=base64_encode($_GET['location'])?>" title="Export expense details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export expense details in excel"></i></a>


            </div>

          </div>

	    </div><!--close form group-->
	  
	  
	   <?php
	    }
       ?>
		
      <form class="form-horizontal" role="form">
	   <div class="col-md-12"><label class="col-md-5 control-label"></label>	  
			
        <button title="Add New Expense" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='add_expense.php?op=add<?=$pagenav?>'"><span>Add New Expense</span></button>&nbsp;&nbsp; 
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th><a href="#" name="entity_id" title="asc" ></a>#</th>
			  <th ><a href="#" name="entity_id" title="asc" ></a>Doc No.</th>
			  <th ><a href="#" name="entity_id" title="asc" ></a>Location</th>
              <th><a href="#" name="name" title="asc" class="not-sort"></a>Amount</th>
              
              <th><a href="#" name="name" title="asc" ></a>Expense Date</th>
              <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Narration </th>
			  <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Status</th>
			  <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Payment Mode</th>
			  <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Attachment</th>
			  <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>View</th>

			  
             
			  
              
            </tr>
          </thead>
          <tbody>
             <?php $i=1;
			 
	 	$sql1 = "SELECT * FROM locationwise_expense  where $sql_date $location and entry_by = '".$_SESSION['userid']."' order by id desc ";
       $rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	   while($row1=mysqli_fetch_assoc($rs1)) { ?>
	    <tr class="even pointer">
		<td><?php echo $i ;?><div align="left"></div></td>
		<td><?php echo $row1['doc_no']?></td>
		 <td><?php echo getLocationDetails($row1['location_code'],"name",$link1);?></td>
		<td > <?php echo $row1['amount']?></td>        
          <td ><?php echo $row1['expense_date']?></td>
		  <td ><?php echo $row1['narration']?></td>
		  <td ><?php echo $row1['status']?></td>
		  <td ><?php echo $row1['payment_mode']?></td>
		  <td ><?php if($row1['attachment'] != ''){?><a href='<?=$row1['attachment']?>' title='view' target='_blank'><i class='fa fa-download ' title='Download Image'></i></a><?php }?></td>
          
          <td align="center"><a href='view_expense.php?op=edit&id=<?php echo base64_encode($row1['id']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
		 <!-- <td align="center"><a href='reply_query.php?op=reply&id=<?php echo base64_encode($row1['id']);?><?=$pagenav?>'  title='reply'><i class="fa fa-edit fa-lg" title="reply"></i></a></td>-->
            </tr>
	   <?php 
	  $i++;
	   }?>
	   
	  
          </tbody>
          </table>
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

<?php
 mysqli_query($link1,"insert into asc_master");

?>
