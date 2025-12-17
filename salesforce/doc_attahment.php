<?php
require_once("../config/config.php");
$url =  "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
$getid=base64_decode($_REQUEST['id']);

$data = mysqli_fetch_array(mysqli_query($link1,"select reference from sf_lead_master where lid = '".$getid."' "));

////// final submit form ////
@extract($_POST);
if($_POST){
	if($_POST['Submit']=='Save'){
		///// Insert in document attach detail by picking each data row one by one
		foreach($document_name as $k=>$val){
			////////////////upload file
			$filename = "fileupload".$k;
			$file_name = $_FILES[$filename]["name"];
			//$file_basename = substr($file_name, 0, strripos($file_name, '.')); // get file extention
			$file_ext = substr($file_name, strripos($file_name, '.')); // get file name
			//////upload image
			if ($_FILES[$filename]["error"] > 0){
				$code=$_FILES[$filename]["error"];
			}
			else{
				// Rename file
				$newfilename = $loccode."_".$todayt.$now.$file_ext;
				move_uploaded_file($_FILES[$filename]["tmp_name"],"../doc_attach/lead/".$newfilename);
				$file="../doc_attach/lead/".$newfilename;
				//chmod ($file, 0755);
			}
			$sql_inst = "INSERT INTO document_attachment set ref_no='".$reference."', ref_type='Lead Document',document_name='".ucwords($document_name[$k])."', document_path='".$file."', updatedate='".$datetime."'";
			$res_inst = mysqli_query($link1,$sql_inst);
		}
		dailyActivity($_SESSION['userid'],$reference,"Lead","Document",$ip,$link1,"");
		////// return message
		$msg="You have successfully attached the document ";
	
	///// move to parent page
    header("Location:lead_list.php?msg=".$msg."".$pagenav);
	exit;
  }
}

