<table width="100%" style="max-width:700px;background:#fff;border-left:1px solid #e4e4e4;border-right:1px solid #e4e4e4;border-bottom:1px solid #e4e4e4;font-family:Arial" border="0" cellpadding="0" cellspacing="0" align="center">
	<tbody>
    	<tr>
    		<td style="border-top:solid 10px #d8232a;line-height:1">
            	<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #e4e4e4;border-top:none">
        			<tbody>
                    	<tr>
          					<td align="left" valign="top" style="padding:14px 0 0 11px"><img src="https://pre.cansale.in/demodms/img/ds_logo.png"><div style="padding-bottom:6px"> &nbsp;</div></td>
          					<td align="right" valign="top" style="padding:12px 11px 7px 0;font-family:Arial;font-size:12px;color:#000"></td>
        				</tr>
      				</tbody>
          		</table>
    		</td>
  		</tr>
  		<tr>
    		<td>
            	<table width="100%" border="0" cellspacing="0" cellpadding="0">
        			<tbody>
                    	<tr>
							<td style="padding:8px 8px 5px 8px;text-align:left;line-height:16px;background:#f7f7f7;font-size:16px;font-weight:bold;color:#333"><?=$_REQUEST['title']?></td>
			        	</tr>
      				</tbody>
               	</table>
         	</td>
  		</tr>
  		<tr>
			<td style="padding:8px 8px 16px 8px;background:#fff">
            	<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tbody>
                    	<tr>
		  					<td style="font-family:Arial">
                            <div style="clear:both">
			  					<div> </div>
								<div style="float:left;max-width:700px">
                                    <div style="padding-bottom:6px"> &nbsp;</div>
                                    <div style="padding-bottom:6px"> &nbsp;</div> 
                                    <div style="padding-bottom:6px"> Hi <?=$_REQUEST['uname']?>,</div>
                                    <div style="padding-bottom:6px"> &nbsp;</div>
                                    <div style="padding-bottom:6px"> Greetings from candoursoft.com!!!</div>
                                    <div style="padding-bottom:6px"> &nbsp;</div>
			  						<div style="padding-bottom:6px"><?=$_REQUEST['msg']?></div>
                  					<div style="padding-bottom:6px"> &nbsp;</div>  
                  					<div style="padding-bottom:6px"> &nbsp;</div> 
	 								<div style="padding-bottom:6px">
                                        Thanks &amp; Regards
                                        <br>
                                        Team CandourSoft<br>
                                        candoursoft.com<br>
                                        info@candoursoft.com<br>
                                    </div>
			  					</div>
                            </div>   
              				<div style="clear:both"></div>
			 				</td>
						</tr>
	  				</tbody>
             	</table>
          	</td>
		</tr>	  
  		<tr>
    		<td style="background:#d7d7d7;height:8px"></td>
  		</tr>		
        <tr>
        	<td style="background:#d7d7d7;height:8px"></td>
        </tr>
   		<?php /*?><tr>
    		<td style="border-bottom:4px solid #d8232a;border-top:1px solid #dedede;padding:0 16px">
      			<div style="color:#999999;font-size:16px;padding:8px;font-family:Arial;"> 
                	You received this email because you are registered on servindia.co with the &nbsp;email address: <?=$_REQUEST['email']?>  &nbsp;<a href="#" style="color:#333333;text-decoration:underline" target="_blank" data-saferedirecturl="#"></a>
	  			</div>
	 		</td>
  		</tr><?php */?>
  		<?php /*?><tr>
    		<td style="border-bottom:4px solid #d8232a;border-top:1px solid #dedede;padding:0 16px">
      			<div style="color:#999999;font-size:16px;padding:8px;font-family:Arial;"><p>We respect your privacy. View our Privacy Policy.If you believe this has been sent to you in 
error, please safely  <a href="<?=$_REQUEST['apiurl']?>ussubscribed.php?email=<?=base64_encode($_REQUEST['email'])?>" style="color:#333333;text-decoration:underline" target="_blank" data-saferedirecturl="#">&nbsp;Unsubscribe&nbsp;</a> here. </p>
	  			</div>
	 		</td>
  		</tr><?php */?>
	</tbody>
</table>
