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
    <link rel="stylesheet" href="project.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <style>
        .container1{
            margin:10px;
            margin-left:250px;
            margin-right:250px;
        }


        .form-inline {
            display: inline-block;
            margin-right: 15px;
        }

        .form-inline label{
            justify-content: left;
        }
        
        .form-group {
            display: flex;
            flex-wrap: wrap;
        }

        .form-group .form-inline {
            flex: 1;
            min-width: 200px;  /* Adjust this value as needed */
        }

        .image-container {
            flex: 0 0 30%;
            max-width: 30%;
            padding-right: 15px; /* Space between image and description */
        }

        .description-container {
            flex: 1; /* Take up the remaining 70% */
        }

        #backToTopBtn {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 30px;
            z-index: 99;
            border: none;
            outline: none;
            background-color: #555;
            color: white;
            cursor: pointer;
            padding: 15px;
            border-radius: 10px;
            font-size: 18px;
        }

        #backToTopBtn:hover {
            background-color: #000;
        }
        .swal2-confirm {
        background-color: #8CD4F5 !important; /* Original blue color */
        }
        </style>


    </style>
    </head>
<body>
<section class="header">
    <nav>
        <div class="nav-links" id="navLinks">
            <i class="fa fa-times hide-icon" onclick="hidemenu()"></i>
            <ul>
                <?php if(isset($_SESSION['login_user'])): ?>
                    <li><a href="home.php"><img src="Domain_picture/transRP.png" alt="Logo"></a></li>
                    <li><a href="#" onclick="swal.fire('Error', 'Unable to edit your profile. Submit or save your work before proceeding.', 'error'); return false;"><span class="welcome-message" style="color: #FFFFFF;">Welcome, <?php echo $_SESSION['login_user']; ?></span></a></li>
                    <?php if($isAdmin): ?>
                        <li><a href="login222/dashboard.php"<?php echo $_SESSION['login_user_id'] ?>">
                                <span style="color: #fff;">Dashboard</span>
                        </a></li>
                        <li><a href="login222/users.php">Admin Panel</a></li>
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
        <i class="fa fa-bars show-icon" onclick="showmenu()"></i>
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
    <form action="upload3.php" method="post" style="min-height:100vh;">
        <div class='container1'>
            <h2>Review the Information</h2>

            <div class="form-group">
                <label for="project-title" class="col-form-label">Title:</label>
                <textarea name="Etitle" class="form-control" required><?php echo htmlspecialchars($title); ?></textarea>
            </div>
    
            <div class="form-group">
                <label for="project-title" class="col-form-label">Organisation:</label>
                <textarea name="Eorganisation" class="form-control" rows='4'><?php echo htmlspecialchars($organisation); ?></textarea>
            </div>

            <div class="form-group">
                <label for="project-title" class="col-form-label">Project Overview:</label>
                <textarea name="Eoverview" class="form-control" rows='15' required><?php echo htmlspecialchars($overview); ?></textarea>
            </div>

            <div class="form-group">
                <label for="project-title" class="col-form-label">Members:</label>
                <textarea name="Emembers" class="form-control" rows='4' required><?php echo htmlspecialchars($members); ?></textarea>
            </div>

            <div class="form-group">
                <label for="project-title" class="col-form-label">Supervisor:</label>
                <textarea name="Esupervisor" class="form-control" rows='4' required><?php echo htmlspecialchars($supervisor); ?></textarea>
            </div>

            <div class="form-group">
                <label for="project-title" class="col-form-label">Image Description:</label>
                <textarea name="Eimage_description" class="form-control" rows='10'><?php echo htmlspecialchars($image_description); ?></textarea>
            </div>

            <div class="form-group">

                <div class="form-inline">
                    <label for="domain" class="col-form-label" required>Domain:</label>
                    <select name="domain" id="domain" class="form-control">
                    <?php
                        // Fetch domains again for the modal dropdown
                        $domains_result;
                        while ($domain_row = $domains_result->fetch_assoc()) {
                            echo "<option value='{$domain_row['domain_id']}'>{$domain_row['domain_name']}</option>";
                        }
                    ?>
                    </select>
                </div>

                <div class="form-inline">
                    <label for="year" class="col-form-label" required>Year:</label>
                    <select name="year" id="year" class="form-control">
                        <option value="2021">2021</option>
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                    </select>
                </div>

                <div class="form-inline">
                    <label for="semester" class="col-form-label" required>Semester:</label>
                    <select name="semester" id="semester" class="form-control">
                        <option value="Sem 1">Sem 1</option>
                        <option value="Sem 2">Sem 2</option>
                    </select>
                </div>               
            </div>
            <br>
            <h2>Selected Images</h2>
            <?php $index = 1;
            foreach ($selectedImages as $project_image):?>
            <div class="image-description-group">

                <div class="form-group">

                    <div class="image-container">
                        <label>Image <?php echo $index; ?></label>
                        <input type="checkbox" name="selected_images[]" value="<?php echo $project_image ?>" checked><br>
                        <?php echo '<img src="data:image/jpeg;base64,'.htmlspecialchars($project_image).'" style="max-width: 100%; max-height: 300px;"/>'; ?>
                    </div>

                    <div class="description-container">
                        <label class="col-form-label">Image Description:</label>
                        <textarea name="image_description_<?php echo $index; ?>" class="form-control" rows="7"></textarea>
                    </div>

                </div>
                <br>

            </div>
            <?php 
            $index++; // Increment the index variable
            endforeach; ?>

            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<div class="content">
        <footer class="footer">
            <div class="footer-content container">
                <div class="col-md-3">
                    <h3><a href="https://www.rp.edu.sg/about-us" target="_blank">About Us</a></h3>
                    <ul>
                        <li><a href="https://www.rp.edu.sg/about-us/who-we-are" target="_blank">Who We Are</a></li>
                        <li><a href="https://www.rp.edu.sg/about-us/our-people" target="_blank">Our People</a></li>
                        <li><a href="https://www.rp.edu.sg/about-us/media" target="_blank">Media</a></li>
                    </ul>
                </div>

                <div class="footer-section social-media">
                    <h3>Connect With Us</h3>
                    <ul>
                        <li><a href="http://www.facebook.com/republicpolytechnic" target="_blank" aria-label="Facebook"><em class="fa fa-facebook" style="font-size: 22px;"></em></a></li>
                        <li><a href="https://sg.linkedin.com/school/republic-polytechnic/" target="_blank" aria-label="LinkedIn"><em class="fa fa-linkedin" style="font-size: 22px;"></em></a></li>
                        <li><a href="http://www.youtube.com/channelRP" target="_blank" aria-label="YouTube"><em class="fa fa-youtube" style="font-size: 22px;"></em></a></li>
                        <li><a href="http://www.instagram.com/republicpoly" target="_blank" aria-label="Instagram"><em class="fa fa-instagram" style="font-size: 22px;"></em></a></li>
                        <li><a href="http://twitter.com/republicpoly" target="_blank" aria-label="Twitter">
                            <svg style="margin-bottom:5px;width:22px;height:22px; vertical-align: middle;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" xml:space="preserve" enable-background="new 0 0 24 24"><path d="M14.095 10.316 22.286 1h-1.94L13.23 9.088 7.551 1H1l8.59 12.231L1 23h1.94l7.51-8.543L16.45 23H23l-8.905-12.684zm-2.658 3.022-.872-1.218L3.64 2.432h2.98l5.59 7.821.869 1.219 7.265 10.166h-2.982l-5.926-8.3z" fill="#ffffff" class="fill-000000"></path></svg>
                        </a></li>
                        <li><a href="https://www.tiktok.com/@republicpoly" target="_blank" aria-label="TikTok">
                            <svg style="width: 33px; height: 33px; vertical-align: middle;" viewBox="10 8 48 48" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="footer-socialicon"><title>Tiktok</title>
                            <g id="Icon/Social/tiktok-black" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><path d="M38.0766847,15.8542954 C36.0693906,15.7935177 34.2504839,14.8341149 32.8791434,13.5466056 C32.1316475,12.8317108 31.540171,11.9694126 31.1415066,11.0151329 C30.7426093,10.0603874 30.5453728,9.03391952 30.5619062,8 L24.9731521,8 L24.9731521,28.8295196 C24.9731521,32.3434487 22.8773693,34.4182737 20.2765028,34.4182737 C19.6505623,34.4320127 19.0283477,34.3209362 18.4461858,34.0908659 C17.8640239,33.8612612 17.3337909,33.5175528 16.8862248,33.0797671 C16.4386588,32.6422142 16.0833071,32.1196657 15.8404292,31.5426268 C15.5977841,30.9658208 15.4727358,30.3459348 15.4727358,29.7202272 C15.4727358,29.0940539 15.5977841,28.4746337 15.8404292,27.8978277 C16.0833071,27.3207888 16.4386588,26.7980074 16.8862248,26.3604545 C17.3337909,25.9229017 17.8640239,25.5791933 18.4461858,25.3491229 C19.0283477,25.1192854 19.6505623,25.0084418 20.2765028,25.0219479 C20.7939283,25.0263724 21.3069293,25.1167239 21.794781,25.2902081 L21.794781,19.5985278 C21.2957518,19.4900128 20.7869423,19.436221 20.2765028,19.4380839 C18.2431278,19.4392483 16.2560928,20.0426009 14.5659604,21.1729264 C12.875828,22.303019 11.5587449,23.9090873 10.7814424,25.7878401 C10.003907,27.666593 9.80084889,29.7339663 10.1981162,31.7275214 C10.5953834,33.7217752 11.5748126,35.5530237 13.0129853,36.9904978 C14.4509252,38.4277391 16.2828722,39.4064696 18.277126,39.8028054 C20.2711469,40.1991413 22.3382874,39.9951517 24.2163416,39.2169177 C26.0948616,38.4384508 27.7002312,37.1209021 28.8296253,35.4300711 C29.9592522,33.7397058 30.5619062,31.7522051 30.5619062,29.7188301 L30.5619062,18.8324027 C32.7275484,20.3418321 35.3149087,21.0404263 38.0766847,21.0867664 L38.0766847,15.8542954 Z" id="Fill-1" fill="#FFFFFF"></path></g>
                            </svg>
                        </a></li>
                    </ul>
                </div>
            </div>
            <div class="bottom">
                <div class="nav2-links" id="navLinks">
                    <div class="container">
                        <ul>
                            <li><a href="home.php">Home</a></li>
                            <li class="separator">|</li>
                            <li><a href="https://www.rp.edu.sg/service-excellence/contact-us" target="_blank">Contact</a></li>
                        </ul>
                    </div>
                    <div class="test" style="font-size: 14px"> Woodlands Avenue 9, Singapore 738964 <br>
                    Copyright © Republic Polytechnic. All Rights Reserved.</div>
            </div>
        </footer>
    </div>

    <script>
        var navLinks = document.getElementById("navLinks");

        function showmenu() {   
            navLinks.style.top = "0";
        }

        function hidemenu() {
            navLinks.style.top = "-100vh";
        }

        function handleScroll() {
            const showIcon = document.querySelector('.show-icon');
            if (window.innerWidth <= 768) {
                if (window.scrollY > 0) {
                    showIcon.style.visibility = 'hidden';
                } else {
                    showIcon.style.visibility = 'visible';
                }
            } else {
                showIcon.style.visibility = 'hidden'; // Ensure icon is visible when not in @media range
            }
        }

        // Event listener for scroll
        window.addEventListener('scroll', handleScroll);

        // Event listener for resize to handle screen size changes
        window.addEventListener('resize', handleScroll);

        // Initial check
        handleScroll();
    </script>

    <!-- Back to Top Button -->
    <button id="backToTopBtn" title="Back to Top">
        <i class="fa fa-arrow-up"></i>
    </button>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Show or hide the button based on scroll position
        $(window).scroll(function() {
            if ($(this).scrollTop() > 100) {
                $('#backToTopBtn').fadeIn();
            } else {
                $('#backToTopBtn').fadeOut();
            }
        });

        // Smooth scroll to top
        $('#backToTopBtn').click(function() {
            $('html, body').animate({scrollTop: 0}, 400);
            return false;
        });

    });
</script>
</body>
</html>