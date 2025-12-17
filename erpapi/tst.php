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
  CURLOPT_POSTFIELDS =>'{
    "orderNo": "UNCA0020PO2",
    "customerNo": "UNCA0020",
    "orderDate": "2023-10-09",
    "postingDate": "2023-10-09",
    "locationCode": "BHD",
    "lines1": [
        {
            "orderNo": "UNCA0020PO2",
            "orderLineNo": 13,
            "itemNo": "UNFG0001",
            "quantity": 2,
            "unitofMeasureCode": "NOS",
            "unitPrice": 4000
        },
        {
            "orderNo": "UNCA0020PO2",
            "orderLineNo": 14,
            "itemNo": "UNFG0003",
            "quantity": 1,
            "unitofMeasureCode": "NOS",
            "unitPrice": 2000
        }
    ]
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
curl_setopt($curl, CURLOPT_UNRESTRICTED_AUTH, true);
curl_setopt($curl, CURLOPT_USERPWD, "WIN-HV7GDLNVVA1\SALES:Dontconnect@123");
$response = curl_exec($curl);
//$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
if($errno = curl_errno($curl)) {
    $error_message = curl_strerror($errno);
    echo "cURL error ({$errno}):\n {$error_message}";
}
curl_close($curl);
echo $response;
