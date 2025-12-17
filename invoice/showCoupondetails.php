<?php
require_once("../config/config.php");
$coupan_name = $_REQUEST['couponcode'];
$coupan_amt = $_REQUEST['coupon_amt'];
$coupon_namenew = ltrim($coupan_name,',');
?>
<div class="row">
	<div class="col-sm-12 table-responsive">
    	<div class="panel panel-info table-responsive">
      <div class="panel-heading heading1">Available Coupon</strong></div>
      <div class="panel-body">
	  <?php  if(($coupon_namenew != '')) {?>
       <table class="table table-bordered" width="100%">
            <thead>
            <tr class="<?=$tableheadcolor?>">
                <td width="5%" align="center">Sno</th>
                <td width="13%" align="center">Coupon Code</th>
              </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			?>
            <tr>
			<td align="center"><?=$i?></td>
              <td align="center"><strong><?=$coupon_namenew?></strong></td>
             
              </tr>
            <?php
			$i++;
			?>
             
            </tbody>
          </table>
		  

      
		  
		  <?php } else {?>
		  <span style="color:#FF0000">No Coupon Code Available</span>
		  <?php }?>
      </div><!--close panel body-->
    </div><!--close panel-->
	</div>
  </div>
  

  