<?php
ob_start();
session_start();

$conn = mysqli_connect("localhost", "root", "", "fyp_test");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$pageTitle = 'Manage User';

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

if (isset($_SESSION['login_user'])) {
    include 'connect.php';
    include 'Includes/functions/functions.php';
    include 'Includes/templates/header.php';
    // include 'Includes/templates/navbar.php';
?>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../footer.css">

    
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    
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
        .swal2-confirm {
        background-color: #8CD4F5 !important; /* Original blue color */
        }
        .btn-success{
            background-color: #007bff !important;
            border-color: #007bff !important;
        }
        .fa1 {
            display: inline-block;
            font: normal !important;
            font-size: inherit;
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
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
        table {
            table-layout: fixed;
        }
      
        th:nth-child(1), td:nth-child(1) {
            width: 5%;
        }

        th:nth-child(2), td:nth-child(2) {
            width: 30%;
        }

        th:nth-child(3), td:nth-child(3) {
            width: 30%;
        }

        th:nth-child(4), td:nth-child(4) {
            width: 6%;
        }

        th:nth-child(5), td:nth-child(5) {
            width: 8%;
        }
    </style>

    <!-- navbar -->
<section class="header">
        <nav>
            <div class="nav-links" id="navLinks">
                <i class="fas fa-times hide-icon" onclick="hidemenu()"></i>
                <ul>
                    <?php if(isset($_SESSION['login_user'])): ?>
                        <li><a href="../home.php"><img src="../Domain_picture/transRP.png" alt="Logo"></a></li>
                        <li>
                            <a href="#" onclick="Swal.fire('Warning', 'Unable to edit your profile. Use the Manage User section to search for your current user.', 'warning'); return false;">
                                <span class="welcome-message" style="color: #FFFFFF;">Welcome, <?php echo $_SESSION['login_user']; ?></span>
                            </a>
                        </li>
                        <?php if($isAdmin): ?>
                            <li><a href="../login222/dashboard.php"<?php echo $_SESSION['login_user_id'] ?>>
                                    <span style="color: #fff;">Dashboard</span>
                            </a></li>
                            <li><a href="../login222/users.php">Admin Panel</a></li>
                        <?php endif; ?>
                        <li><a href="../edit.php">Manage Domain</a></li>
                        <li><a href="../upload.php">Manage Project</a></li>
                        <li><a href="logout.php">Sign out</a></li>
                    <?php else: ?>
                        <li><a href="../home.php"><img src="../Domain_picture/transRP.png" alt="Logo"></a></li>
                        <li><a href="#" onclick="showLoginModal()">Log in</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <i class="fas fa-bars show-icon" onclick="showmenu()"></i>
        </nav>
    </section>

    <!-- START BODY CONTENT  -->

    <div id="content"> 
    <section class="content-wrapper" style="width: 100%;">
        <div class="inside-page" style="padding-bottom: 40px;">
            <div class="page_title_top" style="text-align: center;">
                <h1 style="color: #212529!important;font-size: 2.5rem;font-weight: 500;">
                    <?php echo $pageTitle; ?>
                </h1>
            </div>
        </div>
    </section>
</div>     

<style>
    .inside-page{
    display: flex;
    justify-content: center;
    align-items: center;
    height: 140px;
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
                <label type="text" class="form-control-plaintext" id="searchBoxLabel" for="searchBox" style="margin-right: 10px; text-align:center;">
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
                <label for="filterRole" class="form-control-plaintext" style="margin-right: 10px; text-align:center;">
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
                // Inside the table row rendering loop for Manage
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
                    if ($user['user_id'] == $currentUserId) {
                        echo "<button class='btn btn-danger btn-sm rounded-0' onclick='forbidDeletion(); return false;'>";
                        echo "<i class='fa fa-trash'></i>";
                        echo "</button>";
                    } else {
                        echo "<button class='btn btn-danger btn-sm rounded-0' onclick='confirmDeletion(" . $user['user_id'] . "); return false;'>";
                        echo "<i class='fa fa-trash'></i>";
                        echo "</button>";
                    }
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
                    Swal.fire({
                        title: 'Add User',
                        text: 'User has been added successfully',
                        icon: 'success',
                        confirmButtonColor: '#8CD4F5'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.replace("users.php");
                        }
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
                    Swal.fire({
                        title: 'Delete User',
                        text: 'User has been deleted successfully',
                        icon: 'success',
                        confirmButtonColor: '#8CD4F5'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.replace("users.php");
                        }
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
                    <div class="card-header" style="text-align:center;">Edit User</div>
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
                                        <select class="form-control" name="user_role" id="user_role" <?php echo $isCurrentUser ? 'disabled' : ''; ?> required>
                                            <option value="Staff" <?php echo isset($user['user_role']) && $user['user_role'] == 'Staff' ? 'selected' : ''; ?>>Staff</option>
                                            <option value="Admin" <?php echo isset($user['user_role']) && $user['user_role'] == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                        <?php if ($isCurrentUser): ?>
                                            <input type="hidden" name="user_role" value="<?php echo $user['user_role']; ?>">
                                        <?php endif; ?>
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




    <script>
        var navLinks = document.getElementById("navLinks");

        function showmenu() {   
            navLinks.style.top = "0";
        }

        function hidemenu() {
            navLinks.style.top = "-100vh";
        }

        function confirmDeletion(userId) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'users.php?do=Delete&user_id=' + userId;
            }
        })
    }

    function forbidDeletion() {
        Swal.fire({
            title: 'Action Forbidden',
            text: 'You cannot delete your own user account.',
            icon: 'error',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
        });
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
    

<script>
    document.addEventListener("DOMContentLoaded", function() {
    const rows = document.querySelectorAll('.responsive-table tbody tr');

    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('td'));
        cells.forEach((cell, index) => {
            // Only set data-label for columns that are visible
            if (index === 1 || index === 2 || index === 4) {
                const dataLabel = cell.textContent.trim();
                cell.setAttribute('data-label', dataLabel);
            }
        });
    });
});
</script>


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
                                Swal.fire({
                                    title: 'Edit User',
                                    text: 'User has been updated successfully',
                                    icon: 'success',
                                    confirmButtonColor: '#8CD4F5'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.replace("users.php");
                                    }
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
                                Swal.fire({
                                    title: 'Edit User',
                                    text: 'User has been updated successfully',
                                    icon: 'success',
                                    confirmButtonColor: '#8CD4F5'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.replace("users.php");
                                    }
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