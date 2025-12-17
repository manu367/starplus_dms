// JavaScript Document
//// Enter Only Numeric value//////
function onlyNumbers(evt){  
  var e = event || evt; // for trans-browser compatibility
  var charCode = e.which || e.keyCode;  
  if (charCode > 31 && (charCode < 48 || charCode > 57)){
    return false;
  }
  return true;
}
////////////////////////
///// Enter Only Float Value/////////
function onlyFloatNum(evt){  
  var e = event || evt; // for trans-browser compatibility
  var charCode = e.which || e.keyCode;  
  if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode!=46){
     return false;
  }
  return true;
}
///////////////////////////////////////
/// Enter only character and number////
function validate(field){
//	alert(field);
  var valid = "ABCDEFabcdef0123456789"
  var ok = "yes";
  var temp;
  for (var i=0; i<field.value.length; i++) {
      temp = "" + field.value.substring(i, i+1);
      if (valid.indexOf(temp) == "-1") ok = "no";
  }
  if (ok == "no") {
     alert("Invalid entry!  Only characters from A-F and numbers are accepted!");
    field.focus();
    field.select();
  }
}
////////////////////////////
/// Enter only character////
function onlyCharcter(field,fieldid) {
 if(document.getElementById(fieldid).value){	
  var x =/^[a-zA-Z]+$/;
  if (!x.test(field)){
	//alert("Enter the correct Email Addraess.");
	document.getElementById(fieldid).value="";
	field.focus();
  }
 }
}
////////////////////////////
/// Currency Format//////////// 
function formatCurrency(num) {
   num = num.toString().replace(/\$|\,/g,'');
   if(isNaN(num))
    num = "0";
    signt = (num == (num = Math.abs (num)));
	num = Math.floor(num*100+0.50000000001);
	cents = num%100;
	num = Math.floor(num/100).toString();
   if(cents<10)
	cents = "0" + cents;
	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++) 
	num = num.substring(0,num.length-(4*i+3))+''+
	num.substring(num.length-(4*i+3));
	return (((signt)?'':'-') + '' + num + '.' + cents);
}
///////////////////////
//// disable enter key
function keyPressed(e)
{ 
     var key;      
     if(window.event)
          key = window.event.keyCode; //IE
     else
          key = e.which; //firefox      

     return (key != 13);
}
///////////////////////
//// convert 01 string in 1
function myFunction(val,ind,elementid){
	//alert(val+","+ind+","+elementid);
  var test5 = new String(val);
  var n = Number(test5);
  if(ind=="none"){
      document.getElementById(elementid).value=n;
  }else{
	  document.getElementById(elementid+"["+ind+"]").value=n;
  }
}
//// check pincode length
function pincodeV(field){
 if(field.value){	
  if (field.value.length !=6 ){	
        alert("Pincode must be in 6 digit.");
		field.value='';
		//field.focus();
  }
 }
}
///// check email id
function checkEmail(field,fieldid) {
 if(document.getElementById(fieldid).value){	
  var x =/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
  if (!x.test(field)){
	alert("Enter the correct Email Addraess.");
	document.getElementById(fieldid).value="";
	field.focus();
  }
 }
}
///// check only numeric value
function checknumb(field){
   pattern = /^[0-9][0-9]*\.?[0-9]*$/;
   if(pattern.test(field.value) == false){
	alert("Only Numeric Value Enter: " + field.value);
	field.focus();
   }
}
//// FOR POP window close
function windowclose(){
	window.close();
	window.opener.location.reload(true);
}
/// check phone no. length
function phoneN(field){
  if(field.value){	
   if((isNaN(field.value)) || (field.length !=10)){
        alert("Enter Valid contact No.It must be in 10 digit.");
		field.value='';
		//field.focus();
   }
  }
}
//// hide submit button
function hideThis(val){
 if(val!="" ){
   document.getElementById(val).style.display= 'none';
 }
}
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
 /////////// confirmation alert ///
function MM_openBrWindow(theURL,winName,features) { //v2.0
window.open(theURL,winName,features);
}
/////-------------------------------------------------
function confirmDel(store){
var where_to= confirm("Do you really want to take this action ??");
 if (where_to== true){
	//alert(window.location.href)
	var url="";
    window.location=url+store;
 }
 else{
  return false;
 }
}
////////////
function myConfirm() {
    var txt;
    var r = confirm("Do you really want to take this action ??");
    if (r == true) {
		return true;
    } else {
        return false;
    }
}