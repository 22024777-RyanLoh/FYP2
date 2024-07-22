<?php
ob_start();
session_start();

$conn = mysqli_connect("localhost", "root", "", "fyp_test");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$pageTitle = 'Add and Manage Users';

if (isset($_SESSION['login_user'])) {
    include 'connect.php';
    include 'Includes/functions/functions.php';
    include 'Includes/templates/header.php';
    include 'Includes/templates/navbar.php';
    include 'Includes/templates/footer.php';
?>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../footer.css">

    
<script>
    $(document).ready(function() {
        // Toggle password visibility
        $(".toggle-password").click(function() {
            $(this).toggleClass("fa-eye fa-eye-slash");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });

        // Password validation
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

    // Load table data based on search and pagination
    function loadTableData(page = 1) {
        var rowsPerPage = $("#showAll").is(":checked") ? 0 : $("#rowsPerPage").val();
        var search = $("#searchBox").val();
        var filterRole = $("#filterRole").val();

        $.ajax({
            url: 'fetch_users.php',
            type: 'GET',
            data: {
                rows: rowsPerPage,
                search: search,
                role: filterRole,
                page: page
            },
            success: function(response) {
                $("#usersTable tbody").html(response);
                updatePaginationLinks(page, rowsPerPage);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    function updatePaginationLinks(currentPage, rowsPerPage) {
        var search = $("#searchBox").val();
        var filterRole = $("#filterRole").val();

        $.ajax({
            url: 'fetch_pagination_users.php',
            type: 'GET',
            data: {
                rows: rowsPerPage,
                search: search,
                role: filterRole,
                page: currentPage
            },
            success: function(response) {
                $("#paginationLinks").html(response);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    $("#rowsPerPage, #filterRole").on("change", function() {
        loadTableData();
    });

    $("#showAll").on("change", function() {
        if ($(this).is(":checked")) {
            if (confirm("Are you sure you want to load all rows? This might take a while if there are many rows.")) {
                loadTableData();
            } else {
                $(this).prop("checked", false);
            }
        } else {
            loadTableData();
        }
    });

    $("#searchButton").on("click", function () {
        loadTableData();
    });

    $("#resetButton").on("click", function () {
        $("#searchBox").val('');
        loadTableData();
    });

    $("#searchBox").on("keyup", function (e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            loadTableData();
        }
    });

    window.loadPage = function(page) {
        loadTableData(page);
    };

    loadTableData();
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
}
</script>

    <style>
        .glyphicon-ok {
            color: #00A41E;
        }

        .glyphicon-remove {
            color: #FF0004;
        }

        .bold {
            font-weight: bold;
        }

        .input-group-prepend .input-group-text {
            cursor: pointer;
        }

        #searchBox {
            width: 200px; /* Adjust the width of the search box here */
        }

        .nav a.page-link {
            color: black;
        }

        .ul li a em  {
            color: #fff !important;
        }

    </style>

<?php
$do = isset($_GET['do']) ? htmlspecialchars($_GET['do']) : 'Manage';

if ($do == "Manage") {
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = isset($_GET['rows']) ? intval($_GET['rows']) : 5;
    $start = ($page - 1) * $limit;

    $sql = "SELECT * FROM user ORDER BY user_id DESC LIMIT $start, $limit";
    $result = $conn->query($sql);

    $total_records_sql = "SELECT COUNT(*) FROM user";
    $total_records_result = $conn->query($total_records_sql);
    $total_records = $total_records_result->fetch_array()[0];
    $total_pages = ceil($total_records / $limit);

    $users = $result->fetch_all(MYSQLI_ASSOC);
    $currentUserId = $_SESSION['login_user_id'];
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <?php echo "$pageTitle"; ?>
            <div class="d-flex ml-3">
                <label type="text" class="form-control-plaintext" id="searchBoxLabel" for="searchBox">
                    Filter rows:
                </label>
                <input type="text" class="form-control ml-2" placeholder="Search" id="searchBox">
            </div>
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" id="searchButton">
                    <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-outline-secondary" type="button" id="resetButton">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="form-check ml-3">
                <input class="form-check-input" type="checkbox" value="" id="showAll">
                <label class="form-check-label" for="showAll">
                    Show all rows
                </label>
            </div>
            <!-- Filter by User Role -->
            <div class="d-flex ml-3">
                <label for="filterRole" class="form-control-plaintext" style="margin-right: 10px;">
                    Filter by role:
                </label>
            </div>
            <div>
                <select class="form-control" id="filterRole">
                    <option value="all">All</option>
                    <option value="Staff">Staff</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
        </div>
        <a href="users.php?do=Add" class="btn btn-primary">Add User</a>
    </div>
    <div class="card-body">
        <!-- USERS TABLE -->
        <table class="table table-bordered users-table" id="usersTable">
            <thead>
                <tr>
                    <th scope="col">No.</th>
                    <th scope="col">E-mail</th>
                    <th scope="col">Full Name</th>
                    <th scope="col">Role</th>
                    <th scope="col">Manage</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = ($page - 1) * $limit + 1;
                foreach ($users as $user) {
                    $boldClass = ($user['user_id'] == $currentUserId) ? 'bold' : '';
                    echo "<tr>";
                    echo "<td>$i</td>";
                    echo "<td class='$boldClass'>";
                    echo $user['email'];
                    if ($user['user_id'] == $currentUserId) {
                        echo " (Current User)";
                    }
                    echo "</td>";
                    echo "<td class='$boldClass'>";
                    echo $user['user_fullname'];
                    echo "</td>";
                    echo "<td class='$boldClass'>";
                    echo isset($user['user_role']) ? $user['user_role'] : 'N/A';
                    echo "</td>";
                    echo "<td>";
                    echo "<a href='users.php?do=Edit&user_id=" . $user['user_id'] . "' class='btn btn-success btn-sm rounded-0'>";
                    echo "<i class='fa fa-edit'></i>";
                    echo "</a> ";
                    echo "<a href='users.php?do=Delete&user_id=" . $user['user_id'] . "' class='btn btn-danger btn-sm rounded-0' onclick='return confirm(\"Are you sure you want to delete this user?\");'>";
                    echo "<i class='fa fa-trash'></i>";
                    echo "</a>";
                    echo "</td>";
                    echo "</tr>";
                    $i++;
                }
                ?>
            </tbody>
        </table>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <label for="rowsPerPage">Rows per page:</label>
                <select id="rowsPerPage" class="custom-select">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                </select>
            </div>
            <!-- Pagination links -->
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center" id="paginationLinks">
                    <?php
                    if ($total_pages > 1) {
                        if ($page > 1) {
                            echo '<li class="page-item"><a class="page-link" style="color: black" href="#" onclick="loadPage(' . ($page - 1) . ')">Previous</a></li>';
                        }

                        for ($i = 1; $i <= $total_pages; $i++) {
                            echo '<li class="page-item' . ($page == $i ? ' active' : '') . '"><a class="page-link" href="#" onclick="loadPage(' . $i . ')">' . $i . '</a></li>';
                        }

                        if ($page < $total_pages) {
                            echo '<li class="page-item"><a class="page-link" href="#" onclick="loadPage(' . ($page + 1) . ')">Next</a></li>';
                        }
                    }
                    ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

        <?php
    } elseif ($do == 'Add') {
        ?>
        <div class="card">
            <div class="card-header">
                Add New User
            </div>
            <div class="card-body">
                <form method="POST" class="menu_form" action="users.php?do=Insert" onsubmit="return validateForm();">
                    <div class="panel-X">
                        <div class="panel-header-X">
                            <div class="main-title">Addition of New Staff/Admin</div>
                        </div>
                        <div class="save-header-X">
                            <div style="display:flex">
                                <div class="icon">
                                    <i class="fa fa-sliders-h"></i>
                                </div>
                                <div class="title-container">User details</div>
                            </div>
                            <div class="button-controls">
                                <button type="submit" name="add_user_sbmt" class="btn btn-primary">Confirm Add User</button>
                            </div>
                        </div>
                        <div class="panel-body-X">
                            <!-- FULL NAME INPUT -->
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input type="text" class="form-control" placeholder="Full Name" name="full_name" required>
                            </div>

                            <!-- User Email INPUT with Validation -->
                            <div class="form-group">
                                <label for="user_email">User E-mail</label>
                                <input type="email" class="form-control" placeholder="User Email" name="user_email" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$">
                            </div>

                            <!-- User Role INPUT -->
                            <div class="form-group">
                                <label for="user_role">User Role</label>
                                <select class="form-control" name="user_role" id="user_role" required>
                                    <option value="Staff">Staff</option>
                                    <option value="Admin">Admin</option>
                                </select>
                            </div>

                            <!-- User Password INPUT -->
                            <div class="form-group">
                                <label for="user_password">New User Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" placeholder="Password" name="user_password" id="user_password" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fa fa-eye toggle-password" toggle="#user_password"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Re-Enter Password INPUT -->
                            <div class="form-group">
                                <label for="re_user_password">Re-Enter Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" placeholder="Re-Enter Password" name="re_user_password" id="re_user_password" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="fa fa-eye toggle-password" toggle="#re_user_password"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Password Criteria -->
                            <div class="password-criteria">
                                <ul>
                                    <li id="8char" class="glyphicon glyphicon-remove"> 8 Characters Long</li>
                                    <li id="ucase" class="glyphicon glyphicon-remove"> One Uppercase Letter</li>
                                    <li id="lcase" class="glyphicon glyphicon-remove"> One Lowercase Letter</li>
                                    <li id="num" class="glyphicon glyphicon-remove"> One Number</li>
                                    <li id="pwmatch" class="glyphicon glyphicon-remove"> Passwords Match</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
    } elseif ($do == 'Insert') {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_fullname = test_input($_POST['full_name']);
            $user_email = test_input($_POST['user_email']);
            $user_password = md5(test_input($_POST['user_password']));
            $user_role = test_input($_POST['user_role']);

            try {
                $stmt = $con->prepare("INSERT INTO user (email, user_fullname, user_password, user_role) VALUES (?, ?, ?, ?)");
                $stmt->execute(array($user_email, $user_fullname, $user_password, $user_role));

        ?> 
                <!-- SUCCESS MESSAGE -->
                <script type="text/javascript">
                    swal("Add User", "User has been added successfully", "success").then((value) => {
                        window.location.replace("users.php");
                    });
                </script>
        <?php
            } catch (Exception $e) {
                echo 'Error occurred: ' . $e->getMessage();
            }
        } else {
            header('Location: users.php');
            exit();
        }
    } elseif ($do == 'Delete') {
        $user_id = (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) ? intval($_GET['user_id']) : 0;

        if ($user_id) {
            try {
                $stmt = $con->prepare("DELETE FROM user WHERE user_id = ?");
                $stmt->execute(array($user_id));
        ?> 
                <!-- SUCCESS MESSAGE -->
                <script type="text/javascript">
                    swal("Delete User", "User has been deleted successfully", "success").then((value) => {
                        window.location.replace("users.php");
                    });
                </script>
        <?php
            } catch (Exception $e) {
                echo 'Error occurred: ' . $e->getMessage();
            }
        } else {
            header('Location: users.php');
            exit();
        }
    } elseif ($do == 'Edit') {
        $user_id = (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) ? intval($_GET['user_id']) : 0;

        if ($user_id) {
            $stmt = $con->prepare("SELECT * FROM user WHERE user_id = ?");
            $stmt->execute(array($user_id));
            $user = $stmt->fetch();
            $count = $stmt->rowCount();
            if ($count > 0) {
                $isCurrentUser = $user_id == $_SESSION['login_user_id'];
        ?>

                <div class="card">
                    <div class="card-header">Edit User</div>
                    <div class="card-body">
                        <form method="POST" class="menu_form" action="users.php?do=Edit&user_id=<?php echo $user['user_id'] ?>" onsubmit="return <?php echo $isCurrentUser ? 'validatePassword()' : 'true'; ?>">
                            <div class="panel-X">
                                <div class="panel-header-X">
                                    <div class="main-title"><?php echo $user['user_fullname']; ?></div>
                                </div>
                                <div class="save-header-X">
                                    <div style="display:flex">
                                        <div class="icon">
                                            <i class="fa fa-sliders-h"></i>
                                        </div>
                                        <div class="title-container">User details</div>
                                    </div>
                                    <div class="button-controls">
                                        <button type="submit" name="edit_user_sbmt" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                                <div class="panel-body-X">
                                    <!-- User ID -->
                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">

                                    <!-- FULL NAME INPUT -->
                                    <div class="form-group">
                                        <label for="full_name">Full Name</label>
                                        <input type="text" class="form-control" value="<?php echo $user['user_fullname'] ?>" placeholder="Full Name" name="full_name" required>
                                    </div>

                                    <!-- User Email INPUT with Validation -->
                                    <div class="form-group">
                                        <label for="user_email">User E-mail</label>
                                        <input type="email" class="form-control" value="<?php echo $user['email'] ?>" placeholder="User Email" name="user_email" required pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$" title="Invalid email address. You are missing an '@' and '.' in your email.">
                                    </div>

                                    <!-- User Role INPUT -->
                                    <div class="form-group">
                                        <label for="user_role">User Role</label>
                                        <select class="form-control" name="user_role" id="user_role" required>
                                            <option value="Staff" <?php echo isset($user['user_role']) && $user['user_role'] == 'Staff' ? 'selected' : ''; ?>>Staff</option>
                                            <option value="Admin" <?php echo isset($user['user_role']) && $user['user_role'] == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                    </div>

                                    <!-- User Password INPUT -->
                                    <?php if ($isCurrentUser) : ?>
                                        <div class="form-group">
                                            <label for="user_password">Change Password (Optional)</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" placeholder="Change password" name="user_password" id="user_password">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-eye toggle-password" toggle="#user_password"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Re-Enter Password INPUT -->
                                        <div class="form-group">
                                            <label for="re_user_password">Re-Enter Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" placeholder="Re-Enter Password" name="re_user_password" id="re_user_password">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-eye toggle-password" toggle="#re_user_password"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Password Criteria -->
                                        <div class="password-criteria">
                                            <ul>
                                                <li id="8char" class="glyphicon glyphicon-remove"> 8 Characters Long</li>
                                                <li id="ucase" class="glyphicon glyphicon-remove"> One Uppercase Letter</li>
                                                <li id="lcase" class="glyphicon glyphicon-remove"> One Lowercase Letter</li>
                                                <li id="num" class="glyphicon glyphicon-remove"> One Number</li>
                                                <li id="pwmatch" class="glyphicon glyphicon-remove"> Passwords Match</li>
                                            </ul>
                                        </div>
                                    <?php else: ?>
                                        <div class="form-group">
                                            <label for="user_password">Change Password (Disabled)</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" placeholder="Password" id="user_password" disabled>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-eye toggle-password" toggle="#user_password"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="re_user_password">Re-Enter Password (Disabled)</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" placeholder="Re-Enter Password" id="re_user_password" disabled>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-eye toggle-password" toggle="#re_user_password"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </form>
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
                <ul style="color: #fff !important;" >
                    <li><a href="http://www.facebook.com/republicpolytechnic" target="_blank" class="footer-socialicon" aria-label="Facebook"><em class="fa fa-facebook" style="color: #fff !important;"></em></a></li>
                    <li><a href="https://sg.linkedin.com/school/republic-polytechnic/" target="_blank" class="footer-socialicon" aria-label="LinkedIn"><em class="fa fa-linkedin"></em></a></li>
                    <li><a href="http://www.youtube.com/channelRP" target="_blank" class="footer-socialicon" aria-label="YouTube"><em class="fa fa-youtube"></em></a></li>
                    <li><a href="http://www.instagram.com/republicpoly" target="_blank" class="footer-socialicon" aria-label="Instagram"><em class="fa fa-instagram"></em></a></li>
                    <li><a href="http://twitter.com/republicpoly" target="_blank" class="footer-socialicon" aria-label="Twitter"><svg style="margin-bottom:5px;width:22px;height:22px; vertical-align: middle;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" xml:space="preserve" enable-background="new 0 0 24 24"><path d="M14.095 10.316 22.286 1h-1.94L13.23 9.088 7.551 1H1l8.59 12.231L1 23h1.94l7.51-8.543L16.45 23H23l-8.905-12.684zm-2.658 3.022-.872-1.218L3.64 2.432h2.98l5.59 7.821.869 1.219 7.265 10.166h-2.982l-5.926-8.3z" fill="#ffffff" class="fill-000000"></path></svg></a></li>
                    <li><a href="https://www.tiktok.com/@republicpoly" target="_blank" aria-label="TikTok"><svg style="margin-bottom:-5px; width: 33px; height: 33px; vertical-align: middle;" viewBox="10 8 48 48" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="footer-socialicon"><title>Tiktok</title><g id="Icon/Social/tiktok-black" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><path d="M38.0766847,15.8542954 C36.0693906,15.7935177 34.2504839,14.8341149 32.8791434,13.5466056 C32.1316475,12.8317108 31.540171,11.9694126 31.1415066,11.0151329 C30.7426093,10.0603874 30.5453728,9.03391952 30.5619062,8 L24.9731521,8 L24.9731521,28.8295196 C24.9731521,32.3434487 22.8773693,34.4182737 20.2765028,34.4182737 C19.6505623,34.4320127 19.0283477,34.3209362 18.4461858,34.0908659 C17.8640239,33.8612612 17.3337909,33.5175528 16.8862248,33.0797671 C16.4386588,32.6422142 16.0833071,32.1196657 15.8404292,31.5426268 C15.5977841,30.9658208 15.4727358,30.3459348 15.4727358,29.7202272 C15.4727358,29.0940539 15.5977841,28.4746337 15.8404292,27.8978277 C16.0833071,27.3207888 16.4386588,26.7980074 16.8862248,26.3604545 C17.3337909,25.9229017 17.8640239,25.5791933 18.4461858,25.3491229 C19.0283477,25.1192854 19.6505623,25.0084418 20.2765028,25.0219479 C20.7939283,25.0263724 21.3069293,25.1167239 21.794781,25.2902081 L21.794781,19.5985278 C21.2957518,19.4900128 20.7869423,19.436221 20.2765028,19.4380839 C18.2431278,19.4392483 16.2560928,20.0426009 14.5659604,21.1729264 C12.875828,22.303019 11.5587449,23.9090873 10.7814424,25.7878401 C10.003907,27.666593 9.80084889,29.7339663 10.1981162,31.7275214 C10.5953834,33.7217752 11.5748126,35.5530237 13.0129853,36.9904978 C14.4509252,38.4277391 16.2828722,39.4064696 18.277126,39.8028054 C20.2711469,40.1991413 22.3382874,39.9951517 24.2163416,39.2169177 C26.0948616,38.4384508 27.7002312,37.1209021 28.8296253,35.4300711 C29.9592522,33.7397058 30.5619062,31.7522051 30.5619062,29.7188301 L30.5619062,18.8324027 C32.7275484,20.3418321 35.3149087,21.0404263 38.0766847,21.0867664 L38.0766847,15.8542954 Z" id="Fill-1" fill="#FFFFFF"></path></g></svg></a></li>
                </ul>
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
                    <p style="padding: 0;">9 Woodlands Avenue 9, Singapore 738964 <br> Copyright © Republic Polytechnic. All Rights Reserved.</p>
                </div>
            </nav2>
        </div>
    </footer>
</div>

<style>
.footer-socialicon svg, .footer-socialicon em {
    width: 33px !important;
    height: 33px !important;
    fill: #FFFFFF !important;
    color: #FFFFFF !important;
}
.footer-socialicon svg path, .footer-socialicon em {
    fill: #FFFFFF !important;
    color: #FFFFFF !important;
}
</style>


        <?php
                if (isset($_POST['edit_user_sbmt']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
                    $user_id = test_input($_POST['user_id']);
                    $user_fullname = test_input($_POST['full_name']);
                    $user_email = test_input($_POST['user_email']);
                    $user_role = test_input($_POST['user_role']);
                    $user_password = isset($_POST['user_password']) ? test_input($_POST['user_password']) : '';
                
                    if (empty($user_password)) {
                        try {
                            $stmt = $con->prepare("UPDATE user SET email = ?, user_fullname = ?, user_role = ? WHERE user_id = ?");
                            $stmt->execute(array($user_email, $user_fullname, $user_role, $user_id));
                
                            // Only update session if the current user is the one being edited
                            if ($user_id == $_SESSION['login_user_id']) {
                                $_SESSION['login_user'] = $user_fullname;
                            }
                
                            ?> 
                            <!-- SUCCESS MESSAGE -->
                            <script type="text/javascript">
                                swal("Edit User", "User has been updated successfully", "success").then((value) => {
                                    window.location.replace("users.php");
                                });
                            </script>
                            <?php
                        } catch (Exception $e) {
                            echo 'Error occurred: ' . $e->getMessage();
                        }
                    } else {
                        $user_password = md5($user_password);
                        try {
                            $stmt = $con->prepare("UPDATE user SET email = ?, user_fullname = ?, user_password = ?, user_role = ? WHERE user_id = ?");
                            $stmt->execute(array($user_email, $user_fullname, $user_password, $user_role, $user_id));
                
                            // Only update session if the current user is the one being edited
                            if ($user_id == $_SESSION['login_user_id']) {
                                $_SESSION['login_user'] = $user_fullname;
                            }
                
                            ?> 
                            <!-- SUCCESS MESSAGE -->
                            <script type="text/javascript">
                                swal("Edit User", "User has been updated successfully", "success").then((value) => {
                                    window.location.replace("users.php");
                                });
                            </script>
                            <?php
                
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
                                $mail->setFrom('naolao11111@gmail.com', 'fyp');
                                $mail->addAddress($user_email); // Add a recipient
                
                                // Content
                                $mail->isHTML(true); // Set email format to HTML
                                $mail->Subject = 'Password Changed Successfully';
                                $mail->Body = "Hi, $user_fullname <br><br> Your password has been successfully reset. If you did not initiate this change, please contact Republic Polytechnic's support immediately. <br><br>
                                Please do not reply to this email with your password. We will never ask for your password, and we strongly discourage you from sharing it with anyone.";
                
                                $mail->send();
                            } catch (Exception $e) {
                                // Handle the error if email could not be sent
                            }
                        } catch (Exception $e) {
                            echo 'Error occurred: ' . $e->getMessage();
                        }
                    }
                }
                
            } else {
                header('Location: users.php');
            }
        } else {
            header('Location: users.php');
        }
    }

    include 'Includes/templates/footer.php';
} else {
    header('Location: index.php');
    exit();
}
?>

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
                    <p style="padding: 0;">9 Woodlands Avenue 9, Singapore 738964 <br> Copyright © Republic Polytechnic. All Rights Reserved.</p>
                </div>
            </nav2>
        </div>
    </footer>
</div>