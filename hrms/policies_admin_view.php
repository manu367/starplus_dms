<?php
require_once("../config/config.php");
$id = base64_decode($_REQUEST['id']);

$info = mysqli_fetch_array(mysqli_query($link1, "SELECT * FROM hrms_policy_master WHERE sno = '".$id."' "));

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
 
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-file-text-o"></i> View Policy </h2>
      <?php if($_REQUEST['msg']!=''){?>
      	<h4 align="center">
        	<span 
			<?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";} else echo "class='info-fail' style='color:#FF0033'";?>>
			<?php echo $_REQUEST['msg'];?>
			</span>
        </h4>
	  <?php }?>
      <br>     
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data" >
                               
          <div class="panel-group">
            <div class="panel panel-info table-responsive">
                <div class="panel-heading heading1"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Policy Details</div>
                 <div class="panel-body">
                 	<div style="text-align:center; font-weight:600;"> Release Date : <?=$info['release_date']?> </div>
                    <div style="text-align:center; margin-top:20px; margin-bottom:40px; font-weight:600;"><u> Subject : <?=$info['subject']?> </u></div>
                    <div>
                    <?=$info['msg']?>
                    </div>
                    <div style="text-align:right; margin-top:30px; margin-bottom:30px; font-weight:600;" >
                    	<?php if($info['filepath']) {?> Please find the attachment :  <a href='<?=$info['filepath']?>' target='_blank' title='download'><i style="margin-right: 20px;margin-left: 20px;font-size: 25px;color: blue;" class='fa fa-paperclip ' title='Download Document'></i></a><?php }?>
                    </div>                
                </div><!--close panel body-->
            </div><!--close panel-->
          </div>
                    
          <br><br>
          <div class="form-group">
              <div class="col-md-12" style="text-align:center;" > 
                  <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='policies_admin_list.php?<?=$pagenav?>'">
              </div>  
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