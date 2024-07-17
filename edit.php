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

// Define default values for page and limit
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 5;
$start = ($page - 1) * $limit;

// Fetch data with pagination
$sql = "SELECT * FROM domains ORDER BY domain_id DESC LIMIT $start, $limit";
$result = $conn->query($sql);

// Fetch total number of records
$total_records_sql = "SELECT COUNT(*) FROM domains";
$total_records_result = $conn->query($total_records_sql);
$total_records = $total_records_result->fetch_array()[0];
$total_pages = ceil($total_records / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Domain and Homepage Images</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="edit.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<section class="header">
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

<div class="container">
    <h1>Edit Images & Domains</h1>
</div>

<div class="">
    <div class='row'>
        <div class='col-md-6 mx-auto'>
            <?php
            // Handle domain image upload and new domain addition
            if (isset($_POST["submit_add_domain"])) {
                if (isset($_FILES["domain_image"])) {
                    $allowedTypes = ["png", "jpg", "jpeg"];
                    $fileType = strtolower(pathinfo($_FILES["domain_image"]["name"], PATHINFO_EXTENSION));
                    $domain = $_POST["new_domain"];
                    $description = $_POST["description"];
            
                    // Validate image file
                    if (!in_array($fileType, $allowedTypes) || $_FILES["domain_image"]["size"] > 1007200) {
                        $message = "<div class='alert alert-danger'>Image Upload Failed. Invalid format or size limit exceeded.</div>";
                    } else {
                        $fileName = time() . "." . $fileType;
                        $uploadPath = "Domain_picture/" . $fileName;
            
                        if (move_uploaded_file($_FILES["domain_image"]["tmp_name"], $uploadPath)) {
                            // Insert new domain and image into the database
                            $sql = "INSERT INTO domains (domain_name, domain_image, domain_description) VALUES ('$domain', '$fileName', '$description')";
                            if ($conn->query($sql)) {
                                $message = "<div class='alert alert-success'>Domain Image & Description Uploaded Successfully.</div>";
                            } else {
                                $message = "<div class='alert alert-danger'>Image & Description Upload Failed. Error: " . $conn->error . "</div>";
                            }
                        } else {
                            $message = "<div class='alert alert-danger'>Image Upload Failed. Try Again.</div>";
                        }
                    }
                }
            }
            ?>
            
            <!-- Form for adding a new domain and uploading an image -->
            <form method="post" action="edit.php" enctype="multipart/form-data">
                <?php echo isset($message) ? $message : ''; ?>
                <div class="form-group">
                    <label>New Domain Name</label>
                    <input type="text" name="new_domain" class="form-control">
                </div>

                <div class="form-group">
                    <label>Domain Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="-"></textarea>
                </div>
                <div class="form-group">
                    <label>Choose Image</label>
                    <input type="file" name="domain_image" required class="form-control">   
                </div>
                <input type="submit" name="submit_add_domain" value="Add Domain" class="btn btn-primary">
            </form>
        </div>
    </div>
</div>
        
<div class="table-container">
    <div class="row">
        <div class="col-md-12">
            <h2>Images</h2>
            <!-- New Search Input -->
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Search Domain" id="domainSearch">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="searchButton">
                        <i class="fas fa-search"></i>
                    </button>
                    <button class="btn btn-outline-secondary" type="button" id="resetButton">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <table class="table">
                <thead>
                <tr>
                    <th>SNo</th>
                    <th>Image</th>
                    <th>Domain</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody id="domainTableBody">
                <?php
                $i = ($page - 1) * $limit + 1;
                while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><img src="Domain_picture/<?php echo $row['domain_image']; ?>" style="height:150px; width:250px;"></td>
                        <td><?php echo $row['domain_name']; ?></td>
                        <td><?php echo $row['domain_description']; ?></td>
                        <td>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#editModal"
                                    data-id="<?php echo $row['domain_id']; ?>"
                                    data-domain="<?php echo $row['domain_name']; ?>"
                                    data-description="<?php echo $row['domain_description']; ?>"
                                    data-image="<?php echo $row['domain_image']; ?>">Edit
                            </button>
                            <a href="image_delete.php?id=<?php echo $row['domain_id']; ?>&name=<?php echo $row['domain_image']; ?>"
                                class="btn btn-danger delete-link" onclick="return confirm('Are you sure you want to delete this item?');"> Delete
                            </a>
                        </td>
                    </tr>
                    <?php
                    $i++;
                endwhile;
                ?>
                </tbody>
            </table>
            <!-- Pagination links -->
            <div class="pagination justify-content-center" id="paginationLinks">
                <?php
                if ($total_pages > 1) {
                    if ($page > 1) {
                        echo '<li class="page-item"><a class="page-link" href="#" onclick="loadPage(' . ($page - 1) . ')">Previous</a></li>';
                    }

                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo '<li class="page-item' . ($page == $i ? ' active' : '') . '"><a class="page-link" href="#" onclick="loadPage(' . $i . ')">' . $i . '</a></li>';
                    }

                    if ($page < $total_pages) {
                        echo '<li class="page-item"><a class="page-link" href="#" onclick="loadPage(' . ($page + 1) . ')">Next</a></li>';
                    }
                }
                ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal for editing images -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="editId">
                    <div class="form-group">
                        <label>Current Image</label>
                        <img src="" id="editImage" style="height:250px;" class="img-fluid">
                    </div>

                    <div class="form-group">
                        <label>Current Domain</label>
                        <input type="text" name="domain" id="editDomain" class="form-control" readonly>
                    </div>

        
                    <div class="form-group">
                        <label>Edit Description</label>
                        <textarea name="description" id="editDescription" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Choose New Image</label>
                        <input type="file" name="domain_image" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="edit_current_image" id="editCurrentImage">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="editForm" name="submit_edit" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

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

