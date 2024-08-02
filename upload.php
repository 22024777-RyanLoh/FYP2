<?php
session_start();

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
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Upload Poster</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="project.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
         
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">

    <!-- SweetAlert JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

        <style>
        .swal2-confirm {
            background-color: #8CD4F5 !important; /* Original blue color */
        }
        
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

        .ml-3{
        background-color: #007BFF !important;
        border-color: #007BFF !important;
        }

        </style>

</head>
    <!-- Rest of the body content -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<body>

<script>
function showProfileModal() {
        window.location.href = "/fyp/login222/users.php?showProfileModal=true";
    }

window.showProfileModal = function() {
            $('#myProfileModal').modal('show');
        }
</script>

<?php
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

<section class="header">
    <nav>
        <div class="nav-links" id="navLinks">
            <i class="fas fa-times hide-icon" onclick="hidemenu()"></i>
            <ul>
                <?php if(isset($_SESSION['login_user'])): ?>
                    <li><a href="home.php"><img src="Domain_picture/transRP.png" alt="Logo"></a></li>
                    <li><a href="#" onclick="showProfileModal()"><span class="welcome-message" style="color: #FFFFFF;">Welcome, <?php echo $_SESSION['login_user']; ?></span></a></li>
                    <?php if($isAdmin): ?>
                        <li><a href="login222/dashboard.php"<?php echo $_SESSION['login_user_id'] ?>">
                                <span style="color: #fff;">Dashboard</span>
                        </a></li>
                        <li><a href="login222/users.php">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="edit.php">Manage Domain</a></li>
                    <li><a href="upload.php">Manage Project</a></li>
                    <li><a href="logout.php">Sign out</a></li>
                <?php else: ?>
                    <li><a href="home.php"><img src="Domain_picture/transRP.png" alt="Logo"></a></li>
                    <li><a href="#" onclick="showLoginModal()">Log in</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <i class="fas fa-bars show-icon" onclick="showmenu()"></i>
    </nav>
</section>

    <div class="title-text">
        <h1>Manage Project</h1>
    </div>

<div class="project_control">
    <form id= "addProjectForm" action="upload1.php" method="post" enctype="multipart/form-data">
        <div class="form-input">
            <label>New Project</label>
            <input type="file" name="pdf_file" required class="form-control" style="display:auto" >
            <button type="submit" class="btn btn-primary" style="margin:10px">Upload</button>
        </div>
    </form>
