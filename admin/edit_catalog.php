<?php
////// Function ID ///////
$fun_id = array("u"=>array(151)); // User:, Location:, Admin:22:
//////////////////////////
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}

$refid = base64_decode($_REQUEST['id']);
$res_catalog = mysqli_query($link1,"SELECT * FROM catalog_master WHERE id = '".$refid."'");
$row_catalog = mysqli_fetch_array($res_catalog);
////// if we hit process button
if($_POST){
	if($_POST['Submit']=='Save'){
		///// check for duplicate entry, we will make a post pattern variable to check if data is post same again
		$messageIdent = md5($_POST['Submit'].$_POST['catalog_name']);
		//and check it against the stored value:
		$sessionMessageIdent = isset($_SESSION['msgcatalog'])?$_SESSION['msgcatalog']:'';
		if($messageIdent!=$sessionMessageIdent){//if its different:
			//save the session var:
			$_SESSION['msgcatalog'] = $messageIdent;
			$expl_pcat = explode("~",$_POST['category']);
			if($_POST['status']=="Deactive"){
				///// deactive all data
				$res_deactiv = mysqli_query($link1,"UPDATE catalog_master SET status='Deactive' WHERE productid = '".$expl_pcat[0]."'");
				//// check if query is not executed
				if (!$res_deactiv) {
					 $flag = false;
					 $err_msg = "Error Code0.01:".mysqli_error($link1);
				}
			}else{
				$allowedExts1 = array("gif","jpeg","jpg","png","PNG","GIF","JPEG","JPG","xlsx","xls","doc","docx","ppt","pptx","txt","pdf");
				$allowedExts2 = array("gif","jpeg","jpg","png","PNG","GIF","JPEG","JPG");
				////// check account should not be duplicate
				if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM catalog_master WHERE catalog_name='".$_POST['catalog_name']."'"))==0){
					///check directory
					$dirct = "../catalog_doc/".date("Y-m");
					if (!is_dir($dirct)) {
						mkdir($dirct, 0777, 'R');
					}
					////// check attach file
					if($_FILES['attach']['name'] != ''){	
						$temp = explode(".", $_FILES["attach"]["name"]);
						$extension = end($temp);
						$f_size=$_FILES["attach"]["size"];
						///// check extension
						if(!in_array($extension, $allowedExts1)){
							$msg = ".".$extension." ". "not allowed";
							$cflag = "danger";
							$cmsg = "Failed";
							header("location:catalog_master.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
							exit;
						}
						////// check file size upto 2 MB
						if ($_FILES["attach"]["size"]>2097152){
							$msg = "File size should be less than or equal to 2 mb";
							$cflag = "danger";
							$cmsg = "Failed";
							header("location:catalog_master.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
							exit;
						}
						else{ 
							$file_name1 = $_FILES['attach']['name'];
							$file_tmp = $_FILES['attach']['tmp_name'];
							$up = move_uploaded_file($file_tmp,$dirct."/".time().$file_name1);
							$path1 = $dirct."/".time().$file_name1;	
						}
					}
					$s = 0;
					$psubcategory = $_POST['psubcategory'];
					///// delete all data
					$res_del = mysqli_query($link1,"DELETE FROM catalog_master WHERE productid = '".$expl_pcat[0]."'");
					//// check if query is not executed
					if (!$res_del) {
						 $flag = false;
						 $err_msg = "Error Code0.1:".mysqli_error($link1);
					}
					foreach($psubcategory as $k=>$val){
						$expl_psc = explode("~",$psubcategory[$k]);
						////////////////upload file
						$filename = "fileupload".$k;
						$file_name = $_FILES[$filename]["name"];
						if($file_name){
							//$file_basename = substr($file_name, 0, strripos($file_name, '.')); // get file extention
							$file_ext = substr($file_name, strripos($file_name, '.')); // get file name
							//////upload image
							if ($_FILES[$filename]["error"] > 0){
								$code=$_FILES[$filename]["error"];
							}
							else{
								// Rename file
								$newfilename = str_replace(" ","_",$expl_psc[1])."_".$today.$now.$file_ext;
								move_uploaded_file($_FILES[$filename]["tmp_name"],$dirct."/".$newfilename);
								$file = $dirct."/".$newfilename;
								//chmod ($file, 0755);
							}
							$res_inst = mysqli_query($link1,"INSERT INTO catalog_master SET product_category = '".$expl_pcat[1]."', productid = '".$expl_pcat[0]."', prod_sub_cat = '".$expl_psc[1]."', psubcatid = '".$expl_psc[0]."', catalog_name = '".ucwords($_POST['catalog_name'])."', catalog_description='".ucwords($_POST['catalog_desc'])."', icon_img='".$file."', attachment='".$path1."', status = '".$_POST['status']."', create_by = '".$_SESSION["userid"]."', create_on = '".$datetime."'");
							 //// check if query is not executed
							if (!$res_inst) {
								 $flag = false;
								 $err_msg = "Error Code0.11:".mysqli_error($link1);
							}
							$s++;
						}else{
							
						}
					}
					if($s>0){
						////// insert in activity table////
						dailyActivity($_SESSION['userid'],$_POST['catalog_name'],"CATALOG","ADD",$ip,$link1,"");	
						//return message
						$msg="You have successfully added a new catalog ".$_POST['catalog_name'];
						$cflag = "success";
						$cmsg = "Success";
					}else{
						$msg = "Somthing went wrong.";
						$cflag = "danger";
						$cmsg = "Failed";
					}
				}
				else{
					////// return message
					$msg="Entered catalog is already exist. Please add new only.";
					$cflag = "danger";
					$cmsg = "Failed";
				}
			}
			///// move to parent page
			header("location:catalog_master.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
			exit;
		}else{
			//you've sent this already!
			$msg = "Re-submission was detected.";
			$cflag = "warning";
			$cmsg = "Warning";
			///// move to parent page
			header("location:catalog_master.php?msg=" . $msg . "&chkflag=" . $cflag . "&chkmsg=" . $cmsg . "" . $pagenav);
			exit;
		}
	}
}
if($_REQUEST['category']){ $cat = $_REQUEST['category'];}else{ $cat = $row_catalog['productid']."~".$row_catalog['product_category'];}
$expl_pc = explode("~",$cat);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="../img/titleimg.png" type="image/png">
<title><?=siteTitle?></title>
<script src="../js/jquery.min.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css"> 
<link rel="stylesheet" href="../css/bootstrap-select.min.css">
<script src="../js/bootstrap-select.min.js"></script>
<script>
$(document).ready(function(){
	var spinner = $('#loader');
    $("#frm1").validate({
		submitHandler: function (form) {
			if (!this.wasSent) {
				this.wasSent = true;
				$(':submit', form).val('Please wait...')
						.attr('disabled', 'disabled')
						.addClass('disabled');
				spinner.show();
				form.submit();
			} else {
				return false;
			}
		}
	});
});
function HandleBrowseClick(ind){
    var fileinput = document.getElementById("browse"+ind);
    fileinput.click();
}
function Handlechange(ind){
	var fileinput = document.getElementById("browse"+ind);
	var textinput = document.getElementById("filename"+ind);
	textinput.value = fileinput.value;
}
///// add new row for document attachment
$(document).ready(function() {
	$("#add_row3").click(function() {		
		var numi = document.getElementById('rowno3');
		var itm = "psubcategory[" + numi.value+"]";
		var preno=document.getElementById('rowno3').value;
		var num = (document.getElementById("rowno3").value -1)+2;
		numi.value = num;
		if ((document.getElementById(itm).value != "") || ($("#addr_doc" + numi.value + ":visible").length == 0)) {
			var r = '<tr id="addr_doc'+num+'"><td><i class="fa fa-close fa-lg" onClick="fun_remove3('+num+');"></i><select name="psubcategory['+num+']" id="psubcategory['+num+']" class="form-control selectpicker required" data-live-search="true" required onChange="checkDuplicate('+num+', this.value)"><option value="">--Please Select--</option><?php $pcat=mysqli_query($link1,"Select * from product_sub_category where status = '1'  and productid = '".$expl_pc[0]."' ");while($row_pcat=mysqli_fetch_array($pcat)){?><option value="<?=$row_pcat['psubcatid']."~".$row_pcat['prod_sub_cat']?>"><?=$row_pcat['prod_sub_cat']?></option><?php }?></select></td><td><div style="display:inline-block; float:left"><input type="file" id="browse'+num+'" name="fileupload'+num+'" style="display: none" onChange="Handlechange('+num+');" accept="image/*"/><input type="text" id="filename'+num+'" readonly="true" style="width:300px;" class="form-control"/></div><div style="display:inline-block; float:left">&nbsp;&nbsp;<input type="button" value="Click to upload attachment" id="fakeBrowse'+num+'" onclick="HandleBrowseClick('+num+');" class="btn btn-warning"/></div></td></tr>';
			$('#itemsTable3').append(r);
			makeSelect();
		}
	});
});
function fun_remove3(con){
	var c = document.getElementById('addr_doc' + con);
	c.parentNode.removeChild(c);
	con--;
	document.getElementById('rowno3').value = con;
}
function makeSelect(){
	$('.selectpicker').selectpicker({
		liveSearch: true,
		showSubtext: true
	});
}
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<link href="../css/loader.css" rel="stylesheet"/>
</head>
<body>
	<div class="container-fluid">
  		<div class="row content">
		<?php 
    	include("../includes/leftnav2.php");
    	?>
    		<div class="col-sm-9">
      			<h2 align="center"><i class="fa fa-book"></i>&nbsp;&nbsp;Edit/View Catalog</h2><br/>
      			<div class="form-group"  id="page-wrap" style="margin-left:10px;">
          		<form  name="frm1"  id="frm1"  class="form-horizontal" action="" method="post" enctype="multipart/form-data">
          			<div class="form-group">
            			<div class="col-md-10"><label class="col-md-4 control-label">Category<span class="red_small">*</span></label>
              				<div class="col-md-6">
                 				<select name="category"  id= "category" class="form-control selectpicker required" onChange="document.frm1.submit();" required>
                                    <option value="">--Please Select--</option>
                                    <?php
                                    $pcat=mysqli_query($link1,"Select catid , cat_name  from product_cat_master where status = '1' ");
                                    while($row_pcat=mysqli_fetch_array($pcat)){
                                    ?>
                                    <option value="<?=$row_pcat['catid']."~".$row_pcat['cat_name']?>" <?php if($cat == $row_pcat['catid']."~".$row_pcat['cat_name']) { echo "selected" ;}?>><?=$row_pcat['cat_name']?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
              				</div>
            			</div>
          			</div>
                    <div class="form-group">
            			<div class="col-md-10"><label class="col-md-4 control-label">Catalog Name<span class="red_small">*</span></label>
              				<div class="col-md-6">
                 				<input type="text" name="catalog_name" class="form-control required mastername" id="catalog_name" required value="<?=$row_catalog['catalog_name']?>"/>
              				</div>
            			</div>
          			</div>
                    <div class="form-group">
            			<div class="col-md-10"><label class="col-md-4 control-label">Catalog Description</label>
              				<div class="col-md-6">
                 				<textarea name="catalog_desc" id="catalog_desc" style="resize:none" class="form-control addressfield"><?=$row_catalog['catalog_description']?></textarea>
              				</div>
            			</div>
          			</div>
                    <div class="form-group">
            			<div class="col-md-10"><label class="col-md-4 control-label">Attachment<span class="red_small">*</span></label>
              				<div class="col-md-6">
                 				<input type="file" name="attach" id="attach" class="form-control required" required accept=".xlsx,.xls,image/*,.doc,.docx,.ppt,.pptx,.txt,.pdf"/>&nbsp;<a href='<?=$row_catalog['attachment']?>' target='_blank' title='Download Attachment'><i class='fa fa-download fa-lg faicon' title='Download Attachment'></i></a>
              				</div>
            			</div>
          			</div>
          			<div class="form-group">
            			<div class="col-md-10"><label class="col-md-4 control-label">Status<span class="red_small">*</span></label>
              				<div class="col-md-6">
                 				<select name='status' id='status' class="form-control required" required/>
                                    <option value="Active"<?php if($row_catalog['status']=="Active"){ echo "selected";}?>>Active</option>
                                    <option value="Deactive"<?php if($row_catalog['status']=="Deactive"){ echo "selected";}?>>Deactive</option>
                 				</select>
              				</div>
            			</div>
          			</div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <table class="table table-bordered" width="75%" id="itemsTable3">
                                <thead>
                                    <tr class="<?=$tableheadcolor?>" >
                                        <th width="25%">Sub-Category</th>
                                        <th width="50%">Icon Image</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	<?php 
									$j=0;
									$res_catalogd = mysqli_query($link1,"SELECT * FROM catalog_master WHERE productid = '".$expl_pc[0]."'");
									while($row_catalogd = mysqli_fetch_array($res_catalogd)){
									?>
                                    <tr id="addr_doc<?=$j?>">
                                        <td>
                                        <i class="fa fa-close fa-lg" onClick="fun_remove3(<?=$j?>);"></i>
                                        <select name="psubcategory[<?=$j?>]"  id= "psubcategory[<?=$j?>]" class="form-control selectpicker required" data-live-search="true" required onChange="checkDuplicate(<?=$j?>, this.value)">
                                            <option value="">--Please Select--</option>
                                            <?php
                                            $pcat=mysqli_query($link1,"Select *  from product_sub_category where status = '1' and productid = '".$expl_pc[0]."' ");
                                            while($row_pcat=mysqli_fetch_array($pcat)){
                                            ?>
                                            <option value="<?=$row_pcat['psubcatid']."~".$row_pcat['prod_sub_cat']?>"<?php if($row_catalogd['psubcatid']==$row_pcat['psubcatid']){ echo "selected";}?>><?=$row_pcat['prod_sub_cat']?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                        </td>
                                        <td>
                                            <div style="display:inline-block; float:left">
                                            <input type="file" class="required" id="browse<?=$j?>" name="fileupload<?=$j?>" style="display: none" onChange="Handlechange(<?=$j?>);" accept="image/*"/>
                                            <input type="text" id="filename<?=$j?>" readonly style="width:300px;" class="form-control"/>
                                            </div><div style="display:inline-block; float:left">&nbsp;&nbsp;
                                            <input type="button" value="Click to upload attachment" id="fakeBrowse<?=$j?>" onClick="HandleBrowseClick(<?=$j?>);" class="btn btn-warning"/>
                                            <img src='<?=$row_catalogd['icon_img']?>' width='150px' height='auto'/>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
										$j++;
									}
									?>
                                </tbody>
                            </table>   
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4" style="display:inline-block; float:left">
                            <a id="add_row3" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add More Row</a>
                            <input type="hidden" name="rowno3" id="rowno3" value="<?=($j-1)?>"/>
                        </div>
                        <div class="col-md-8" style="display:inline-block; float:right" align="left">
                            <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Save" title="Update this details" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>
                            <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='catalog_master.php?<?=$pagenav?>'">
                        </div>
                    </div>
    			</form>
      			</div>
    		</div>
		</div>
	</div>
    <div id="loader"></div> 
	<?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
<script type="text/javascript">
///// function for checking duplicate Product value
function checkDuplicate(fldIndx1, enteredsno) { 
 document.getElementById("save").disabled = false;
	if (enteredsno != '') {
		var check2 = "psubcategory[" + fldIndx1 + "]";
		var flag = 1;
		for (var i = 0; i <= fldIndx1; i++) {
			var check1 = "psubcategory[" + i + "]";
			if (fldIndx1 != i && (document.getElementById(check2).value == document.getElementById(check1).value )){
				if ((document.getElementById(check2).value == document.getElementById(check1).value)) {
					alert("Duplicate Row Selection.");
					document.getElementById(check2).value = '';
					document.getElementById(check2).style.backgroundColor = "#F66";
					flag *= 0;
				}
				else {
					document.getElementById(check2).style.backgroundColor = "#FFFFFF";
					flag *= 1;
					///do nothing
				}
			}
		}//// close for loop
		if (flag == 0) {
			return false;
		} else {
			return true;
		}
	}	
}
</script>    
</body>
</html>