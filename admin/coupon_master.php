<?php

require_once("../config/config.php");

///// get access location ///

$accessLocation=getAccessLocation($_SESSION['userid'],$link1);



@extract($_GET);


## selected  Status


if($status!=""){

	$status="where status='".$status."'";

}else{

	$status="";

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
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>

 <script>

$(document).ready(function(){

    $('#myTable').dataTable();

});

</script>

<title><?=siteTitle?></title>

</head>

<body>

<div class="container-fluid">

  <div class="row content">

	<?php 

    include("../includes/leftnav2.php");

    ?>

    <div class="col-sm-9 tab-pane fade in active" id="home">

      <h2 align="center"><i class="fa fa-codiepie"></i> Coupon Master</h2>

      <?php if($_REQUEST['msg']){?><br>

      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>

      <?php }?>

	  <form class="form-horizontal" role="form" name="form1" action="" method="get">

	   

	    <div class="form-group">

         <div class="col-md-6"><label class="col-md-5 control-label"> Status</label>	  

			<div class="col-md-5" align="left">

			   <select name="status" id="status" class="form-control selectpicker" data-live-search="true"  onChange="document.form1.submit();">

                    <option value=""<?php if($_REQUEST['status']==''){ echo "selected";}?>>--Please Select--</option>

                    <option value="active"<?php if($_REQUEST['status']=='active'){ echo "selected";}?>>Active</option>

                    <option value="deactive"<?php if($_REQUEST['status']=='deactive'){ echo "selected";}?>>Deactive</option>

                </select>

            </div>

          </div>

		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  

			<div class="col-md-5" align="left">
             
			 <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>

               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>

               <input name="Submit" type="submit" class="btn <?=$btncolor?>" value="GO"  title="Go!">
			  
            </div>

          </div>

	    </div><!--close form group-->

        <div class="form-group">

          <div class="col-md-6"><label class="col-md-5 control-label"></label>

            <div class="col-md-5">

               

            </div>

          </div>

		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  

			<div class="col-md-5" align="left">

               <a href="excelexport.php?rname=<?=base64_encode("coupon_report")?>&rheader=<?=base64_encode("Coupon Report")?>&status=<?=base64_encode($_GET['status'])?>" title="Export coupon details in excel"><i class="fa fa-file-excel-o fa-2x" title="Export coupon details in excel"></i></a>


            </div>

          </div>

	    </div><!--close form group-->

	  </form>

      <form class="form-horizontal table-responsive" role="form">

	
        <button title="Add Coupon" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='add_coupon.php?op=add<?=$pagenav?>'"><span>Add Coupon</span></button><br/><br/>


      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->

       <table  width="99%" id="myTable" class="table-striped table-bordered table-hover" align="center">

          <thead>

            <tr class="<?=$tableheadcolor?>" >

              <th><a href="#" name="entity_id" title="asc" ></a>S.No</th>

              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Coupon Code</th>

              <th><a href="#" name="name" title="asc" ></a>Valid From</th>

              <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Valid To</th>

              <th data-hide="phone,tablet"><a href="#" name="phone" title="asc" class="not-sort"></a>Amount</th>

              <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Create Date</th>

              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Status</th>

              <th data-hide="phone,tablet">View/Edit</th>
			 <th data-hide="phone,tablet">Coupon Mapping</th> 

            </tr>

          </thead>

          <tbody>

            <?php

			 $sno=0;
			
			 $sql=mysqli_query($link1,"Select * from coupon_master  $status order by id");

			while($row=mysqli_fetch_assoc($sql)){

				  $sno=$sno+1;

			?>

            <tr class="even pointer">

              <td><?php echo $sno;?></td>

              <td><?php echo $row['coupon_code'];?></td>

              <td><?php echo $row['valid_from'];?></td>
			  
			  <td><?php echo $row['valid_to'];?></td>

              <td><?php echo $row['amount'];?></td>

              <td><?php echo $row['create_date'];?></td>

              <td><?php echo $row['status'];?></td>

              <td align="center"><a href='add_coupon.php?op=edit&id=<?php echo $row['id'];?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td> 
			  
			<td align="center"><span><button title="Coupon Mapping " type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='coupon_mapping.php?coupon_code=<?=$row['coupon_code']?><?=$pagenav?>'"><strong> Coupon Mapping</strong></button></span></td>

            </tr>

            <?php }?>

          </tbody>

          </table>

      <!--</div>-->

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