<?php
require_once("../config/config.php");
$docid = base64_decode($_REQUEST['id']);

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
 <link href="../css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
	$(document).ready(function(){
		$('#myTable1').dataTable();
	});
	$(document).ready(function(){
		$('#myTable2').dataTable();
	});
 </script>
 </head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-institution"></i> HR. Related </h2>
      <h4 align="center">
          <?=getAnyDetails($docid,'name','username','admin_users',$link1)."  (".$docid.")";?>
        </h4>
		<br><br>
<!---------------- Use for claim list ---------------------->
<div class="panel-group">
<div class="panel panel-info table-responsive">
<div class="panel-heading heading1"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Claim List</div>
<div class="panel-body">

<div class="row">
	<div class="col-sm-12 table-responsive">
    	<table  width="99%" id="myTable1" class="table-striped table-bordered table-hover" align="center">
        	<thead>
            	<tr class="<?=$tableheadcolor?>">
              		<th style="text-align:center;" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
                    <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Claim No.</th>
                    <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Claim Date</th>
                    <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>
                    <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>View</th>
            	</tr>
          	</thead>
          	<tbody>
            <?php
			$sno=0;
			$sql=mysqli_query($link1,"Select * from hrms_claim_request where emp_id ='".$docid."' order by sno desc");
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
			?>
            <tr class="even pointer">
              <td style="text-align:center;" ><?php echo $sno;?></td>
              <td style="text-align:center;" ><?php echo $row['claim_id']; ?></td>
              <td style="text-align:center;" ><?php echo dt_format($row['claim_date']); ?></td>
              <td style="text-align:center;" ><?php echo $row['status']; ?></td>
              <td align="center">
			  	<a href="admin_employee_claims_view.php?id=<?=base64_encode($row['sno']);?>&user=<?=base64_encode($docid);?><?=$pagenav?>" title="View"><i class="fa fa-eye fa-lg" title="View"></i></a>
			  </td>
            </tr>
            <?php }?>
        	</tbody>
    	</table> 
	</div>
</div>

</div>                                                      
</div><!--close panel body-->
</div><!--close panel-->

<!---------------- Use for leave list ---------------------->
<div class="panel-group">
<div class="panel panel-info table-responsive">
<div class="panel-heading heading1"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Leave List</div>
<div class="panel-body">

<div class="row">
	<div class="col-sm-12 table-responsive">
    	<table  width="99%" id="myTable2" class="table-striped table-bordered table-hover" align="center">
        	<thead>
            	<tr class="<?=$tableheadcolor?>">
              		<th style="text-align:center;" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
                    <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Leave Type</th>
					<th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>From Date</th>
					<th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>To Date</th>
                    <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>
                    <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>View</th>
            	</tr>
          	</thead>
          	<tbody>
            <?php
			$sno1=0;
			$sql1=mysqli_query($link1,"Select * from hrms_leave_request where empid ='".$docid."' order by id desc");
			while($row1=mysqli_fetch_assoc($sql1)){
				  $sno1=$sno1+1;
			?>
            <tr class="even pointer">
              <td style="text-align:center;" ><?php echo $sno1;?></td>
              <td style="text-align:center;" ><?php echo $row1['leave_type']; ?></td>
              <td style="text-align:center;" ><?php echo dt_format($row1['from_date']); ?></td>
			  <td style="text-align:center;" ><?php echo dt_format($row1['to_date']); ?></td>
              <td style="text-align:center;" ><?php if($row1['status'] == '3') { echo "Pending for Approval";}else if($row1['status'] == '4') { echo "Approve";} else if($row1['status'] == '5') { echo "Reject" ;} else {  echo "";}?></td>
              <td align="center">
			  	<a href="admin_leave_apply_view.php?id=<?=base64_encode($row1['id']);?>&user=<?=base64_encode($docid);?><?=$pagenav?>" title="View"><i class="fa fa-eye fa-lg" title="View"></i></a>
			  </td>
            </tr>
            <?php }?>
        	</tbody>
    	</table> 
	</div>
</div>

</div>                                                      
</div><!--close panel body-->
</div><!--close panel-->

