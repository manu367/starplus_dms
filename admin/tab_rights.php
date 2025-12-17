<?php 
require_once("../config/config.php");
@extract($_POST);
############# if form 1 is submitted #################
if($_POST['submitTab']){
	// Update Masters Rights
	$report=$_REQUEST['report'];
	$count=count($report);
    $j=0;
	 mysqli_query($link1 ,"update report_master set utype = '1' ");
    while($j < $count){  	
   mysqli_query($link1,"update report_master set utype='2' where id='".$report[$j]."' ")or die("er3".mysqli_error($link1));  
         $j++;
	}

	
   // end Rights
}

############# if form 2 is submitted #################
if($_POST['submitTab1']){
//////////////  main function tab right //////////////////////////////
 	$function = $_REQUEST['funcTB1'];
	$count=count($function);
    $j=0;
	 mysqli_query($link1 ,"update function_master set utype = '1' ");
	  while($j < $count){  	
   mysqli_query($link1,"update function_master set utype='2' where id='".$function[$j]."' ")or die("er3".mysqli_error($link1));  
         $j++;
	}
	// Update Function Rights
	///////////////  sub function tab rights ///////////////////////////
	$report1 = $_REQUEST['report1'];
	$count1=count($report1);
    $k=0;
	 mysqli_query($link1 ,"update sub_function_master set utype = '1' ");
	  while($k < $count1){  	
   mysqli_query($link1,"update sub_function_master set utype='2' where id='".$report1[$k]."' ")or die("er3".mysqli_error($link1));  
         $k++;
	}
	
	
	///////////////////////////////////////////////////////////////////
	
	// end Function Rights
}

############# if form 4 is submitted #################
if($_POST['submitTab3']){
	// Updatign Cancellation Rights
	$report3=$_REQUEST['report3'];
	$count3=count($report3);
	$l=0;
	mysqli_query($link1,"update excel_cancel_rights  set utype = '1' ")or die(mysqli_error($link1));
	while($l < $count3){
	 mysqli_query($link1,"update excel_cancel_rights set utype='2' where id='".$report3[$l]."' ")or die("er3".mysqli_error($link1));  
	  
	   $l++;
	}
	// end Cancellation Rights
}

