<?php
session_start();
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database Connection
$conn = mysqli_connect("localhost", "root", "", "fyp_test");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the user is an admin
$isAdmin = false;
if (isset($_SESSION['login_user_id'])) {
    $userId = $_SESSION['login_user_id'];
    $sql = "SELECT user_role FROM user WHERE user_id = '$userId'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $isAdmin = ($row['user_role'] === 'Admin');
    }
}

// Profile update handling logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile_sbmt'])) {
    $user_id = $_POST['user_id'];
    $full_name = $_POST['full_name'];
    $email = $_POST['user_email'];
    $role = $_POST['user_role'];
    $password = isset($_POST['user_password']) && !empty($_POST['user_password']) ? md5($_POST['user_password']) : '';

    // Prepare the SQL statement
    if (empty($password)) {
        $sql = "UPDATE user SET user_fullname = ?, email = ?, user_role = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssi', $full_name, $email, $role, $user_id);
    } else {
        $sql = "UPDATE user SET user_fullname = ?, email = ?, user_password = ?, user_role = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssi', $full_name, $email, $password, $role, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['login_user'] = $full_name;
        $_SESSION['login_email'] = $email;
        $_SESSION['user_role'] = $role;
        echo '<script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function() {
                    swal("Profile Updated", "Your profile has been updated successfully.", "success").then((value) => {
                        window.location.replace("home.php");
                    });
                });
              </script>';
    } else {
        echo '<script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function() {
                    swal("Update Failed", "An error occurred while updating your profile.", "error").then((value) => {
                        window.location.replace("home.php");
                    });
                });
              </script>';
    }

    $stmt->close();

}





// Function to fetch domain id from the database
function fetchDomainname($conn) {
    $sql = "SELECT * FROM domains";
    $result = mysqli_query($conn, $sql);
    $domains = array();
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $domains[] = $row['domain_name'];
        }
    }
    return $domains;
}

// Function to fetch domain information
function fetchDomainInfo($conn, $domainId) {
    $sql = "SELECT domain_name, domain_description FROM domains WHERE domain_id = '$domainId' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $domainInfo = array();
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $domainInfo['name'] = $row["domain_name"];
        $domainInfo['description'] = $row["domain_description"];
    } else {
        $domainInfo['name'] = "No name available for domain with ID: $domainId.";
        $domainInfo['description'] = "No description available for domain with ID: $domainId.";
    }
    return $domainInfo;
}

// Fetch domain information based on id
$domains = array();
$backgroundImages = array();
$descriptions = array();

$sql = "SELECT domain_id, domain_name, domain_image, domain_description FROM domains";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $domainInfo = fetchDomainInfo($conn, $row['domain_id']);
    $domains[$row['domain_id']] = $row['domain_name'];
    $backgroundImages[$row['domain_id']] = "Domain_picture/" . $row['domain_image'];
    $descriptions[$row['domain_id']] = $row['domain_description'];
}


// File to store click counts
$clickCountsFile = 'click_counts.json';

// Read the current click counts from the file
$clickCounts = [];
if (file_exists($clickCountsFile)) {
    $clickCounts = json_decode(file_get_contents($clickCountsFile), true);
} else {
    // Initialize click counts if the file doesn't exist
    foreach ($domains as $domain) {
        $clickCounts[$domain] = 0;
    }
    file_put_contents($clickCountsFile, json_encode($clickCounts));
}

// Initialize session click counts if not set
if (!isset($_SESSION['click_counts'])) {
    $_SESSION['click_counts'] = [];
}

mysqli_close($conn);

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'fyp_test';
$dbconfig = mysqli_connect($host,$username,$password,$database) or die("An Error occured when connecting to the database");

// Login handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    include 'Login222/connect.php'; 
    $email = mysqli_real_escape_string($dbconfig, $_POST['email']);
    $password = mysqli_real_escape_string($dbconfig, $_POST['password']);
    $password = md5($password); // hashing with md5
    $sql_query = "SELECT user_id, user_fullname, email, user_role FROM user WHERE email='$email' and user_password='$password'";
    $result = mysqli_query($dbconfig, $sql_query);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $count = mysqli_num_rows($result);

    if ($count == 1 && $row !== null) { // if login success
        $_SESSION['login_user'] = $row['user_fullname'];
        $_SESSION['login_email'] = $row['email'];
        $_SESSION['login_user_id'] = $row['user_id']; // Add user_id to session
        $_SESSION['user_role'] = $row['user_role']; // Add user role to session

        header("Location: home.php"); // Redirect to home.php
        exit(); // Make sure to exit after the redirection
    } else {
        $error = "Invalid login details. Email or password may be incorrect.";
        echo "<script>localStorage.setItem('loginError', 'true');</script>";
    }
}

