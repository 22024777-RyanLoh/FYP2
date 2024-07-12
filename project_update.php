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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $title = $_POST["title"];
    $project_body = $_POST["body"];
    $organisation = $_POST["organisation"];
    $members = $_POST["members"];
    $supervisor = $_POST["supervisor"];
    $domain = $_POST["domain"];
    $project_year = $_POST["year"];
    $project_semester = $_POST["semester"];
    
    $sql_update = "UPDATE project SET Project_title = ?, Project_body = ?, Organisation = ?, Members = ?, Supervisor = ?, domain_id = ?, Project_year = ?, Project_semester = ? WHERE Project_ID = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssssissi", $title, $project_body, $organisation, $members, $supervisor, $domain, $project_year, $project_semester, $id);
    $stmt_update->execute();
    if ($stmt_update->affected_rows > 0) {
        echo "Project has been updated successfully.";
    } else {
        echo "Failed to update new information in the project table.";
    }

} else {
    echo "Invalid request method.";
}
?>
