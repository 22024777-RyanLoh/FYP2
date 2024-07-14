<?php
include("db_config.php");
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Sanitize and retrieve input data
    $email = mysqli_real_escape_string($dbconfig, $_POST['email']);
    $fullname = mysqli_real_escape_string($dbconfig, $_POST['fullname']); // Updated key to 'fullname'
    $password = mysqli_real_escape_string($dbconfig, $_POST['password']); // Updated key to 'password'
    $password = md5($password); // Encrypt password with md5
    
    $sql = "INSERT INTO user(user_password, user_fullname, email) VALUES('$password', '$fullname', '$email')";
    $result = mysqli_query($dbconfig, $sql);
    $msg = "Registered";
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Registration</title>
    <script>
        function togglePasswordVisibility(id) {
            var passwordField = document.getElementById(id);
            var eyeIcon = document.getElementById(id + "-icon");
            if (passwordField.type === "password") {
                passwordField.type = "text";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            }
        }

        function validateForm() {
            var password = document.getElementById("password").value;
            var retypePassword = document.getElementById("retypePassword").value;

            if (password !== retypePassword) {
                alert("Passwords do not match. Please try again.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<section class="bg-light py-3 py-md-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4">
        <div class="card border border-light-subtle rounded-3 shadow-sm">
          <div class="card-body p-3 p-md-4 p-xl-5">
            <div class="text-center mb-3">
              <a href="../home.php">
              <img src="Uploads/logo1.png" alt="Republic Polytechnic" width="175" height="57">
              </a>
            </div>
            <h2 class="fs-6 fw-normal text-center text-secondary mb-4">Enter your details to register</h2>
            <form method="post" action="" onsubmit="return validateForm()">
              <div class="row gy-2 overflow-hidden">
                <div class="col-12">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" name="fullname" id="fullname" placeholder="Full Name" required>
                    <label for="fullname" class="form-label">Full Name</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-floating mb-3 position-relative">
                    <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required>
                    <label for="email" class="form-label">Email</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-floating mb-3 position-relative">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                    <label for="password" class="form-label">Password</label>
                    <i class="fa fa-eye position-absolute top-50 end-0 translate-middle-y pe-3" id="password-icon" onclick="togglePasswordVisibility('password')" style="cursor: pointer;"></i>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-floating mb-3 position-relative">
                    <input type="password" class="form-control" name="retypePassword" id="retypePassword" placeholder="Re-type Password" required>
                    <label for="retypePassword" class="form-label">Re-Enter Password</label>
                    <i class="fa fa-eye position-absolute top-50 end-0 translate-middle-y pe-3" id="retypePassword-icon" onclick="togglePasswordVisibility('retypePassword')" style="cursor: pointer;"></i>
                  </div>
                </div>
                <?php if (isset($msg)) { ?>
                    <div class='alert alert-success' role='alert'>
                        <span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
                        <span class='sr-only'>Account successfully </span><?php echo $msg; ?>
                    </div>
                <?php } ?>
                <div class="col-12">
                  <div class="d-grid my-3">
                    <button class="btn btn-primary btn-lg" type="submit">Sign up</button>
                  </div>
                </div>
                <div class="col-12">
                  <p class="m-0 text-secondary text-center">Already have an account? <a href="index.php" class="link-primary text-decoration-none">Log in</a></p>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</body>
</html>