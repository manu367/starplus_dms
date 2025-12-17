<?php
////// Function ID ///////
$fun_id = array("a"=>array(91));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"],$_SESSION["utype"])){exit;}
function makeTree($par_id,$link1) {
    //your sql code here
	$subsql = mysqli_query($link1,"SELECT username FROM admin_users WHERE reporting_manager = '".$par_id."'");
    $pages = mysqli_num_rows($subsql);

    if ($pages>0) {
        echo '<ul>';
        while ($page=mysqli_fetch_array($subsql)) {
			$userdetails = mysqli_fetch_assoc(mysqli_query($link1,"SELECT uid,name,designationid FROM admin_users WHERE username='".$page["username"]."'"));
            echo '<li><a href="#">'.$userdetails['name'].'<br/><i>('.getAnyDetails($userdetails["designationid"],"designame","designationid","hrms_designation_master",$link1).')</i></a>';
				makeTree($page['username'],$link1);
            echo '</li>';
        }
        echo '</ul>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="shortcut icon" href="../img/titleimg.png" type="image/png">
 <link href="../css/btree.css" type="text/css" rel="stylesheet"/>
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
	<div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa <?=$fa_icon?>"></i> Employee Hierarchy</h2>
      <form class="form-horizontal" role="form" name="form1" id="form1" action="" method="post">
        <div class="row">
                <div class="col-sm-3 col-md-3 col-lg-3" align="right"><strong>Employee Name</strong></div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                <select  name='user_id' id="user_id" class='form-control selectpicker' data-live-search="true">
                  <option value=''>--Select one--</option>
                    <?php
					if($_SESSION["userid"]=="admin"){
						$sql = "SELECT username,name,oth_empid,designationid FROM admin_users where 1 and oth_empid!='' order by name";
					}else{
                    	$sql = "SELECT username,name,oth_empid,designationid FROM admin_users where 1 AND (reporting_manager='".$_SESSION["userid"]."' or username='".$_SESSION["userid"]."') order by name";
					}
                    $res = mysqli_query($link1,$sql);
                    while($row = mysqli_fetch_array($res)){
                    ?>
                  <option value="<?=$row['username']."~".$row['designationid']."~".$row['name']?>"<?php if($_REQUEST['user_id']==$row['username']."~".$row['designationid']."~".$row['name']){echo 'selected';}?>><?=$row['name']." | ".$row['username']." | ".$row['oth_empid']?></option>
                    <?php
                    }
                    ?>
               </select>
             </div>
          <div class="col-sm-3 col-md-3 col-lg-3">
               <input name="Submit" type="submit" class="btn <?=$btncolor?>" value="GO"  title="Go!">
                <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
            </div>
        </div><!--close form row-->
      </form>
      <?php 
	  $expl_usr = explode("~",$_REQUEST["user_id"]); 
	  if($expl_usr[0]){
	  ?>
       <div class="tree" style="overflow-x: auto">
    	<ul>
        	<li><a href="#"><?=$expl_usr[2]?><br><i>(<?=getAnyDetails($expl_usr[1],"designame","designationid","hrms_designation_master",$link1)?>)</i></a>
    			<?php echo makeTree($expl_usr[0],$link1); ?>
        	</li>
        </ul>
	</div>
    <?php }?>
    </div>
    </div>
    </div>
</body>
</html>
