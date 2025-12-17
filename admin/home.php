<?php
include("../config/config.php");
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script src="../js/bootstrap.min.js"></script>
<style type="text/css">
<!--
.style1 {font-family: Papyrus}
-->
</style>
	<script type="text/javascript">
    <!--
    var b_timer = null; // blink timer
    var b_on = true; // blink state
    var blnkrs = null; // array of spans
    function blink() {
    var tmp = document.getElementsByTagName("span");
    if (tmp) {
    blnkrs = new Array();
    var b_count = 0;
    for (var i = 0; i < tmp.length; ++i) {
    if (tmp[i].className == "blink") {
    blnkrs[b_count] = tmp[i];
    ++b_count;
    }
    }
    // time in m.secs between blinks
    // 500 = 1/2 second
    blinkTimer(500);
    }
    }
    function blinkTimer(ival) {
    if (b_timer) {
    window.clearTimeout(b_timer);
    b_timer = null;
    }
    blinkIt();
    b_timer = window.setTimeout('blinkTimer(' + ival + ')', ival);
    }
    function blinkIt() {
    for (var i = 0; i < blnkrs.length; ++i) {
    if (b_on == true) {
    blnkrs[i].style.visibility = "hidden";
    }
    else {
    blnkrs[i].style.visibility = "visible";
    }
    }
    b_on =!b_on;
    }
    //-->
    </script>
</head>
<body onLoad="blink();">

<?php 
include("../includes/leftnav.php");
?>
<div class="container">
 <div class="col-sm-4" align="left">&nbsp;</div>
 <div class="col-sm-8" align="center" style="height:500px;margin-top:200px;"><span class="blink"><strong style="color:#F00;font-size:20px" class="style1">WELCOME &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; TO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; CANSALE&nbsp;&nbsp; DMS</strong></span></div>
<?php
include("../includes/footer.php");
?>
</div>
</body>
</html>