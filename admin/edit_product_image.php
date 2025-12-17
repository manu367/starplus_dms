<?php
require_once("../config/config.php");
  $url =  "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

$curr_time = time();
@extract($_GET);

// fetch data from product master          
	 $sql1 ="SELECT * FROM product_master where id= '".$_REQUEST['id']."' order by productname";
       $rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	  $row1=mysqli_fetch_assoc($rs1);
          
			 $statusval = $row1['status'];
           $row2 =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT cat_name FROM product_cat_master where catid = '".$row1['productcategory']."'"));
           $row3 =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT prod_sub_cat FROM product_sub_category where psubcatid = '".$row1['productsubcat']."'"));
           $row4 =  mysqli_fetch_assoc(mysqli_query($link1,"SELECT make FROM make_master where id = '".$row1['brand']."'"));
           
           
          $sql2 ="SELECT * FROM pr_images where product_id= '".$_REQUEST['id']."' and image_type='banner'";
          $res2 = mysqli_query($link1,$sql2) or die(mysqli_error($link1));
	  $image_res1=mysqli_fetch_assoc($res2); 
          
          $sql3 ="SELECT * FROM pr_images where product_id= '".$_REQUEST['id']."' and image_type='product'";
          $res3 = mysqli_query($link1,$sql3) or die(mysqli_error($link1));
	  $image_res2=mysqli_fetch_assoc($res3);
         
          $sql4 ="SELECT * FROM pr_images where product_id= '".$_REQUEST['id']."' and image_type='product_desc'";
          $res4 = mysqli_query($link1,$sql4) or die(mysqli_error($link1));
	  $res_des = mysqli_query($link1,$sql4) or die(mysqli_error($link1));
          $res_des1 = mysqli_query($link1,$sql4) or die(mysqli_error($link1));
          if($image_res1['image_type']=='banner'){
              $banner_image = $image_res1['image_url'];
          }
          if($image_res2['image_type']=='product'){
              $product_image = $image_res2['image_url'];
          }
          
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
//delete pr_image data record
if($_GET['delete_id']){
   
   //// initialize transaction parameters
        $flag = true;
        mysqli_autocommit($link1, false);
        $err_msg = ""; 
        $sql2 ="SELECT image_url FROM pr_images where img_id= '".$_GET['delete_id']."'";
          $res2 = mysqli_query($link1,$sql2) or die(mysqli_error($link1));
	  $image_res1=mysqli_fetch_assoc($res2);
        unlink("../API/bannerimg/".$image_res1['image_url']);
       $req_res1 = mysqli_query($link1, "delete from pr_images where img_id='".$_GET['delete_id']."'");

//// check if query is not executed
                if (!$req_res1) {
                    $flag = false;
                    $err_msg = "Error Code1: " . mysqli_error($link1);
                } 
///// check both master and data query are successfully executed
        if ($flag) {
            mysqli_commit($link1);
            $msg = "Banner image is deleted";
            $cflag = "success";
            $cmsg = "Success";
        } else {
            mysqli_rollback($link1);
            $msg = "Banner image could not be deleted " . $err_msg . ". Please try again.";
            $cflag = "danger";
            $cmsg = "Failed";
        }
        mysqli_close($link1);
        ////// insert in activity table////
        $flag = dailyActivity($_SESSION['userid'], $dispatch_no, "IMAGE DELETE", "DELETE", $ip, $link1, $flag);
        ///// check both master and data query are successfully executed
    header("location:product_image_list.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
    exit;
}else if($_GET['delete_pro_id']){

   //// initialize transaction parameters
        $flag = true;
        mysqli_autocommit($link1, false);
        $err_msg = ""; 
        $sql2 ="SELECT image_url,product_id FROM pr_images where img_id= '".$_GET['delete_pro_id']."'";
        $res2 = mysqli_query($link1,$sql2) or die(mysqli_error($link1));
	$image_res1=mysqli_fetch_assoc($res2);
          
        $sql1 ="SELECT * FROM product_master where id= '".$image_res1['product_id']."'";
        $rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	$row1=mysqli_fetch_assoc($rs1); 
          
        unlink('../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'].'/product/'.$image_res1['image_url']);
        $req_res1 = mysqli_query($link1, "delete from pr_images where img_id='".$_GET['delete_pro_id']."'");

//// check if query is not executed
                if (!$req_res1) {
                    $flag = false;
                    $err_msg = "Error Code1: " . mysqli_error($link1);
                } 
///// check both master and data query are successfully executed
        if ($flag) {
            mysqli_commit($link1);
            $msg = "Product image is deleted";
            $cflag = "success";
            $cmsg = "Success";
        } else {
            mysqli_rollback($link1);
            $msg = "Product image could not be deleted " . $err_msg . ". Please try again.";
            $cflag = "danger";
            $cmsg = "Failed";
        }
        mysqli_close($link1);
        ////// insert in activity table////
        $flag = dailyActivity($_SESSION['userid'], $dispatch_no, "IMAGE DELETE", "DELETE", $ip, $link1, $flag);
        ///// check both master and data query are successfully executed
    header("location:product_image_list.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
    exit;
}else if($_GET['delete_prodesc_id']){

   //// initialize transaction parameters
        $flag = true;
        mysqli_autocommit($link1, false);
        $err_msg = ""; 
        $sql2 ="SELECT image_url,product_id FROM pr_images where img_id= '".$_GET['delete_prodesc_id']."'";
        $res2 = mysqli_query($link1,$sql2) or die(mysqli_error($link1));
	$image_res1=mysqli_fetch_assoc($res2);
          
        $sql1 ="SELECT * FROM product_master where id= '".$image_res1['product_id']."'";
        $rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	$row1=mysqli_fetch_assoc($rs1); 
          
        unlink('../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'].'/product_desc/'.$image_res1['image_url']);
        $req_res1 = mysqli_query($link1, "delete from pr_images where img_id='".$_GET['delete_prodesc_id']."'");

//// check if query is not executed
                if (!$req_res1) {
                    $flag = false;
                    $err_msg = "Error Code1: " . mysqli_error($link1);
                } 
///// check both master and data query are successfully executed
        if ($flag) {
            mysqli_commit($link1);
            $msg = "Product description image is deleted";
            $cflag = "success";
            $cmsg = "Success";
        } else {
            mysqli_rollback($link1);
            $msg = "Product description image could not be deleted " . $err_msg . ". Please try again.";
            $cflag = "danger";
            $cmsg = "Failed";
        }
        mysqli_close($link1);
        ////// insert in activity table////
        $flag = dailyActivity($_SESSION['userid'], $dispatch_no, "IMAGE DELETE", "DELETE", $ip, $link1, $flag);
        ///// check both master and data query are successfully executed
    header("location:product_image_list.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
    exit;
}

//////End filters value/////
if ($_POST) { 
    if ($_POST['submit1'] == 'Upload') {
        
        //// initialize transaction parameters
        $flag = true;
        mysqli_autocommit($link1, false);
        $err_msg = "";
                /////// insert image file into database
                $image_id = $_POST['imgid'];
                $file = $_FILES['banner_image'];
                $file_name = $file['name'];
                $file_type = $file['type'];
                $file_size = $file['size'];
                $file_path = $file['tmp_name'];
                $temp = explode(".", $file_name);   
                ///add date into image name before extension
                $fileName = $temp[0].'_'.$curr_time.'.'.$temp[1];               
                            
if ($file_name != "" && ($file_type == "image/jpeg" || $file_type == "image/png" || $file_type == "image/jpg" || $file_type == "image/gif")) {
                    $filename = '../API/bannerimg/' . $fileName;
                    $sql2 ="SELECT image_url FROM pr_images where img_id= '".$image_id."'";
          $res2 = mysqli_query($link1,$sql2) or die(mysqli_error($link1));
	  $image_res1=mysqli_fetch_assoc($res2);
        unlink("../API/bannerimg/".$image_res1['image_url']);
                        move_uploaded_file($file_path, $filename);
                        $req_res1 = mysqli_query($link1, "update pr_images set image_url='".$fileName."' where img_id='".$image_id."'");
                    
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
        $flag = dailyActivity($_SESSION['userid'], $dispatch_no, "IMAGE UPLOAD", "UPLOAD", $ip, $link1, $flag);
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
    //// initialize transaction parameters
        $flag = true;
        mysqli_autocommit($link1, false);
        $err_msg = "";
                /////// insert image file into database
                $image_id = $_POST['proimgid'];
                $file = $_FILES['pro_image'];
                $file_name = $file['name'];
                $file_type = $file['type'];
                $file_size = $file['size'];
                $file_path = $file['tmp_name'];
                $temp = explode(".", $file_name);   
                ///add date into image name before extension
                $fileName = $temp[0].'_'.$curr_time.'.'.$temp[1];               
                            
if ($file_name != "" && ($file_type == "image/jpeg" || $file_type == "image/png" || $file_type == "image/jpg" || $file_type == "image/gif")) {
                    $filename = '../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'].'/product/' . $fileName;
                    $sql2 ="SELECT image_url FROM pr_images where img_id= '".$image_id."'";
          $res2 = mysqli_query($link1,$sql2) or die(mysqli_error($link1));
	  $image_res1=mysqli_fetch_assoc($res2);
        unlink('../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'].'/product/'.$image_res1['image_url']);
                        move_uploaded_file($file_path, $filename);
                        $req_res1 = mysqli_query($link1, "update pr_images set image_url='".$fileName."' where img_id='".$image_id."'");
                    
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
        $flag = dailyActivity($_SESSION['userid'], $dispatch_no, "IMAGE UPLOAD", "UPLOAD", $ip, $link1, $flag);
        ///// check both master and data query are successfully executed
        if ($flag) {
            mysqli_commit($link1);
            $msg = "Product image is uploaded";
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
}else if($_POST['submit3'] == 'Upload') {
    //// initialize transaction parameters
        $flag = true;
        mysqli_autocommit($link1, false);
        $err_msg = "";
                /////// insert image file into database
                $image_id = $_POST['prodescimgid'];
                $file = $_FILES['prodesc_image'];
                $file_name = $file['name'];
                $file_type = $file['type'];
                $file_size = $file['size'];
                $file_path = $file['tmp_name'];
                $temp = explode(".", $file_name);   
                ///add date into image name before extension
                 $fileName = $temp[0].'_'.$curr_time.'.'.$temp[1];               
                            
if ($file_name != "" && ($file_type == "image/jpeg" || $file_type == "image/png" || $file_type == "image/jpg" || $file_type == "image/gif")) {
                    $filename = '../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'].'/product_desc/' . $fileName;
                    $sql2 ="SELECT image_url FROM pr_images where img_id= '".$image_id."'";
          $res2 = mysqli_query($link1,$sql2) or die(mysqli_error($link1));
	  $image_res1=mysqli_fetch_assoc($res2);
          
        unlink('../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'].'/product_desc/'.$image_res1['image_url']);
                        move_uploaded_file($file_path, $filename);
                        $req_res1 = mysqli_query($link1, "update pr_images set image_url='".$fileName."' where img_id='".$image_id."'");
                    
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
        $flag = dailyActivity($_SESSION['userid'], $dispatch_no, "IMAGE UPLOAD", "UPLOAD", $ip, $link1, $flag);
        ///// check both master and data query are successfully executed
        if ($flag) {
            mysqli_commit($link1);
            $msg = "Product description image is uploaded";
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

<!-- <script>
$(document).ready(function(){
    $('#myTable').dataTable();
});

$(document).ready(function(){
     $("#add_row").click(function(){  
                var numi = document.getElementById('rowno');
		var num = (document.getElementById("rowno").value -1)+ 2;
		if($("#addr"+numi.value+":visible").length>=0){
		numi.value = num;		
     	var r='<tr id="addr'+num+'" class="col-md-12"><td class="col-md-1" colspan="3"><div style="display:inline-block;float:right"><i class="fa fa-close fa-lg" onClick="deleteRow('+num+');"></i></div></td><td class="col-md-5"><label class="col-md-6 control-label">Image Sequence</label><div class="col-md-6"><input type="text" name="image_seq[' + num + ']" id="image_seq[' + num + ']" class="form-control"></div></td><td class="col-md-6"><label class="col-md-6 control-label">Upload Image</label><div class="col-md-6"><input type="file" name="product_image[' + num + ']" id="product_image[' + num + ']" class="form-control"></div></td></tr>';
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

//delete record of banner
function delete_data(id)
{ 
 if(confirm('Are you sure to delete? ?'))
 {
  window.location.href='<?=$escaped_url?>?delete_id='+id;
 // window.location.reload();
 }
}

</script>-->
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-file-image-o fa-lg"></i>&nbsp;Product Image View</h2>
      <?php if($_REQUEST[msg]){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      <?php }?>
	  <br><br>
          <table width="100%" class="table table-bordered table-hover">
              <thead>
              <tr class="bg-primary1">
                  <th style="font-size:13px;" colspan="4">Product Details</th>                
                </tr>
            </thead>
        <tbody>
              <tr>                                 
              <td class="col-md-3 control-label">Part Name</td>                      
              <td class="col-md-3"><?=$row1['productname'].'('.$row1['productcode'].')'?></td>
              <td class="col-md-3 control-label">Brand</td>                      
              <td class="col-md-3"><?=$row4['brandname']?></td>                  
              </tr>
              <tr>                                 
              <td class="col-md-3 control-label">Part Category</td>                      
              <td class="col-md-3"><?=$row2['cat_name']?></td>
              <td class="col-md-3 control-label">Part Sub Category</td>                      
              <td class="col-md-3"><?=$row3['prod_sub_cat']?></td>                  
              </tr>
              <tr>                                 
              <td class="col-md-3 control-label">Status</td>                      
              <td class="col-md-3"><?=$statusval?></td>
              <td class="col-md-3 control-label"></td>                      
              <td class="col-md-3"></td>                  
              </tr>
            </tbody>   
          </table>
          <?php if($banner_image != ""){ ?>
          <form id="frm" name="frm" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
          <table width="100%" class="table table-bordered table-hover">
            <thead>
              <tr class="bg-primary1">
                <th style="font-size:13px;" colspan="4">Banner Image</th>                
                </tr>
            </thead>
            <tbody>
              <tr>                                 
              <td class="col-md-6" align="center"><img src="../API/bannerimg/<?php echo $banner_image; ?>" alt="" width="100" height="200" class="img-responsive" /></td>                      
              <td class="col-md-2" align="center"><a id="myBtn" title='view'><i class="fa fa-eye fa-lg" title="view image"></i></a></td>
              <td class="col-md-2" align="center"><a id="editBtn" title='edit'><i class="fa fa-edit fa-lg" title="edit image"></i></a></td>                      
              <td class="col-md-2" align="center"><a onClick="confirmDel('&delete_id=<?=$image_res1['img_id']?>')" href="#" title='delete'><i class="fa fa-trash fa-lg" title="delete image"></i></a></td>                  
              </tr>
            </tbody>            
            
          </table>
          </form>
          <?php }  if($product_image != ""){ ?>
               <table width="100%"  class="table table-bordered table-hover">
            <thead>
              <tr class="bg-primary1">
                <th style="font-size:13px;" colspan="4">Product Image</th>                
              </tr>
            </thead>
            <tbody>
              <tr>                                 
              <td class="col-md-6" align="center"> <img src="<?='../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'].'/product/'. $product_image; ?>" alt="" width="100" height="200" class="img-responsive" /></td>                      
              <td class="col-md-2" align="center"><a id="proBtn" title='view'><i class="fa fa-eye fa-lg" title="view image"></i></a></td>
              <td class="col-md-2" align="center"><a id="proeditBtn" title='edit'><i class="fa fa-edit fa-lg" title="edit image"></i></a></td>                      
              <td class="col-md-2" align="center"><a onClick="confirmDel1('&delete_pro_id=<?=$image_res2['img_id']?>')" href="#" title='delete'><i class="fa fa-trash fa-lg" title="delete image"></i></a></td>                  
              </tr>
            </tbody>   
          </table>
          <?php } ?> 
       <form id="frm2" name="frm2" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
               <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
            <thead>
              <tr class="bg-primary1">
                  <th style="font-size:13px;" colspan="4">Product Description Image</th>                
                </tr>
            </thead>
            <tbody>
              <?php
                  while ($pro_desc_img=  mysqli_fetch_assoc($res4)){ 
                     
                  if($pro_desc_img['image_url'] !="") {  
              ?>              
              <tr>                                 
              <td class="col-md-6" align="center"><img src="<?='../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'].'/product_desc/'. $pro_desc_img['image_url']; ?>" alt="" width="100" height="200" class="img-responsive" /></td>                      
              <td class="col-md-2" align="center"><a id="prodescBtn<?=$pro_desc_img['img_sequence'];?>" onclick="viewData(<?=$pro_desc_img['img_sequence'];?>)" title='view'><i class="fa fa-eye fa-lg" title="view image"></i></a></td>
              <td class="col-md-2" align="center"><a onclick="editData(<?=$pro_desc_img['img_sequence']?>)" title='edit'><i class="fa fa-edit fa-lg" title="edit image"></i></a></td>                      
              <td class="col-md-2" align="center"><a onClick="confirmDel2('&delete_prodesc_id=<?=$pro_desc_img['img_id']?>')" href="#" title='delete'><i class="fa fa-trash fa-lg" title="delete image"></i></a></td>                  
              </tr>
                  <?php }} ?>
            </tbody>
            <tfoot id='productfooter' style="z-index:-9999;">
<!--               <tr>
                <td style="font-size:13px;">
                    <a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a>
                    <input type="hidden" name="rowno" id="rowno" value="0"/>
                </td>
              </tr>-->
                 
            </tfoot>
          </table>
            </form>
          <div class="form-group">
            <div class="col-md-12" align="center">             
              <input type="hidden" name="keyid" id="keyid" value="<?=base64_encode($part_id)?>"/>
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='product_image_list.php?<?=$pagenav?>'">
            </div>
          </div>

  </div>

</div>
</div>

<style>
body {font-family: Arial, Helvetica, sans-serif;}

/* The Modal (background) */
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 40%;
}

/* The Close Button */
.close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}
/* The Close Button */
.close1 {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close1:hover,
.close1:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}
/* The Close Button */
.close2 {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close2:hover,
.close2:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}
/* The Close Button */
.close3 {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close3:hover,
.close3:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}
</style>
</head>
<body>
<!--
<h2>Modal Example</h2>-->

<!-- Trigger/Open The Modal -->
<!--<button >Open Modal</button>-->

<!-- The banner Modal -->
<div id="myModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <span class="close">&times;</span>    
    <img  src="../API/bannerimg/<?php echo $banner_image; ?>" alt="" width="500" height="200" class="img-responsive" />
  </div>
</div>
<div id="editModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
   <span class="close1">&times;</span>
   <form id="frm1" name="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
   <img  src="../API/bannerimg/<?php echo $banner_image; ?>" alt="" width="500" height="200" class="img-responsive" />
   <input type="file" name="banner_image" id="banner_image" class="form-control">
   <input type="hidden" name="imgid" value="<?=$image_res1['img_id']?>">
   <input type="submit" class="btn btn-primary" name="submit1" id="save" value="Upload" title="Add Specification">
   </form>
   </div>
</div>
<!-- The end banner Modal -->

<!-- The product Modal -->
<div id="proModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <span class="close2">&times;</span>
    <img  src="<?='../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'].'/product/'. $product_image; ?>" alt="" width="500" height="200" class="img-responsive" />
  </div>
</div>
<div id="proeditModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
   <span class="close3">&times;</span>
   <form id="frm2" name="frm2" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
   <img  src="<?='../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'].'/product/'. $product_image; ?>" alt="" width="500" height="200" class="img-responsive" />
   <input type="file" name="pro_image" id="pro_image" class="form-control">
   <input type="hidden" name="proimgid" value="<?=$image_res2['img_id']?>">
   <input type="submit" class="btn btn-primary" name="submit2" id="save" value="Upload" title="Add Specification">
   </form>
   </div>
</div>
<!-- The end product Modal -->

<!-- The product desc Modal -->
<?php 
      while ($pro_desc_img=  mysqli_fetch_assoc($res_des)){
      
      if($pro_desc_img['image_url'] !="") {  
              ?> 
<div id="prodescModal<?=$pro_desc_img['img_sequence']?>" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
      <span class="close3" onclick ="div_hide(<?=$pro_desc_img['img_sequence']?>)">&times;</span>
    <img  src="<?='../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'].'/product_desc/'. $pro_desc_img['image_url']; ?>" alt="" width="500" height="200" class="img-responsive" />
  </div>
</div>
      <?php }}  
      while ($pro_desc_img=  mysqli_fetch_assoc($res_des1)){
      
      if($pro_desc_img['image_url'] !="") { ?>
<div id="prodescModal1<?=$pro_desc_img['img_sequence']?>" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
   <span class="close3" onclick ="div_hide1(<?=$pro_desc_img['img_sequence']?>)">&times;</span>
   <form id="frm3" name="frm3" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
   <img  src="<?='../API/pc'.$row1['productcategory'].'/psc'.$row1['productsubcat'].'_'.$row1['productcategory'].'/brand'.$row1['brand'].'_'.$row1['productsubcat'].'_'.$row1['productcategory'].'/product_desc/'. $pro_desc_img['image_url']; ?>" alt="" width="500" height="200" class="img-responsive" />
   <input type="file" name="prodesc_image" id="prodesc_image" class="form-control">
   <input type="hidden" name="prodescimgid" value="<?=$pro_desc_img['img_id']?>">
   <input type="submit" class="btn btn-primary" name="submit3" id="save" value="Upload" title="Add Specification">
   </form>
   </div>
</div>
      <?php }} ?>
<!-- The end product desc Modal -->
<script>
///delete function
function confirmDel(store){
var where_to= confirm("Are you sure to delete banner image?");
if (where_to== true)
 {
  //alert(window.location.href)
  var url="<?php echo $url ?>";
  window.location=url+store;
}
else
 {
return false;
  }
}
function confirmDel1(store){
var where_to= confirm("Are you sure to delete product image?");
if (where_to== true)
 {
  //alert(window.location.href)
  var url="<?php echo $url ?>";
  window.location=url+store;
}
else
 {
return false;
  }
}
function confirmDel2(store){
var where_to= confirm("Are you sure to delete product description image?");
if (where_to== true)
 {
  //alert(window.location.href)
  var url="<?php echo $url ?>";
  window.location=url+store;
}
else
 {
return false;
  }
}
///////   get banner modal //////
// Get the modal
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}
// When the user clicks the button, open the modal 
// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
    modal.style.display = "none";
    }
}

    // Get the modal
var modal1 = document.getElementById('editModal');

// Get the button that opens the modal
var btn1 = document.getElementById("editBtn");
// Get the <span> element that closes the modal
var span1 = document.getElementsByClassName("close1")[0];
btn1.onclick = function() {
    modal1.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span1.onclick = function() {
    modal1.style.display = "none";
}
// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal1) {
    modal1.style.display = "none";
    }
}
//////   end get banner modal   ///////


///////   get product modal //////
// Get the modal
var modal2 = document.getElementById('proModal');

// Get the button that opens the modal
var btn2 = document.getElementById("proBtn");

// Get the <span> element that closes the modal
var span2 = document.getElementsByClassName("close2")[0];

// When the user clicks the button, open the modal 
btn2.onclick = function() {
    modal2.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span2.onclick = function() {
    modal2.style.display = "none";
}
// When the user clicks the button, open the modal 
// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal2) {
    modal2.style.display = "none";
    }
}

    // Get the modal
var modal3 = document.getElementById('proeditModal');

// Get the button that opens the modal
var btn3 = document.getElementById("proeditBtn");
// Get the <span> element that closes the modal
var span3 = document.getElementsByClassName("close3")[0];
btn3.onclick = function() {
    modal3.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span3.onclick = function() {
    modal3.style.display = "none";
}
// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal3) {
    modal3.style.display = "none";
    }
}
///////  end get product modal //////
function viewData(val){
document.getElementById('prodescModal'+val).style.display = "block";
} 
function editData(val){  
document.getElementById('prodescModal1'+val).style.display = "block";
}
///////   get product desc modal  //////
//Function to Hide Popup
function div_hide(val){
document.getElementById('prodescModal'+val).style.display = "none";
}
function div_hide1(val){
document.getElementById('prodescModal1'+val).style.display = "none";
}
</script>

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>