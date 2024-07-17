<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="edit.css">
</head>
<body>
    
</body>
</html>

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
?>