// Forgot Password handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $email_reg = mysqli_real_escape_string($dbconfig, $_POST['reset_email']);
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
            echo "<script>localStorage.setItem('passwordResetSuccess', 'true');</script>";
        } catch (Exception $e) {
            $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            echo "<script>localStorage.setItem('passwordResetError', 'true');</script>";
        }
    } else {
        $message = "Sorry! no account associated with this email.";
        echo "<script>localStorage.setItem('passwordResetError', 'true');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Of Infocomm</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="footer.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">

    <!-- SweetAlert JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>





    <style>
    #backToTopBtn {
        display: none;
        position: fixed;
        bottom: 20px;
        right: 30px;
        z-index: 99;
        border: none;
        outline: none;
        background-color: #555;
        color: white;
        cursor: pointer;
        padding: 15px;
        border-radius: 10px;
        font-size: 18px;
    }

    #backToTopBtn:hover {
        background-color: #000;
    }
</style>


</head>

    <!-- Rest of the body content -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<section class="header">
    <nav>
        <div class="nav-links" id="navLinks">
            <i class="fas fa-times" onclick="hidemenu()"></i>
            <ul>
                <?php if(isset($_SESSION['login_user'])): ?>
                    <li><a href="home.php"><img src="Domain_picture/transRP.png" alt="Logo"></a></li>
                    <li><a href="#" onclick="showProfileModal()"><span class="welcome-message" style="color: #FFFFFF;">Welcome, <?php echo $_SESSION['login_user']; ?></span></a></li>
                    <?php if($isAdmin): ?>
                        <li><a href="/fyp/Login222/dashboard.php">
                                <span style="color: #fff;">Dashboard</span>
                        </a></li>
                        <li><a href="login222/users.php">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="edit.php">Domain</a></li>
                    <li><a href="upload.php">Project</a></li>
                    <li><a href="logout.php">Sign out</a></li>
                <?php else: ?>
                    <li><a href="home.php"><img src="Domain_picture/transRP.png" alt="Logo"></a></li>
                    <li><a href="#" onclick="showLoginModal()">Log in</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <i class="fas fa-bars" onclick="showmenu()"></i>
    </nav>

        

    <div class="text-box">
        <h1>Welcome to SOI Projects</h1>
        <p>Scroll down to see your Projects</p>
    </div>
