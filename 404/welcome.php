<?php
include("../config/config.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title><?=siteTitle?></title>
  <meta charset="utf-8">
  <link rel="shortcut icon" href="../img/titleimg.png" type="image/png">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
    <style>
        :root {
            font-family: sans-serif, Arial, Verdana, "Trebuchet MS";
            font-size: xxx-large !important;
        }
    </style>
    <style type="text/css">
        body {
            background: #f5f7fa;
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .style1 {
            font-family: Papyrus, "Segoe UI", Arial, sans-serif;
        }

        #page-wrap {
            background: #ffffff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            margin-top: 60px;
        }

        .uc-wrapper {
            min-height: 70vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .uc-image {
            max-width: 320px;
            width: 100%;
            margin-bottom: 20px;
            opacity: 0.95;
        }

        .uc-text {
            font-weight: 500;
            color: #555;
            letter-spacing: 0.5px;
        }
        /* 🔥 MESSAGE CARD */
        .msg-card {
            text-transform: uppercase;
            background: #ffffff;
            padding: 20px 30px;
            border-radius: 20px;
            outline: 1px solid rgba(139, 0, 0, 0.4);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        /* ✨ HOVER EFFECT */
        .msg-card:hover {
            background: linear-gradient(135deg, #fff5f5, #ffffff);
        }
    </style>
</head>
<body onLoad="">
<div class="container-fluid">
  <div class="row content">
	<?php
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <div class="form-group" id="page-wrap" style="margin-left:10px;">
          <div class="uc-wrapper">
              <img src="../img/uc.jpg" class="uc-image" alt="Under Construction">
              <?php if($_REQUEST['msg']==''){ ?>
                  <h3 class="uc-text">We’re building something solid. Stay tuned.</h3>
              <?php } else { ?>
                  <h3 class="msg-card">
                      <?=$_REQUEST['msg']?>
                  </h3>
              <?php } ?>
          </div>
      </div>
    </div>
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
<script src="https://cdn.jsdelivr.net/npm/motion@11.11.13/dist/motion.js"></script>
<script>
    const { animate, scroll } = Motion
    animate(".uc-wrapper", { scale: [0.4, 1] },
        { ease: "circInOut", duration: 10 })
</script>
<script>
    class  Node{
        constructor(data) {
            this.data=data;
            this.negihbor=[];
        }
    }
    class GrphNode{
        constructor() {
            this.adjaencylist=new Map();
        }
        addNde(data){
            if(data<=0)return;
            if(this.adjaencylist.has(data))return;
            this.adjaencylist.set(data,new Node(data));
        }
        adEdge(data,data2){
            if(!this.adjaencylist.has(data))return;
            if(!this.adjaencylist.has(data2))return;
            const node1=this.adjaencylist.get(data);
            const node2=this.adjaencylist.get(data2);
            node1.negihbor.push(node2);
            node2.negihbor.push(node1)
        }
    }
    const  graph=new GraphNode();
    graph.addNode(12);
    graph.addNode(13);

</script>

</html>