############# if form 6 is submitted #################
if($_POST['submitTab5']){
	// Updating Users Rights
	$mapped_code=$_REQUEST['mapped_code'];
	$count=count($mapped_code);
	$j=0;
	mysqli_query($link1,"update admin_users  set utype = '1' ")or die(mysqli_error($link1));
	while($j < $count){
	 mysqli_query($link1,"update admin_users set utype='2' where id='".$mapped_code[$j]."' ")or die("er3".mysqli_error($link1));  
	 $j++;
	}
	// end Users Rights
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
 <script language="javascript" src="../js/ajax.js"></script>
 <script>
 function checkAll(field){
   for (i = 0; i < field.length; i++)
        field[i].checked = true ;
 }
 function uncheckAll(field){
   for (i = 0; i < field.length; i++)
        field[i].checked = false ;
 }
 ///// multiple check all function
 function checkFunc(field,ind,val){
	var chk=document.getElementById(val+""+ind).checked;
	if(chk==true){ checkAll(field); }
	else{ uncheckAll(field);}
 }

</script>
<script>
$(document).ready(function() {
    if (location.hash) {
        $("a[href='" + location.hash + "']").tab("show");
    }
    $(document.body).on("click", "a[data-toggle]", function(event) {
        location.hash = this.getAttribute("href");
    });
});
$(window).on("popstate", function() {
    var anchor = location.hash || $("a[data-toggle='tab']").first().attr("href");
    $("a[href='" + anchor + "']").tab("show");
	if(location.hash=="#menu1"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="none";
	}
	else if(location.hash=="#menu3"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="none";
	}
	else if(location.hash=="#menu5"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="";
	}
	else{
		document.getElementById("home").style.display="";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="none";
	}
});
function tabDisplay(){
$(document).ready(function() {
    if (location.hash) {
        $("a[href='" + location.hash + "']").tab("show");
    }
    $(document.body).on("click", "a[data-toggle]", function(event) {
        location.hash = this.getAttribute("href");
    });
});
if(location.hash=="#menu1"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="none";
	}
	else if(location.hash=="#menu3"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="none";
	}
	else if(location.hash=="#menu5"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="";
	}
	else{
		document.getElementById("home").style.display="";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="none";
		document.getElementById("menu4").style.display="none";
		document.getElementById("menu5").style.display="none";
	}
}


</script>
</head>
<body onLoad="tabDisplay();">
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-users"></i> Update Location Right</h2>
      <h4 align="center">
      <?php if($_POST['submitTab']=='Save' || $_POST['submitTab1']=='Save'  || $_POST['submitTab3']=='Save'  || $_POST['submitTab5']=='Save'){ ?>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <span style="color:#FF0000"><?php if($_POST['submitTab']=="Save"){ echo "Master";}elseif($_POST['submitTab1']=="Save"){ echo "Processes";}elseif($_POST['submitTab3']=="Save"){ echo "Cancellation";} ?> permissions are updated.</span>
   <?php } ?>
      </h4>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
         <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#home">Master </a></li>
          <li><a data-toggle="tab" href="#menu1">Processes</a></li>
          <li><a data-toggle="tab" href="#menu3">Cancellation</a></li>
        
         </ul>
         <!-- Tab 1 Master / Region Rights-->
           <div id="home" class="tab-pane fade in active">
          <form id="frm" name="frm" class="form-horizontal" action="" method="post">
          <div class="table-responsive"> 
              <div class="form-buttons" style="float:right">
                <input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm.report)" value="Check All" />
                <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm.report)" value="Uncheck All" /></div>
                 <table id="myTable" class="table table-hover">
                <?php
				$rs=mysqli_query($link1,"select header_id,header from report_master where status='Y' group by header_id ORDER by header_id");
				$num=mysqli_num_rows($rs);
				if ($num > 0) {
				?>
                 <?php $j=1;
						while($row=mysqli_fetch_array($rs)){
						$report="Select * from report_master where status='Y' and header_id='$row[header_id]' order by header";
						$rs_report=mysqli_query($link1,$report) or die(mysqli_error($link1));
						?>
                <thead>
                  <tr>
                    <th style="border:none">&nbsp;<?=$row['header']?></th>
                  </tr>
                </thead>
                <tbody>
                <?php $i=1;
						while($row_report=mysqli_fetch_array($rs_report)){
						if($i%4==1){
						?>
                  <tr>
                  <?php 
				 
				  	}
						?>
                    <td>				
					<input style="width:20px" type="checkbox"   id="report" name="report[]" value="<?=$row_report[id]?>" <?php if($row_report['utype'] == '2')  echo "checked";?>  />
					<?php
					$totalid = array();		
				   if($totalid==""){
							$totalid[] = $row_report['id'] ;
 								}else{
							$totalid[]= $row_report['id'];			
							} 
							?>
					<input type="hidden" id="reportid" name="reportid[]" value="<?=$totalid?>" >
					
                <?=$row_report[name]?></td>
                  <?php if($i/4==0){?>
                  </tr>
                  <?php 		
				  }
 				
				  $i++;
						}
						} 
                   $j++;
				?>
			
                </tbody>
              <?php
				}
			  ?>
                
            
              </table>
              </div>
              <div id="state_dis"></div>
              <div class="form-buttons" align="center">
              <input type="submit" class="btn btn-primary" name="submitTab" id="submitTab" value="Save"> 
              <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu1'">Next</button>
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo $_REQUEST['userid'];?><?=$pagenav?>'">
              </div>
          </form>
          </div>
          <!-- Tab 2 Processes Rights-->
          <div id="menu1" class="tab-pane fade">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="table-responsive"> 
                <table id="myTable" class="table table-hover">
                <?php 
				$rs=mysqli_query($link1,"select * from function_master where status='Active'");
                $num=mysqli_num_rows($rs);
                if($num > 0){
                   $j=1;
                   while($row=mysqli_fetch_array($rs)){
                   $report="select * from sub_function_master where function_id='$row[function_id]' and status='Y' ORDER by sub_name";
                   $rs_report=mysqli_query($link1,$report) or die(mysqli_error($link1));
                ?>
                <thead>
                  <tr>
                    <th style="border:none">&nbsp;<?=$row['function_name']?>&nbsp;<input style="width:20px"  type="checkbox" id="funcTB1" name="funcTB1[]"  <?php if($row['utype'] == '2')  echo "checked";?>  value="<?=$row['id'];?>"/>  </th>
                  </tr>
                </thead>
                <tbody>
                 <?php 
				   $i=1;
                    while($row_report=mysqli_fetch_array($rs_report)){
                       if($i%4==1){?>
                  <tr>
                  <?php
                       }
                    ?>
                    <td><input style="width:20px"  type="checkbox" id="report1" name="report1[]" value="<?=$row_report['id']?>" <?php if($row_report['utype'] == '2')  echo "checked";?> /><?=$row_report['sub_name']?></td>
                  <?php if($i/4==0){?>
                  </tr>
                  <?php 
                        }
						$i++;
                    }////// Close 2nd While Loop of TAB 2
                    $j++;
				   }  
				}?>
                </tbody>
                </table>
                </div>
            <div class="form-buttons" align="center">
              <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#home'">Previous</button>
              <input type="submit" class="btn btn-primary" name="submitTab1" id="submitTab1" value="Save"> 
              <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu2'">Next</button>
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo $_REQUEST['userid'];?><?=$pagenav?>'">
            </div>
          </form>
          </div>

          <!-- Tab 4 Cancellation Rights-->
          <div id="menu3" class="tab-pane fade">
          <form id="frm3" name="frm3" class="form-horizontal" action="" method="post">
            <div class="table-responsive"> 
              <div class="form-buttons" style="float:right">
                <input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm3.report3)" value="Check All" />
                <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm3.report3)" value="Uncheck All" /></div>
                 <table id="myTable" class="table table-hover">
                <?php
				$rs=mysqli_query($link1,"select * from excel_cancel_rights where status='A' group by tab_name");
				$num=mysqli_num_rows($rs);
				if ($num > 0) {
				?>

                 <?php $j=1;
						while($row=mysqli_fetch_array($rs)){
						$report="Select * from excel_cancel_rights where status='A' and tab_name='$row[tab_name]' and tab_type='CANCEL' order by transaction_type";
						$rs_report=mysqli_query($link1,$report) or die(mysqli_error($link1));
						?>
                <thead>
                  <tr>
                    <th style="border:none">&nbsp;<?=$row['tab_name']?></th>
                  </tr>
                </thead>
                <tbody>
                <?php $i=1;
						while($row_report=mysqli_fetch_array($rs_report)){
						if($i%4==1){
						?>
                  <tr>
                  <?php }
						?>
                    <td><input style="width:20px" type="checkbox" id="report3" name="report3[]" value="<?=$row_report['id']?>" <?php if($row_report['utype'] == '2' ) echo "checked";?> />
                <?=$row_report['transaction_type']?></td>
                  <?php if($i/4==0){?>
                  </tr>
                  <?php 				
				  }
				  $i++;
						}
						} 
                   $j++;
				?>
                </tbody>
              <?php
				}
			  ?>
              </table>
              </div>
            <div class="form-buttons" align="center">
             <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#menu2'">Previous</button>
             <input type="submit" class="btn btn-primary" name="submitTab3" id="submitTab3" value="Save"> 
             <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu4'">Next</button>
             <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='addAdminUser.php?op=edit&id=<?php echo $_REQUEST['userid'];?><?=$pagenav?>'">
             </div>
          </form>
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