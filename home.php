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

// Fetch domain names from the database
$domains = fetchDomainname($conn);

// Function to fetch domain information
function fetchDomainInfo($conn, $domainName) {
    $sql = "SELECT domain_image, domain_description FROM domains WHERE domain_name = '$domainName' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $domainInfo = array();
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $domainInfo['image'] = "Domain_picture/" . $row["domain_image"];
        $domainInfo['description'] = $row["domain_description"];
    } else {
        $domainInfo['image'] = "";
        $domainInfo['description'] = "No description available for $domainName.";
    }
    return $domainInfo;
}

// Fetch domain information for each category
$backgroundImages = array();
$descriptions = array();

foreach ($domains as $key => $domain) {
    $domainInfo = fetchDomainInfo($conn, $domain);
    $backgroundImages[$key] = $domainInfo['image'];
    $descriptions[$key] = $domainInfo['description'];
}

mysqli_close($conn);

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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <!-- Rest of the body content -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
<body>
<section class="header">
    <nav>
        <div class="nav-links" id="navLinks">
            <i class="fas fa-times" onclick="hidemenu()"></i>
            <ul>
                <?php if(isset($_SESSION['login_user'])): ?>
                    <li><a href="home.php"><img src="Domain_picture/transRP.png" alt="Logo"></a></li>
                    <li><span class="welcome-message" style="color: #FFFFFF;">Welcome, <?php echo $_SESSION['login_user']; ?></span></li>
                    <?php if($isAdmin): ?>
                        <li><a href="Login222/users.php?do=Edit&user_id=<?php echo $_SESSION['login_user_id'] ?>">
                                <span style="padding-left:6px">My Profile</span>
                        </a></li>
                        <li><a href="login222/dashboard.php">Admin Panel</a></li>
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
    <?php if ($isAdmin): ?>
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
        <?php endif; ?>
    </section>

    <section class="cards">
        
        <?php foreach ($domains as $key => $domain) : ?>
            
            <div class="card card<?php echo $key + 1; ?>" style="background-image: linear-gradient(rgba(4,9,30,0.5), rgba(4,9,30,0.5)),url('<?php echo isset($backgroundImages[$key]) ? $backgroundImages[$key] : ''; ?>');">
                <div class="card-text">
                    <h2><?php echo $domain; ?></h2>
                    <p><?php echo isset($descriptions[$key]) ? $descriptions[$key] : ''; ?></p>
                    <!-- Display click count dynamically -->
                    <?php if ($isAdmin): ?>
                        <p id="clickCount_<?php echo $domain; ?>">Click Count: <?php echo isset($clickCounts[$domain]) ? $clickCounts[$domain] : 0; ?></p>
                    <?php endif; ?>
                    <a href="domain_page.php?domain=<?php echo urlencode($domain); ?>" class="learn-more-btn" onclick="updateClickCount('<?php echo $domain; ?>')">Learn More</a>
                </div>
            </div>
        <?php endforeach; ?>
        
    </section>

    <section class="bottom">
        <nav2>
            <a href="home.php"></a>
            <div class="nav2-links" id="navLinks">
                <ul>
                    <li><a href="home.php">HOME</a></li>
                </ul>
            </div>
        </nav2>
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
                            <a href="#" onclick="closeForgotPasswordAndShowLogin()">Log in</a>
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
        });
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
</body>
</html>
