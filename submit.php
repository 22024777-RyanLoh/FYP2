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
        <title>Project Details Editor</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="project.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" href="test2.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <style>
        
          .keyword-container {
            margin: 10px 0;
          }
          .keyword {
            color: orange;
            cursor: pointer;
            text-decoration: underline;
          }
          .copy-message {
            display: none;
            color: blue;
            margin-left: 10px;
          }
    </style>

</head>
<body>
    <section class = "header">
    <nav>
    <div class="nav-links" id="navLinks">
            <i class="fas fa-times" onclick="hidemenu()"></i>
            <ul>
                <?php if(isset($_SESSION['login_user'])): ?>
                    <li><a href="home.php"><img src="Domain_picture/transRP.png" alt="Logo"></a></li>
                    <li><span class="welcome-message" style="color: #FFFFFF;">Welcome, <?php echo $_SESSION['login_user']; ?></span></li>
                    <?php if($isAdmin): ?>  
                        <li><a href="Login222/users.php?do=Edit&user_id=<?php echo $_SESSION['login_user_id'] ?>">
                                <span>My Profile</span>
                        </a></li>
                        <li><a href="login222/dashboard.php">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="edit.php">Domain</a></li>
                    <li><a href="upload.php">Project</a></li>
                    <li><a href="logout.php">Sign out</a></li>
                <?php else: ?>
                    <li><a href="home.php"><img src="Domain_picture/transRP.png" alt="Logo"></a></li>
                    <li><a href="#" onclick="showLoginModal()">Log in</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <i class="fas fa-bars" onclick="showmenu()"></i>
    </nav>
    </section>

