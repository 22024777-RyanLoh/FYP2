<?php 
session_start();
$pageTitle = 'Admin / Staff Login';

if(isset($_SESSION['login_user'])) {
    header('Location: dashboard.php');
    exit(); // Make sure to exit after the redirection
}

// PHP INCLUDES
include 'connect.php'; 
include 'Includes/functions/functions.php'; 
include 'Includes/templates/header.php';

include("db_config.php");
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $email = mysqli_real_escape_string($dbconfig, $_POST['email']);
    $password = mysqli_real_escape_string($dbconfig, $_POST['password']);
    $password = md5($password); // hashing with md5
    $sql_query = "SELECT user_id, user_fullname, email, user_role FROM user WHERE email='$email' and user_password='$password'";
    $result = mysqli_query($dbconfig, $sql_query);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $count = mysqli_num_rows($result);

    if($count == 1 && $row !== null){    // if login success
        $_SESSION['login_user'] = $row['user_fullname'];
        $_SESSION['login_email'] = $row['email'];
        $_SESSION['login_user_id'] = $row['user_id']; // Add user_id to session
        $_SESSION['user_role'] = $row['user_role']; // Add user role to session

        if ($row['user_role'] == 'Admin') {
            header("location: dashboard.php");
        } else {
            header("location: ../home.php");
        }
        exit(); // Make sure to exit after the redirection
    } else {
        $error = "Invalid login details";
    }
}
?>

<!-- LOGIN FORM -->
<div class="login">
    <form class="login-container validate-form" name="login-form" action="index.php" method="POST">
        <div class="text-center mb-4">
            <a href="../home.php">
                <img src="../Domain_picture/logo1.png" alt="Republic Polytechnic" width="175" height="57">
            </a>
        </div>
        <span class="login100-form-title p-b-32">
               Admin / Staff Login
        </span>

        <!-- EMAIL INPUT -->
        <div class="form-input">
            <span class="txt1">Email address</span>
            <input type="email" name="email" class="form-control form-control-lg" oninput="document.getElementById('email_required').style.display = 'none'" id="email" autocomplete="off" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
            <div class="invalid-feedback" id="email_required">Email is required!</div>
        </div>

        <!-- PASSWORD INPUT -->
        <div class="form-input">
            <span class="txt1">Password</span>
            <div class="input-group">
                <input type="password" name="password" class="form-control form-control-lg" id="password" autocomplete="new-password">
                <div class="input-group-append">
                    <input type="checkbox" id="togglePasswordCheckbox">
                    <label for="togglePasswordCheckbox" class="mb-0" style="margin-left: 7px;"> Show Password</label>
                </div>
            </div>
            <div class="invalid-feedback" id="password_required">Password is required!</div>
        </div>

        <!-- SIGNIN BUTTON -->
        <p>
            <button type="submit" name="submit" class="btn btn-primary btn-sm">Sign In</button>
        </p>

        <!-- ERROR MESSAGE -->
        <?php if (isset($error)) {
            echo "<div class='alert alert-danger'>
                    <button data-dismiss='alert' class='close close-sm' type='button'>
                        <span aria-hidden='true'>×</span>
                    </button>
                    <div class='messages'>
                        <div>$error</div>
                    </div>
                  </div>";
        } ?>

        <!-- FORGOT PASSWORD PART -->
        <span class="forgotPW">Forgot your password ? <a href="forgot_password.php">Reset it here.</a></span>
        
        <span class="forgotPW"><a href="../home.php">Back to homepage</a></span>
    </form>
</div>

<?php include 'Includes/templates/footer.php'; ?>

<script>
document.getElementById('togglePasswordCheckbox').addEventListener('change', function (e) {
    var password = document.getElementById('password');
    if (this.checked) {
        password.type = 'text';
    } else {
        password.type = 'password';
    }
});

function validateLoginForm() {
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;

    if (email === "") {
        document.getElementById('email_required').style.display = 'block';
        return false;
    }

    if (password === "") {
        document.getElementById('password_required').style.display = 'block';
        return false;
    }

    return true;
}
</script>

<style>
.input-group .input-group-text,
.input-group .btn {
    cursor: pointer;
    background: none;
    border: none;
}
</style>