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

$pdfText = ''; 

// Ensure the form is submitted with POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // File upload path
    $fileName = basename($_FILES["pdf_file"]["name"]); 
    $fileType = pathinfo($fileName, PATHINFO_EXTENSION); 
     
    // Allow certain file formats
    $allowTypes = array('pdf'); 
    if (in_array($fileType, $allowTypes)) { 
        // Include autoloader file 
        require 'vendor/autoload.php'; 
         
        // Initialize and load PDF Parser library 
        $parser = new \Smalot\PdfParser\Parser(); 
         
        // Source PDF file to extract text 
        $file = $_FILES["pdf_file"]["tmp_name"]; 
         
        // Parse pdf file using Parser library 
        $pdf = $parser->parseFile($file); 
         
        // Extract text from PDF 
        $text = $pdf->getText(); 
         
        // Add line break 
        $pdfText = $text; 

        // Extract image from PDF
        $project_images = $pdf->getObjects();

    } else {
        echo '<p>Sorry, only PDF files are allowed to upload.</p>';
    }
    
}

// Close MySQL connection
$conn->close();
?>

<div class="m-3">
    <h1>Project Details Editor</h1>
    <b><span style="color: grey">Instruction: To ensure the text is correctly parsed, you need to add spaces to split it into six sections based on specific keywords. 
    The sections are separated by double line breaks.<br></span>Keywords: <span style="color: orange"> Partner Organisation:</span>, <span style="color: orange">Project Overview</span>, 
    <span style="color: orange">Team Members:</span>, <span style="color: orange">Supervisor:</span>
</div>
    <form action="process.php" method="post">
        <div class="m-3">
            <label for="inputText" class="form-label"></label>
            <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($pdfText)) {
                echo '<div style="display: flex;">';
                echo '<textarea class="form-control" id="inputText" name="inputText" rows="30" style="width: 50%">' . htmlspecialchars($pdfText) . '</textarea>';
                echo '<pre class="form-control" style="width: 50%" height: 800px >'.
                '<b><span style="color: grey">Example:</span><br>'.
                'Design and Development of 3D Assets for Istana Heritage Mobile Trail for Community Engagement<br><br>'.
                '<span style="color: orange">Partner Organisation:</span> LDR PteLtd & The Istana<br><br>'.
                '<span style="color: orange">Project Overview</span><br>'.
                'The project allows visitors of The Istana to better understand the heritage and <br>'.
                'history of The Istana through LDR’s  LocoMolemobile app. <br>'.
                'The 3D assets, which are showcased via Augmented Reality (AR) from the <br>'.
                'mobile app, aimed  to create an interactive and engaging experience for users <br>on the Istana ground. <br>'.
                'Keytechnologies:<br>-3DsMax<br>-AdobeIllustrator<br>-AdobePhotoshop<br>-WikitudeSDKViewer<br><br>'.
                '<span style="color: orange">Team Members:</span><br>Ryan Wong<br>Jasmine Seow<br>Ang Jia Yi<br><br>'.
                '<span style="color: orange">Supervisor:</span><br>MsTan Hwee Yong<br><br>'.
                'The Istana Building<br>YusofIshakBust<br>The Project Team<br>Map of The Istana'.'</pre>';
                echo '</div>';
                 }
            ?>
        </div>
        
        <?php foreach ($project_images as $project_image):
            $ImageData = base64_encode($project_image->getContent()); 
            if (substr($ImageData, 0, 3) === '/9j') :
                echo '<img src="data:image/jpeg;base64,'.$ImageData.'" style="margin: 10px; max-width: 300px; max-height: 300px;"/>'; 
            ?>
                <input type="checkbox" name="selected_images[]" value="<?php echo $ImageData ?>"><br>
            <?php endif; ?>
        
        <?php endforeach; ?>
    <button type="submit" class="btn btn-primary">Submit</button>
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