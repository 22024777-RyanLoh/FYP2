<?php
session_start();
$isAdmin = true; // Bypass admin check for testing

if (!$isAdmin) {
    header("HTTP/1.1 403 Forbidden");
    exit("You are not authorized to perform this action.");
}

// Database Connection
$conn = mysqli_connect("localhost", "root", "", "fyp_test");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $domain = $_POST["domain"];
    $description = $_POST["description"]; // New addition for description handling

    // Check if a new image file is uploaded
    if (isset($_FILES["domain_image"]) && $_FILES["domain_image"]["error"] === UPLOAD_ERR_OK) {
        // Allowed file types and size check
        $allowedTypes = ["png", "jpg", "jpeg"];
        $fileType = strtolower(pathinfo($_FILES["domain_image"]["name"], PATHINFO_EXTENSION));

        if (!in_array($fileType, $allowedTypes)) {
            exit("Invalid image format. Only PNG, JPG, and JPEG files are allowed.");
        }

        if ($_FILES["domain_image"]["size"] > 1007200) {
            exit("Image size exceeds the limit. Maximum allowed size is 1MB.");
        }

        // Read the image file as binary data and encode it in base64
        $imageData = base64_encode(file_get_contents($_FILES["domain_image"]["tmp_name"]));

        // Update database record with new image (base64) and description information
        $sql_update = "UPDATE domains SET domain_image = ?, domain_name = ?, domain_description = ? WHERE domain_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $imageData, $domain, $description, $id);
        $stmt_update->execute();

        if ($stmt_update->affected_rows > 0) {
            echo '<script type="text/javascript">
                swal("Update Domain", "Domain has been updated successfully", "success").then((value) => {
                    window.location.replace("edit.php");
                });
            </script>';
        } else {
            echo '<script type="text/javascript">
                swal("Update Domain", "Failed to update image and domain in the database.", "error");
            </script>';
        }
        // Close the statement
        $stmt_update->close();
    } else {
        // If no new image file is uploaded, update only the domain name and description
        $sql_update = "UPDATE domains SET domain_name = ?, domain_description = ? WHERE domain_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $domain, $description, $id);
        $stmt_update->execute();

        if ($stmt_update->affected_rows > 0) {
            echo '<script type="text/javascript">
                swal("Update Domain", "Domain has been updated successfully", "success").then((value) => {
                    window.location.replace("edit.php");
                });
            </script>';
        } else {
            echo '<script type="text/javascript">
                swal("Update Domain", "Please make a change to the description, or upload a new image to save changes.", "error");
            </script>';
        }
        

        // Close the statement
        $stmt_update->close();
    }
} else {
    echo "Invalid request method.";
}
?>
