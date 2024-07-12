<?php
session_start();
$isAdmin = isset($_SESSION["admin"]) && $_SESSION["admin"] === true;
?>

<!doctype html>
<html lang="en">
    <head>
    <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Upload Poster</title>
        <link rel="stylesheet" href="edit.css" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" href="test2.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>
<body>
    <section class = "header">
        <nav>
            <a href="home.php"><img src="Domain_picture/logo1.png" alt="Logo"></a>
            <div class="nav-links" id="navLinks">
                <i class="fas fa-times" onclick="hidemenu()"></i>
                <ul>
                <li><a href="home.php">Home</a></li>
                    <?php if ($isAdmin): ?>
                        <li><a href="edit.php">Domain </a></li>
                        <li><a href="upload.php">Project </a></li>
                        <li><a href="logout.php">Sign out</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Log in</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <i class="fas fa-bars" onclick="showmenu()" ></i>
        </nav>
    </section>

<?php
// Connect to MySQL database (replace with your credentials)
$servername = "localhost";
$username = "root";
$password = "";
$database = "fyp_test";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$selectedImages = isset($_POST['selected_images']) ? $_POST['selected_images'] : [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the edited values from the textarea fields
    $title = $_POST['Etitle'] ?? '';
    $organisation = $_POST['Eorganisation'] ?? '';
    $body = $_POST['Eoverview'] ?? '';
    $members = $_POST['Emembers'] ?? '';
    $supervisor = $_POST['Esupervisor'] ?? '';
    $domain = $_POST['domain'] ?? '';
    $year = $_POST['year'] ?? '';
    $semester = $_POST['semester'] ?? '';

    // Now you can use these variables as needed, such as storing them in a database or performing any other processing.
}

 // Insert text into project table
$stmt = $conn->prepare("INSERT INTO project (Project_title, Project_body, Organisation, Members, Supervisor, domain_id, Project_year, Project_semester) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssis", $title, $body, $organisation, $members, $supervisor, $domain, $year, $semester);

// Execute the statement
if ($stmt->execute()) {
    $Project_ID = $stmt->insert_id;

    // Insert images into project_image table
    $conn->query("SET GLOBAL max_allowed_packet=67108864");

    $stmt = $conn->prepare("INSERT INTO project_image (Project_ID, Project_image, Image_description) VALUES (?, ?, ?)");

    foreach ($selectedImages as $index => $project_image) {
        $description = $_POST['image_description_' . $index+1];
        $stmt->bind_param("iss", $Project_ID, $project_image, $description);
        $stmt->execute();
    }
 
    header("Location: upload.php");
} else {
    echo "Error: " . $stmt->error;
}
//Close the statement
$stmt->close();
?>
