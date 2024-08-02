<?php
include 'connect.php';
$conn = mysqli_connect("localhost", "root", "", "fyp_test");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $display_year = $_POST['display_year'];

    $stmt = $conn->prepare("UPDATE years SET display_year = ? WHERE year_id = ?");
    $stmt->bind_param('ii', $display_year, $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