<form name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
  <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading heading1"><i class="fa fa-inr fa-lg"></i>&nbsp;&nbsp;Salary Slip</div>
         <div class="panel-body">
         
            <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label"> Month <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                    <select id="emp_month" name="emp_month" class="form-control required" required>
                        <option value="" <?php if($_REQUEST['emp_month'] == "") { echo "selected" ;} ?> > -- Please Select -- </option>
                        <option value="01" <?php if($_REQUEST['emp_month']=='01')echo "selected";?>>JAN</option>
                        <option value="02" <?php if($_REQUEST['emp_month']=='02')echo "selected";?>>FEB</option>
                        <option value="03" <?php if($_REQUEST['emp_month']=='03')echo "selected";?>>MAR</option>
                        <option value="04" <?php if($_REQUEST['emp_month']=='04')echo "selected";?>>APR</option>
                        <option value="05" <?php if($_REQUEST['emp_month']=='05')echo "selected";?>>MAY</option>
                        <option value="06" <?php if($_REQUEST['emp_month']=='06')echo "selected";?>>JUN</option>
                        <option value="07" <?php if($_REQUEST['emp_month']=='07')echo "selected";?>>JUL</option>
                        <option value="08" <?php if($_REQUEST['emp_month']=='08')echo "selected";?>>AUG</option>
                        <option value="09" <?php if($_REQUEST['emp_month']=='09')echo "selected";?>>SEP</option>
                        <option value="10" <?php if($_REQUEST['emp_month']=='10')echo "selected";?>>OCT</option>
                        <option value="11" <?php if($_REQUEST['emp_month']=='11')echo "selected";?>>NOV</option>
                        <option value="12" <?php if($_REQUEST['emp_month']=='12')echo "selected";?>>DEC</option>	 
                    </select> 
                  </div>    
              </div>  
            </div>
            
            <div class="form-group">
              <div class="col-md-12" > 
                  <label class="col-md-4 control-label"> Year <span class="red_small">*</span></label> 
                  <div class="col-md-6">
                    <select id="emp_year" name="emp_year" class="form-control required" required>
                        <option value="" <?php if($_REQUEST['emp_year']==""){ echo "selected";}?> > -- Please Select -- </option>
                        <?php
                        $currrent_year=date('Y');
                        $last_year=$currrent_year-1;
                        $sec_last_year=$currrent_year-2;
                        ?>
                        <option value="<?=$sec_last_year?>" <?php if($_REQUEST['emp_year']==$sec_last_year)echo "selected";?>><?=$sec_last_year?></option>
                        <option value="<?=$last_year?>" <?php if($_REQUEST['emp_year']==$last_year)echo "selected";?>><?=$last_year?></option>
                        <option value="<?=$currrent_year?>" <?php if($_REQUEST['emp_year']==$currrent_year)echo "selected";?>><?=$currrent_year?></option>
                    </select> 
                  </div>    
              </div>  
            </div>
            
            <?php if($_REQUEST['submit'] == "Show"){ ?>
            <br>
            <div class="form-group" style="text-align:center;">
                Print Salary sheet : &nbsp;&nbsp;               
                <a href='../print/print_salary_sheet.php?rb=view&emp_id=<?php echo base64_encode($docid);?>&emp_month=<?php echo base64_encode($_REQUEST['emp_month']);?>&emp_year=<?php echo base64_encode($_REQUEST['emp_year']);?><?=$pagenav?>' target="_blank"  title='Print Salary Sheets'>
                <i class="fa fa-print fa-lg" title="Print<?=$imeitag?>"></i>
                </a> 
            </div>
            <?php } ?>    
            
            <br><br>
            <div class="form-group">
                <div class="col-md-12" style="text-align:center;" > 
                   <button class="btn <?=$btncolor?>" type="submit" name="submit" value="Show"> Show </button>  
                   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='emp_history.php?empcode=<?=base64_encode($docid);?><?=$pagenav?>'">
                </div>  

            </div>                        
        </div><!--close panel body-->
    </div><!--close panel-->
  </div>
</form>  


<br><br>
</div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>

</body>
</html>