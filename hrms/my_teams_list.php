<?php
require_once("../config/config.php");
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
 
 <style type="text/css">
 	.dsn {
		display: inline-block; border: 1px solid #2e6da4; padding: 5px; border-radius: 10px; width: 200px; color: #2e6da4;
	}
	.dsn:hover {
		inline-block; border: 1px solid #006633; padding: 5px; border-radius: 10px; width: 200px; color: #006633;
	}
 </style>
 
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-users"></i> My Teams </h2>
      <?php if($_REQUEST['msg']!=''){?>
      	<h4 align="center">
        	<span 
			<?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";} else echo "class='info-fail' style='color:#FF0033'";?>>
			<?php echo $_REQUEST['msg'];?>
			</span>
        </h4>
	  <?php }?>
      <br><br> 
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" >
           
          <div style="text-align:center; color: #006633; font-weight:600;"> --- Manager --- </div> 
          <br>      
              <div style="text-align:center;">     
                  <a href="my_teams_view.php?id=<?=base64_encode($_SESSION['userid']);?>" title="Click for detail">                
                      <div class="dsn"> 
                          <img src="../img/emp1.png" > <br>
                          <?php 
						  echo getAnyDetails($_SESSION['userid'],'name','username','admin_users',$link1)." <br> ( ".$_SESSION['userid'];
						  //echo getAnyDetails($_SESSION['userid'],'empname','loginid','hrms_employe_master',$link1)." <br> ( ".$_SESSION['userid']; ?> ) 
                      </div>
                   </a>    
              </div>
         
          <br><br>
          <div style="text-align:center; color: #006633; font-weight:600;"> --- Team Members --- </div> 
          <div class="form-group">
          <?php 
		  	$i = 1;
		  	//$sql = mysqli_query($link1, "SELECT empname, loginid FROM hrms_employe_master WHERE managerid = '".$_SESSION['userid']."' ");
			$sql = mysqli_query($link1, "SELECT name AS empname, username AS loginid FROM admin_users WHERE reporting_manager = '".$_SESSION['userid']."' ");
		  	while($row =  mysqli_fetch_array($sql)){
			if($row!=""){	
		  ?>
          	<div class="col-md-4">
              	        
                      <div style="text-align:center;margin-top:30px;">     
                          <a href="my_teams_view.php?id=<?=base64_encode($row['loginid']);?>" title="Click for detail">               
                              <div class="dsn"> 
                                  <img src="../img/emp1.png" > <br>
                                  <?php echo $row['empname']." <br> ( ".$row['loginid']; ?> ) 
                              </div>
                          </a>    
                      </div>
              </div>
           <?php }} ?>   
          </div>
          
                              
          <br><br>

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