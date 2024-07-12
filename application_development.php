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

// Fetch background image path from the "Application Development" category
$domain = "Application Development";
$sql = "SELECT domain_image FROM domains WHERE domain_name = '$domain' LIMIT 1";
$result = mysqli_query($conn, $sql);
$backgroundImagePath = "";
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $backgroundImagePath = "Domain_picture/" . $row["domain_image"];
} else {
    // Handle case when no image is found
    echo "No image found for Application Development.";
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Development</title>
    <link rel="stylesheet" href="domain_page.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
    <style>
        .header {
            min-height: 40vh;
            width: 100%;
            background-image: linear-gradient(240deg, rgb(241, 70, 2), rgba(255, 197, 142, 0.7)), url('<?php echo $backgroundImagePath; ?>');
            background-position: center;
            background-size: cover;
            position: relative;
        }

        .year-buttons {
            display: flex;
            justify-content: center;
            margin: 20px;
        }

        .year-buttons button {
            margin: 0 10px;
            padding: 10px 20px;
            background-color: #ff7300;
            color: #fff;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .year-buttons button.active {
            background-color: #e66000;
        }

        .year-section {
            display: none;
        }

        .year-section.active {
            display: block;
        }
        
    </style>
</head>
<body>
<section class="header">
    <nav>
        <a href="home.php"><img src="Domain_picture/logo1.png" alt="Logo"></a>
        <div class="nav-links" id="navLinks">
            <i class="fas fa-times" onclick="hidemenu()"></i>
            <ul>
                <li><a href="home.php">Home</a></li>
                <?php if ($isAdmin): ?>
                    <li><a href="edit.php">Edit</a></li>
                    <li><a href="upload.php">Upload</a></li>
                    <li><a href="logout.php">Sign out</a></li>
                <?php else: ?>
                    <li><a href="login.php">Log in</a></li>
                <?php endif; ?>
            </ul>
        </div>
        <i class="fas fa-bars" onclick="showmenu()"></i>
    </nav>

    <div class="text-box">
        <h1>Application Development</h1>
    </div>
</section>

<div class="year-buttons">
    <button onclick="showYearSection('year2020')" class="active">2020</button>
    <button onclick="showYearSection('year2021')">2021</button>
</div>

<div class="year-section active" id="year2020">
    <div class="year-sem">
        <h3>2020</h3>
        <h4>Sem 1</h4>
    </div>
    <div class="content-section">
        <div class="card-container">
            <div class="card" data-title="Course Promotion 2D Infographic Animation" data-image="Domain_picture/test4.png" 
            data-description="<p>The project allows visitors of The Istana to better understand the heritage and 
                history of The Istana through LDR’s LocoMole mobile app.</p><br>
                <p>The 3D assets, which are showcased via Augmented Reality (AR) from the 
                mobile app, aimed to create an interactive and engaging experience for users 
                on the Istana ground.</p><br>
                <p>Key technologies:<br>
                - 3Ds Max<br>
                - Adobe Illustrator<br>
                - Adobe Photoshop<br>
                <br><p>Team Members: Ryan Wong, Jasmine Seow, Ang Jia Yi</p><br>
                <p>Supervisor: Ms Tan Hwee Yong </p><br>">
                <div class="image-wrapper">
                    <img src="Domain_picture/test4.png">
                </div>
                <div class="description">
                    <h2>Design and Development of 3D Assets for Istana 
                    Heritage Mobile Trail for Community Engagement</h2>
                    <p>The project allows visitors of The Istana to better understand the heritage and 
                    history of The Istana through LDR’s LocoMole mobile app</p>
                    <p><b>Team Members</b>:<br>Ryan Wong, Jasmine Seow, Ang Jia Yi</p>
                    <p><b>Supervisor</b>:<br>Ms Tan Hwee Yong </p>
                    <button class="learn-more-btn" onclick="openModal()">Learn More</button>
                </div>
            </div>
        </div>
    </div>

    <div class="year-sem">
        <h4>Sem 2</h4>
    </div>
    <div class="content-section">
        <div class="card-container">
            <div class="card" data-title="Course Promotion 2D Infographic Animation" 
            data-image="Domain_picture/test5.png" data-description="<p>The project allows visitors of The Istana to better understand the heritage and 
                history of The Istana through LDR’s LocoMole mobile app.</p><br>
                <p>The 3D assets, which are showcased via Augmented Reality (AR) from the 
                mobile app, aimed to create an interactive and engaging experience for users 
                on the Istana ground.</p><br>
                <p>Key technologies:<br>
                - 3Ds Max<br>
                - Adobe Illustrator<br>
                - Adobe Photoshop<br>
                <br><p>Team Members: Ryan Wong, Jasmine Seow, Ang Jia Yi</p><br>
                <p>Supervisor: Ms Tan Hwee Yong </p><br>">
                <div class="image-wrapper">
                    <img src="Domain_picture/test5.png">
                </div>
                <div class="description">
                    <h2>3D Virtual Laboratory</h2>
                    <p>The School of Applied Science (SAS) 3D virtual laboratory will</p>
                    <p><b>Team Members</b>:<br>Ryan Wong, Jasmine Seow, Ang Jia Yi</p>
                    <p><b>Supervisor</b>:<br>Ms Tan Hwee Yong </p>
                    <button class="learn-more-btn" onclick="openModal()">Learn More</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="year-section" id="year2021">
    <div class="year-sem">
        <h3>2021</h3>
        <h4>Sem 1</h4>
    </div>
    <div class="content-section">
        <div class="card-container">
            <div class="card" data-title="2021 Project 1" data-image="Domain_picture/test6.png" 
            data-description="<p>Description of the project goes here.</p>">
                <div class="image-wrapper">
                    <img src="Domain_picture/test6.png">
                </div>
                <div class="description">
                    <h2>2021 Project 1</h2>
                    <p>Brief description of the project.</p>
                    <button class="learn-more-btn" onclick="openModal()">Learn More</button>
                </div>
            </div>
        </div>
    </div>

    <div class="year-sem">
        <h4>Sem 2</h4>
    </div>
    <div class="content-section">
        <div class="card-container">
            <div class="card" data-title="2021 Project 2" 
            data-image="Domain_picture/test7.png" data-description="<p>Description of the project goes here.</p>">
                <div class="image-wrapper">
                    <img src="Domain_picture/test7.png">
                </div>
                <div class="description">
                    <h2>2021 Project 2</h2>
                    <p>Brief description of the project.</p>
                    <button class="learn-more-btn" onclick="openModal()">Learn More</button>
                </div>
            </div>
        </div>
    </div>
</div>

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

<div class="modal-container" id="modalContainer">
    <div class="modal-content" id="modalContent">
        <span class="close" onclick="closeModal()">&times;</span>
        <img id="modalImage" src="" alt="Modal Image">
        <h2 id="modalTitle"></h2>
        <div id="modalDescription"></div>
    </div>
</div>

<script>
function openModal() {
    var modalContainer = document.getElementById('modalContainer');
    var modalImage = document.getElementById('modalImage');
    var modalTitle = document.getElementById('modalTitle');
    var modalDescription = document.getElementById('modalDescription');

    // Get the card data
    var card = event.target.closest('.card');
    var title = card.getAttribute('data-title');
    var image = card.getAttribute('data-image');
    var description = card.getAttribute('data-description');

    // Set the modal data
    modalImage.src = image;
    modalTitle.innerText = title;
    modalDescription.innerHTML = description;

    // Show the modal
    modalContainer.style.display = 'block';
}

function closeModal() {
    var modalContainer = document.getElementById('modalContainer');
    modalContainer.style.display = 'none';
}

function showYearSection(yearId) {
    var yearSections = document.querySelectorAll('.year-section');
    yearSections.forEach(function(section) {
        section.classList.remove('active');
    });

    var buttons = document.querySelectorAll('.year-buttons button');
    buttons.forEach(function(button) {
        button.classList.remove('active');
    });

    document.getElementById(yearId).classList.add('active');
    event.target.classList.add('active');
}
</script>

<script src="view.js"></script>
</body>
</html>