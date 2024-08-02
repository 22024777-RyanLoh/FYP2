<?php
include 'connect.php';
$conn = mysqli_connect("localhost", "root", "", "fyp_test");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM years WHERE year_id = ?");
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }

    $stmt->close();
    $conn->close();
}
?>
