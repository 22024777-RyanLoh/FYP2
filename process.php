<?php
session_start();

// Database Connection
$conn = mysqli_connect("localhost", "root", "", "fyp_test");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the user is an admin
$isAdmin = false;
if (isset($_SESSION['login_user_id'])) {
    $userId = $_SESSION['login_user_id'];
    $sql = "SELECT user_role FROM user WHERE user_id = '$userId'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result); 
        $isAdmin = ($row['user_role'] === 'Admin');
    }
}
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
    <style>
        .container1{
            margin:20px;
        }
    </style>
    </head>
<body>
    <section class = "header">
        <nav>
            <a href="home.php"><img src="Domain_picture/logo1.png" alt="Logo"></a>
            <div class="nav-links" id="navLinks">
                <i class="fas fa-times" onclick="hidemenu()"></i>
                <ul>
                    <?php if(isset($_SESSION['login_user'])): ?>
                        <li><span class="welcome-message" style="color: #ffffff;">Welcome, <?php echo $_SESSION['login_user']; ?></span></li>
                        <?php if($isAdmin): ?>
                        <li><a href="login222/dashboard.php">Admin Panel</a></li>
                        <?php endif; ?>
                        <li><a href="edit.php">Domain</a></li>
                        <li><a href="upload.php">Project</a></li>
                        <li><a href="logout.php">Sign out</a></li>
                    <?php else: ?>
                        <li><a href="Login222/index.php">Log in</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <i class="fas fa-bars" onclick="showmenu()"></i>
        </nav>
    </section>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $text = $_POST['inputText'];
    // Split the text by double line breaks to get each section
    $sections = preg_split('/\r\n\r\n|\r\r|\n\n/', $text);

    // Fetch all from domains
    $conn = mysqli_connect("localhost", "root", "", "fyp_test");
    $domains_sql = "SELECT * FROM domains";
    $domains_result = $conn->query($domains_sql);

    // Initialize variables
    $title = $organisation = $overview = $members = $supervisor = $image_description = '';
    $selectedImages = isset($_POST['selected_images']) ? $_POST['selected_images'] : [];

    // Assign each section to a variable
    foreach ($sections as $section) {
        if (empty($title)) {
            $title = $section;
        } elseif (empty($organisation) && strpos($section, 'Partner Organisation:') !== false) {
            $organisation = str_replace('Partner Organisation: ', '', $section);
        } elseif (empty($overview) && strpos($section, 'Project Overview') !== false) {
            $overview = preg_replace('/^Project Overview\s*/', '', $section);
        } elseif (empty($members) && strpos($section, 'Team Members') !== false) {
            $members = preg_replace('/^Team Members:?\s*/', '', $section);
        } elseif (empty($supervisor) && strpos($section, 'Supervisor') !== false) {
            $supervisor = preg_replace('/^Supervisor:?\s*/', '', $section);
        } else {
            $image_description .= $section . "\n\n";
        }
    }


    // Trim trailing newlines from image_description
    $image_description = trim($image_description);
} else {
    echo "Invalid request method.";
}
?>
    <form action="save_to_database.php" method="post">
        <div class='container1'>
            <h2>Review the Information</h2>
            <strong>Title:</strong><br>
            <textarea name="Etitle" rows='4' cols='120' required><?php echo htmlspecialchars($title); ?></textarea><br><br>
            <strong>Organisation:</strong><br>
            <textarea name="Eorganisation" rows='4' cols='120'><?php echo htmlspecialchars($organisation); ?></textarea><br><br>
            <strong>Overview:</strong><br>
            <textarea name="Eoverview" rows='15' cols='120' required><?php echo htmlspecialchars($overview); ?></textarea><br><br>
            <strong>Members:</strong><br>
            <textarea name="Emembers" rows='4' cols='120' required><?php echo htmlspecialchars($members); ?></textarea><br><br>
            <strong>Supervisor:</strong><br>
            <textarea name="Esupervisor" rows='4' cols='120' required><?php echo htmlspecialchars($supervisor); ?></textarea><br><br>
            <strong>Image Description:</strong><br>
            <textarea name="Eimage_description" rows='10' cols='120'><?php echo htmlspecialchars($image_description); ?></textarea><br><br>
            <label for="domain" required>Domain:</label>
            <select name="domain" id="domain">
            <?php
                            // Fetch domains again for the modal dropdown
                            $domains_result;
                            while ($domain_row = $domains_result->fetch_assoc()) {
                                echo "<option value='{$domain_row['domain_id']}'>{$domain_row['domain_name']}</option>";
                            }
                            ?>
            </select>
            <label for="year" required>Year:</label>
            <select name="year" id="year">
                <option value="2021">2021</option>
                <option value="2022">2022</option>
                <option value="2023">2023</option>
                <option value="2024">2024</option>
            </select>

            <label for="semester" required>Semester:</label>
            <select name="semester" id="semester">
                <option value="Sem 1">Sem 1</option>
                <option value="Sem 2">Sem 2</option>
            </select>

            <h2>Selected Images</h2>
            <?php 
            $index = 1;
            foreach ($selectedImages as $project_image):?>
            <div>
            <h3>Image <?php echo $index ?></h3>
            <?php echo '<img src="data:image/jpeg;base64,'.htmlspecialchars($project_image).'" style="margin: 10px; max-width: 300px; max-height: 300px;"/>'; ?>
            <input type="checkbox" name="selected_images[]" value="<?php echo $project_image ?>" checked><br>
            <strong>Image Description:</strong><br>
            <textarea name="image_description_<?php echo $index; ?>" rows='3' cols='120'></textarea><br><br>

            </div>
            <?php 
            $index++; // Increment the index variable
            endforeach; ?>

            <button type="submit">Submit</button>
        </div>
    </form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <section class="bottom">
        <nav2>
            <a href="home.php"></a>
            <div class="nav2-links" id="navLinks">
                <ul>
                    <li><a href="home.php">HOME</a></li>
                </ul>
            </div>
        </nav2>
    </section>
    <script>
        var navLinks = document.getElementById("navLinks");
        function showmenu() {
            navLinks.style.right = "0";
        }
        function hidemenu() {
            navLinks.style.right = "-200px";
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>