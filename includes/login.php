<?php
 
if ($_SERVER['REQUEST_METHOD'] == "GET" && realpath(__FILE__) == realpath($_SERVER['SCRIPT_FILENAME'])) {
  header('Location: ../index.php');
}
  if(isset($_POST['login']) && !empty($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if(!empty($email) or !empty($password)) {
      $email = $getFromU->checkInput($email);
      $password = $getFromU->checkInput($password);

      if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $errorMsg = "Invalid format";
      }else {
        if($getFromU->login($email, $password) === false){
          $errorMsg = "The email or password is incorrect!";
        }
      }
    }else {
      $errorMsg = "Please enter username and password!";
    }
  }
?>

<!--
<div class="login-div">
<form method="post">
	<ul>
		<li>
		  <input type="text" name="email" placeholder="Please enter your Email here"/>
		</li>
		<li>
		  <input type="password" name="password" placeholder="password"/><input type="submit" name="login" value="Log in"/>
		</li>
		<li>
		  <input type="checkbox" Value="Remember me">Remember me
		</li>
    
	</ul>

	</form>
</div>
-->

<div class="top">
    <form method="post" autocomplete="off">
    <img src="assets/images/bird-dance.gif" alt="Logo" class="logo-gif" />
        <h1 class="mb-2" style="text-align:center;">
            Find your dream job <br />or the perfect candidate.
        </h1>

        <div class="form-group form-row">
            <input class="form-control col-4 mr-3 ml-5 mt-1 p-3" name="email" type="text" placeholder="Email" style="height:50px;" />
            <input class="form-control col-4 mr-3 mt-1 p-3" name="password" type="password" placeholder="Password" style="height:50px;" />
        
            <input class="new-btn col-2 mt-1" name="login" type="submit" value="login" style="height:50px; font-size:20px;">
        </div>

        <p style="text-align:center; margin-top:20px;">
            Don't have an account? <a href="?signup=true"style="color:#f62626;">Sign up here</a>
        </p>

        <?php
        if (isset($errorMsg)) {
            echo '<div class="alert alert-danger" role="alert" style="width:400px; margin:20px auto; text-align:center;">
                    ' . $errorMsg . '
                  </div>';
        }
        ?> 
    
    </form>
</div>
<?php
