<?php


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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Of Infocomm</title>
    <link rel="stylesheet" href="home.css">
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



<style>

.header {
    min-height: 15vh;
    width: 100%;
    background-image: linear-gradient(rgba(4,9,30,0.7), rgba(4,9,30,0.7)), url(../Domain_picture/homepage.png);
    background-position: center;
    background-size: cover;
    position: relative;
}

.bottom {
    min-height: 10vh;
    width: 100%;
    background-color: rgb(241, 70, 2);
    background-position: center;
    position: relative;
}

nav {
    display: flex;
    padding: 2% 6%;
    justify-content: space-between;
    align-items: center;
}

nav img {
    width: 150px;
}

.nav-links {
    flex: 1;
    text-align: right;
}

.nav-links ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-links ul li {
    display: inline-block;
    padding: 8px 12px;
    position: relative;
}

.nav-links ul li a,
.welcome-message {
    color: #FFF;
    text-decoration: none;
    font-size: 15px;
}

.nav-links ul li::after {
    content: '';
    width: 0%;
    height: 2px;
    background: #f44336;
    display: block;
    margin: auto;
    transition: 0.5s;
}

.nav-links ul li:hover::after {
    width: 100%;
}

nav2 {
    display: flex;
    padding: 2% 6%;
    justify-content: space-between;
    margin-top: 130px;
    align-items: center;
}

.nav2-links {
    flex: 1;
    text-align: left;
}

.nav2-links ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav2-links ul li {
    display: inline-block;
    padding: 8px 12px;
    position: relative;
}

.nav2-links ul li a {
    color: #fff;
    text-decoration: none;
    font-size: 15px;
}

.nav2-links ul li::after {
    content: '';
    width: 0%;
    height: 2px;
    background: #f44336;
    display: block;
    margin: auto;
    transition: 0.5s;
}

.nav2-links ul li:hover::after {
    width: 100%;
}

</style>

<section class = "header">
    <nav>
        <a href="../home.php"><img src="../Domain_picture/transRP.png" alt="Logo"></a>
        <div class="nav-links" id="navLinks">
            <i class="fas fa-times" onclick="hidemenu()"></i>
            <ul>
                <?php if(isset($_SESSION['login_user'])): ?>
                    <li><span class="welcome-message" style="color: #FFFFFF;">Welcome, <?php echo $_SESSION['login_user']; ?></span></li>
                    <?php if($isAdmin): ?>
                        <li><a href="users.php?do=Edit&user_id=<?php echo $_SESSION['login_user_id'] ?>">
                                <span style="padding-left:6px">My Profile</span>
                        </a></li>
                        <li><a href="dashboard.php">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="../edit.php">Domain</a></li>
                    <li><a href="../upload.php">Project</a></li>
                    <li><a href="logout.php">Sign out</a></li>
                <?php else: ?>
                    <li><a href="#" onclick="showLoginModal()">Log in</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <i class="fas fa-bars" onclick="showmenu()"></i>
    </nav>
    </section>


    <!-- ADMIN NAVBAR HEADER
    <header class="headerMenu clearfix sb-page-header">
        <div class="nav-header">
            <a class="navbar-brand" href="users.php">Admin Panel</a>
        </div>
        <div class="nav-controls top-nav ml-auto">
            <ul class="nav top-menu ml-auto">
                <li id="user-btn" class="main-li dropdown" style="background:none;">
                    <div class="dropdown show">
                        <a class="btn btn-secondary dropdown-toggle" href="" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-user"></i>
                            <span class="username"><?php echo $_SESSION['login_user']; ?></span>
                            <b class="caret"></b>
                        </a>
                         DROPDOWN MENU 
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <a class="dropdown-item" href="users.php?do=Edit&user_id=<?php echo $_SESSION['login_user_id'] ?>">
                                <i class="fas fa-user-cog"></i>
                                <span style="padding-left:6px">Edit My Profile</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i>
                                <span style="padding-left:6px">Logout</span>
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </header> -->
</body>
</html>


<!-- VERTICAL NAVBAR 

<aside class="vertical-menu" id="vertical-menu">
    <div>
        <ul class="menu-bar">
            <div class="sidenav-menu-heading">
                Core
            </div>

            <div class="dropdown-divider"></div>

            <li>
                <a href="dashboard.php" class="a-verMenu dashboard_link">
                    <i class="fas fa-tachometer-alt icon-ver"></i>
                    <span style="padding-left:6px;">Dashboard</span>
                </a>
            </li>

            <div class="dropdown-divider"></div> 

            
            
            <div class="sidenav-menu-heading">
                Staff and Admin
            </div>
            
            <div class="dropdown-divider"></div>
           
            <li>
                <a href="users.php" class="a-verMenu users_link">
                    <i class="far fa-user icon-ver"></i>
                    <span style="padding-left:6px;">Staff and Admin</span>
                </a>
            </li>
            

            <div class="dropdown-divider"></div>

        </ul>
    </div>
</aside>-->

<!-- START BODY CONTENT  -->

<div id="content" style="margin-left:240px;"> 
    <section class="content-wrapper" style="width: 100%;padding: 70px 0 0;">
        <div class="inside-page" style="padding:20px">
            <div class="page_title_top" style="margin-bottom: 1.5rem!important;">
                <h1 style="color: #5a5c69!important;font-size: 1.75rem;font-weight: 400;line-height: 1.2;">
                    <?php echo $pageTitle; ?>
                </h1>
            </div>
