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

// Function to fetch domain id from the database
function fetchDomainname($conn) {
    $sql = "SELECT * FROM domains";
    $result = mysqli_query($conn, $sql);
    $domains = array();
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $domains[] = $row['domain_name'];
        }
    }
    return $domains;

}


// Fetch domain names from the database
$domains = fetchDomainname($conn);


// Function to fetch domain information
function fetchDomainInfo($conn, $domainName) {
    $sql = "SELECT domain_image, domain_description FROM domains WHERE domain_name = '$domainName' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $domainInfo = array();
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $domainInfo['image'] = "Domain_picture/" . $row["domain_image"];
        $domainInfo['description'] = $row["domain_description"];
    } else {
        $domainInfo['image'] = "";
        $domainInfo['description'] = "No description available for $domainName.";
    }
    return $domainInfo;
}

// Fetch domain information for each category
$backgroundImages = array();
$descriptions = array();

foreach ($domains as $key => $domain) {
    $domainInfo = fetchDomainInfo($conn, $domain);
    $backgroundImages[$key] = $domainInfo['image'];
    $descriptions[$key] = $domainInfo['description'];
}

mysqli_close($conn);

// File to store click counts
$clickCountsFile = 'click_counts.json';

// Read the current click counts from the file
$clickCounts = [];
if (file_exists($clickCountsFile)) {
    $clickCounts = json_decode(file_get_contents($clickCountsFile), true);
} else {
    // Initialize click counts if the file doesn't exist
    foreach ($domains as $domain) {
        $clickCounts[$domain] = 0;
    }
    file_put_contents($clickCountsFile, json_encode($clickCounts));
}

// Initialize session click counts if not set
if (!isset($_SESSION['click_counts'])) {
    $_SESSION['click_counts'] = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Of Infocomm</title>
    <link rel="stylesheet" href="home.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
</head>
<body>
    <section class="header">
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
            <h1>Welcome to SOI Projects</h1>
            <p>Scroll down to see your Projects</p>
        </div>
    </section>

    <?php if ($isAdmin): ?>
    <!-- Add a filter button -->
    <button class="filter-btn" onclick="toggleFilterBox()">Filter</button>

    <!-- Add a filter box -->
    <div class="filter-box" id="filterBox">
      <div class="filter-header">
        <span>Filter Projects</span>
        <i class="fas fa-times" onclick="toggleFilterBox()"></i>
      </div>
      <div class="filter-content">
        <div class="filter-items">
          <label><input type="checkbox" value="Application Development"> Application Development</label>
          <label><input type="checkbox" value="Artificial Intelligence"> Artificial Intelligence</label>
          <label><input type="checkbox" value="Data Analytics"> Data Analytics</label>
          <label><input type="checkbox" value="Fintech"> Fintech</label>
          <label><input type="checkbox" value="Infocomm Security"> Infocomm Security</label>
          <label><input type="checkbox" value="Internet of Things"> Internet of Things</label>
          <label><input type="checkbox" value="Network & Systems"> Network & Systems</label>
          <label><input type="checkbox" value="Specialist Diploma"> Specialist Diploma</label>
          <label><input type="checkbox" value="Staff Project"> Staff Project</label>
        </div>
        <div class="filter-actions">
          <button onclick="applyFilter()">Apply Filter</button>
          <button onclick="clearFilter()">Clear Filter</button>
        </div>
      </div>
    </div>
<?php endif; ?>

    <section class="cards">
        <?php foreach ($domains as $key => $domain) : ?>
            <div class="card card<?php echo $key + 1; ?>" style="background-image: linear-gradient(rgba(4,9,30,0.5), rgba(4,9,30,0.5)),url('<?php echo isset($backgroundImages[$key]) ? $backgroundImages[$key] : ''; ?>');">
                <div class="card-text">
                    <h2><?php echo $domain; ?></h2>
                    <p><?php echo isset($descriptions[$key]) ? $descriptions[$key] : ''; ?></p>
                    <!-- Display click count dynamically -->
                    <?php if ($isAdmin): ?>
                        <p id="clickCount_<?php echo $domain; ?>">Click Count: <?php echo isset($clickCounts[$domain]) ? $clickCounts[$domain] : 0; ?></p>
                    <?php endif; ?>
                    <a href="domain_page.php?domain=<?php echo urlencode($domain); ?>" class="learn-more-btn" onclick="updateClickCount('<?php echo $domain; ?>')">Learn More</a>
                </div>
            </div>
        <?php endforeach; ?>
    </section>

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
        
        function toggleFilterBox() {
            var filterBox = document.getElementById("filterBox");
            if (filterBox.style.display === "block") {
                filterBox.style.display = "none";
            } else {
                filterBox.style.display = "block";
            }
        }

        function applyFilter() {
            var checkboxes = document.querySelectorAll(".filter-items input[type='checkbox']");
            var selectedFilters = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value.toLowerCase());
            var cards = document.querySelectorAll(".card");
            cards.forEach(card => {
                var cardTitle = card.querySelector("h2").textContent.toLowerCase();

                var match = false;
                selectedFilters.forEach(filter => {
                    if (cardTitle.includes(filter)) {
                        match = true;
                    }
                });

                if (selectedFilters.length === 0 || match) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });
            document.getElementById("filterBox").style.display = "none";
        }

        function clearFilter() {
            var checkboxes = document.querySelectorAll(".filter-items input[type='checkbox']");
            checkboxes.forEach(cb => cb.checked = false);
            var cards = document.querySelectorAll(".card");
            cards.forEach(card => card.style.display = "block");
            document.getElementById("filterBox").style.display = "none";
        }

        function updateClickCount(domain) {
    // Check if the user is not an admin before counting clicks
    if (!<?php echo json_encode($isAdmin); ?>) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "update_click_count.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Update the click count dynamically
                    var clickCountElement = document.getElementById("clickCount_" + domain);
                    if (clickCountElement) {
                        clickCountElement.innerHTML = "Click Count: " + xhr.responseText;
                    }
                } else {
                    console.error("Error updating click count");
                }
            }
        };
        xhr.send("domain=" + domain);
    }
}
    </script>
</body>
</html>
