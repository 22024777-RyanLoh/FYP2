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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../home.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

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

<style>
* {
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
}

.header {
    min-height: 40px;
    width: 100%;
    background-image: linear-gradient(rgba(4,9,30,0.7), rgba(4,9,30,0.7)), url(Domain_picture/homepage.png);
    background-position: center;
    background-size: cover;
    position: relative;
}

@media (max-width: 768px) {
    .header {
        min-height: 15vh;
    }
}

.card-body {
    display: flex;
    flex-direction: column;
    min-height: 73vh;
    font-family: 'Poppins', sans-serif;
    margin: 100px;
}


.bottom {
    min-height: 10vh;
    width: 100%;
    background-color:#333;
    background-position: center;
    position: relative;
}

/*--------------navbar------------------*/
.hide-icon {
    visibility: hidden;
}

.show-icon {
    visibility: hidden;
    position: fixed !important;
    top: 20px; /* Adjust the top position as needed */
    right: 10px; /* Adjust the right position as needed */
}

@media (max-width: 768px) {
    .show-icon {
        visibility: visible;
    }

    .hide-icon {
        visibility: visible;
    }
}

nav {
    display: flex;
    padding: 2% 6%;
    justify-content: space-between;
    align-items: center;
}

@media (max-width:768px){
    .nav-links fal fa-times{
        justify-items: right;
    }

}

nav img {
    width: 150px;
}

.nav-links {
    flex: 1;
    text-align: right;
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
    display: inline-block;
    padding: 8px 12px;
    position: relative;
}

.nav-links ul li a,
.welcome-message {
    color: #fff;
    text-decoration: none;
    font-size: 15px;
}

.nav-links ul li:first-child {
    margin-right: auto !important; /* Push the logo to the left */
}

.nav-links ul li:first-child a {
    padding: 0 m !important; /* Remove padding for the logo */
}

.nav-links ul li:first-child a img {
    width: 150px !important;
}

.nav-links ul li:first-child::after {
    content: none !important; /* Remove the orange line under the logo */
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

@media(max-width: 768px) {

    .text-box h1 {
        font-size: 20px;
    }

    .nav-links ul{
        display: block;
    }

    .nav-links {
        position: absolute;
        background: #333;
        height: 70vh;
        width: 100%; /* Full width to cover the screen */
        top: -100vh; /* Start off-screen at the top */
        left: 0; /* Ensure it covers the entire width */
        text-align: center;
        z-index: 1000; /* Ensure it appears on top */
        transition: 1s;
    }

    .nav-links.active {
        top: 0 !important;
    }

    nav .fa {
        display: block;
        color: #fff;
        margin: 10px;
        font-size: 10px;
        cursor: pointer;
    }

    .nav-links ul li {
        display: block !important; /* Block display for list items */
        margin: 20px 0; /* Spacing between items */
    }

    nav .fa-bars, nav .fa-times {
        justify-content: right;
        color: #fff;
        margin: 10px;
        font-size: 30px;
        cursor: pointer;
    }

}
/*--------------navbar------------------*/

</style>

<section class="header">
    <nav>
        <div class="nav-links" id="navLinks">
            <i class="fas fa-times hide-icon" onclick="hidemenu()"></i>
            <ul>
                <?php if(isset($_SESSION['login_user'])): ?>
                    <li><a href="../home.php"><img src="../Domain_picture/transRP.png" alt="Logo"></a></li>
                    <li><a href="#" onclick="showProfileModal()"><span class="welcome-message" style="color: #FFFFFF;">Welcome, <?php echo $_SESSION['login_user']; ?></span></a></li>
                    <?php if($isAdmin): ?>
                        <li><a href="../login222/dashboard.php"<?php echo $_SESSION['login_user_id'] ?>">
                                <span style="color: #fff;">Dashboard</span>
                        </a></li>
                        <li><a href="../login222/users.php">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="../edit.php">Manage Domain</a></li>
                    <li><a href="../upload.php">Manage Project</a></li>
                    <li><a href="../logout.php">Sign out</a></li>
                <?php else: ?>
                    <li><a href="../home.php"><img src="Domain_picture/transRP.png" alt="Logo"></a></li>
                    <li><a href="#" onclick="showLoginModal()">Log in</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <i class="fas fa-bars show-icon" onclick="showmenu()"></i>
    </nav>
</section>

</body>
</html>


<!-- START BODY CONTENT  -->

<div id="content"> 
    <section class="content-wrapper" style="width: 100%;">
        <div class="inside-page">
            <div class="page_title_top" style="text-align: center;">
                <h1 style="color: #212529!important;font-size: 2.5rem;font-weight: 500;">
                    <?php echo "Dashboard"; ?>
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
    height: 100px;
    }
</style>