</section>


    <section class="filter">
        <div class="filter-container">
            <button class="filter-btn" onclick="toggleFilterBox()">Filter</button>
            <div class="filter-box" id="filterBox">
                <div class="filter-header">
                    <span>Filter Projects</span>
                    <i class="fas fa-times" onclick="toggleFilterBox()"></i>
                </div>
                <div class="filter-content">
                    <div class="filter-items">
                        <label><input type="checkbox" value="Application Development"> Application Development</label>
                        <label><input type="checkbox" value="Artificial Intelligence"> Artificial Intelligence</label>
                        <label><input type="checkbox" value="Data Analytics"> Data Analytics</label>
                        <label><input type="checkbox" value="Fintech"> Fintech</label>
                        <label><input type="checkbox" value="Infocomm Security"> Infocomm Security</label>
                        <label><input type="checkbox" value="Internet of Things"> Internet of Things</label>
                        <label><input type="checkbox" value="Network & Systems"> Network & Systems</label>
                        <label><input type="checkbox" value="Specialist Diploma"> Specialist Diploma</label>
                        <label><input type="checkbox" value="Staff Project"> Staff Project</label>
                    </div>
                    <div class="filter-actions">
                        <button onclick="applyFilter()">Apply Filter</button>
                        <button onclick="clearFilter()">Clear Filter</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cards">
        
    <?php foreach ($domains as $id => $domain) : ?>
            
        <div class="card card<?php echo $id; ?>" style="background-image: linear-gradient(rgba(4,9,30,0.5), rgba(4,9,30,0.5)),url('<?php echo isset($backgroundImages[$id]) ? $backgroundImages[$id] : ''; ?>');">
            <div class="card-text">
                    <h2><?php echo $domain; ?></h2>
                    <p><?php echo isset($descriptions[$id]) ? $descriptions[$id] : ''; ?></p>
                    <!-- Display click count dynamically -->
                    <?php if ($isAdmin): ?>
                      <p id="clickCount_<?php echo $domain; ?>">Click Count: <span id="click-count-<?php echo $domain; ?>"><?php 
                      $jsonFile = 'click_counts1.json';
                     if (file_exists($jsonFile) && is_readable($jsonFile)) {
                     $jsonData = json_decode(file_get_contents($jsonFile), true);
                     if (isset($jsonData[$domain])) {
                      echo $jsonData[$domain];
                     } else {
                     echo 0;
                    } 
                    } else {
                       echo 0;
                     }
                     ?></span></p>
                    <?php endif; ?>
                    <a href="domain_page.php?domain_id=<?php echo urlencode($id); ?>" class="learn-more-btn">Learn More</a>
                </div>
            </div>
        <?php endforeach; ?>
        
    </section>



    <!-- LOGIN MODAL -->
    <div id="loginModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Admin / Staff Login</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="login">
                        <form class="login-container validate-form" name="login-form" action="home.php" method="POST">
                            <div class="text-center mb-4">
                                <a href="home.php">
                                    <img src="Domain_picture/rp-logo.png" alt="Republic Polytechnic" width="175" height="57">
                                </a>
                            </div>
                            <div class="form-group">
                                <label for="email">Email address</label>
                                <input type="email" name="email" class="form-control" id="email" autocomplete="off" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$" title="Invalid email address. You are missing an '@' and '.' in your email.">
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control" id="password" autocomplete="new-password" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fa fa-eye toggle-password" toggle="#password"></i></span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary btn-block">Sign In</button>
                            <?php if (isset($error)) {
                                echo "<div class='alert alert-danger mt-3'>$error</div>";
                            } ?>
                            <div class="d-flex justify-content-between mt-3">
                                <a href="#" onclick="showForgotPasswordModal()">Forgot your password?</a>
                                <a href="home.php">Back to homepage</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FORGOT PASSWORD MODAL -->
    <div id="forgotPasswordModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Account Recovery</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <a href="home.php"><img src="Domain_picture/rp-logo.png" alt="RP LOGO" width="175" height="57"></a>
                    <p style="color: #000000;">Provide the email address associated with your account to recover your password.</p>
                </div>
                <div class="forgot-password">
                    <form class="forgot-password-container validate-form" name="forgot-password-form" action="home.php" method="POST">
                        <div class="form-group">
                            <label for="reset_email">Email address <span style="color: red;">*</span></label>
                            <input type="email" name="reset_email" class="form-control" id="reset_email" autocomplete="off" value="<?php echo isset($_POST['reset_email']) ? $_POST['reset_email'] : ''; ?>" required pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$" title="Invalid email address. You are missing an '@' and '.' in your email.">
                        </div>
                        <button type="submit" name="reset_password" class="btn btn-primary btn-block">Reset Password</button>
                        <?php if (isset($message)) {
                            echo "<div class='alert alert-danger mt-3'>$message</div>";
                        } ?>
                        <?php if (isset($message_success)) {
                            echo "<div class='alert alert-success mt-3'>$message_success</div>";
                        } ?>
                        <div class="text-center mt-3">
                            <a href="#" onclick="closeForgotPasswordAndShowLogin()">Back to Log in</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function closeForgotPasswordAndShowLogin() {
        $('#forgotPasswordModal').modal('hide');
        $('#loginModal').modal('show');
    }

    function showProfileModal() {
        window.location.href = "/fyp/login222/users.php?showProfileModal=true";
    }
