// JavaScript Document
	var cal = new CalendarPopup("testdiv1");
	cal.offsetX=-20;
	cal.offsetY=20;
	var testpopup5 = new PopupWindow("timelayer");
testpopup5.offsetX=-20;
testpopup5.offsetY=20;
testpopup5.autoHide();
var testpopup5input=null;
function test5popupactivate(obj,anchor) {
	testpopup5input=obj;
	testpopup5.showPopup(anchor);
	}
function testpopup5pick(val) {
	testpopup5input.value = val;
	testpopup5.hidePopup();
	}
function null_out(t,i) {
	if ((t.value == "all") || (t.value == "tba")){
		eval("t.form.start_time_" + i +".disabled=true");
		eval("t.form.end_time_" + i +".disabled=true");
		
		
		eval("t.form.start_time_" + i +".value=\'12:00 am\'");
		if (t.value == "all") {
			eval("t.form.end_time_" + i +".value=\'11:59 pm\'");
		} else {
			eval("t.form.end_time_" + i +".value=\'12:00 am\'");
		}
		eval("turn_off(\'anchor_time_start_" + i +"\')");
		eval("turn_off(\'anchor_time_end_" + i +"\')");
	} else {
		eval("t.form.start_time_" + i +".disabled=false");
		eval("t.form.end_time_" + i +".disabled=false");
		
		eval("turn_on(\'anchor_time_start_" + i +"\')");
		eval("turn_on(\'anchor_time_end_" + i +"\')");
	}
}
function turn_on(whichLayer) {
	if (document.getElementById) {
		// this is the way the standards work
		var style2 = document.getElementById(whichLayer).style;
		style2.visibility = "visible";
	} else if (document.all) {
		// this is the way old msie versions work
		var style2 = document.all[whichLayer].style;
		style2.visibility = "visible";
	} else if (document.layers) {
		// this is the way nn4 works
		var style2 = document.layers[whichLayer].style;
		style2.visibility = "show";
	}
}
function turn_off(whichLayer) {
	if (document.getElementById) {
		// this is the way the standards work
		var style2 = document.getElementById(whichLayer).style;
		style2.visibility = "hidden";
	} else if (document.all) {
		// this is the way old msie versions work
		var style2 = document.all[whichLayer].style;
		style2.visibility = "hidden";
	} else if (document.layers) {
		// this is the way nn4 works
		var style2 = document.layers[whichLayer].style;
		style2.visibility = "hide";
	}
}
