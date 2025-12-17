<?php
require_once("../config/config.php");
@extract($_GET);
////// filters value/////
## selected Product Sub Category
if($product_subcat!=""){
	$psc = " sub_categaory_id='".$product_subcat."'";
}else{
	$psc = " 1";
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
      <h2 align="center"><i class="fa fa-list-ul"></i>&nbsp;Parameter Master</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	   <div class="form-group">
          <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label"> Product Sub Category:</label>
             <div class="col-sm-5 col-md-5 col-lg-5">
                <select name="product_subcat" id="product_subcat" class="form-control"  onChange="document.form1.submit();">
                  <option value=''>All</option>
                  <?php
				$circlequery="select psubcatid,prod_sub_cat from product_sub_category where status='1' order by prod_sub_cat";
				$circleresult=mysqli_query($link1,$circlequery) or die(mysqli_error($link1));
				while($circlearr=mysqli_fetch_array($circleresult)){
				?>
				  <option value="<?=$circlearr['psubcatid']?>"<?php if($_REQUEST['product_subcat']==$circlearr['psubcatid']){ echo "selected";}?>><?=ucwords($circlearr['prod_sub_cat'])?></option>
				<?php 
				}
                ?>
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
				$processid=getExlCnclProcessid("Parameter",$link1);
			    ////// check this user have right to export the excel report
			    if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
			   ?>
               <a href="excelexport.php?rname=<?=base64_encode("parametermaster")?>&rheader=<?=base64_encode("Parameter Master")?>&product_subcat=<?=base64_encode($_GET['product_subcat'])?>" title="Export Parameter details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export Parameter details in excel"></i></a>
               <?php
				}
				?>
            </div>
          </div>
	    </div><!--close form group-->	    
	  </form>
      <form class="form-horizontal" role="form">
	   <div class="col-md-12"><label class="col-md-5 control-label"></label>	  
			
        <button title="Add New Parameter" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addParameter.php?op=add<?=$pagenav?>'"><span>Add New Parameter</span></button>&nbsp;&nbsp; 
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr>
              <th><a href="#" name="entity_id" title="asc" ></a>#</th>
              <th><a href="#" name="name" title="asc" class="not-sort"></a>Parameter Name</th>
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Product Sub Category</th>
              <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Status</th>
			  <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>View/Edit</th>        
            </tr>
          </thead>
          <tbody>
             <?php 
			 $i=1;
			$sql1 = "SELECT * FROM pr_parameter_master where ".$psc." order by sub_categaory_id,parameter_name";
       $rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	   while($row1=mysqli_fetch_assoc($rs1)) { ?>
	    <tr class="even pointer">
		<td><?php echo $i;?></td>
		<td><?php echo $row1['parameter_name']?></td>
          <td><?php echo getAnyDetails($row1['sub_categaory_id'],"prod_sub_cat","psubcatid","product_sub_category",$link1);?></td>
          <td align="center"><?php if($row1['status'] == '1'){ echo $status = "Active";}
else { echo $status = "Deactive";}?></td>
          <td align="center"><a href='edit_parameter.php?op=edit&id=<?php echo base64_encode($row1['parameter_id']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
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