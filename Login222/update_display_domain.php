<?php
include 'connect.php';
$conn = mysqli_connect("localhost", "root", "", "fyp_test");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $display_domain = $_POST['display_domain'];

    $stmt = $conn->prepare("UPDATE domains SET display_domain = ? WHERE domain_id = ?");
    $stmt->bind_param('ii', $display_domain, $id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
