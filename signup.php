<?php
if(isset($_GET['step']) === true && empty($_GET['step']) === false){
include '../core/init.php';
if (isset($_SESSION['user_id']) === false) {
  header('Location: ../index.php');
}

$user_id = $_SESSION['user_id'];
$user = $getFromU->userData($user_id);
$step = $_GET['step'];

if(isset($_POST['next'])){
  $username = $getFromU->checkInput($_POST['username']);

  if (!empty($username)) {
    if(strlen($username) > 20){
      $error = "Username must be between in 6-20 characters";
    }else if(!preg_match('/^[a-zA-Z0-9]{6,}$/', $username)){
      $error = 'Username must be longer than 6 alphanumeric characters without any spaces';
    } else if($getFromU->checkUsername($username) === true){
      $error = "Username is already taken!";
    }else{
      $getFromU->update('users', $user_id, array('username' => $username));
      header('Location: signup.php?step=2');
    }
  }else{
    $error = "Please enter your username to choose";
  }
}
  ?>
  <!doctype html>
  <html>
  	<head>
  		<title>THE ICTU GRAD CAREER PORTAL</title>
  		<meta charset="UTF-8" />
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css"/>
        <script src="https://code.jquery.com/jquery-1.10.2.js"></script>
   		<link rel="stylesheet" href="../assets/css/style-complete.css"/>
   		<link rel="stylesheet" href="../assets/css/font-awesome.css"/>
	<link rel='stylesheet' href='<?php echo BASE_URL; ?>assets/css/style-complete.css' />
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>assets/css/style.css' />
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>assets/css/font-awesome.css' />
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>assets/css/bootstrap.css' />
    <script src='<?php echo BASE_URL; ?>assets/js/jquery-3.1.1.min.js'></script>

    <script src = 'https://code.jquery.com/jquery-3.2.1.min.js'></script>
  	</head>
  	<!--Helvetica Neue-->
  <body>
  <header class="grad-header">
        <div class="header-left">
            <h1>Grad Career Portal</h1>
        </div>
        <div class="header-right">
        <img src="../assets/images/TY.png" alt="Custom Image" style="width:100px; height:60px;">
        </div>
    </header>

  <!---Inner wrapper-->
  <div class="inner-wrapper">
  	<!-- main container -->
  	<div class="main-container">
  		<!-- step wrapper-->
    <?php if ($_GET['step'] == '1') {?>
   		<div class="step-wrapper">
  		    <div class="step-container">
  				<form method="post" autocomplete="off">
  					<h2>Choose a Username</h2>
  					<h4>Don't worry, you can always change it later.</h4>
  					<div class="form-group">
  						<input class="form-control"type="text" name="username" placeholder="Username" style="font-size: 16px;"/>
  					</div>
  					<div>
  						<ul>
  						  <li><?php if (isset($error)){echo $error;} ?></li>
  						</ul>
  					</div>
  					<div>
  						<input type="submit" name="next" value="Next" style="background-color: red; color: white;" />
  					</div>
  				 </form>
  			</div>
  		</div>
    <?php } ?>
    <?php if ($_GET['step'] == '2'){?>
  	<div class='lets-wrapper'>
  		<div class='step-letsgo'>
  			<h1>We're glad you're here, <?php echo $user->screenName; ?> </h1>
  			<p style="font-size:22px;">The ICTU GRAD CAREER PORTAL is a constantly updating stream of available, accessible, local or abroad, Online and more Jobs--all tailored just for you.</p>
  			<br>
  			<p style="font-size:22px;">
  				Tell us about all the stuff you love and we'll help you get set up.
  			</p>
  			<span>
  				<a href='../home.php' class='backButton' style="color:var(--twitter-color);">Let's go!</a>
  			</span>
  		</div>
  	</div>
  <?php } ?>

  	</div><!-- main container end -->

  </div><!-- inner wrapper ends-->
  </div><!-- ends wrapper -->

  </body>
  </html>

  <?php
}
?>
