<?php
require_once("../config/config.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= siteTitle ?></title>
    <script src="../js/jquery-1.10.1.min.js"></script>
 	<link href="../css/font-awesome.min.css" rel="stylesheet">
 	<link href="../css/abc.css" rel="stylesheet">
 	<script src="../js/bootstrap.min.js"></script>
 	<link href="../css/abc2.css" rel="stylesheet">
 	<link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 	<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 	<script type="text/javascript">
	$(document).ready(function(){
		$('#myTable').dataTable();		
	});
	function openJsonModel(docid){
		var decodedString = atob(docid);
		 $('#myModal .modal-body').html(decodedString);
	}
	function saveTabInfo(refid,indx){
		var tabrefid = refid;
		var mntb = $('#mt'+indx).val();
		var mntbid = $('#mtid'+indx).val();
		var sbtb = $('#st'+indx).val();
		var fntn = $('#fn'+indx).val();
		var icon = $('#ic'+indx).val();
		var stas = $('#ss'+indx).val();
		var modu = $('#md'+indx).val();
		$.ajax({
			type:'post',
			url:'../includes/postAzaxFields.php',
			data:{tabEditSave:"Y", tabid:tabrefid, mntbb:mntb, mntbidd:mntbid, sbtbb:sbtb, fntnn:fntn, iconn:icon, stass:stas, moduu:modu},
			success:function(data){
				var resp = data.split('~');
				$('#infomsg'+indx).html("<br/>"+resp[1]);
			}
		});
	}
	function enableRow(indx){
		////// disabled all row
		$('.editrow').attr("disabled","disabled");
		///// enable only selected
		$('#mt'+indx).removeAttr("disabled");
		$('#mtid'+indx).removeAttr("disabled");
		$('#st'+indx).removeAttr("disabled");
		$('#fn'+indx).removeAttr("disabled");
		$('#ic'+indx).removeAttr("disabled");
		$('#ss'+indx).removeAttr("disabled");
		$('#md'+indx).removeAttr("disabled");
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
            	<h2 align="center"><i class="fa fa-list"></i> Master/Reports/Dashboard Tabs</h2>
                <?php if(isset($_REQUEST['msg'])){?>
                <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                  </button>
                    <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
                </div>
              	<?php }?>                  
<table width="100%" id="myTable" class="table-striped table-bordered table-hover">
  <thead>
  <tr class="<?=$tableheadcolor?>">
    <th width="2%">S.No.</th>
    <th width="15%">Main Tab</th>
    <th width="15%">Sub Tab</th>
    <th width="20%">File Name</th>
    <th width="13%">Icon</th>
    <th width="5%">Status</th>
    <th width="15%">Module Name</th>
    <th width="15%">Action</th>
  </tr>
  </thead>
  <tbody>
  <?php
	$i = 1;
	$res_dv = mysqli_query($link1,"SELECT * FROM report_master WHERE 1 ORDER BY header_id")or die("ER1 ".mysqli_error($link1));
	if(mysqli_num_rows($res_dv)>0){
	while($row_dv = mysqli_fetch_array($res_dv)){
	?>
  <tr>
    <td><?=$i?></td>
    <td><input name="mt<?=$row_dv['id']?>" id="mt<?=$i?>" value="<?=$row_dv['header']?>" disabled class="form-control mastername editrow"/><input name="mtid<?=$row_dv['id']?>" id="mtid<?=$i?>" value="<?=$row_dv['header_id']?>" disabled class="form-control mastername editrow" type="hidden"/></td>
    <td><input name="st<?=$row_dv['id']?>" id="st<?=$i?>" value="<?=$row_dv["name"]?>" disabled class="form-control mastername editrow"/></td>
    <td><textarea name="fn<?=$row_dv['id']?>" id="fn<?=$i?>" disabled class="form-control mastername editrow" style="resize:vertical"><?=$row_dv["file_name"]?></textarea></td>
    <td><i class="fa <?=$row_dv["icon_img"]?> fa-lg"></i><br/><input name="ic<?=$row_dv['id']?>" id="ic<?=$i?>" value="<?=$row_dv["icon_img"]?>" disabled class="form-control mastername editrow"/></td>
    <td align="center"><input name="ss<?=$row_dv['id']?>" id="ss<?=$i?>" value="<?=$row_dv["status"]?>" disabled class="form-control enterrname editrow"/></td>
    <td><textarea name="md<?=$row_dv['id']?>" id="md<?=$i?>" disabled class="form-control mastername editrow" style="resize:vertical"><?=$row_dv["module_name"]?></textarea></td>
    <td><button type="button" class="btn btn-success btn-sm" onClick="enableRow('<?=$i?>');" title="Edit"><i class='fa fa-pencil-square-o'></i> Edit</button>&nbsp;<button type="button" class="btn btn-primary btn-sm" onClick="saveTabInfo('<?=$row_dv['id']?>','<?=$i?>');" title="Save"><i class='fa fa-floppy-o'></i> Save</button><span id="infomsg<?=$i?>"></span></td>
  </tr>
  <?php
		$i++;
    }
  ?>
  <?php 
  }else{
  ?>
  <tr>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
    <td align="center">No</td>
    <td align="center">Data</td>
    <td align="left">Found</td>
    <td align="center">&nbsp;</td>
    <td align="center">&nbsp;</td>
  </tr>
  <?php
  }
  ?>
  </tbody>
</table>
       	  </div>
      		</div>
		</div>
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
</body>
</html>