<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageID = $_POST['image_id'];

    // Connect to your database
    $conn = mysqli_connect("localhost","root","","fyp_test");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Delete the image from the database
    $sql = "DELETE FROM project_image WHERE Image_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $imageID);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }

    $stmt->close();
    $conn->close();
}
?>