/*
if($_GET['delete_prodesc_id']!=''){
	$flag = true;
        $sql2 ="SELECT document_path,ref_no FROM document_attachment where id= '".$_GET['delete_prodesc_id']."'";
        $res2 = mysqli_query($link1,$sql2) or die(mysqli_error());
		$image_res1=mysqli_fetch_assoc($res2); 
          if($image_res1["ref_no"]){
        unlink($image_res1['document_path']);
        $req_res1 = mysqli_query($link1, "delete from document_attachment where id='".$_GET['delete_prodesc_id']."'");
		//// check if query is not executed
		if (!$req_res1) {
			$flag = false;
			$err_msg = "Error Code1: " . mysqli_error($link1);
		} 
		///// check both master and data query are successfully executed
        if ($flag) {
            $msg = "Document is deleted";
        } else {
            $msg = "Document could not be deleted " . $err_msg . ". Please try again.";
        }
        ////// insert in activity table////
        $flag = dailyActivity($_SESSION['userid'], $image_res1["ref_no"], "DOCUMENT DELETE", "DELETE", $ip, $link1, "");
		  }
		  ///// move to parent page
			//header("Location:lead_list.php?msg=".$msg."".$pagenav);
			//exit;
}
*/
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
 <script>
	$(document).ready(function(){
		$("#frm3").validate();
    });
	
 </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
 <script language="javascript" type="text/javascript">
  
  
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
		var itm = "document_name[" + numi.value+"]";
		var preno=document.getElementById('rowno3').value;
		var num = (document.getElementById("rowno3").value -1)+2;
		numi.value = num;
		if ((document.getElementById(itm).value != "") || ($("#addr_doc" + numi.value + ":visible").length == 0)) {
			var r = '<tr id="addr_doc'+num+'"><td width="30%"><div style="display:inline-block;float:right"><input type="text" class="form-control entername required cp" name="document_name['+num+']"  id="document_name['+num+']" value="" style="width:270px;"></div><div style="display:inline-block;float:left;"><i class="fa fa-close fa-lg" onClick="fun_remove3('+num+');"></i></div></td><td width="70%"><div style="display:inline-block; float:left"><input type="file" id="browse'+num+'" name="fileupload'+num+'" style="display: none" onChange="Handlechange('+num+');" accept="image/*"/><input type="text" id="filename'+num+'" readonly="true" style="width:300px;" class="form-control"/></div><div style="display:inline-block; float:left">&nbsp;&nbsp;<input type="button" value="Click to upload attachment" id="fakeBrowse'+num+'" onclick="HandleBrowseClick('+num+');" class="btn btn-warning"/></div></td></tr>';
			$('#itemsTable3').append(r);
		}
	});
});
function fun_remove3(con){
	var c = document.getElementById('addr_doc' + con);
	c.parentNode.removeChild(c);
	con--;
	document.getElementById('rowno3').value = con;
}
function confirmDel(store){
var where_to= confirm("Are you sure to delete this document?");
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
  </script>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-upload"></i> Document Attachment</h2>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
      	  
            	<form  name="frm3" id="frm3" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="form-group">
                	<div class="col-sm-12">
                	<table class="table table-bordered" width="100%" id="itemsTable3">
                    	<thead>
                        	<tr class="<?=$tableheadcolor?>" >
                            	<th width="30%">Document Name</th>
                            	<th width="70%">Attachment</th>
                        	</tr>
                    	</thead>
                    	<tbody>
                        	<tr id="addr_doc0">
                        		<td><input type="text" class="form-control entername required cp" name="document_name[0]"  id="document_name[0]" value=""></td>
                            	<td>
                                	<div style="display:inline-block; float:left">
                                    <input type="file" class="required" id="browse0" name="fileupload0" style="display: none" onChange="Handlechange(0);" accept=".xlsx,.xls,image/*,.doc, .docx,.ppt, .pptx,.txt,.pdf"/>
                                    <input type="text" id="filename0" readonly style="width:300px;" class="form-control required"/>
                                    </div><div style="display:inline-block; float:left">&nbsp;&nbsp;
                                    <input type="button" value="Click to upload attachment" id="fakeBrowse0" onclick="HandleBrowseClick(0);" class="btn btn-warning"/>
                            		</div>
                            	</td>
                        	</tr>
                    	</tbody>
                	</table>   
                	</div>
                </div>
                <div class="form-group">
           			<div class="col-sm-4" style="display:inline-block; float:left">
           			<a id="add_row3" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add More Attachment</a><input type="hidden" name="rowno3" id="rowno3" value="0"/></div>
            		<div class="col-md-8" style="display:inline-block; float:right" align="left">
                    <input type="submit" class="btn <?=$btncolor?>" name="Submit" id="save" value="Save" title="" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>
                    <input name="refid" id="refid" type="hidden" value="<?=$getid?>"/>
                    <input type="hidden" name="reference" id="reference" value="<?=$data['reference']?>" />
                    <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='lead_list.php?<?=$pagenav?>'">
            		</div>
          		</div>
              	</form>
                <form id="frm2" name="frm2" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
               <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
            <thead>
              <tr class="<?=$tableheadcolor?>" >
                  <th style="font-size:13px;" colspan="4">Uploaded Document</th>                
                </tr>
            </thead>
            <tbody>
              <?php
			  	  $res4 = mysqli_query($link1,"select * from document_attachment where ref_no='".$row_locdet['asc_code']."' order by document_name");
                  while ($pro_desc_img=  mysqli_fetch_assoc($res4)){ 
                     
                  if($pro_desc_img['document_path'] !="") {  
              ?>              
              <tr> 
              <td class="col-md-2" align="left"><?=$pro_desc_img['document_name'];?></td>                                   
              <td class="col-md-6" align="center"><?php /*?><img src="<?=$pro_desc_img['document_path'];?>" alt="" width="100" height="200" class="img-responsive" /><?php */?><a href="<?=$pro_desc_img['document_path'];?>" target="_blank"><i class="fa fa-download fa-lg" title="view document"></i></a></td>
             <!-- <td class="col-md-2" align="center"><a onClick="confirmDel('&delete_prodesc_id=<?=$pro_desc_img['id']?>')" href="#" title='delete'><i class="fa fa-trash fa-lg" title="delete document"></i></a></td>     -->             
              </tr>
                  <?php }} ?>
            </tbody>
          </table>
            </form>
         
           <br/>
              
           
         
      </div><!--End form group-->
    </div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>