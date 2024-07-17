<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
$message = "";
$valid = 'true';
include("db_config.php");
session_start();
if (isset($_GET['key']) && isset($_GET['email'])) {
    $key = $_GET['key'];
    $email = $_GET['email'];
    $check = mysqli_query($dbconfig, "SELECT * FROM forget_password WHERE email='$email' and temp_key='$key'");
    if (mysqli_num_rows($check) != 1) {
        echo "This url is invalid or already been used. Please verify and try again.";
        exit;
    }
} else {
    header('location:index.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password1 = mysqli_real_escape_string($dbconfig, $_POST['password1']);
    $password2 = mysqli_real_escape_string($dbconfig, $_POST['password2']);
    if ($password2 == $password1) {
        $message_success = "New password has been set for " . $email;
        $password = md5($password1);
        mysqli_query($dbconfig, "DELETE FROM forget_password where email='$email' and temp_key='$key'");
        mysqli_query($dbconfig, "UPDATE user set user_password='$password' where email='$email'");
        
        // Send confirmation email
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = 0; // Disable verbose debug output
            $mail->isSMTP(); // Send using SMTP
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = 'naolao11111@gmail.com'; // SMTP username
            $mail->Password = 'lkvy cveu vsnn xlql'; // SMTP password
            $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587; // TCP port to connect to

            // Recipients
            $mail->setFrom('naolao11111@gmail.com', 'RP Requesting change of password');
            $mail->addAddress($email); // Add a recipient

            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Password Changed Successfully';
            $mail->Body = "Hi $email, <br><br> We wanted to let you know that your password was reset. If you did not initiate this change, please contact Republic Polytechnic's support immediately. <br><br>
            Please do not reply to this email with your password. We will never ask for your password, and we strongly discourage you from sharing it with anyone.";
            $mail->send();
        } catch (Exception $e) {
            // Handle the error if email could not be sent
        }
    } else {
        $message = "Verify your password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Reset Password</title>
    <style>
        .rounded-box {
            background-color: #D2D1D1;
            border-radius: 15px;
            padding: 20px;
        }
        .text-center {
            text-align: center;
        }
        .input-group .input-group-text {
            cursor: pointer;
        }
        .glyphicon-ok {
            color: #00A41E;
        }
        .glyphicon-remove {
            color: #FF0004;
        }
        .password-criteria ul {
            list-style-type: none;
            padding: 0;
        }
        .password-criteria ul li {
            display: flex;
            align-items: center;
        }
        .password-criteria ul li .glyphicon {
            margin-right: 8px;
        }
        .custom-background {
            background-image: linear-gradient(rgba(4,9,30,0.7), rgba(4,9,30,0.7)), url('../Domain_picture/frontRP.jpg');
            background-size: 100% 100%; /* Stretch the image to fit the entire container */
            background-repeat: no-repeat; /* Ensure the image doesn't repeat */
            background-position: center; /* Center the image */
        }
        .form-container {
            margin: auto; /* Center the form */
            padding: 20px; /* Add some padding for better spacing */
        }
    </style>
</head>
<body>
<div class="custom-background py-3 py-md-5">
  <div class="container">
    <div class="row justify-content-md-center">
      <div class="col-12 col-md-11 col-lg-8 col-xl-7 col-xxl-6 form-container">
        <div class="bg-white p-4 p-md-5 rounded shadow-sm">
          <div class="row gy-3 mb-5">
            <div class="col-12">
              <div class="text-center">
                <a href="../home.php">
                  <img src="../Domain_picture/rp-logo.png" alt="Republic Polytechnic" width="175" height="57">
                </a>
              </div>
            </div>
            <div class="col-12">
              <h2 class="fs-6 fw-normal text-center text-secondary m-0 px-md-5">Use the form below to change your password. Your password cannot be the same as your username.</h2>
            </div>
          </div>
          <form method="POST" id="passwordForm" onsubmit="return validatePassword();">
            <div class="row gy-3 gy-md-4 overflow-hidden">
              <div class="col-12">
                <label for="password1" class="form-label">New Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="password" class="form-control form-control-lg" name="password1" id="password1" placeholder="New Password" autocomplete="off" required>
                  <span class="input-group-text">
                    <i class="fa fa-eye toggle-password" toggle="#password1"></i>
                  </span>
                </div>
                <small id="passwordHelpBlock" class="form-text text-muted">
                  Your password must be at least 8 characters long, contain a mix of upper and lowercase letters, numbers, and must not contain spaces, special characters, or emojis.
                </small>
              </div>
              <div class="col-12">
                <label for="password2" class="form-label">Repeat Password <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input type="password" class="form-control form-control-lg" name="password2" id="password2" placeholder="Repeat Password" autocomplete="off" required>
                  <span class="input-group-text">
                    <i class="fa fa-eye toggle-password" toggle="#password2"></i>
                  </span>
                </div>
              </div>
              <div class="password-criteria">
                  <ul>
                      <li id="8char" class="glyphicon glyphicon-remove"> 8 Characters Long</li>
                      <li id="ucase" class="glyphicon glyphicon-remove"> One Uppercase Letter</li>
                      <li id="lcase" class="glyphicon glyphicon-remove"> One Lowercase Letter</li>
                      <li id="num" class="glyphicon glyphicon-remove"> One Number</li>
                      <li id="pwmatch" class="glyphicon glyphicon-remove"> Passwords Match</li>
                  </ul>
              </div>
              <div class="col-12">
                <div class="d-grid">
                  <button class="btn btn-primary btn-lg" type="submit">Change Password</button>
                </div>
              </div>
            </div>
          </form>
          <div class="row">
            <div class="col-12">
              <hr class="mt-5 mb-4 border-secondary-subtle">
              <div class="d-flex gap-4 justify-content-center">
                <a href="../home.php" class="link-secondary text-decoration-none">Back to Login</a><br><br>
              </div>
            </div>
          </div>
          <?php if ($message != "") {
              echo "<div class='alert alert-danger' role='alert'>
                <span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
                <span class='sr-only'>Error:</span>" . $message . "</div>";
          } ?>
          <?php if (isset($message_success)) {
              echo "<div class='alert alert-success' role='alert'>
                <span class='glyphicon glyphicon-ok' aria-hidden='true'></span>
                <span class='sr-only'></span>" . $message_success . "</div>";
          } ?>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script>
  $("input[type=password]").keyup(function(){
      var ucase = new RegExp("[A-Z]+");
      var lcase = new RegExp("[a-z]+");
      var num = new RegExp("[0-9]+");
      
      if($("#password1").val().length >= 8){
          $("#8char").removeClass("glyphicon-remove");
          $("#8char").addClass("glyphicon-ok");
          $("#8char").css("color","#00A41E");
      }else{
          $("#8char").removeClass("glyphicon-ok");
          $("#8char").addClass("glyphicon-remove");
          $("#8char").css("color","#FF0004");
      }
      
      if(ucase.test($("#password1").val())){
          $("#ucase").removeClass("glyphicon-remove");
          $("#ucase").addClass("glyphicon-ok");
          $("#ucase").css("color","#00A41E");
      }else{
          $("#ucase").removeClass("glyphicon-ok");
          $("#ucase").addClass("glyphicon-remove");
          $("#ucase").css("color","#FF0004");
      }
      
      if(lcase.test($("#password1").val())){
          $("#lcase").removeClass("glyphicon-remove");
          $("#lcase").addClass("glyphicon-ok");
          $("#lcase").css("color","#00A41E");
      }else{
          $("#lcase").removeClass("glyphicon-ok");
          $("#lcase").addClass("glyphicon-remove");
          $("#lcase").css("color","#FF0004");
      }
      
      if(num.test($("#password1").val())){
          $("#num").removeClass("glyphicon-remove");
          $("#num").addClass("glyphicon-ok");
          $("#num").css("color","#00A41E");
      }else{
          $("#num").removeClass("glyphicon-ok");
          $("#num").addClass("glyphicon-remove");
          $("#num").css("color","#FF0004");
      }
      
      if($("#password1").val() == $("#password2").val()){
          $("#pwmatch").removeClass("glyphicon-remove");
          $("#pwmatch").addClass("glyphicon-ok");
          $("#pwmatch").css("color","#00A41E");
      }else{
          $("#pwmatch").removeClass("glyphicon-ok");
          $("#pwmatch").addClass("glyphicon-remove");
          $("#pwmatch").css("color","#FF0004");
      }
  });

  document.querySelectorAll('.toggle-password').forEach(item => {
    item.addEventListener('click', event => {
        const input = document.querySelector(item.getAttribute('toggle'));
        if (input.type === 'password') {
            input.type = 'text';
            item.classList.remove('fa-eye-slash');
            item.classList.add('fa-eye');
        } else {
            input.type = 'password';
            item.classList.remove('fa-eye');
            item.classList.add('fa-eye-slash');
        }
    });
  });

  function validatePassword() {
    var password1 = document.getElementById("password1").value;
    var password2 = document.getElementById("password2").value;
    var ucase = new RegExp("[A-Z]+");
    var lcase = new RegExp("[a-z]+");
    var num = new RegExp("[0-9]+");

    if (password1.length < 8) {
        alert("Password must be at least 8 characters long.");
        return false;
    }

    if (!ucase.test(password1)) {
        alert("Password must contain at least one uppercase letter.");
        return false;
    }

    if (!lcase.test(password1)) {
        alert("Password must contain at least one lowercase letter.");
        return false;
    }

    if (!num.test(password1)) {
        alert("Password must contain at least one number.");
        return false;
    }

    if (/\s/.test(password1)) {
        alert("Password must not contain spaces.");
        return false;
    }

    if (password1 !== password2) {
        alert("Passwords do not match.");
        return false;
    }

    return true;
  }
</script>

</body>
</html>
