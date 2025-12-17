<?php
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://103.166.62.193:7048/BC210/api/bctech/demo/v1.0/companies(d4c49028-da25-ee11-85e3-00505681c2f5)/CreateOrder',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => $data,
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
curl_setopt($curl, CURLOPT_UNRESTRICTED_AUTH, true);
curl_setopt($curl, CURLOPT_USERPWD, "WIN-HV7GDLNVVA1\SALES:Dontconnect@123");
$response = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$err = curl_error($curl);
curl_close($curl);
mysqli_query($link1,"INSERT INTO gapi_address_request SET api_name='CreateOrder', userid='".$po_no."', request_data='".$data."', response_data='".$response."',emp_id='".$httpcode."', request_date='".$today."', entry_by='".$_SESSION['userid']."'");
//////http code
if ($httpcode!=201) {
   $flag = false;
   $err_msg =  "Getting Error From NOVALASH like " . $err . ".";
}