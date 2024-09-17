<?php
	include 'core/init.php';
	if($getFromU->loggedIn() === true){
		header('Location: home.php');
	}

?>
<html>
<head>
    <title>THE ICTU GRAD CAREER PORTAL</title>
    <meta charset="UTF-8" />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css"/>
    <script src="https://code.jquery.com/jquery-1.10.2.js"></script>

    <link rel="shortcut icon" type="image/x-icon" href="./assets/images/fg.jpg">

    <link rel="stylesheet" href="assets/css/style-complete.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/font-awesome.css" />
    <link rel="stylesheet" href="assets/css/bootstrap.css" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/js/popper.min.js" />
    <link rel="stylesheet" href="assets/js/bootstrap.min.js" />
    <script src="assets/js/jquery-3.1.1.min.js"></script>

</head>
<body>

<header class="grad-header">
            <div class="header-left">
            <h1>Grad Career Portal</h1>
            </div>
            <div class="header-right">
            <img src="assets/images/TY.png" alt="Custom Image" class="header-logo">
            </div>
        </header>

    <div class="preloader" id="preloader">
        <div id="loader"></div>
    </div>

    <div class="container-fluid">
        <div class="main-box">

            <div class="main-box-wrapper">
                <div class="row">
                    <div class="left col-md-6 col-12">
                        <div class="items-wrapper">
                            <div class="item">
                                <span class="fa fa-search"></span>
                                <h3>THE ICTU GRAD CAREER PORTAL</h3>
                            </div>
                        </div>
                    </div>
                    <div class="right col-md-6 col-12">
                        <!-- Display login form by default -->
                        <?php
                        if (!isset($_GET['signup'])) {
                            include 'includes/login.php';
                        } else {
                            include 'includes/signup-form.php';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            var preloader = document.getElementsByClassName('preloader')[0];
            setTimeout(function() {
                preloader.style.display = 'none';
            }, 3000);
        };
    </script>

</body>
</html>