</script>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        // Check for login error
        if (localStorage.getItem('loginError') === 'true') {
            $('#loginModal').modal('show');
            localStorage.removeItem('loginError');
        }

        // Check for forgot password errors
        if (localStorage.getItem('passwordResetError') === 'true') {
            $('#forgotPasswordModal').modal('show');
            localStorage.removeItem('passwordResetError');
        }

        // Check for forgot password success
        if (localStorage.getItem('passwordResetSuccess') === 'true') {
            $('#forgotPasswordModal').modal('show');
            localStorage.removeItem('passwordResetSuccess');
        }

        var navLinks = document.getElementById("navLinks");

        window.showmenu = function() {
            navLinks.style.right = "0";
        }

        window.hidemenu = function() {
            navLinks.style.right = "-200px";
        }
        
        window.toggleFilterBox = function() {
            var filterBox = document.getElementById("filterBox");
            if (filterBox.style.display === "block") {
                filterBox.style.display = "none";
            } else {
                filterBox.style.display = "block";
            }
        }

        window.applyFilter = function() {
            var checkboxes = document.querySelectorAll(".filter-items input[type='checkbox']");
            var selectedFilters = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value.toLowerCase());
            var cards = document.querySelectorAll(".card");
            cards.forEach(card => {
                var cardTitle = card.querySelector("h2").textContent.toLowerCase();

                var match = false;
                selectedFilters.forEach(filter => {
                    if (cardTitle.includes(filter)) {
                        match = true;
                    }
                });

                if (selectedFilters.length === 0 || match) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });
            document.getElementById("filterBox").style.display = "none";
        }

        window.clearFilter = function() {
            var checkboxes = document.querySelectorAll(".filter-items input[type='checkbox']");
            checkboxes.forEach(cb => cb.checked = false);
            var cards = document.querySelectorAll(".card");
            cards.forEach(card => card.style.display = "block");
            document.getElementById("filterBox").style.display = "none";
        }

        window.updateClickCount = function(domain) {
            // Check if the user is not an admin before counting clicks
            if (!<?php echo json_encode($isAdmin); ?>) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "update_click_count.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            var clickCountElement = document.getElementById("clickCount_" + domain);
                            if (clickCountElement) {
                                clickCountElement.innerHTML = "Click Count: " + xhr.responseText;
                            }
                        } else {
                            console.error("Error updating click count");
                        }
                    }
                };
                xhr.send("domain=" + domain);
            }
        }

        $(".toggle-password").click(function () {
            $(this).toggleClass("fa-eye fa-eye-slash");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });

        window.showLoginModal = function() {
            $('#loginModal').modal('show');
        }

        window.hideLoginModal = function() {
            $('#loginModal').modal('hide');
        }

        window.showForgotPasswordModal = function() {
            $('#forgotPasswordModal').modal('show');
            $('#loginModal').modal('hide');
        }

        window.hideForgotPasswordModal = function() {
            $('#forgotPasswordModal').modal('hide');
        }  
        
        $("input[type=password]").keyup(function() {
            var ucase = new RegExp("[A-Z]+");
            var lcase = new RegExp("[a-z]+");
            var num = new RegExp("[0-9]+");

            if ($("#user_password").val().length >= 8) {
                $("#8char").removeClass("glyphicon-remove");
                $("#8char").addClass("glyphicon-ok");
                $("#8char").css("color", "#00A41E");
            } else {
                $("#8char").removeClass("glyphicon-ok");
                $("#8char").addClass("glyphicon-remove");
                $("#8char").css("color", "#FF0004");
            }

            if (ucase.test($("#user_password").val())) {
                $("#ucase").removeClass("glyphicon-remove");
                $("#ucase").addClass("glyphicon-ok");
                $("#ucase").css("color", "#00A41E");
            } else {
                $("#ucase").removeClass("glyphicon-ok");
                $("#ucase").addClass("glyphicon-remove");
                $("#ucase").css("color", "#FF0004");
            }

            if (lcase.test($("#user_password").val())) {
                $("#lcase").removeClass("glyphicon-remove");
                $("#lcase").addClass("glyphicon-ok");
                $("#lcase").css("color", "#00A41E");
            } else {
                $("#lcase").removeClass("glyphicon-ok");
                $("#lcase").addClass("glyphicon-remove");
                $("#lcase").css("color", "#FF0004");
            }

            if (num.test($("#user_password").val())) {
                $("#num").removeClass("glyphicon-remove");
                $("#num").addClass("glyphicon-ok");
                $("#num").css("color", "#00A41E");
            } else {
                $("#num").removeClass("glyphicon-ok");
                $("#num").addClass("glyphicon-remove");
                $("#num").css("color", "#FF0004");
            }

            if ($("#user_password").val() === $("#re_user_password").val()) {
                $("#pwmatch").removeClass("glyphicon-remove");
                $("#pwmatch").addClass("glyphicon-ok");
                $("#pwmatch").css("color", "#00A41E");
            } else {
                $("#pwmatch").removeClass("glyphicon-ok");
                $("#pwmatch").addClass("glyphicon-remove");
                $("#pwmatch").css("color", "#FF0004");
            }
        });

        // Existing functionality
        // ...

        window.showProfileModal = function() {
            $('#myProfileModal').modal('show');
        }
    });

    function validatePassword() {
        var password = document.getElementById("user_password").value;
        var rePassword = document.getElementById("re_user_password").value;

        if (password === "" && rePassword === "") {
            return true;
        }

        if (password !== rePassword) {
            alert("Passwords do not match.");
            return false;
        }

        var ucase = new RegExp("[A-Z]+");
        var lcase = new RegExp("[a-z]+");
        var num = new RegExp("[0-9]+");

        if (password.length >= 8 && ucase.test(password) && lcase.test(password) && num.test(password)) {
            return true;
        } else {
            alert("Password must be at least 8 characters long, contain one uppercase letter, one lowercase letter, and one number.");
            return false;
        }
    };
