<?php
// Start session
session_start();

// Set page title
$pageTitle = 'Dashboard';

// PHP INCLUDES
include 'connect.php';
include 'Includes/functions/functions.php'; 
include 'Includes/templates/header.php';


// TEST IF THE SESSION HAS BEEN CREATED BEFORE
if(isset($_SESSION['login_user']) && $_SESSION['user_role'] == 'Admin') {
    include 'Includes/templates/navbar.php';
?>
    <script type="text/javascript">
        var vertical_menu = document.getElementById("vertical-menu");
        var current = vertical_menu.getElementsByClassName("active_link");
        if(current.length > 0) {
            current[0].classList.remove("active_link");
        }
        vertical_menu.getElementsByClassName('dashboard_link')[0].className += " active_link";
    </script>
WORK IN PROGRESS <BR>
WILL ADD THINGS LATER.
<!-- TOP 4 CARDS -->
<!-- Add your card code here -->
<?php
} else {
    header("Location: ../home.php");
    exit();
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../footer.css">
</head>
<body>

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
                <form class="menu_form" id="profileForm" method="POST" action="../home.php" onsubmit="return validatePassword();">
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
                        <ul style="padding-left: 0;">
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

		<div class="content">
			<footer class="footer">
				<div class="footer-content container">
					<div class="col-md-3">
						<h3><a href="https://www.rp.edu.sg/about-us" target="_blank">About Us</a></h3>
						<ul>
							<li><a href="https://www.rp.edu.sg/about-us/who-we-are" target="_blank">Who We Are</a></li>
							<li><a href="https://www.rp.edu.sg/about-us/our-people" target="_blank">Our People</a></li>
							<li><a href="https://www.rp.edu.sg/about-us/media" target="_blank">Media</a></li>
						</ul>
					</div>
	
					<div class="footer-section social-media">
						<h3>Connect With Us</h3>
						<ul>
							<li><a href="http://www.facebook.com/republicpolytechnic" target="_blank" aria-label="Facebook"><em class="fa fa-facebook"></em></a></li>
							<li><a href="https://sg.linkedin.com/school/republic-polytechnic/" target="_blank" aria-label="LinkedIn"><em class="fa fa-linkedin"></em></a></li>
							<li><a href="http://www.youtube.com/channelRP" target="_blank" aria-label="YouTube"><em class="fa fa-youtube"></em></a></li>
							<li><a href="http://www.instagram.com/republicpoly" target="_blank" aria-label="Instagram"><em class="fa fa-instagram"></em></a></li>
							<li><a href="http://twitter.com/republicpoly" target="_blank" aria-label="Twitter">
								<svg style="margin-bottom:5px;width:22px;height:22px; vertical-align: middle;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" xml:space="preserve" enable-background="new 0 0 24 24"><path d="M14.095 10.316 22.286 1h-1.94L13.23 9.088 7.551 1H1l8.59 12.231L1 23h1.94l7.51-8.543L16.45 23H23l-8.905-12.684zm-2.658 3.022-.872-1.218L3.64 2.432h2.98l5.59 7.821.869 1.219 7.265 10.166h-2.982l-5.926-8.3z" fill="#ffffff" class="fill-000000"></path></svg>
							</a></li>
							<li><a href="https://www.tiktok.com/@republicpoly" target="_blank" aria-label="TikTok">
								<svg style="margin-bottom:-5px; width: 33px; height: 33px; vertical-align: middle;" viewBox="10 8 48 48" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="footer-socialicon"><title>Tiktok</title>
								<g id="Icon/Social/tiktok-black" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><path d="M38.0766847,15.8542954 C36.0693906,15.7935177 34.2504839,14.8341149 32.8791434,13.5466056 C32.1316475,12.8317108 31.540171,11.9694126 31.1415066,11.0151329 C30.7426093,10.0603874 30.5453728,9.03391952 30.5619062,8 L24.9731521,8 L24.9731521,28.8295196 C24.9731521,32.3434487 22.8773693,34.4182737 20.2765028,34.4182737 C19.6505623,34.4320127 19.0283477,34.3209362 18.4461858,34.0908659 C17.8640239,33.8612612 17.3337909,33.5175528 16.8862248,33.0797671 C16.4386588,32.6422142 16.0833071,32.1196657 15.8404292,31.5426268 C15.5977841,30.9658208 15.4727358,30.3459348 15.4727358,29.7202272 C15.4727358,29.0940539 15.5977841,28.4746337 15.8404292,27.8978277 C16.0833071,27.3207888 16.4386588,26.7980074 16.8862248,26.3604545 C17.3337909,25.9229017 17.8640239,25.5791933 18.4461858,25.3491229 C19.0283477,25.1192854 19.6505623,25.0084418 20.2765028,25.0219479 C20.7939283,25.0263724 21.3069293,25.1167239 21.794781,25.2902081 L21.794781,19.5985278 C21.2957518,19.4900128 20.7869423,19.436221 20.2765028,19.4380839 C18.2431278,19.4392483 16.2560928,20.0426009 14.5659604,21.1729264 C12.875828,22.303019 11.5587449,23.9090873 10.7814424,25.7878401 C10.003907,27.666593 9.80084889,29.7339663 10.1981162,31.7275214 C10.5953834,33.7217752 11.5748126,35.5530237 13.0129853,36.9904978 C14.4509252,38.4277391 16.2828722,39.4064696 18.277126,39.8028054 C20.2711469,40.1991413 22.3382874,39.9951517 24.2163416,39.2169177 C26.0948616,38.4384508 27.7002312,37.1209021 28.8296253,35.4300711 C29.9592522,33.7397058 30.5619062,31.7522051 30.5619062,29.7188301 L30.5619062,18.8324027 C32.7275484,20.3418321 35.3149087,21.0404263 38.0766847,21.0867664 L38.0766847,15.8542954 Z" id="Fill-1" fill="#FFFFFF"></path></g>
								</svg>
							</a></li>
						</ul>
					</div>
				</div>
				<div class="bottom">
					<div class="nav2-links" id="navLinks">
						<div class="container">
							<ul>
								<li><a href="../home.php">Home</a></li>
								<li class="separator">|</li>
								<li><a href="https://www.rp.edu.sg/service-excellence/contact-us" target="_blank">Contact</a></li>
							</ul>
						</div>
						<p style="padding: 0;">9 Woodlands Avenue 9, Singapore 738964 <br> Copyright © Republic Polytechnic. All Rights Reserved.</p>
					</div>
				</div>
			</footer>
		</div>
	
		<script>
        var navLinks = document.getElementById("navLinks");

        function showmenu() {   
            navLinks.style.top = "0";
        }

        function hidemenu() {
            navLinks.style.top = "-100vh";
        }

        function handleScroll() {
            const showIcon = document.querySelector('.show-icon');
            if (window.innerWidth <= 768) {
                if (window.scrollY > 0) {
                    showIcon.style.visibility = 'hidden';
                } else {
                    showIcon.style.visibility = 'visible';
                }
            } else {
                showIcon.style.visibility = 'hidden'; // Ensure icon is visible when not in @media range
            }
        }

        // Event listener for scroll
        window.addEventListener('scroll', handleScroll);

        // Event listener for resize to handle screen size changes
        window.addEventListener('resize', handleScroll);

        // Initial check
        handleScroll();


        function showProfileModal() {
                window.location.href = "/fyp/login222/users.php?showProfileModal=true";
            }

        window.showProfileModal = function() {
                    $('#myProfileModal').modal('show');
                }

    </script>

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
</script>

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
</body>
</html>