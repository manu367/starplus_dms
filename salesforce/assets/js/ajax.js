// JavaScript Document



function getXMLHttp() {

	var xmlHttp = null;

	if(window.XMLHttpRequest) { // Mozilla, Safari, ...

    	xmlHttp = new XMLHttpRequest();

        if(xmlHttp.overrideMimeType) {

        	xmlHttp.overrideMimeType('text/xml');

		}

	}

    else if(window.ActiveXObject) { // IE

    	try{

        	xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");

		}

        catch(e) {

        	try {

            	xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");

			}

            catch (e) {

            	alert("Error: ActiveXObject is not loaded...");

    		}

		}

	}

    if(!xmlHttp) {

    	alert("Error Occured!! :( Cannot create an xHTTP instance...");

    	return false;

	}

  	return xmlHttp;

	/*

	if(window.ActiveXObject){

		xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");

	}

	if(window.XMLHttpRequest){

		xmlHttp = new XMLHttpRequest();

	}

	return xmlHttp;

	*/

}





function xmlhttpPost(strURL, strSubmit, strResultFunc) {

   var xmlHttpReq = false;

      

        // Mozilla/Safari

        if (window.XMLHttpRequest) {

                xmlHttpReq = new XMLHttpRequest();

             //   xmlHttpReq.overrideMimeType('text/xml');

        }

        // IE

        else if (window.ActiveXObject) {

                xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");

        }

	

	xmlHttpReq.open('POST', strURL, true);

	xmlHttpReq.setRequestHeader('Content-Type','application/x-www-form-urlencoded');

	xmlHttpReq.onreadystatechange = function() {

	if (xmlHttpReq.readyState == 4) {

           strResponse = xmlHttpReq.responseText;

           switch (xmlHttpReq.status) {

                   // Page-not-found error

                   case 404:

                           alert('Error: Not Found. The requested URL ' + 

                                   strURL + ' could not be found.');

                           break;

                   // Display results in a full window for server-side errors

                   case 500:

                           handleErrFullPage(strResponse);

                           break;

                   default:

                           // Call JS alert for custom error or debug messages

                           if (strResponse.indexOf('Error:') > -1 || 

                                   strResponse.indexOf('Debug:') > -1) {

                                   alert(strResponse);

                           }

                           // Call the desired result function

                           else {

							   //alert(strResponse);

							   //return false;

                                 eval(strResultFunc + '(strResponse);');

                           }

                           break;

           }

   }

 }	

 

  xmlHttpReq.send(strSubmit);

}



function handleErrFullPage(strIn) {



        var errorWin;



        // Create new window and display error

        try {

                errorWin = window.open('', 'errorWin');

                errorWin.document.body.innerHTML = strIn;

        }

        // If pop-up gets blocked, inform user

        catch(e) {

                alert('An error occurred, but the error message cannot be' +

                        ' displayed because of your browser\'s pop-up blocker.\n' +

                        'Please allow pop-ups from this Web site.');

        }

}