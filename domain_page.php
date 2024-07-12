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

// Fetch domain name from URL parameter
$domain = isset($_GET['domain']) ? urldecode($_GET['domain']) : '';
$year = isset($_GET['year']) ? $_GET['year'] : ''; // Year parameter from URL
$semester = isset($_GET['semester']) ? $_GET['semester'] : ''; // Semester parameter from URL

// Fetch domain information based on the domain name
function fetchDomainInfo($conn, $domain) {
    $sql = "SELECT domain_image FROM domains WHERE domain_name = '$domain' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $domainInfo = array();
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $domainInfo['image'] = "Domain_picture/" . $row["domain_image"]; // Adjust this path as per your actual directory structure
    } else {
        $domainInfo['image'] = "default_image.jpg"; // Provide a default image path or handle case when no image is found
    }
    return $domainInfo;
}

function fetchProjectDetails($conn, $domain) {
    $sql = "SELECT DISTINCT p.Project_ID, p.Project_title, p.Project_year, p.Project_semester, 
                   p.Project_body, p.Organisation, p.Members, p.Supervisor,
                   pi.Image_ID, pi.Project_image, pi.Image_description 
            FROM project p 
            LEFT JOIN domains d ON p.domain_id = d.domain_id
            LEFT JOIN project_image pi ON p.Project_ID = pi.Project_ID 
            WHERE d.domain_name = '$domain'";
    $result = mysqli_query($conn, $sql);
    $projects = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $projectID = $row['Project_ID'];
            if (!isset($projects[$projectID])) {
                // Initialize project array if not exists
                $projects[$projectID] = array(
                    'Project_ID' => $row['Project_ID'],
                    'Project_title' => $row['Project_title'],
                    'Project_year' => $row['Project_year'],
                    'Project_semester' => $row['Project_semester'],
                    'Project_body' => $row['Project_body'],
                    'Organisation' => $row['Organisation'],
                    'Members' => $row['Members'],
                    'Supervisor' => $row['Supervisor'],
                    'images' => array()
                );
            }
            // Add image details to the project's images array
            if (!empty($row['Image_ID'])) {
                $projects[$projectID]['images'][] = array(
                    'Image_ID' => $row['Image_ID'],
                    'Project_image' => $row['Project_image'],
                    'Image_description' => $row['Image_description']
                );
            }
        }
    }
    return $projects;
}

// Fetch domain information
$domainInfo = fetchDomainInfo($conn, $domain);

// Fetch project details for the domain
$projects = fetchProjectDetails($conn, $domain);

// Sort the array keys to ensure correct button order
$years = array();
foreach ($projects as $project) {
    $years[$project['Project_year']][] = $project;
}
ksort($years);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $domain; ?></title>
    <link rel="stylesheet" href="domain_page.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .header {
            min-height: 40vh;
            width: 100%;
            background-image: linear-gradient(240deg, rgb(241, 70, 2), rgba(255, 197, 142, 0.7)), url('<?php echo $domainInfo['image']; ?>');
            background-position: center;
            background-size: cover;
            position: relative;
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
                    <?php if (isset($_SESSION['login_user'])): ?>
                        <li><span class="welcome-message">Welcome, <?php echo $_SESSION['login_user']; ?></span></li>
                        <?php if ($_SESSION['user_role'] === 'Admin'): ?>
                            <li><a href="login222/dashboard.php">Admin Panel</a></li>
                        <?php endif; ?>
                        <li><a href="edit.php">Edit</a></li>
                        <li><a href="upload.php">Upload</a></li>
                        <li><a href="logout.php">Sign out</a></li>
                    <?php else: ?>
                        <li><a href="Login222/index.php">Log in</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <i class="fas fa-bars" onclick="showmenu()"></i>
        </nav>

    <div class="text-box">
        <h1><?php echo $domain; ?></h1>
    </div>
</section>

<div class="year-buttons">
    <?php foreach ($years as $year => $yearProjects): ?>
        <button onclick="showYearSection('<?php echo 'year' . $year; ?>')" class="<?php echo $year == 2020 ? 'active' : ''; ?>"><?php echo $year; ?></button>
    <?php endforeach; ?>
</div>

