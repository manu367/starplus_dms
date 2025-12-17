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
	$part_code = " id='".$partcode."'";
}else{
	$part_code = " 1";
}
//////End filters value/////

 $sql1 ="SELECT * FROM product_master where id= '".$_REQUEST['partcode']."' order by productname";
       $rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	  $row1=mysqli_fetch_assoc($rs1); 


if ($_POST) { 
$curr_time = time();
    if ($_POST['submit1'] == 'Upload') {
        
        //// initialize transaction parameters
        $flag = true;
        mysqli_autocommit($link1, false);
        $err_msg = "";
                /////// insert image file into database
                $image_seq = $_POST['image_seq'];
                $file = $_FILES['banner_image'];
                $file_name = $file['name'];
                $file_type = $file['type'];
                $file_size = $file['size'];
                $file_path = $file['tmp_name'];
                $temp = explode(".", $file_name);   
                ///add date into image name before extension
                $fileName = $temp[0].'_'.$curr_time.'.'.$temp[1];               
                if (!is_dir('../API/bannerimg/')) {
                mkdir('../API/bannerimg/', 0777, 'R');              
                }              
if ($file_name != "" && ($file_type == "image/jpeg" || $file_type == "image/png" || $file_type == "image/jpg" || $file_type == "image/gif")) {
                    $filename = '../API/bannerimg/' . $fileName;
                        move_uploaded_file($file_path, $filename);
                        $req_res1 = mysqli_query($link1, "INSERT INTO pr_images set product_id='" . $_REQUEST["partcode"] . "',image_url='".$fileName."',image_type='banner',img_sequence='".$_POST['image_seq']."'");
                    
                } else {
                    $flag = false;
                    $err_msg = "File not uploaded.";
                }
                //// check if query is not executed
                if (!$req_res1) {
                    $flag = false;
                    $err_msg = "Error Code1: " . mysqli_error($link1);
                }   
                    
                
        ////// insert in activity table////
        $flag = dailyActivity($_SESSION['userid'], $dispatch_no, "IMAGE Add", "ADD", $ip, $link1, $flag);
        ///// check both master and data query are successfully executed
        if ($flag) {
            mysqli_commit($link1);
            $msg = "Banner image is uploaded";
            $cflag = "success";
            $cmsg = "Success";
        } else {
            mysqli_rollback($link1);
            $msg = "Pdf could not be processed " . $err_msg . ". Please try again.";
            $cflag = "danger";
            $cmsg = "Failed";
        }
        mysqli_close($link1);
  
    ///// move to parent page
    header("location:product_image_list.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
    exit;
}else if($_POST['submit2'] == 'Upload') {
   //print_r($_REQUEST);
        //// initialize transaction parameters
        $flag = true;
        mysqli_autocommit($link1, false);
        $err_msg = "";
                /////// insert image file into database
                $image_seq = $_POST['image_seq'];
                $file = $_FILES['product_image'];
                $file_name = $file['name'];
                $file_type = $file['type'];
                $file_size = $file['size'];
                $file_path = $file['tmp_name'];
                $temp = explode(".", $file_name);   
                ///add date into image name before extension
                $fileName = $temp[0].'_'.$curr_time.'.'.$temp[1];                
				if ($file_name != "" && ($file_type == "image/jpeg" || $file_type == "image/png" || $file_type == "image/jpg" || $file_type == "image/gif")) {
     				if (!is_dir('../API/pc'.$row1['productcategory'])) {
                		mkdir('../API/pc'.$row1['productcategory'], 0777, 'R'); 
					}
                	if (!is_dir('../API/pc'.$row1['productcategory'].'/psc'.$row1['product_subcat'].'_'.$row1['productcategory'])) {
                 		mkdir('../API/pc'.$row1['productcategory'].'/psc'.$row1['product_subcategory'].'_'.$row1['productcategory'], 0777, 'R');   
					}
					if (!is_dir('../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'])) {
                 		mkdir('../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'], 0777, 'R'); 
					}
					if (!is_dir('../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'].'/product/')) {
                 		mkdir('../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'].'/product/', 0777, 'R');   
					}
                    $filename = '../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'].'/product/' . $fileName;
                        move_uploaded_file($file_path, $filename);
                        $req_res1 = mysqli_query($link1, "INSERT INTO pr_images set product_id='" . $_REQUEST["partcode"] . "',image_url='".$fileName."',image_type='product',img_sequence='".$_POST['image_seq']."'");
                    
                } else {
                    $flag = false;
                    $err_msg = "File not uploaded.";
                }
                //// check if query is not executed
                if (!$req_res1) {
                    $flag = false;
                    $err_msg = "Error Code1: " . mysqli_error($link1);
                }   
                    
                
        ////// insert in activity table////
        $flag = dailyActivity($_SESSION['userid'], $dispatch_no, "IMAGE Add", "ADD", $ip, $link1, $flag);
        ///// check both master and data query are successfully executed
        if ($flag) {
            mysqli_commit($link1);
            $msg = "Product image is uploaded";
            $cflag = "success";
            $cmsg = "Success";
        } else {
            mysqli_rollback($link1);
            $msg = "Request could not be processed " . $err_msg . ". Please try again.";
            $cflag = "danger";
            $cmsg = "Failed";
        }
        mysqli_close($link1);
  
    ///// move to parent page
    header("location:product_image_list.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
    exit;
}else if($_POST['submit3'] == 'Upload') {

        //// initialize transaction parameters
        $flag = true;
        mysqli_autocommit($link1, false);
        $err_msg = "";
                /////// insert image file into database
         for($i=0;$i<=$_REQUEST['rowno'];$i++){
                $image_seq = $_POST['image_seq'][$i];
                $file = $_FILES['product_image'];
                $file_name = $file['name'][$i];
                $file_type = $file['type'][$i];
                $file_size = $file['size'][$i];
                $file_path = $file['tmp_name'][$i];
                $temp = explode(".", $file_name);   
                ///add date into image name before extension
                $fileName = $temp[0].'_'.$curr_time.'.'.$temp[1];               

if ($file_name != "" && ($file_type == "image/jpeg" || $file_type == "image/png" || $file_type == "image/jpg" || $file_type == "image/gif")) {
  
     if (!is_dir('../API/pc'.$row1['productcategory'])) {
                    
                mkdir('../API/pc'.$row1['productcategory'], 0777, 'R'); 
                
                }
                if (!is_dir('../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'])) {
                    
                 mkdir('../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'], 0777, 'R');   
                 
                }if (!is_dir('../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'])) {
                 mkdir('../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'], 0777, 'R'); 
                 
                }if (!is_dir('../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'].'/product_desc/')) {
                 mkdir('../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'].'/product_desc/', 0777, 'R');   
                }
    
                    $filename = '../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'].'/product_desc/' . $fileName;
                        move_uploaded_file($file_path, $filename);
                        $req_res1 = mysqli_query($link1, "INSERT INTO pr_images set product_id='" . $_REQUEST["partcode"] . "',image_url='".$fileName."',image_type='product_desc',img_sequence='".$image_seq."'");
                    
                } else {
                    $flag = false;
                    $err_msg = "File not uploaded.";
                }
                //// check if query is not executed
                if (!$req_res1) {
                    $flag = false;
                    $err_msg = "Error Code1: " . mysqli_error($link1);
                }   
                    
         }
        ////// insert in activity table////
        $flag = dailyActivity($_SESSION['userid'], $dispatch_no, "IMAGE Add", "ADD", $ip, $link1, $flag);
        ///// check both master and data query are successfully executed
        if ($flag) {
            mysqli_commit($link1);
            $msg = "Product Desc image is uploaded";
            $cflag = "success";
            $cmsg = "Success";
        } else {
            mysqli_rollback($link1);
            $msg = "Request could not be processed " . $err_msg . ". Please try again.";
            $cflag = "danger";
            $cmsg = "Failed";
        }
        mysqli_close($link1);
  
    ///// move to parent page
    header("location:product_image_list.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
    exit;
}
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

