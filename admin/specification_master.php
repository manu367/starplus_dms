<?php
require_once("../config/config.php");
@extract($_GET);
////// filters value/////
## selected Product Category
if($product_cat!=""){
	$pc = " productid='".$product_cat."'";
	$pcat = " productcategory='".$product_cat."'";
}else{
	$pc = " 1";
	$pcat = " 1";
}
## selected Product Sub Category
if($product_subcat!=""){
	$psc = " psubcatid='".$product_subcat."'";
	$pscat = " productsubcat='".$product_subcat."'";
}else{
	$psc = " 1";
	$pscat = " 1";
}
## selected brand
if($brand!=""){
	$brd = " brand='".$brand."'";
}else{
	$brd = " 1";
}
## selected product id
if($partcode!=""){
	$part_code = " product_id='".$partcode."'";
}else{
	$part_code = " 1";
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
      <h2 align="center"><i class="fa fa-film"></i>&nbsp;Specification Master</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
      <div class="form-group">
          <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label"> Product Category:</label>
             <div class="col-sm-5 col-md-5 col-lg-5">
             	<select name="product_cat" id="product_cat" class="form-control"  onChange="document.form1.submit();">
                	<option value=''>All</option>
                  	<?php
					$sql1 = "select catid,cat_name from product_cat_master where status='1' order by cat_name";
					$res1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
					while($row1 = mysqli_fetch_array($res1)){
					?>
				  	<option value="<?=$row1['catid']?>"<?php if($_REQUEST['product_cat']==$row1['catid']){ echo "selected";}?>><?=$row1['cat_name']?></option>
					<?php 
					}
                	?>
                </select>
             </div>
          </div> 
          <div class="col-md-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label"> Product Sub Category:</label>  
			<div class="col-md-5" align="left">
			   <select name="product_subcat" id="product_subcat" class="form-control"  onChange="document.form1.submit();">
                	<option value=''>All</option>
                  	<?php
					$sql2 = "select psubcatid,prod_sub_cat from product_sub_category where ".$pc." and status='1' order by prod_sub_cat";
					$res2 = mysqli_query($link1,$sql2) or die(mysqli_error($link1));
					while($row2 = mysqli_fetch_array($res2)){
					?>
				  	<option value="<?=$row2['psubcatid']?>"<?php if($_REQUEST['product_subcat']==$row2['psubcatid']){ echo "selected";}?>><?=$row2['prod_sub_cat']?></option>
					<?php 
					}
                	?>
                </select>
            </div>
          </div>
	    </div><!--close form group-->
	   <div class="form-group">
          <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label"> Brand:</label>
             <div class="col-sm-5 col-md-5 col-lg-5">
                <select name="brand" id="brand" class="form-control"  onChange="document.form1.submit();">
                	<option value=''>All</option>
                  	<?php
					$sql3 = "select id, make from make_master where status='1' order by make";
					$res3 = mysqli_query($link1,$sql3) or die(mysqli_error($link1));
					while($row3 = mysqli_fetch_array($res3)){
					?>
				  	<option value="<?=$row3['id']?>"<?php if($_REQUEST['brand']==$row3['id']){ echo "selected";}?>><?=$row3['make']?></option>
					<?php 
					}
                	?>
                </select>
             </div>
          </div> 
          <div class="col-md-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label"> Product:</label>
			<div class="col-md-5" align="left">
			   <select name="partcode" id="partcode" class="form-control"  onChange="document.form1.submit();">
                	<option value=''>All</option>
                  	<?php
					$sql4 = "select id, productname from product_master where ".$pcat." and ".$pscat." and ".$brd." and status='active' order by productname";
					$res4 = mysqli_query($link1,$sql4) or die(mysqli_error($link1));
					while($row4 = mysqli_fetch_array($res4)){
					?>
				  	<option value="<?=$row4['id']?>"<?php if($_REQUEST['partcode']==$row4['id']){ echo "selected";}?>><?=$row4['productname']?></option>
					<?php 
					}
                	?>
                </select>
            </div>
          </div>
	    </div><!--close form group-->
        <div class="form-group">
          <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label"> </label>
             <div class="col-sm-5 col-md-5 col-lg-5">
             	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               	<input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               	<input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
             </div>
          </div> 
          <div class="col-md-6">	  
			<div class="col-md-5" align="left">
               <?php
			    //// get excel process id ////
				$processid=getExlCnclProcessid("Specification",$link1);
			    ////// check this user have right to export the excel report
			    if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
			   ?>
               <a href="excelexport.php?rname=<?=base64_encode("specificationmaster")?>&rheader=<?=base64_encode("Specification Master")?>&product_subcat=<?=base64_encode($_GET['product_subcat'])?>" title="Export Specification details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export Specification details in excel"></i></a>
               <?php
				}
				?>
            </div>
          </div>
	    </div><!--close form group-->    
	  </form>
      <form class="form-horizontal" role="form">		
        <button title="Add New Specification" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addSpecification.php?op=add<?=$pagenav?>'"><span>Add New Specification</span></button>&nbsp;&nbsp; 
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr>
              <th><a href="#" name="entity_id" title="asc" ></a>#</th>
              <th><a href="#" name="name" title="asc" class="not-sort"></a>Product Name</th>
              <th data-hide="phone,tablet">Product Category</th>
              <th data-hide="phone,tablet">Product Sub Category</th>
              <th data-hide="phone,tablet">Brand</th>
              <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>View/Edit</th>        
            </tr>
          </thead>
          <tbody>
            <?php 
			$i=1;
			$sql1 = "SELECT * FROM pr_specification where ".$part_code." group by product_id";
       		$rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	   		while($row1 = mysqli_fetch_assoc($rs1)) { 
			$prd_det = explode("~",getPRD($row1['product_id'],"productname,productcategory,productsubcat,brand",$link1));
			$row2 =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT cat_name FROM product_cat_master where catid = '".$prd_det[1]."'"));
           	$row3 =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT prod_sub_cat FROM product_sub_category where psubcatid = '".$prd_det[2]."'"));
           	$row4 =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT make FROM make_master where id = '".$prd_det[3]."'"));
			?>
	    	<tr class="even pointer">
				<td><?php echo $i;?></td>
				<td><?php echo $prd_det[0]?></td>
                <td><?php echo $row2['cat_name']?></td>
                <td><?php echo $row3['prod_sub_cat']?></td> 
          		<td><?php echo $row4['make']?></td>
                <td align="center"><a href='edit_specification.php?op=edit&prdid=<?php echo base64_encode($row1['product_id']);?>&pscid=<?php echo base64_encode($prd_det[2]);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
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