<?php foreach ($years as $year => $yearProjects): ?>
    <div class="year-section <?php echo $year == array_key_first($years) ? 'active' : ''; ?>" id="<?php echo 'year' . $year; ?>">
        <?php foreach ($yearProjects as $project): ?>
            <div class="year-semester">
                <h3><?php echo $year; ?></h3>
                <h4><?php echo $project['Project_semester']; ?></h4>
            </div>
            <div class="content-section">
                <div class="card-container">
                    <div class="card" data-title="<?php echo htmlspecialchars($project['Project_title']); ?>" 
                        data-description="<?php echo htmlspecialchars($project['Project_body']); ?>"
                        data-organisation="<?php echo htmlspecialchars($project['Organisation']); ?>"
                        data-members="<?php echo htmlspecialchars($project['Members']); ?>"
                        data-supervisor="<?php echo htmlspecialchars($project['Supervisor']); ?>"
                        data-project-id="<?php echo htmlspecialchars($project['Project_ID']); ?>">
                        <div class="description">
                            <h2><?php echo $project['Project_title']; ?></h2>
                            <?php
                            // Extract first sentence from Project_body
                            $description = $project['Project_body'];
                            $firstSentence = strtok($description, '.'); // Get first sentence
                            ?>
                            <p class="short-description"><?php echo htmlspecialchars($firstSentence). '...'; ?></p>
                            <p class="full-description" style="display: none;"><?php echo htmlspecialchars($project['Project_body']); ?></p>
                            <p><b>Organisation</b>: <?php echo $project['Organisation']; ?></p>
                            <p><b>Members</b>: <?php echo $project['Members']; ?></p>
                            <p><b>Supervisor</b>: <?php echo $project['Supervisor']; ?></p>
                            <button class="learn-more-btn">Learn More</button>
                        </div>
                        <div class="image-wrapper">
                            <?php foreach ($project['images'] as $image): ?>
                                <img src="<?php echo 'data:image/jpeg;base64,' . ($image['Project_image']); ?>">
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

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

<div class="modal-container" id="modalContainer">
    <div class="modal-content" id="modalContent">
        <span class="close" onclick="closeModal()">&times;</span>
        <div id="modalImageContainer"></div>
        <h2 id="modalTitle"></h2>
        <p id="modalDescription"></p>
        <br><p id="modalOrganisation"></p>
        <br><p id="modalMembers"></p>
        <br><p id="modalSupervisor"></p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var buttons = document.querySelectorAll('.learn-more-btn');
    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            var card = button.closest('.card');
            var title = card.getAttribute('data-title');
            var description = card.getAttribute('data-description');
            var organisation = card.getAttribute('data-organisation');
            var members = card.getAttribute('data-members');
            var supervisor = card.getAttribute('data-supervisor');
            var projectID = card.getAttribute('data-project-id');
            openModal(title, description, projectID, organisation, members, supervisor);
        });
    });
});

function openModal(title, description, projectID, organisation, members, supervisor) {
    console.log('openModal called with:', title, description, projectID, organisation, members, supervisor);

    var modalContainer = document.getElementById('modalContainer');
    var modalImageContainer = document.getElementById('modalImageContainer');
    var modalTitle = document.getElementById('modalTitle');
    var modalDescription = document.getElementById('modalDescription');
    var modalOrganisation = document.getElementById('modalOrganisation');
    var modalMembers = document.getElementById('modalMembers');
    var modalSupervisor = document.getElementById('modalSupervisor');

    // Set the modal data
    modalTitle.innerText = title;
    modalDescription.innerText = description;
    modalOrganisation.innerHTML = "<b>Organisation:</b> " + organisation;
    modalMembers.innerHTML = "<b>Members:</b> " + members.split(',').join('<br>');
    modalSupervisor.innerHTML = "<b>Supervisor:</b> " + supervisor;

    // Clear previous images
    modalImageContainer.innerHTML = '';

    // AJAX setup
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'fetch_project_images.php', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                var images = JSON.parse(xhr.responseText);
                if (images.length > 0) {
                    // Create image grid
                    var imageGrid = document.createElement('div');
                    imageGrid.classList.add('image-grid');

                    // Append images to grid
                    images.forEach(function (image, index) {
                        var imgWrapper = document.createElement('div');
                        var img = document.createElement('img');
                        img.src = 'data:image/jpeg;base64,' + image.Project_image;
                        img.alt = 'Image ' + (index + 1);
                        imgWrapper.appendChild(img);

                        // Add image description
                        var imgDescription = document.createElement('p');
                        imgDescription.textContent = image.Image_description;
                        imgWrapper.appendChild(imgDescription);

                        imageGrid.appendChild(imgWrapper);
                    });

                    // Handle centering of the third image in the second row if odd number of images
                    if (images.length % 2 !== 0 && images.length > 1) {
                        var thirdImage = imageGrid.children[images.length - 1];
                        thirdImage.style.margin = 'auto';
                    }

                    // Append image grid to modal
                    modalImageContainer.appendChild(imageGrid);
                } else {
                    // No images message
                    var noImageMessage = document.createElement('p');
                    noImageMessage.textContent = 'No images available.';
                    modalImageContainer.appendChild(noImageMessage);
                }
            } else {
                // Error handling
                var errorMessage = document.createElement('p');
                errorMessage.textContent = 'Failed to fetch images.';
                modalImageContainer.appendChild(errorMessage);
            }
        }
    };
    xhr.send(JSON.stringify({ projectID: projectID }));

    // Show the modal
    modalContainer.style.display = 'block';
}

function closeModal() {
    var modalContainer = document.getElementById('modalContainer');
    modalContainer.style.display = 'none';
}

function showYearSection(yearId) {
    var yearSections = document.querySelectorAll('.year-section');
    yearSections.forEach(function (section) {
        section.classList.remove('active');
    });

    var buttons = document.querySelectorAll('.year-buttons button');
    buttons.forEach(function (button) {
        button.classList.remove('active');
    });

    document.getElementById(yearId).classList.add('active');
    event.target.classList.add('active');
}
</script>
</body>
</html>