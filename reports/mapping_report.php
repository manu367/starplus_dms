<?php
////// Function ID ///////
$fun_id = array("a"=>array(68));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
@extract($_GET);
//////////   fetch acess location from function//////////////////////////////////////////////
$acessloc = getAccessLocation($_SESSION['userid'],$link1);
$val   = explode("~" , $_REQUEST['party']);
if($val[1] != ''){
$mappedcode = "mapped_code = '".$val[1]."' ";
}else {
$mappedcode ="1";
}


?>
<!DOCTYPE html>
<html>
 <head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
	
});
$(document).ready(function(){
    $("#frm2").validate();
});


</script>
 <title><?= siteTitle ?></title>
</head>
<body>
    <div class="container-fluid">
        <div class="row content">
            <?php
            include("../includes/leftnav2.php");
            ?>
            <div class="col-sm-9 tab-pane fade in active" id="home">
                <h2 align="center"><i class="fa fa-map"></i> Party Master Report</h2><br>
                <!--<?php if ($_REQUEST['msg']) { ?>c
                                <h4 align="center" style="color:#FF0000"><?= $_REQUEST['msg'] ?></h4>
                <?php } ?>-->
                <form class="form-horizontal" role="form" name="form1" action="" method="get">

                    <div class="form-group">
                        <div class="col-md-6" style=" display:inline" id="show"><label class="col-md-5 control-label">Party Name :</label>	  
                            <div class="col-md-6" align="left">
                                <select name="party" id="party" class="form-control"  onChange="document.form1.submit();">
                                    <option value="">Select Name</option>
                                    <?php
                                    $sql = mysqli_query($link1, "Select distinct(mapped_code) , uid   from mapped_master where uid  in ($acessloc)  and status = 'Y' order by mapped_code");
                                    while ($row = mysqli_fetch_assoc($sql)) {
								 $name = mysqli_fetch_array(mysqli_query($link1,"select uid, name from asc_master where  uid = '".$row['mapped_code']."' " ));									
                                        ?>
										 <option data-tokens="<?=$row['mapped_code']?>" value="<?=$row['uid']."~".$row['mapped_code']?>" <?php if($row['uid']."~".$row['mapped_code'] ==$_REQUEST['party'])echo "selected";?> >
                       <?=$name['name']." | ".$row['mapped_code']?>
          
										</option>
                                            <?php } ?>											
                                </select>
                            </div>
                        </div>
					
					   <div class="form-group">
                        <div class="col-md-6"><label class="col-md-5 control-label">Location Name :</label>	  
                            <div class="col-md-6" align="left">
							<select name="location" id="location" class="form-control"  >
                                    <option value="">Select Name</option>
                                    <?php
                                    $sql = mysqli_query($link1, "Select uid from  mapped_master  where  mapped_code  in ('".$val[1]."') ");
                                    while ($row = mysqli_fetch_assoc($sql)) {
									 $name_pary = mysqli_fetch_array(mysqli_query($link1,"select uid, name from asc_master where  uid = '".$row['uid']."' " ));
                                        ?>
                                        <option value="<?= $name_pary['uid']; ?>" <?php
                                        if ($_REQUEST['location'] == $name_pary['uid']) {
                                            echo "selected";
                                        }
                                        ?>><?= $name_pary['name']."($name_pary[uid])"; ?></option>
                                            <?php } ?>
                                </select>
						     
                            </div>
                        </div>
						</div>
                        <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
                            <div class="col-md-6" align="left">
							<input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
                             <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                            </div>
                        </div>                  
                    </div><!--close form group-->
					
                    <div class="form-group">
                        <div class="col-md-6">&nbsp;</div>
                        <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
                            <div class="col-md-5" align="left">
                                <?php
               
                                ////// check this user have right to export the excel report
                                if ($_REQUEST['Submit'] != '') {
                                    ?>
                                    <a href="excelexport.php?rname=<?= base64_encode("mappingReport") ?>&rheader=<?= base64_encode("Mapping Report") ?>&user_id=<?=base64_encode($_REQUEST['location'])?>&partyname=<?=base64_encode($val[1])?>" title="Export user details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export  details in excel"></i></a>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div><!--close form group-->
                </form>
                <form class="form-horizontal table-responsive" role="form">
                    <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
                        <thead>
                            <tr>     
							<th><a href="#" name="name" title="asc" ></a>SNo.</th>         
                              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Party  Name</th>
							  <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Mapped Location Name</th>
                                <th><a href="#" name="name" title="asc" ></a>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($_REQUEST['Submit']!=""){			
							if($_REQUEST['location'] != ''){
							$st = " uid  = '".$_REQUEST['location']."' ";							
							}else {							
							$st = "1";}	
									
							$i=1;	
						
							////////////   fetching data //////////////////////////////////////////////////////////////////				
                            $sql = mysqli_query($link1, "Select distinct(mapped_code) , uid , status   from mapped_master where $st and $mappedcode  and  status = 'Y' order by mapped_code");
                            while ($row = mysqli_fetch_assoc($sql)) {
                           
                                ?>
                                <tr class="even pointer">  
									<td><?=$i?></td>   
									<td><?= getLocationDetails($row['mapped_code'],"name" ,$link1) ."(" . $row['mapped_code'].")" ; ?></td>        
                                    <td><?=getLocationDetails($row['uid'],"name" ,$link1) ."(".$row['uid'].")";  ;?></td>									
									<td><?php if($row['status'] == 'Y') {echo "Active";} else { echo "Deactive";}?></td>
                                </tr>
                            <?php $i++; }}?>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>


    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>

    <script>
        $(document).ready(function() {
            $('#myTable').dataTable();
        });
   
    </script>
</body>
</html>