<?php
// Database Connection
$conn = mysqli_connect("localhost", "root", "", "fyp_test");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if 'id' parameter is set
if (isset($_GET["id"])) {
    $id = intval($_GET["id"]);
    
    // Prepare and execute deletion query
    $stmt = $conn->prepare("DELETE FROM domains WHERE domain_id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // If deletion was successful, delete the image from the server
        $imagePath = "/xampp/htdocs/fyp/domain_name/{$_GET['name']}";
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Redirect to index page with status = 1 (success)
        header("Location: edit.php?status=1");
    } else {
        // Redirect to index page with status = 0 (failure)
        header("Location: edit.php?status=0");
    }
    
    // Close statement and connection
    $stmt->close();
} else {
    // Redirect if no 'id' parameter is set
    header("Location: edit.php?status=0");
}

$conn->close();
?>
