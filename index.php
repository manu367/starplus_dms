<?php
$page_type = "insecure";
require_once("security/backend.php");
require_once("includes/common_function.php");
require_once("includes/globalvariables.php");

if (isset($_SESSION['userid']) && $_SESSION['userid']) {
    header("Location:admin/home2.php");
    exit;
} else {
    include('chatBot/database.inc.php');
    session_start();
    $browserid = session_id();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= siteTitle ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="img/titleimg.png" type="image/png">
    <link href="loginpages/bootstrap.min.css" rel="stylesheet">
    <script src="loginpages/jquery.min.js"></script>
    <script src="loginpages/bootstrap.min.js"></script>

    <style>
        body {
            min-height: 100vh;
            background:
                    linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)),
                    url('img/Banner-1.jpg') no-repeat center center / cover;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Segoe UI", system-ui, -apple-system;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: rgba(255,255,255,0.95);
            border-radius: 18px;
            padding: 35px 30px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.35);
        }

        .login-logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .login-logo img {
            width: 180px;
        }

        .login-title {
            text-align: center;
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 25px;
            color: #333;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #444;
        }

        .form-control {
            height: 48px;
            border-radius: 10px;
            font-size: 15px;
        }

        .form-control:focus {
            border-color: #4e73df;
            box-shadow: none;
        }

        .btn-login {
            margin-top: 10px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(135deg, #4e73df, #224abe);
            border: none;
            font-size: 16px;
            font-weight: 600;
            color: #fff;
        }

        .btn-login:hover {
            opacity: 0.95;
        }

        .alert-danger {
            font-size: 13px;
            border-radius: 10px;
            padding: 10px;
            margin-top: 15px;
        }

        .footer-text {
            text-align: center;
            font-size: 12px;
            color: #eee;
            margin-top: 20px;
        }

        .footer-text a {
            color: #9db7ff;
            text-decoration: none;
        }

        .footer-text a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="login-wrapper">
    <div class="login-card">

        <div class="login-logo">
            <img src="img/logo.png" alt="Logo">
        </div>

        <div class="login-title">Secure Login</div>

        <form id="login_form" method="post" action="verify.php" novalidate>

            <div class="form-group">
                <label for="userid">USER ID</label>
                <input type="text" class="form-control" id="userid" name="userid" required placeholder="Enter User ID">
            </div>

            <div class="form-group">
                <label for="pwd">PASSWORD</label>
                <input type="password" class="form-control" id="pwd" name="pwd" required placeholder="Enter Password">
            </div>

            <button type="submit" id="sbmt" name="sbmt" class="btn btn-login btn-block">
                Login
            </button>

            <?php if (isset($_REQUEST["msg"])) { ?>
                <div class="alert alert-danger text-center">
                    <?= errorMsg($_REQUEST["msg"]); ?>
                </div>
            <?php } ?>

            <?php
            if (isset($_SESSION["logres"]["msg"])) {
                $color = ($_SESSION["logres"]["status"] == "success") ? "#2e9e2e" : "#e51111";
                echo '<div class="alert text-center" style="color:' . $color . '; background:#f4f4f4; margin-top:15px; border-radius:10px;">
                ' . $_SESSION["logres"]["msg"] . '
                </div>';
                unset($_SESSION["logres"]);
            }
            ?>

        </form>

    </div>

    <div class="footer-text">
        © <?= COMPANYNAME ?> <?= date("Y") ?> · Powered by
        <a href="http://www.candoursoft.com/" target="_blank">Candour Software</a>
    </div>
</div>

<script>
    $("#sbmt").on("click", function (event) {
        var form = $("#login_form")[0];
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
    });
</script>

</body>
</html>