<?php

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
        <b><h3>Instruction:<br></h3>
        To split the text into six paragraphs, follow these steps:<br>
        1. Place the cursor at the end of the first paragraph.<br>
        2. Press "Enter" to start a new line.<br>
        3. Repeat this process at the end of each paragraph until the text is divided into six paragraphs.<br><br>

        <h3>Click on a keyword to copy it:</h3>

        <div class="keyword-container">
            <span class="keyword" onclick="copyToClipboard('Partner Organisation:', this)">Partner Organisation:</span>
            <span class="copy-message">Copied!</span>
        </div>

        <div class="keyword-container">
            <span class="keyword" onclick="copyToClipboard('Project Overview', this)">Project Overview:</span>
            <span class="copy-message">Copied!</span>
        </div>

        <div class="keyword-container">
            <span class="keyword" onclick="copyToClipboard('Team Members:', this)">Team Members:</span>
            <span class="copy-message">Copied!</span>
        </div>

        <div class="keyword-container">
            <span class="keyword" onclick="copyToClipboard('Supervisor:', this)">Supervisor:</span>
            <span class="copy-message">Copied!</span>
        </div></b>

    </div>

    <form action="process.php" method="post">
        <div class="m-3">
            <label for="inputText" class="form-label"></label>
            <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($pdfText)) {
                echo '<div style="display: flex;">';
                echo '<textarea class="form-control" id="inputText" name="inputText" rows="30" style="width: 50%">' . htmlspecialchars($pdfText) . '</textarea>';
                echo '<pre class="form-control" style="width: 50%" height: 800px >'.
                '<span>Example:</span><br>'.
                '(Paragraph 1)<br>'.
                '<b>Design and Development of 3D Assets for Istana Heritage Mobile Trail </b><br><br>'.
                '(Paragraph 2)<br>'.
                '<span style="color: orange">Partner Organisation:</span><b> LDR PteLtd & The Istana</b><br><br>'.
                '(Paragraph 3)<br>'.
                '<span style="color: orange">Project Overview</span><br><b>'.
                'The project allows visitors of The Istana to better understand the heritage and <br>'.
                'history of The Istana through LDR’s  LocoMolemobile app. <br>'.
                'The 3D assets, which are showcased via Augmented Reality (AR) from the <br>'.
                'mobile app, aimed  to create an interactive and engaging experience for users <br>on the Istana ground. </b><br><br>'.
                '(Paragraph 4)<br>'.
                '<span style="color: orange">Team Members:</span><br><b>Ryan Wong<br>Jasmine Seow<br>Ang Jia Yi</b><br><br>'.
                '(Paragraph 5)<br>'.
                '<span style="color: orange">Supervisor:</span><br><b>MsTan Hwee Yong</b><br><br>'.
                '(Paragraph 6)<br>'.
                '<b>The Istana Building<br>YusofIshakBust<br>The Project Team<br>Map of The Istana'.'</pre></b>';
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
    <button type="submit" class="btn btn-primary" style="margin: 10px;">Submit</button>
    </form>

<script>
    function copyToClipboard(text, element) {
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        
        const message = element.nextElementSibling;
        message.style.display = 'inline';
        
        setTimeout(() => {
          message.style.display = 'none';
        }, 2000);
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<div class="content">
    
    <footer class="footer">
        <div class="footer-content container">
            <div class="col-md-3">
                <h3><a href="https://www.rp.edu.sg/about-us" & target=_blank>About Us</a></h3>
                <ul>
                    <li><a href="https://www.rp.edu.sg/about-us/who-we-are" & target=_blank >Who We Are</a></li>
                    <li><a href="https://www.rp.edu.sg/about-us/our-people" & target=_blank>Our People</a></li>
                    <li><a href="https://www.rp.edu.sg/about-us/media" & target=_blank>Media</a></li>
                </ul>
            </div>

            <div class="footer-section social-media">
                <h3>Connect With Us</h3>
                <ul>
                    <li><a href="http://www.facebook.com/republicpolytechnic" target="_blank" class="footer-socialicon" aria-label="Facebook" data-sf-ec-immutable=""><em class="fa fa-facebook"></em></a></li>
                    <li><a href="https://sg.linkedin.com/school/republic-polytechnic/" target="_blank" class="footer-socialicon" aria-label="LinkedIn" data-sf-ec-immutable=""><em class="fa fa-linkedin"></em></a></li>
                    <li><a href="http://www.youtube.com/channelRP" target="_blank" class="footer-socialicon" aria-label="YouTube" data-sf-ec-immutable=""><em class="fa fa-youtube"></em></a></li>
                    <li><a href="http://www.instagram.com/republicpoly" target="_blank" class="footer-socialicon" aria-label="Instagram" data-sf-ec-immutable=""><em class="fa fa-instagram"></em></a></li>
                    <li><a href="http://twitter.com/republicpoly" target="_blank" class="footer-socialicon" aria-label="Twitter" data-sf-ec-immutable=""><svg style="margin-bottom:5px;width:22px;height:22px; vertical-align: middle;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" xml:space="preserve" enable-background="new 0 0 24 24"><path d="M14.095 10.316 22.286 1h-1.94L13.23 9.088 7.551 1H1l8.59 12.231L1 23h1.94l7.51-8.543L16.45 23H23l-8.905-12.684zm-2.658 3.022-.872-1.218L3.64 2.432h2.98l5.59 7.821.869 1.219 7.265 10.166h-2.982l-5.926-8.3z" fill="#ffffff" class="fill-000000"></path></svg></a></li>
                    <li><a href="https://www.tiktok.com/@republicpoly" target="_blank" aria-label="TikTok" data-sf-ec-immutable="">
                    <svg style="margin-bottom:-5px; width: 33px; height: 33px; vertical-align: middle;" viewBox="10 8 48 48" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="footer-socialicon"><title>Tiktok</title>
                    <g id="Icon/Social/tiktok-black" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><path d="M38.0766847,15.8542954 C36.0693906,15.7935177 34.2504839,14.8341149 32.8791434,13.5466056 C32.1316475,12.8317108 31.540171,11.9694126 31.1415066,11.0151329 C30.7426093,10.0603874 30.5453728,9.03391952 30.5619062,8 L24.9731521,8 L24.9731521,28.8295196 C24.9731521,32.3434487 22.8773693,34.4182737 20.2765028,34.4182737 C19.6505623,34.4320127 19.0283477,34.3209362 18.4461858,34.0908659 C17.8640239,33.8612612 17.3337909,33.5175528 16.8862248,33.0797671 C16.4386588,32.6422142 16.0833071,32.1196657 15.8404292,31.5426268 C15.5977841,30.9658208 15.4727358,30.3459348 15.4727358,29.7202272 C15.4727358,29.0940539 15.5977841,28.4746337 15.8404292,27.8978277 C16.0833071,27.3207888 16.4386588,26.7980074 16.8862248,26.3604545 C17.3337909,25.9229017 17.8640239,25.5791933 18.4461858,25.3491229 C19.0283477,25.1192854 19.6505623,25.0084418 20.2765028,25.0219479 C20.7939283,25.0263724 21.3069293,25.1167239 21.794781,25.2902081 L21.794781,19.5985278 C21.2957518,19.4900128 20.7869423,19.436221 20.2765028,19.4380839 C18.2431278,19.4392483 16.2560928,20.0426009 14.5659604,21.1729264 C12.875828,22.303019 11.5587449,23.9090873 10.7814424,25.7878401 C10.003907,27.666593 9.80084889,29.7339663 10.1981162,31.7275214 C10.5953834,33.7217752 11.5748126,35.5530237 13.0129853,36.9904978 C14.4509252,38.4277391 16.2828722,39.4064696 18.277126,39.8028054 C20.2711469,40.1991413 22.3382874,39.9951517 24.2163416,39.2169177 C26.0948616,38.4384508 27.7002312,37.1209021 28.8296253,35.4300711 C29.9592522,33.7397058 30.5619062,31.7522051 30.5619062,29.7188301 L30.5619062,18.8324027 C32.7275484,20.3418321 35.3149087,21.0404263 38.0766847,21.0867664 L38.0766847,15.8542954 Z" id="Fill-1" fill="#FFFFFF"></path></g>
                    </svg>
                    </a></li>

                </ul>
            </div>
        </div>
        <div class="bottom">
        <nav2>
        <div class="nav2-links" id="navLinks">
            <div class="container">
            <ul>
                <li><a href="home.php">Home</a></li>
                <li class="separator">|</li>
                <li><a href="https://www.rp.edu.sg/service-excellence/contact-us" & target=_blank>Contact</a></li>
            </ul>
            </div>
            <p style="padding: 0;">9 Woodlands Avenue 9, Singapore 738964 <br> Copyright © Republic Polytechnic. All Rights Reserved.</p>
            
        </div>
    </nav2>
    </footer>

</div>
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