</script>


    <style>
        .input-group .input-group-text,
        .input-group .btn {
            cursor: pointer;
            background: none;
            border: 1px solid #ced4da;
        }

        /* Modal styles */
        .modal-body .form-group {
            margin-bottom: 1.5rem;
        }

        .invalid-feedback {
            display: block;
        }

        .alert {
            margin-top: 1rem;
        }

        .forgot-password-container .text-center img {
            margin-bottom: 1rem;
        }
    </style>
    <!-- Back to Top Button -->
<button id="backToTopBtn" title="Back to Top">
    <i class="fa fa-arrow-up"></i>
</button>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Show or hide the button based on scroll position
        $(window).scroll(function() {
            if ($(this).scrollTop() > 100) {
                $('#backToTopBtn').fadeIn();
            } else {
                $('#backToTopBtn').fadeOut();
            }
        });

        // Smooth scroll to top
        $('#backToTopBtn').click(function() {
            $('html, body').animate({scrollTop: 0}, 400);
            return false;
        });

    });


    // Success and Error Alert Handling
function showSuccessAlert(title, text) {
    swal({
        title: title,
        text: text,
        icon: "success"
    }).then((value) => {
        window.location.replace("home.php");
    });
}

function showErrorAlert(title, text) {
    swal({
        title: title,
        text: text,
        icon: "error"
    }).then((value) => {
        window.location.replace("home.php");
    });
}
</script>

<!-- MY PROFILE MODAL -->
<div id="myProfileModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">My Profile</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="menu_form" id="profileForm" method="POST" action="home.php" onsubmit="return validatePassword();">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['login_user_id']; ?>">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" class="form-control" name="full_name" id="full_name" value="<?php echo $_SESSION['login_user']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="user_email">Email</label>
                        <input type="email" class="form-control" name="user_email" id="user_email" value="<?php echo $_SESSION['login_email']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="user_role">User Role</label>
                        <select class="form-control" name="user_role" id="user_role" required>
                            <option value="Staff" <?php echo isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'Staff' ? 'selected' : ''; ?>>Staff</option>
                            <option value="Admin" <?php echo isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="user_password">New Password (Optional)</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="user_password" id="user_password">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fa fa-eye toggle-password" toggle="#user_password"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="re_user_password">Re-Enter Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="re_user_password" id="re_user_password">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fa fa-eye toggle-password" toggle="#re_user_password"></i>
                                </span>
                            </div>
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
                    <button type="submit" name="update_profile_sbmt" class="btn btn-primary btn-block">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>


</body>
<body>

<div class="content">
    <footer class="footer">
        <div class="footer-content container" style="justify-content:center; align-items:center">
            <div class="row">
                <div class="" style="min-width:100vh;">
                <div class="col-md-3" >
                    <h3 class="row"><a href="https://www.rp.edu.sg/about-us" target="_blank">About Us</a></h3>
                    <ul>
                        <li class="row"><a href="https://www.rp.edu.sg/about-us/who-we-are" target="_blank">Who We Are</a></li>
                        <li class="row"><a href="https://www.rp.edu.sg/about-us/our-people" target="_blank">Our People</a></li>
                        <li class="row"><a href="https://www.rp.edu.sg/about-us/media" target="_blank">Media</a></li>
                    </ul>
                </div>
                </div>
