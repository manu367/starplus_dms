<?php
require_once("../config/config.php");
if ($_POST['impemail']) {
	$ck_flag = 1;
	/* gmail connection,with port number 993 */ 
	$host = '{mail.candoursoft.com:110/pop3/notls}INBOX'; 
	/* Your gmail credentials */ 
	$user = 'sales@candoursoft.com'; 
	$password = 'Cs@Sales12345'; 
	/* Establish a IMAP connection */ 
	$conn = imap_open($host, $user, $password)or die('unable to connect Email: ' . imap_last_error()); 
	/* Search emails from gmail inbox*/ 
	$mails = imap_search($conn, 'SUBJECT "Lead"'); 
	/* loop through each email id mails are available. */ 
	if ($mails) { 
		/* rsort is used to display the latest emails on top */ 
		rsort($mails);
		/* For each email */ 
		foreach ($mails as $email_number) { 
			/* Retrieve specific email information*/ 
			$headers = imap_fetch_overview($conn, $email_number, 0); 
			//if($headers[0]->subject=="Create Lead"){
			/* Returns a particular section of the body*/ 
			$message = imap_fetchbody($conn, $email_number, '1'); 
			$subMessage = substr($message, 0, 250); 
			//$subMessage = $message; 
			$finalMessage = trim(quoted_printable_decode($subMessage)); 
			//// create lead script
			$party_id = $headers[0]->from;
			$remark = $finalMessage;
			//$makedate = strtotime($headers[0]->date) ;
			//$leaddate = date('Y-m-d' , $makedate);
			$priority = "Warm";
			//Shekhar Gupta <shekhar@candoursoft.com>
			if( strpos( $party_id, "<" ) !== false) {
				$expl_email = explode("<",$party_id);
				$email = str_replace(">","",$expl_email[1]);
				$party_name = rtrim($expl_email[0]," ");
			}else{
				$email = $party_id;
				$party_name = $party_id;
			}
			
			$type_of_lead = "General";
			$source = "2";
			$party_add = $headers[0]->subject;
			
			///// check regenerate same lead
			if(mysqli_num_rows(mysqli_query($link1,"select lid FROM sf_lead_master WHERE import_id='".$email_number."'"))==0){
				$ref = mysqli_query($link1,"select max(lid) as cnt from sf_lead_master order by lid desc");
				$row = mysqli_fetch_assoc($ref);
				$result=$row[cnt]+1;
				$pad=str_pad($result,3,"0",STR_PAD_LEFT);  
				$reference="LD".$pad;
				
				mysqli_query($link1,"insert into sf_status_history set party_id='".$party_name."', status_id='7', trans_type='add_lead', trans_no='".$reference."',update_by='".$_SESSION['userid']."'");
				
				$expl_prod = explode("~",$prod_code);
				
				mysqli_query($link1,"insert into sf_lead_master set partyid='".$party_name."', party_address='".cleanData($party_add)."', intial_remark='".$remark."', priority='".$priority."', vcard_url='".$path1."', reference='".$reference."', type='Lead', category='', tdate='".$today."', status='7', ip='".$ip."', sales_executive='".$designation."', dept_id='".$dept."', party_state='".$locationstate."', party_city='".$locationcity."',party_contact='".$contact_no."',party_email='".$email."', party_country='".$circle."', lead_source='".$source."', create_location='".$_SESSION['mapped_location']."', create_by='".$_SESSION['userid']."',productcode='".$expl_prod[0]."', productname='".$expl_prod[1]."',type_of_lead='".$type_of_lead."',import_id='".$email_number."'");
				if(mysqli_insert_id($link1)>0){	
					dailyActivity($_SESSION['userid'],$reference,"LEAD","ADD",$ip,$link1,"");
					if($email){
						//include "lead_followup.php";
					}
				}
				$ck_flag *= 0;
			}else{
				$ck_flag *= 1;
			}
			//}
		}// End foreach 
	}else{//endif 
		$ck_flag = "";
	}
	/* imap connection is closed */ 
	imap_close($conn); 
	echo $ck_flag;
}
?> 