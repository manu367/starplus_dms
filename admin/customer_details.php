<?php
////// Function ID ///////
$fun_id = array("a"=>array(76));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
///// get access location ///
$accessLocation=getAccessLocation($_SESSION['userid'],$link1);
$accessState=getAccessState($_SESSION['userid'],$link1);
@extract($_GET);
////// filters value/////
## selected state
## selected state
if($location!=""){
	$loc_code="and mapplocation ='".$location."' ";
}else{
	$loc_code="and mapplocation in (".$accessLocation.")";
}
if($locationstate!=""){
	$loc_state="state='".$locationstate."'";
}else{
	$loc_state="state in (".$accessState.")";
}
## selected city
if($locationcity!=""){
	$loc_city="and city='".$locationcity."'";
}else{
	$loc_city="";
}
## Category
if($customertype!=""){
	$loc_cat="and category='".$customertype."'";
}else{
	$loc_cat="";
}

## selected location Status
if($locationstatus!=""){
	$loc_status="and status='".$locationstatus."'";
}else{
	$loc_status="";
}
//////End filters value/////
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
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
	$(document).ready(function(){
		$('#myTable').dataTable();
	});
 </script>
<script language="javascript">
</script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-user-o"></i> Customer Master</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	    <form class="form-horizontal" role="form" name="form1" id="form1" action="" method="get">
	   <div class="form-group">
          <div class="col-sm-6 col-md-6 col-lg-6">
            <label class="col-sm-5 col-md-5 col-lg-5 control-label">Customer State:</label>
             <div class="col-sm-5 col-md-5 col-lg-5">
                <select name="locationstate" id="locationstate" class="form-control"  onChange="document.form1.submit();">
                  <option value=''>--Please Select-</option>
                  <?php
				$circlequery="select distinct(state) from state_master  where state in ($accessState)  order by state";
				$circleresult=mysqli_query($link1,$circlequery) or die(mysqli_error($link1));
				while($circlearr=mysqli_fetch_array($circleresult)){
				?>
				  <option value="<?=$circlearr['state']?>"<?php if($_REQUEST['locationstate']==$circlearr['state']){ echo "selected";}?>><?=ucwords($circlearr['state'])?></option>
				<?php 
				}
                ?>
                </select>
             </div>
          </div> 
          <div class="col-md-6"><label class="col-md-5 control-label">Customer City:</label>
            <div class="col-md-5">
                <select  name='locationcity' id="locationcity" class='form-control'  onChange="document.form1.submit();">
                  <option value=''>--Please Select-</option>
				  <?php
				$model_query="SELECT distinct city FROM district_master where state='$_REQUEST[locationstate]' order by city";
				$check1=mysqli_query($link1,$model_query);
				while($br = mysqli_fetch_array($check1)){
			    ?>
				  <option value="<?=$br['city']?>"<?php if($_REQUEST['locationcity']==$br['city']){echo 'selected';}?>><?=$br['city']?></option>
				<?php
                }
				?>
               </select>
            </div>
          </div>
	    </div><!--close form group-->
        
           <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">Location:</label>
            <div class="col-md-5">
                <select name="location" id="location"  class="form-control selectpicker required " data-live-search="true" required >
                    <option value="" selected="selected">Please Select </option>
                    <?php $sql_parent = "select name , city, state,id_type,asc_code from asc_master where asc_code in  (".$accessLocation.")";
                    $res_parent = mysqli_query($link1, $sql_parent);
                    while ($party_det = mysqli_fetch_array($res_parent)) {
                   // $party_det = mysqli_fetch_array(mysqli_query($link1, "select name , city, state,id_type from asc_master where asc_code='" . $result_parent['location_id'] . "' and id_type='".$_REQUEST['locationtype']."'"));?>
                    <option data-tokens="<?= $party_det['name'] . " | " . $_SESSION['userid'] ?>" value="<?=$party_det['asc_code'] ?>" <?php if ($party_det['asc_code'] == $_REQUEST['location']) echo "selected"; ?> ><?= $party_det['name'] . " | " . $party_det['city'] . " | " . $party_det['state'] . " | " . $party_det['asc_code'] ?></option>
                    <?php }?>
                </select>
            </div>
          </div>
          <div class="col-md-6"><label class="col-md-5 control-label">Customer Type:</label>
            <div class="col-md-5">
               <select  name="customertype" id="customertype" class='form-control' >
                 <option value=''>--Please Select-</option>
                 <?php
				//$ctype_query="SELECT distinct(category) FROM customer_master where category!='' order by category";
				//$check_ctype=mysqli_query($link1,$ctype_query);
				//while($br_ctype = mysqli_fetch_array($check_ctype)){
				?>
               <!-- <option value="<?=$br_ctype['category']?>"<?php //if($_REQUEST['customertype']==$br_ctype['category']){ echo "selected";}?>><?php //echo $br_ctype['category']?></option>-->
                <option value="RETAIL"<?php if($_REQUEST['customertype']=="RETAIL"){ echo "selected";}?>>RETAIL</option>
                <?php //}?>
               </select>
            </div>
          </div>
          </div>
	    <div class="form-group">
          <div class="col-md-6">
			<label class="col-md-5 control-label">Customer Status:</label>	  
			<div class="col-md-5" align="left">
			   <select name="locationstatus" id="locationstatus" class="form-control">
                    <option value=""<?php if($_REQUEST['locationstatus']==''){ echo "selected";}?>>All</option>
                    <option value="Active"<?php if($_REQUEST['locationstatus']=='Active'){ echo "selected";}?>>Active</option>
                    <option value="deactive"<?php if($_REQUEST['locationstatus']=='deactive'){ echo "selected";}?>>Deactive</option>
                </select>
            </div>
          </div>
	    </div><!--close form group-->
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5">
               <input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST[pid]?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST[hid]?>"/>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
              <?php
			    //// get excel process id ////
				//$processid=getExlCnclProcessid("Customer",$link1);
			    ////// check this user have right to export the excel report
			    //if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
			   ?>
               <a href="../excelReports/customer_report.php?cust_state=<?=$_REQUEST['locationstate']?>&cust_city=<?=$_REQUEST['locationcity'];?>&location=<?=$_REQUEST['location']?>&customertype=<?=$_REQUEST['customertype']?>&status=<?=$_REQUEST['status']?>" title="Export Customer details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Customer details in excel"></i></a>
               <?php
				//}
				?>
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
      <!--<button title="Add New Location" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='asp_add.php?op=add<?=$pagenav?>'"><span>Add New Location</span></button>-->
       <div><div style="display:table-cell;float:right">
        <span><button title="Add Customer" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='sendMailToCustomer.php?<?=$pagenav?>'"><i class="fa fa-envelope-o"><strong> Send Mail</strong></i> </button></span></div><div style="display:table-cell;float:right">&nbsp;&nbsp;&nbsp;</div><div style="display:table-cell;float:right">
       <span><button title="Send SMS " type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='sendSmsToCustomer.php?<?=$pagenav?>'"><i class="fa fa-mobile"><strong> Send SMS</strong></i> </button></span></div>
       <div style="display:table-cell;float:right">&nbsp;&nbsp;&nbsp;</div><div style="display:table-cell;float:right">
        <span><button title="Add Customer" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addNewCustomer.php?<?=$pagenav?>'"><i class="fa fa-user-plus"><strong> Add Customer</strong></i> </button></span></div>
        </div>
      <!--<div class="form-group"  id="page-wrap" style="margin-left:10px;">--><br/><br/>
      <form class="form-horizontal table-responsive" role="form">
       <table  width="99%" id="myTable" class="table-responsive table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th><a href="#" name="entity_id" title="asc"></a>#</th>
              <th data-hide="phone" data-class="expand"><a href="#" name="name" title="asc"></a>Customer Id</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Customer Name</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>State</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Customer Type</th>
			   <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>
              <th data-hide="phone,tablet">View/Edit</th>
              <th data-hide="phone,tablet">History</th>
            </tr>
          </thead>
          <tbody>
          <?php
		    $i=1;
			$query="Select * from customer_master where $loc_state $loc_code $loc_city $loc_cat $loc_status order by id";
			$result=mysqli_query($link1,$query) or die(mysqli_error($link1));
			while($arr_result=mysqli_fetch_array($result)){
          ?>
            <tr>
              <td><?=$i?></td>
              <td><?=$arr_result['customerid']?></td>
              <td><?=$arr_result['customername']?></td>
              <td><?=$arr_result['state']?></td>
              <td><?=$arr_result['category'];?></td>
			  <td><?=$arr_result['status']?></td>
              <td align="center"><a href='edit_Customer.php?op=edit&id=<?php echo base64_encode($arr_result['id']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
              <td align="center"><a href='customer_history.php?id=<?php echo base64_encode($arr_result['customerid']);?><?=$pagenav?>'  title='party history'><i class="fa fa-history fa-lg" title="view history"></i></a></td>
            </tr>
          <?php
		  $i++;
		
			} 
          ?>
          </tbody>
       </table>
       </form>
      <!--</div>--><!--close form group-->
    </div><!--close tab pane-->
  </div><!--close row content-->
</div><!--close container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>