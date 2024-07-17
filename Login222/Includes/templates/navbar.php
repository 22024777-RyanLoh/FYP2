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
    <link rel="stylesheet" href="home.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>


<style>
* {
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
}


.header {
    min-height: 40px;
    width: 100%;
    background-image: linear-gradient(rgba(4,9,30,0.7), rgba(4,9,30,0.7)), url(../Domain_picture/homepage.png);
    background-position: center;
    background-size: cover;
    position: relative;
}

.card-body {
    display: flex;
    flex-direction: column;
    min-height: 73vh;
    font-family: 'Poppins', sans-serif;
    margin: 100px;
}


.bottom {
    min-height: 100px;
    width: 100%;
    background-color: rgb(241, 70, 2);
    background-position: center;
    position: relative;
}

nav {
    justify-content: center; /* Center the navigation links */
    padding: 2% 6%;
    justify-content: space-between;
    align-items: center;
}

.nav-links ul {
    display: flex;
    align-items: center;
    list-style: none;
    margin: 0;
    padding: 0;
    width: 100%;
    justify-content: space-between;
}

.nav-links ul li {
    padding: 0 12px;
    position: relative;
}

.nav-links ul li:first-child {
    margin-right: auto; /* Push the logo to the left */
}

.nav-links ul li:first-child a {
    padding: 0; /* Remove padding for the logo */
}

.nav-links ul li:first-child a img {
    width: 150px;
}

.nav-links ul li:first-child::after {
    content: none; /* Remove the orange line under the logo */
}

.nav-links ul li a,
.welcome-message {
    color: #fff;
    text-decoration: none;
    font-size: 15px;
    white-space: nowrap; /* Ensure the text does not wrap */
}

.nav-links ul li:not(:first-child) {
    margin-left: 0; /* Reset margin to prevent extra spacing */
}

.nav-links ul li:not(:first-child)::after {
    content: '';
    width: 0%;
    height: 2px;
    background: #f44336;
    display: block;
    margin: auto;
    transition: 0.5s;
}

.nav-links ul li:not(:first-child):hover::after {
    width: 100%;
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
/* Navigation bar styles */
.nav2 {
    display: flex;
    justify-content: center; /* Center the navigation links */
    padding: 2% 6%;
    align-items: center;
    color: #fff;
}   

.nav2-links {
    text-align: center;
    padding-top: 10px;
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
    transition: color 0.3s ease;
}

.nav2-links ul li::after {
    content: '';
    width: 0%;
    height: 2px;
    background: #f44336;
    display: block;
    margin: auto;
    transition: width 0.5s;
}

.nav2-links ul li:hover::after {
    width: 100%;
}

.nav2-links ul li:hover a {
    color: #f44336; /* Change link color on hover */
}

.separator {
    color: #fff; /* Color for the separator */
    padding: 8px 12px;
    font-size: 15px;
}

.text-box {
    width: 90%;
    color: #fff;
    position: absolute;
    top: 60%;
    left: 50%;
    transform: translate(-50%,-50%);
    text-align: center;
}

.text-box h1 {
    font-size: 62px;
}

.text-box p {
    margin: 10px 0 40px;
    font-size: 14px;
    color: #fff;
}

nav .fa {
    display: none;
}

@media(max-width: 700px) {
    .text-box h1 {
        font-size: 20px;
    }
    .nav-links ul li {
        display: block;
    }
    .nav-links {
        position: absolute;
        background: #f44336;
        height: 100vh;
        width: 200px;
        top: 0;
        right: -200px;
        text-align: left;
        z-index: 2;
        transition: 1s;
    }
    nav .fa {
        display: block;
        color: #fff;
        margin: 10px;
        font-size: 10px;
        cursor: pointer;
    }
    .nav-links ul {
        padding: 30px;
    }
}
p {
    color: #ffffff;
    font-size: 14px;
    font-weight: 300;
    line-height: 22px;
}

</style>

<section class = "header">
    <nav>
        <div class="nav-links" id="navLinks">
            <i class="fas fa-times" onclick="hidemenu()"></i>
            <ul>
                <?php if(isset($_SESSION['login_user'])): ?>
                    <li><a href="../home.php"><img src="../Domain_picture/transRP.png" alt="Logo"></a></li>
                    <li><span class="welcome-message" style="color: #FFFFFF;">Welcome, <?php echo $_SESSION['login_user']; ?></span></li>
                    <?php if($isAdmin): ?>
                        <li><a href="users.php?do=Edit&user_id=<?php echo $_SESSION['login_user_id'] ?>">
                                <span>My Profile</span>
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

<div id="content"> 
    <section class="content-wrapper" style="width: 100%;padding: 70px 0 0;">
        <div class="inside-page" style="padding:20px">
            <div class="page_title_top" style="margin-bottom: 1.5rem!important;">
                <h1 style="color: #5a5c69!important;font-size: 1.75rem;font-weight: 400;line-height: 1.2;">
                    <?php echo $pageTitle; ?>
                </h1>
    </section>
</div>

            