<script src="https://cdn.jsdelivr.net/npm/jquery@3.3.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-QDT3qP5xXAA2nTeB5S2ur1bV+6vQEO3H+3s5l0ZccRsmkWZi1iXwr17wRlCWc7A9" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
<script>
    $('#editModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var domain = button.data('domain');
        var description = button.data('description');
        var image = button.data('image');

        var modal = $(this);
        modal.find('#editId').val(id);
        modal.find('#editDomain').val(domain);
        modal.find('#editDescription').val(description);
        modal.find('#editImage').attr('src', 'Domain_picture/' + image);
        modal.find('#editCurrentImage').val(image);
    });

    $('#editForm').on('submit', function (event) {
        event.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: 'image_update.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.includes("Updated Successfully")) {
                    $('#editModal').modal('hide');
                    alert(response);
                    location.reload();
                } else {
                    alert(response);
                }
            },
            error: function (xhr, status, error) {
                alert("Error: " + xhr.responseText);
            }
        });
    });

    $(document).ready(function () {
        function loadTableData(search, page = 1) {
            $.ajax({
                url: 'fetch_domains.php',
                type: 'GET',
                data: { search: search, page: page },
                dataType: 'html',
                success: function (response) {
                    $('#domainTableBody').html(response);
                    updatePaginationLinks(search, page);
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        }

        function updatePaginationLinks(search, currentPage) {
            $.ajax({
                url: 'fetch_pagination.php',
                type: 'GET',
                data: { search: search, page: currentPage },
                dataType: 'html',
                success: function (response) {
                    $('#paginationLinks').html(response);
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        }

        $('#searchButton').on('click', function () {
            var search = $('#domainSearch').val().trim();
            loadTableData(search);
        });

        $('#resetButton').on('click', function () {
            $('#domainSearch').val('');
            loadTableData('');
        });

        $('#domainSearch').on('keyup', function (e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                var search = $('#domainSearch').val().trim();
                loadTableData(search);
            }
        });

        window.loadPage = function(page) {
            var search = $('#domainSearch').val().trim();
            loadTableData(search, page);
        };

        loadTableData('');
    });
</script>

</body>
</html>
