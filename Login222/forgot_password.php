<?php
// Load Composer's autoloader
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";
$valid = 'true';
include("db_config.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_reg = mysqli_real_escape_string($dbconfig, $_POST['email']);
    $details = mysqli_query($dbconfig, "SELECT user_fullname, email FROM user WHERE email='$email_reg'");
    if (mysqli_num_rows($details) > 0) {
        $message_success = "Please check your email inbox or spam folder and follow the steps";
        // generating the random key
        $key = md5(time() + 123456789 % rand(4000, 55000000));
        // insert this temporary key into database
        $sql_insert = mysqli_query($dbconfig, "INSERT INTO forget_password(email, temp_key) VALUES('$email_reg', '$key')");
        // sending email about update

        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = 0; // Disable verbose debug output
            $mail->isSMTP(); // Send using SMTP
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = 'naolao11111@gmail.com'; // SMTP username
            $mail->Password = 'lkvy cveu vsnn xlql'; // SMTP password
            $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587; // TCP port to connect to

            //Recipients
            $mail->setFrom('naolao11111@gmail.com', 'fyp');
            $mail->addAddress($email_reg); // Add a recipient

            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Changing password';
            $mail->Body = "Please copy the link and paste in your browser address bar<br>https://localhost/fyp/Login222/forgot_password_reset.php?key=$key&email=$email_reg";

            $mail->send();
            $message_success = "Message has been sent. Please check your email inbox or spam folder and follow the steps required to reset your password.";
        } catch (Exception $e) {
            $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $message = "Sorry! no account associated with this email";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <title>Forgot Password</title>
</head>
<body>
<div class="bg-light py-3 py-md-5">
  <div class="container">
    <div class="row justify-content-md-center">
      <div class="col-12 col-md-11 col-lg-8 col-xl-7 col-xxl-6">
        <div class="bg-white p-4 p-md-5 rounded shadow-sm">
          <div class="row gy-3 mb-5">
            <div class="col-12">
              <div class="text-center">
                <a href="../home.php">
                  <img src="Uploads/logo1.png" alt="Republic Polytechnic" width="175" height="57">
                </a>
              </div>
            </div>
            <div class="col-12">
              <h2 class="fs-6 fw-normal text-center text-secondary m-0 px-md-5">Provide the email address associated with your account to recover your password.</h2>
            </div>
          </div>
          <form method="POST">
            <div class="row gy-3 gy-md-4 overflow-hidden">
              <div class="col-12">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                      <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z" />
                    </svg>
                  </span>
                  <input type="email" class="form-control" name="email" id="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
                </div>
              </div>
              <div class="col-12">
                <div class="d-grid">
                  <button class="btn btn-primary btn-lg" type="submit">Reset Password</button>
                </div>
              </div>
            </div>
          </form>
          <div class="row">
            <div class="col-12">
              <hr class="mt-5 mb-4 border-secondary-subtle">
              <div class="d-flex gap-4 justify-content-center">
                <a href="index.php" class="link-secondary text-decoration-none">Log In<br><br></a><br>
                <!--<a href="registration.php" class="link-secondary text-decoration-none">Register</a> <br><br>
              </div>
            </div>
            -->
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
</body>
</html>