</div>
    <div class="">
        <div class="row">
            <div class="'col-md-6 mx-auto">
            <?php
            // Database Connection
            $conn = mysqli_connect("localhost", "root", "", "fyp_test");
            $message = "";

            // Fetch all from domains
            $domains_sql = "SELECT * FROM domains";
            $domains_result = $conn->query($domains_sql);

            // Pagination configuration
            $limit = 15; // Number of records per page
            $page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number, default is 1
            $start = ($page - 1) * $limit; // Calculate starting point for the query

            // Fetch data from database with pagination
            $sql = "SELECT * FROM project p JOIN domains d ON p.domain_id = d.domain_id JOIN years y ON y.year_id = p.year_id LIMIT $start, $limit";
            $result = $conn->query($sql);
            ?>     
            </div>
        </div>
    </div>

    <div class="table-container">
            <div class="table-content">
                <table class="table responsive-table">
                <h2>Project List</h2>
                    <thead>
                        <tr>
                            <th>SNo</th>
                            <th>Title</th>
                            <th>Domain</th>
                            <th>Year</th>
                            <th>Semester</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php
                        $i = ($page - 1) * $limit + 1;
                        while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $row['Project_title']; ?></td>
                                <td><?php echo $row['domain_name']; ?></td>
                                <td><?php echo $row['year']; ?></td>
                                <td><?php echo $row['Project_semester']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <form action="project_edit.php" method="post" enctype="multipart/form-data" class="mr-2 mb-0">
                                            <input type="hidden" name="ProjectID" value="<?php echo $row['Project_ID']; ?>">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </form>
                                        <button class="btn btn-danger" onclick="confirmProjectDeletion(<?php echo $row['Project_ID']; ?>); return false;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            $i++;
                        endwhile;
                        ?>
                    </tbody>
                </table>

                <!-- Pagination links -->
                <div class="pagination justify-content-center">
                        <?php
                        $sql_count = "SELECT COUNT(Project_ID) AS total FROM project";
                        $result_count = $conn->query($sql_count);
                        $row_count = $result_count->fetch_assoc();
                        $total_pages = ceil($row_count["total"] / $limit);
                        
                        if ($total_pages > 1) {
                            if ($page > 1) {
                                echo '<li class="page-item"><a class="page-link" href="?page=' . ($page - 1) . '">Previous</a></li>';
                            }
                        
                            for ($i = 1; $i <= $total_pages; $i++) {
                                echo '<li class="page-item' . ($page == $i ? ' active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                            }
                        
                            if ($page < $total_pages) {
                                echo '<li class="page-item"><a class="page-link" href="?page=' . ($page + 1) . '">Next</a></li>';
                            }
                        }
                        ?>
                </div>
            </div>
    </div>

    <script>
    function confirmDelete(projectTitle) {
        return confirm('Are you sure you want to delete the project: ' + projectTitle + '?');
    }
    </script>
    
    <script>
function confirmProjectDeletion(projectId, projectName) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success custom-swal-button ml-3',  // Add left margin to the confirm button
            cancelButton: 'btn btn-danger custom-swal-button'  // Add custom class for the cancel button
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'project_delete.php?id=' + projectId + '&name=' + encodeURIComponent(projectName);
        }
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
                        <ul style="padding:0;">
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
            $("#8char").removeClass("glyphicon-remove").addClass("glyphicon-ok").css("color", "#00A41E");
        } else {
            $("#8char").removeClass("glyphicon-ok").addClass("glyphicon-remove").css("color", "#FF0004");
        }

        if (ucase.test($("#user_password").val())) {
            $("#ucase").removeClass("glyphicon-remove").addClass("glyphicon-ok").css("color", "#00A41E");
        } else {
            $("#ucase").removeClass("glyphicon-ok").addClass("glyphicon-remove").css("color", "#FF0004");
        }

        if (lcase.test($("#user_password").val())) {
            $("#lcase").removeClass("glyphicon-remove").addClass("glyphicon-ok").css("color", "#00A41E");
        } else {
            $("#lcase").removeClass("glyphicon-ok").addClass("glyphicon-remove").css("color", "#FF0004");
        }

        if (num.test($("#user_password").val())) {
            $("#num").removeClass("glyphicon-remove").addClass("glyphicon-ok").css("color", "#00A41E");
        } else {
            $("#num").removeClass("glyphicon-ok").addClass("glyphicon-remove").css("color", "#FF0004");
        }

        if ($("#user_password").val() === $("#re_user_password").val()) {
            $("#pwmatch").removeClass("glyphicon-remove").addClass("glyphicon-ok").css("color", "#00A41E");
        } else {
            $("#pwmatch").removeClass("glyphicon-ok").addClass("glyphicon-remove").css("color", "#FF0004");
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
                            <li><a href="home.php">Home</a></li>
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
    </script>

    <script>
        function hidemenu() {
            document.getElementById('navLinks').classList.remove('active');
            document.querySelector('.hide-icon').style.visibility = 'hidden';
            document.querySelector('.show-icon').style.visibility = 'visible';
        }

        function showmenu() {
            document.getElementById('navLinks').classList.add('active');
            document.querySelector('.hide-icon').style.visibility = 'visible';
            document.querySelector('.show-icon').style.visibility = 'hidden';
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
    </script>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.3.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-QDT3qP5xXAA2nTeB5S2ur1bV+6vQEO3H+3s5l0ZccRsmkWZi1iXwr17wRlCWc7A9" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            // Check URL for status parameter
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');

            if (status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'The project has been successfully saved!',
                    confirmButtonColor: '#3085d6' // Blue color for the confirm button
                });
            } else if (status === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while saving the project.'
                });
            }

            if (status === '1') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Your project has been successfully deleted!'
                });
            } else if (status === '0') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while deleting the project.'
                });
            }

        });
    </script>
    
    </script>


<script>
document.addEventListener("DOMContentLoaded", function() {
    const rows = document.querySelectorAll('.responsive-table tbody tr');

    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('td'));
        cells.forEach((cell, index) => {
            // Set data-label for all visible columns
            const headers = row.parentNode.previousElementSibling.querySelectorAll('th'); // Get headers
            const headerText = headers[index] ? headers[index].textContent.trim() : ''; // Get header text
            cell.setAttribute('data-label', headerText); // Set data-label based on header
        });
    });
});


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
            $('html, body').animate({scrollTop: 0}, 10);
            return false;
        });

    });
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
            $('html, body').animate({scrollTop: 0}, 10);
            return false;
        });

    });
</script>

</body>
</html>
