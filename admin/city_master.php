<?php
////// Function ID ///////
$fun_id = array("a"=>array(28));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
@extract($_GET);
////// filters value/////
## selected brand
if($locationstate!=""){
	$state="state='".$locationstate."'";
}else{
	$state="1";
}
## selected product
if($cityname!=""){
	$city_name="city='".$cityname."'";
}else{
	$city_name="1";
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
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-map-marker"></i>&nbsp;City Master</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	   <div class="form-group">
          <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label"> State:</label>
             <div class="col-sm-5 col-md-5 col-lg-5">
                <select name="locationstate" id="locationstate" class="form-control"  onChange="document.form1.submit();">
                  <option value=''>--Select All--</option>
                  <?php
				$circlequery="select distinct(state) from state_master order by state";
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
          <div class="col-md-6"><label class="col-md-5 control-label">City</label>	  
			<div class="col-md-5" align="left">
			    <select name="cityname" class="form-control" id="cityname" onChange="document.form1.submit();">
				<option value="">--Select All--</option>
                <?php
				$circlequery="select distinct(city) from district_master where $state order by city";
				$circleresult=mysqli_query($link1,$circlequery) or die(mysqli_error($link1));
				while($circlearr=mysqli_fetch_array($circleresult)){
				?>
          <option value="<?=$circlearr['city']?>"<?php if($_REQUEST['cityname']==$circlearr['city']){ echo "selected";}?> ><?=$circlearr['city']?></option>
          <?php 
				}
                ?>
          
        </select>
            </div>
          </div>
	    </div><!--close form group-->
	    
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST[pid]?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST[hid]?>"/>
               <input name="Submit" type="submit" class="btn <?=$btncolor?>" value="GO"  title="Go!">
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
               <?php
			    //// get excel process id ////
				//$processid=getExlCnclProcessid("City",$link1);
			    ////// check this user have right to export the excel report
			    //if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
			   ?>
               <a href="excelexport.php?rname=<?=base64_encode("citymaster")?>&rheader=<?=base64_encode("City Master")?>&state=<?=base64_encode($_GET['locationstate'])?>&city_name=<?=base64_encode($_GET['cityname'])?>" title="Export City details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export City details in excel"></i></a>
               <?php
				//}
				?>
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
      <form class="form-horizontal" role="form">
	   <div class="col-md-12"><label class="col-md-5 control-label"></label>	  
			
        <button title="Add New City" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='addCity.php?op=add<?=$pagenav?>'"><span>Add New City</span></button>&nbsp;&nbsp; 
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th style="text-align:center;"><a href="#" name="entity_id" title="asc" ></a>#</th>
              <th style="text-align:center;"><a href="#" name="name" title="asc" class="not-sort"></a>State</th>
              <th style="text-align:center;" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>City Name</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Status</th>
			  <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>View/Edit</th>
            </tr>
          </thead>
          <tbody>
             <?php $i=1;
			$sql1 = "SELECT * FROM district_master where $state and $city_name order by state,city ";
       $rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	   while($row1=mysqli_fetch_assoc($rs1)) { ?>
	    <tr class="even pointer">
		<td><?php echo $i ;?><div align="left"></div></td>
		<td><?php echo $row1['state']?></td>
          <td><?php echo $row1['city']?></td>
          <td align="center"><?php if($row1['status'] == 'A'){ echo "Active"; }else if($row1['status'] == 'D'){ echo "Deactive"; }else{ echo $row1['status']; } ?></td>
          <td align="center"><a href='edit_city.php?op=edit&id=<?php echo base64_encode($row1['id']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
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