<?php
session_start();

// Check if user is an admin (example bypass, should be validated securely)
$isAdmin = true; // Example: should be checked against user roles

if (!$isAdmin) {
    header("HTTP/1.1 403 Forbidden");
    exit("You are not authorized to perform this action.");
}

// Database Connection
$conn = mysqli_connect("localhost", "root", "", "fyp_test");

// Check if ID and name are provided via GET request
if (isset($_GET["id"]) && isset($_GET["name"])) {
    $domain_id = $_GET["id"];
    $domain_name = $_GET["name"];

    // Delete image record from database
    $sql_delete = "DELETE FROM domains WHERE domain_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $domain_id);

    if ($stmt_delete->execute()) {
        // Delete image from server
        $image_path = "Domain_picture/" . $domain_name;
        if (file_exists($image_path)) {
            unlink($image_path); // Delete the image file
        }

        // Check if any rows were affected
        if ($stmt_delete->affected_rows > 0) {
            $_SESSION['delete_message'] = "Domain has been deleted successfully."; // Store success message in session
        } else {
            $_SESSION['delete_message'] = "No domain record found with ID: $domain_id."; // Store message about no record found
        }
    } else {
        $_SESSION['delete_message'] = "Failed to delete domain from the database. Error: " . $conn->error; // Store error message
    }
} else {
    $_SESSION['delete_message'] = "Invalid parameters provided."; // Store message for invalid parameters
}

header("Location: edit.php"); // Redirect back to edit.php after deletion
exit();
?>
