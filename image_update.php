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

        // Generate a unique filename for the uploaded image
        $fileName = time() . "." . $fileType;
        $uploadPath = "Domain_picture/" . $fileName;

        // Move uploaded file to the specified upload directory
        if (move_uploaded_file($_FILES["domain_image"]["tmp_name"], $uploadPath)) {
            // Remove old image file from directory if it exists
            $sql_select = "SELECT domain_image FROM domains WHERE domain_id = ?";
            $stmt_select = $conn->prepare($sql_select);
            $stmt_select->bind_param("i", $_POST["id"]);
            $stmt_select->execute();
            $stmt_select->store_result();
            $stmt_select->bind_result($old_image);
            $stmt_select->fetch();
            
            if (!empty($old_image)) {
                $old_image_path = "Domain_picture/" . $old_image;
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }

            // Update database record with new image and description information
            $sql_update = "UPDATE domains SET domain_image = ?, domain_name = ?, domain_description = ? WHERE domain_id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("sssi", $fileName, $_POST["domain"], $description, $_POST["id"]);
            $stmt_update->execute();

            if ($stmt_update->affected_rows > 0) {
                echo "Image and Domain Updated Successfully.";
            } else {
                echo "Failed to update image and domain in the database.";
            }
        } else {
            echo "Failed to move uploaded file to directory.";
        }
    } else {
        // If no new image file is uploaded, update only the domain name and description
        $sql_update = "UPDATE domains SET domain_name = ?, domain_description = ? WHERE domain_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $_POST["domain"], $description, $_POST["id"]);
        $stmt_update->execute();

        if ($stmt_update->affected_rows > 0) {
            echo "Domain has been updated successfully.";
        } else {
            echo "Please make a change to the description, or upload a new image to save changes.";
        }
    }
} else {
    echo "Invalid request method.";
}
?>
