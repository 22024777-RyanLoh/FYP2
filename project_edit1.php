<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database Connection
$conn = new mysqli("localhost", "root", "", "fyp_test");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is an admin
$isAdmin = false;
if (isset($_SESSION['login_user_id'])) {
    $userId = $_SESSION['login_user_id'];
    $sql = "SELECT user_role FROM user WHERE user_id = '$userId'";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc(); 
        $isAdmin = ($row['user_role'] === 'Admin');
    }
}

// Place these lines in the <head> section of your HTML to include SweetAlert
echo '
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Custom SweetAlert2 Styles */
        .swal2-popup {
            font-family: "Poppins", sans-serif;
            font-size: 16px;
        }
        .swal2-title {
            font-family: "Poppins", sans-serif;
            font-size: 30px;
        }
        .swal2-content {
            font-family: "Poppins", sans-serif;
            font-size: 18px;
        }
        .swal2-styled.swal2-confirm {
            font-family: "Poppins", sans-serif;
        }
        .swal2-confirm {
        background-color: #8CD4F5 !important; /* Original blue color */
        }
    </style>
</head>
';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the edited values from the form
    $id = $_POST['id'] ?? '';
    $title = $_POST['Etitle'] ?? '';
    $organisation = $_POST['Eorganisation'] ?? '';
    $body = $_POST['Eoverview'] ?? '';
    $members = $_POST['Emembers'] ?? '';
    $supervisor = $_POST['Esupervisor'] ?? '';
    $domain = $_POST['domain'] ?? '';
    $year = $_POST['year'] ?? '';
    $semester = $_POST['semester'] ?? '';

    // Update project details
    $stmt = $conn->prepare("UPDATE project SET Project_title = ?, Project_body = ?, Organisation = ?, Members = ?, Supervisor = ?, domain_id = ?, year_id = ?, Project_semester = ? WHERE Project_ID = ?");
    $stmt->bind_param("ssssssisi", $title, $body, $organisation, $members, $supervisor, $domain, $year, $semester, $id);
    $stmtExecuted = $stmt->execute();
    $stmt->close();

    // Initialize variables for checking execution status
    $stmtDescExecuted = true;
    $stmtImgExecuted = true;
    $stmtInsertExecuted = true;

    // Process existing images and descriptions
    if (isset($_POST['image_id'])) {
        $imageIds = $_POST['image_id'];
        $selectedImages = $_POST['selected_images'];
        
        // Update descriptions for existing images
        $stmtDesc = $conn->prepare("UPDATE project_image SET Image_description = ? WHERE Image_ID = ?");
        foreach ($imageIds as $index => $imageId) {
            $description = $_POST['image_description_' . $index];
            if (!empty($description)) {
                $stmtDesc->bind_param("si", $description, $imageId);
                $stmtDescExecuted = $stmtDesc->execute() && $stmtDescExecuted;
            }
        }
        $stmtDesc->close();
        
    }

    // Process new images
    $stmtInsert = $conn->prepare("INSERT INTO project_image (Project_ID, Image_description, Project_image) VALUES (?, ?, ?)");
    foreach ($_FILES as $key => $file) {
        if ($file['error'] == UPLOAD_ERR_OK && strpos($key, 'new_image_') === 0) {
            $index = str_replace('new_image_', '', $key) - 1;
            $descriptionKey = 'new_image_description_' . ($index + 1);
            $description = $_POST[$descriptionKey];

            $imageData = base64_encode(file_get_contents($file['tmp_name']));
            $stmtInsert->bind_param("iss", $id, $description, $imageData);
            $stmtInsertExecuted = $stmtInsert->execute() && $stmtInsertExecuted;
        }
    }
    $stmtInsert->close();

    if ($stmtExecuted && $stmtDescExecuted && $stmtImgExecuted && $stmtInsertExecuted) {
        echo '
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: "success",
                    title: "Project Updated",
                    text: "Your profile has been updated successfully.",
                    showConfirmButton: true,
                }).then(() => {
                    window.location.href = "upload.php";
                });
            });
        </script>
        ';
    } else {
        echo '
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Something went wrong!",
                });
            });
        </script>
        ';
    }
    exit();
}

// Handle image deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image_id'])) {
    $imageId = $_POST['image_id'];

    $stmt = $conn->prepare("DELETE FROM project_image WHERE Image_ID = ?");
    $stmt->bind_param("i", $imageId);
    $stmt->execute();
    $stmt->close();

    echo '
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: "success",
                title: "Image Deleted",
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = "upload.php";
            });
        });
    </script>
    ';
    exit();
}



// Close the connection
$conn->close();
?>
<style>
    .swal2-title {
    font-family: 'Poppins', sans-serif !important;
}
</style>