<?php
/*Secret Key*/
define('SECURTY_KEY',md5('SukamDms@#$%4321'));
define('GOOGLEAPI_KEY','AIzaSyBEbWSe_aKRgsnbFS24G_KtlN_XZ0luwLQ');
define('ACCESS_KEY','862fb088559a6bb31cdc099deb1fa0e50d56150dcb4e72028fc133f09da11cea');
///// API URL
define('ATTACHMENT_URL','https://sukam.cancrm.in/dms/salesapi/');
/*Data Type*/
define('BOOLEAN',1);
define('INTEGER',2);
define('STRING',3);
/*Error Codes*/
define('REQUEST_METHOD_NOT_VALID',100);
define('REQUEST_CONTENTTYPE_NOT_VALID',101);
define('REQUEST_NOT_VALID',102);
define('VALIDATE_PARAMETER_REQUIRED',103);
define('VALIDATE_PARAMETER_DATATYPE',104);
define('API_NAME_REQUIRED',105);
define('API_PARAM_REQUIRED',106);
define('API_DOES_NOT_EXIST',107);
define('INVALID_USER_PASSWORD',108);
define('USER_NOT_ACTIVE',109);
define('JWT_PROCESSING_ERROR',110);
define('USER_NOT_FOUND',111);
define('TOKEN_NOT_EXPIRE',112);
define('SUCCESS_RESPONSE',200);
define('FAILED_RESPONSE',201);

/*Server Error*/
define('AUTHORIZATION_HEADER_NOT_FOUND',301);
define('ACCESS_TOKEN_ERROR',302);
?>