<div style="align-items:center">
                    <h3>Connect With Us</h3>
                    
                    <ul >
                        <a href="http://www.facebook.com/republicpolytechnic" target="_blank" class="footer-socialicon" aria-label="Facebook" data-sf-ec-immutable=""><em class="fa fa-facebook"></em></a>
                        <a href="https://sg.linkedin.com/school/republic-polytechnic/" target="_blank" class="footer-socialicon" aria-label="LinkedIn" data-sf-ec-immutable=""><em class="fa fa-linkedin"></em></a>
                        <a href="http://www.youtube.com/channelRP" target="_blank" class="footer-socialicon" aria-label="YouTube" data-sf-ec-immutable=""><em class="fa fa-youtube"></em></a>
                        <a href="http://www.instagram.com/republicpoly" target="_blank" class="footer-socialicon" aria-label="Instagram" data-sf-ec-immutable=""><em class="fa fa-instagram"></em></a>
                        <a href="http://twitter.com/republicpoly" target="_blank" class="footer-socialicon" aria-label="Twitter" data-sf-ec-immutable="">
                        <svg style="margin-bottom:5px;width:22px;height:22px; vertical-align: middle;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" xml:space="preserve" enable-background="new 0 0 24 24"><path d="M14.095 10.316 22.286 1h-1.94L13.23 9.088 7.551 1H1l8.59 12.231L1 23h1.94l7.51-8.543L16.45 23H23l-8.905-12.684zm-2.658 3.022-.872-1.218L3.64 2.432h2.98l5.59 7.821.869 1.219 7.265 10.166h-2.982l-5.926-8.3z" fill="#ffffff" class="fill-000000"></path></svg></a>
                        <a href="https://www.tiktok.com/@republicpoly" target="_blank" aria-label="TikTok" data-sf-ec-immutable="">
                            <svg style="margin-bottom:-5px; width: 33px; height: 33px; vertical-align: middle;" viewBox="10 8 48 48" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="footer-socialicon"><title>Tiktok</title>
                                <g id="Icon/Social/tiktok-black" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><path d="M38.0766847,15.8542954 C36.0693906,15.7935177 34.2504839,14.8341149 32.8791434,13.5466056 C32.1316475,12.8317108 31.540171,11.9694126 31.1415066,11.0151329 C30.7426093,10.0603874 30.5453728,9.03391952 30.5619062,8 L24.9731521,8 L24.9731521,28.8295196 C24.9731521,32.3434487 22.8773693,34.4182737 20.2765028,34.4182737 C19.6505623,34.4320127 19.0283477,34.3209362 18.4461858,34.0908659 C17.8640239,33.8612612 17.3337909,33.5175528 16.8862248,33.0797671 C16.4386588,32.6422142 16.0833071,32.1196657 15.8404292,31.5426268 C15.5977841,30.9658208 15.4727358,30.3459348 15.4727358,29.7202272 C15.4727358,29.0940539 15.5977841,28.4746337 15.8404292,27.8978277 C16.0833071,27.3207888 16.4386588,26.7980074 16.8862248,26.3604545 C17.3337909,25.9229017 17.8640239,25.5791933 18.4461858,25.3491229 C19.0283477,25.1192854 19.6505623,25.0084418 20.2765028,25.0219479 C20.7939283,25.0263724 21.3069293,25.1167239 21.794781,25.2902081 L21.794781,19.5985278 C21.2957518,19.4900128 20.7869423,19.436221 20.2765028,19.4380839 C18.2431278,19.4392483 16.2560928,20.0426009 14.5659604,21.1729264 C12.875828,22.303019 11.5587449,23.9090873 10.7814424,25.7878401 C10.003907,27.666593 9.80084889,29.7339663 10.1981162,31.7275214 C10.5953834,33.7217752 11.5748126,35.5530237 13.0129853,36.9904978 C14.4509252,38.4277391 16.2828722,39.4064696 18.277126,39.8028054 C20.2711469,40.1991413 22.3382874,39.9951517 24.2163416,39.2169177 C26.0948616,38.4384508 27.7002312,37.1209021 28.8296253,35.4300711 C29.9592522,33.7397058 30.5619062,31.7522051 30.5619062,29.7188301 L30.5619062,18.8324027 C32.7275484,20.3418321 35.3149087,21.0404263 38.0766847,21.0867664 L38.0766847,15.8542954 Z" id="Fill-1" fill="#FFFFFF"></path></g>
                            </svg>
                        </a>
                    </ul>
                </div>
            </div>
        </div>
        <div class="bottom">
            <nav2>
                <div class="nav2-links" id="navLinks">
                    <div class="container">
                        <ul>
                            <li><a href="home.php">Home</a></li>
                            <li class="separator">|</li>
                            <li><a href="https://www.rp.edu.sg/service-excellence/contact-us" target="_blank">Contact</a></li>
                        </ul>
                    </div>
                    <p style="padding: 0">9 Woodlands Avenue 9, Singapore 738964 <br> Copyright © Republic Polytechnic. All Rights Reserved.</p>
                </div>
            </nav2>
        </div>
    </footer>
</div>


</body>
</html>
