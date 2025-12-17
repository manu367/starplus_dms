<?php
/*
// By Hemant : July 27, 2022
// returns: string | ''
*/
function getString($type = '', $size = null){

	$resp = '';
	$charset = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	switch($type){
		case 'random':
			$size = ($size)?(($size > 64)?64:$size):1;
			$resp = substr(str_shuffle(str_repeat($charset, $size)), 0, $size);
			break;
		case 'filename':
			$size = ($size < 8)?8:(($size > 16)?16:$size);
			$s = substr(str_shuffle(str_repeat($charset, $size)), 0, $size);
			$resp = time().'_'.$s;
			break;
		default:
			// do nothing
	}
	return $resp;
}

/*
// By Hemant : July 27, 2022
// returns: string | false
*/
function strFilter($str='', $minl=0, $maxl=0){

    $resp = false;
    $str = trim($str);
    // filter reg-ex
    $str = preg_replace("/[^a-zA-Z0-9\s\/@_.,\'\":!#%&(*)+~^\-?]+/", "", $str);
    $min_l = ($minl)?(int)$minl:0;
    $max_l = ($maxl)?(int)$maxl:0;
    if($min_l >= 0 && $max_l >= 0){
        if(strlen($str) >= $min_l){
			if($max_l === 0){
				$resp = $str;
			}
			if(strlen($str) <= $max_l){
				$resp = $str;
			}
		}		
    }
    return $resp;
}

/*
// By Hemant : July 27, 2022
// returns: string | false
*/
function sqlFilter($link, $string, $minl=0, $maxl=0){

	$filtered = strFilter($string, $minl, $maxl);
	if($filtered){
		$resp = mysqli_real_escape_string($link, $filtered);
	}
	elseif($filtered === false){
		$resp = false;
	}
	else{
		$resp = '';
	}
	return $resp;
}

/*
// By Hemant : July 27, 2022
// returns: array (name, url) | false
*/
function uploadThis($file, $path, $type = ''){

	$resp = false;
	if($file && trim($path)){
		$type_map = [
			"pdf" => "application/pdf",
			"jpg" => "image/jpeg",
			"png" => "image/png",
			"csv" => "text/csv",
			"xls" => "application/vnd.ms-excel"
		];
		$type_arr = ($type)?explode(",", $type):'';

		/// upload location & info
		if(!file_exists($path)){
			$create_status = mkdir($path, 0755, true); //0755: drwxr-xr-x
		}
		$file_name = $file["name"];
		$temp_name = $file["tmp_name"];
		$file_type = $file["type"];
		$extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

		/// type verification
		$is_valid = false;
		foreach($type_arr as $type){
			$type = strtolower(trim($type));
			if($type_map[$type] == $file_type){
				$is_valid = true;
				break;
			}
		}
		// upload only if valid type
		if($is_valid){
			$new_filename = getString("filename", 16).'.'.$extension;
			$move_status = move_uploaded_file($temp_name, $path.$new_filename);
			if($move_status){
				$resp = [ "name" => $file_name, "url" => $path.$new_filename ];
			}
		}
	}
	return $resp;
}

/*
// By Hemant : July 27, 2022
// returns: string | false
*/
function isValidEmail($email){

	$resp = false;
	$email = trim($email);
	if($email && filter_var($email, FILTER_VALIDATE_EMAIL)){
		$resp = $email;
	}
	return $resp;
}

/*
// By Hemant : July 27, 2022
// returns: string | false
*/
function isValidMobile($mobile){

	$resp = false;
	$mobile = trim($mobile);
	if($mobile && strlen($mobile) === 10){
		$resp = $mobile;		
	}
	return $resp;
}

/*
// By Hemant : July 28, 2022
// returns: array
*/
function requestFilter($link1, $data){

	$resp = [];
	if($data){
		foreach($data as $k_e_y => $r_e_q){
			if(is_array($r_e_q)){
				foreach($r_e_q as $key_b => $r){
					$r_e_q[$key_b] = sqlFilter($link1, $r);
				}
				$resp[$k_e_y] = $r_e_q;
			}
			else{
				$resp[$k_e_y] = sqlFilter($link1, $r_e_q);
			}
		}
	}
	return $resp;
}

/*
// By Hemant : August 04, 2022
// returns: string
*/
function encryptIt($str){

	$resp = "";
	$ciphering = "AES-128-CTR";
	$iv_length = openssl_cipher_iv_length($ciphering);
	$options = 0;
	$encryption_iv = "CAN10TO07DEV";
	$encryption_key = "theDefaultKey";
	$encryption = openssl_encrypt($str, $ciphering, $encryption_key, $options, $encryption_iv);
	if($encryption){
		$resp = base64_encode($encryption);
	}
	return $resp;
}

/*
// By Hemant : August 04, 2022
// returns: string
*/
function decryptIt($cy){ //echo "DE".$cy;

	$resp = "";
	$ciphering = "AES-128-CTR";
	$iv_length = openssl_cipher_iv_length($ciphering);
	$options = 0;
	$encryption_iv = "CAN10TO07DEV";
	$encryption_key = "theDefaultKey";
	$decryption = openssl_decrypt(base64_decode($cy), $ciphering, $encryption_key, $options, $encryption_iv);
	if($decryption){
		$resp = $decryption;
	}
	return $resp;
}

/*
// By Hemant : August 04, 2022
// returns: string
*/
function getFileToken($url){

	$resp = '';
	$url = trim($url);
	if($url){
		$cy = encryptIt($url);
		if($cy){
			$resp = $cy;
		}
	}
	return $resp;
}

