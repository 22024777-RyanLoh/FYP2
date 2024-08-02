<?php
include 'connect.php';
$conn = mysqli_connect("localhost", "root", "", "fyp_test");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $year = $_POST['year'];

    // Check if the year is already in the database
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM years WHERE year = ?");
    $check_stmt->bind_param('i', $year);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        echo 'Year already exists.';
    } else {
        $stmt = $conn->prepare("INSERT INTO years (year, display_year) VALUES (?, 0)");
        $stmt->bind_param('i', $year);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error: ' . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>
