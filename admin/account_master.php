<?php
////// Function ID ///////
$fun_id = array("a"=>array(73)); 

require_once("../config/config.php");

////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

if($_POST["accountStatus"]){
	$str = "status = '".$_POST["accountStatus"]."'";
}else{
	$str = "1";
}
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
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-sitemap"></i>&nbsp;Account Master</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="post">
	   <div class="form-group">
          <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label"> Status:</label>
             <div class="col-sm-5 col-md-5 col-lg-5">
                <select name="accountStatus" id="accountStatus" class="form-control">
                  <option value=''>All</option>
				  <option value="A"<?php if($_REQUEST['accountStatus']=="A"){ echo "selected";}?>>Active</option>
                  <option value="D"<?php if($_REQUEST['accountStatus']=="D"){ echo "selected";}?>>Deactive</option>
                </select>
             </div>
          </div> 
          <div class="col-md-6">	  
			<div class="col-md-5" align="left">
			   <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
               <?php
			    //// get excel process id ////
				$processid=getExlCnclProcessid("Account",$link1);
			    ////// check this user have right to export the excel report
			    if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
			   ?>
               <a href="excelexport.php?rname=<?=base64_encode("accountmaster")?>&rheader=<?=base64_encode("Account Master")?>&acountStatus=<?=base64_encode($_GET['accountStatus'])?>" title="Export Account details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export Account details in excel"></i></a>
               <?php
				}
				?>
            </div>
          </div>
	    </div><!--close form group-->	    
	  </form>
      <form class="form-horizontal" role="form">
	   <div class="col-md-12"><label class="col-md-5 control-label"></label>	  
			
        <button title="Add New Account" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addAccount.php?op=add<?=$pagenav?>'"><span>Add New Account</span></button>&nbsp;&nbsp; 
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th><a href="#" ></a>#</th>
              <th><a href="#" class="not-sort"></a>Account Name</th>
              <th data-class="expand"><a href="#" name="entity_id"></a>Account No.</th>
              <th data-class="expand"><a href="#" name="entity_id"></a>Account Type</th>
              <th data-hide="phone,tablet"><a href="#" class="sort"></a>Status</th>
			  <th data-hide="phone,tablet"><a href="#" class="sort"></a>View/Edit</th>
              <th data-hide="phone,tablet"><a href="#" class="sort"></a>Mapping</th>        
            </tr>
          </thead>
          <tbody>
             <?php 
			 $i=1;
			$sql_acc = "SELECT * FROM account_master WHERE ".$str." ORDER BY account_name";
       		$res_acc = mysqli_query($link1,$sql_acc) or die(mysqli_error($link1));
	   		while($row_acc = mysqli_fetch_assoc($res_acc)) { ?>
	    	<tr class="even pointer">
				<td><?php echo $i;?></td>
				<td><?php echo $row_acc['account_name']?></td>
          		<td><?php echo $row_acc['account_no']?></td>
                <td><?php echo $row_acc['account_type']?></td>
          		<td align="center"><?php if($row_acc['status'] == 'A'){ echo $status = "Active";}else { echo $status = "Deactive";}?></td>
          		<td align="center"><a href='edit_account.php?op=edit&id=<?php echo base64_encode($row_acc['id']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
                <td align="center"><a href='mappWithAccount.php?op=edit&id=<?php echo base64_encode($row_acc['id']);?><?=$pagenav?>'  title='Map location'><i class="fa fa-map-signs fa-lg" title="Map location"></i></a></td>
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
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>