$(document).ready(function(){
     $("#add_row").click(function(){  
                var numi = document.getElementById('rowno');
		var num = (document.getElementById("rowno").value -1)+ 2;
		if($("#addr"+numi.value+":visible").length>=0){
		numi.value = num;		
     	var r='<tr id="addr'+num+'" class="col-md-12"><td class="col-md-1" colspan="3"><div style="display:inline-block;float:right"><i class="fa fa-close fa-lg" onClick="deleteRow('+num+');"></i></div></td><td class="col-md-5"><label class="col-md-6 control-label">Image Sequence</label><div class="col-md-6"><input type="text" name="image_seq[' + num + ']" id="image_seq[' + num + ']" class="form-control"></div></td><td class="col-md-6"><label class="col-md-6 control-label">Upload Image</label><div class="col-md-6"><input type="file" name="product_image[' + num + ']" id="product_image[' + num + ']" class="form-control" accept="image/*"></div></td></tr>';
      $('#itemsTable1').append(r);
  }
  });
});
////// delete product row///////////
function deleteRow(ind){  
     var id="addr"+ind; 
     var itemid="param_id"+"["+ind+"]";
	 var qtyid="param_desc"+"["+ind+"]";
	 // hide fieldset \\
    document.getElementById(id).style.display="none";
	// Reset Value\\
	// Blank the Values \\
	document.getElementById(itemid).value="";
	document.getElementById(qtyid).value="";
}
</script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-file-image-o fa-lg"></i>&nbsp;Product Image Add</h2>
      <?php if($_REQUEST[msg]){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      <?php }?>
	  <br><br>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	   <div class="form-group">
                 <div class="col-md-6"><label class="col-md-5 control-label">Product Category <span class="red_small">*</span></label>	  
			<div class="col-md-5" align="left">
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
               
          <div class="col-md-6"><label class="col-md-5 control-label">Product Sub Category <span class="red_small">*</span></label>
              <div class="col-md-5">
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
                 <div class="col-md-6"><label class="col-md-5 control-label">Brand <span class="red_small">*</span></label>	  
			<div class="col-md-5" align="left">
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
               
          <div class="col-md-6"><label class="col-md-5 control-label">Product <span class="red_small">*</span></label>
              <div class="col-md-5">
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
	  </form>
        <?php if($_REQUEST["partcode"]!=""){ ?>       
          <form id="frm" name="frm" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
          <table width="100%" class="table table-bordered table-hover">
            <thead>
              <tr class="bg-primary1">
                <th style="font-size:13px;">Banner Image </th>                
                </tr>
            </thead>
            <tbody>
              <tr>
                  <td class="col-md-3">                  
                      <label class="col-md-4 control-label">Upload Image</label>                      
                      <div class="col-md-3">
                          <input type="file" name="banner_image" id="banner_image" class="form-control" accept="image/*">
                          <input type="hidden" name="image_seq" id="image_seq" value="1" class="form-control">
                      </div>
                      <div class="col-md-5"></div>
                  </td> 
              </tr>
              <tr>
                <td class="col-md-3" align="center">
                    <input type="submit" class="btn<?=$btncolor?>" name="submit1" id="save" value="Upload" title="Upload Image">
                </td>
                </tr>
            </tbody>            
            
          </table>
          </form>
        <form id="frm1" name="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
               <table width="100%"  class="table table-bordered table-hover">
            <thead>
              <tr class="bg-primary1">
                <th style="font-size:13px;">Product Image</th>                
                </tr>
            </thead>
            <tbody>
              <tr>
                  <td class="col-md-3">                  
                      <label class="col-md-4 control-label">Upload Image</label>                      
                      <div class="col-md-3">
                          <input type="file" name="product_image" id="product_image" class="form-control" accept="image/*">
                          <input type="hidden" name="image_seq" id="image_seq" value="1" class="form-control">
                      </div>
                      <div class="col-md-5"></div>
                  </td> 
              </tr>
              <tr>
                <td class="col-md-3" align="center">
                    <input type="submit" class="btn <?=$btncolor?>" name="submit2" id="save" value="Upload" title="Upload Image">
                </td>
                </tr>
            </tbody>   
          </table>
            </form>
       <form id="frm2" name="frm2" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
               <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
            <thead>
              <tr class="bg-primary1">
                  <th style="font-size:13px;">Product Description Image</th>                
                </tr>
            </thead>
            <tbody>
                <tr id='addr0' class="col-md-12">
                  <td class="col-md-6">                  
                      <label class="col-md-6 control-label">Image Sequence</label>                      
                      <div class="col-md-6">
                          <input type="text" name="image_seq[0]" id="image_seq[0]" class="form-control">
                      </div>
                  </td>
                  <td class="col-md-6">  
                      <label class="col-md-6 control-label">Upload Image</label>                      
                      <div class="col-md-6">
                          <input type="file" name="product_image[0]" id="product_image[0]" class="form-control" accept="image/*">
                      </div>
                  </td> 
              </tr>
              
              
            </tbody>
            <tfoot id='productfooter' style="z-index:-9999;">
               <tr>
                <td style="font-size:13px;">
                    <a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a>
                    <input type="hidden" name="rowno" id="rowno" value="0"/>
                </td>
              </tr>
              <tr>
                <td align="center">
                    <input type="submit" class="btn<?=$btncolor?>" name="submit3" id="save" value="Upload" title="Upload Image">
                </td>
              </tr>     
            </tfoot>
          </table>
            </form>
            <input type="hidden" name="keyid" id="keyid" value="<?=base64_encode($part_id)?>"/>
        <?php
		  }
		?>
		<br><br>
	  <div class="form-group">
		<div class="col-md-12" align="center">             
		  <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='product_image_list.php?<?=$pagenav?>'">
		</div>
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