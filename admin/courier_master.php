<?php
////// Function ID ///////
$fun_id = array("a"=>array(30));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
@extract($_GET);
////// filters value/////
## selected state
if($locationstate!=""){
	$loc_state="state='".$locationstate."'";
}else{
	$loc_state="1";
}
## selected city
if($locationcity!=""){
	$loc_city="city='".$locationcity."'";
}else{
	$loc_city="1";
}

## selected location Status
if($status!=""){
	$loc_status="status='".$status."'";
}else{
	$loc_status="1";
}
//////End filters value/////
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
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

<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-truck"></i>&nbsp;Courier Master</h2>
      <?php if($_REQUEST[msg]){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	   <div class="form-group">
          <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label"> State </label>
             <div class="col-sm-5 col-md-5 col-lg-5">
                <select name="locationstate" id="locationstate" class="form-control"  onChange="document.form1.submit();">
                  <option value=''>-- Select All --</option>
                  <?php
				$circlequery="select distinct(state) from diesl_master order by state";
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
          <div class="col-md-6"><label class="col-md-5 control-label"> City </label>
            <div class="col-md-5">
                <select  name='locationcity' id="locationcity" class='form-control'  onChange="document.form1.submit();">
                  <option value=''>-- Select All --</option>
				  <?php
				$model_query="SELECT distinct city FROM diesl_master where state='$_REQUEST[locationstate]' order by city";
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
          
		  <div class="col-md-6"><label class="col-md-5 control-label">Status</label>	  
			<div class="col-md-5" align="left">
			   <select name="status" id="status" class="form-control"  onChange="document.form1.submit();">
                    <option value=""<?php if($_REQUEST['status']==''){ echo "selected";}?>>-- Select All --</option>
                    <option value="active"<?php if($_REQUEST['status']=='active'){ echo "selected";}?>>Active</option>
                    <option value="deactive"<?php if($_REQUEST['status']=='deactive'){ echo "selected";}?>>Deactive</option>
                </select>
            </div>
          </div>
		  <div class="col-md-3"><label class="col-md-5 control-label"></label>
            <div class="col-md-3">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST[pid]?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST[hid]?>"/>
               <input name="Submit" type="submit" class="btn <?=$btncolor?>" value="GO"  title="Go!">
            </div>
          </div>
		  <div class="col-md-3"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-3" align="left">
               <?php
			    //// get excel process id ////
				// $processid=getExlCnclProcessid("Courier",$link1);
			    ////// check this user have right to export the excel report
			    // if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
			   ?>
               <a href="excelexport.php?rname=<?=base64_encode("couriermaster")?>&rheader=<?=base64_encode("Courier Master")?>&courierstate=<?=base64_encode($_GET['locationstate'])?>&couriercity=<?=base64_encode($_GET['locationcity'])?>&courierstatus=<?=base64_encode($_GET['status'])?>" title="Export courier details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export courier details in excel"></i></a>
               <?php
				// }
				?>
            </div>
          </div>
	    </div><!--close form group-->
       
	  </form>
       <form class="form-horizontal" role="form" name="form1" method="get">
        <button title="Add New Courier" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='edit_courier.php?Submit=new<?=$pagenav?>'"><span>Add New Courier</span></button>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" >
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Sno</th>
              <th style="text-align:center;" data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Name</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Contact Person</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Code</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Email</th>
			  <th style="text-align:center;" ><a href="#" name="entity_id" title="asc" ></a>Phone</th>
              <th style="text-align:center;" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Address</th>
			  <th style="text-align:center;" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>City</th>
			  <th style="text-align:center;" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>State</th>
			  <th style="text-align:center;" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Status</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" ></a>Edit</th>
            </tr>
          </thead>
          <tbody>
            <?php $i=1;
	   $sql1 = "SELECT * FROM diesl_master where $loc_state and $loc_city and  $loc_status";
       $rs1 = mysqli_query($link1,$sql1) or die(mysql_error());
	   while($row1=mysqli_fetch_assoc($rs1)) { ?>
	      <td  style="text-align:center;" ><?=$i?><div align="left"></div></td> 
          <td width="5%"><div align="left"><?php echo $row1['couriername'];?>
              </div></td>   
          <td ><div align="left"><?php echo $row1['contact_person'];?> </div></td>
          <td><div align="left"><?php echo $row1['couriercode'];?></div></td>
          <td><div align="right"><?php echo $row1['email'];?></div></td>
          <td><div align="right"><?php echo $row1['phone'];?></div></td>
          <td><?php echo $row1['addrs'];?></td>
          <td><?php echo $row1['city'];?></td>
		  <td><?php echo $row1['state'];?></td>
		  <td><?php echo $row1['status'];?></td>
		  <td align="center"><a href='edit_courier.php?Submit=edit&id=<?php echo base64_encode($row1['sno']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
      </tr>
      <?php 
	  $i++;
	   }  
	   ?>
          </tbody>
          </table>
      </div>
      </form>
    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
?>
</body>
</html>