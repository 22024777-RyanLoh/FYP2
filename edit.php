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
</head>
<body>

<style>

.header {
    min-height: 15vh;
    width: 100%;
    background-image: linear-gradient(rgba(4,9,30,0.7), rgba(4,9,30,0.7)), url(Domain_picture/homepage.png);
    background-position: center;
    background-size: cover;
    position: relative;
}

.bottom {
    min-height: 10vh;
    width: 100%;
    background-color: rgb(241, 70, 2);
    background-position: center;
    position: relative;
}

nav {
    display: flex;
    padding: 2% 6%;
    justify-content: space-between;
    align-items: center;
}

nav img {
    width: 150px;
}

.nav-links {
    flex: 1;
    text-align: right;
}

.nav-links ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav-links ul li {
    display: inline-block;
    padding: 8px 12px;
    position: relative;
}

.nav-links ul li a,
.welcome-message {
    color: #fff;
    text-decoration: none;
    font-size: 15px;
}

.nav-links ul li::after {
    content: '';
    width: 0%;
    height: 2px;
    background: #f44336;
    display: block;
    margin: auto;
    transition: 0.5s;
}

.nav-links ul li:hover::after {
    width: 100%;
}

nav2 {
    display: flex;
    padding: 2% 6%;
    justify-content: space-between;
    margin-top: 130px;
    align-items: center;
}

.nav2-links {
    flex: 1;
    text-align: left;
}

.nav2-links ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav2-links ul li {
    display: inline-block;
    padding: 8px 12px;
    position: relative;
}

.nav2-links ul li a {
    color: #fff;
    text-decoration: none;
    font-size: 15px;
}

.nav2-links ul li::after {
    content: '';
    width: 0%;
    height: 2px;
    background: #f44336;
    display: block;
    margin: auto;
    transition: 0.5s;
}

.nav2-links ul li:hover::after {
    width: 100%;
}

</style>

<section class="header">
    <nav>
        <a href="home.php"><img src="Domain_picture/transRP.png" alt="Logo"></a>
        <div class="nav-links" id="navLinks">
            <i class="fas fa-times" onclick="hidemenu()"></i>
            <ul>
                <?php if(isset($_SESSION['login_user'])): ?>
                    <li><span class="welcome-message" style="color: #FFFFFF;">Welcome, <?php echo $_SESSION['login_user']; ?></span></li>
                    <?php if($isAdmin): ?>
                        <li><a href="Login222/users.php?do=Edit&user_id=<?php echo $_SESSION['login_user_id'] ?>">
                                <span style="padding-left:6px">My Profile</span>
                        </a></li>
                        <li><a href="login222/dashboard.php">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="edit.php">Domain</a></li>
                    <li><a href="upload.php">Project</a></li>
                    <li><a href="logout.php">Sign out</a></li>
                <?php else: ?>
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
                               class="btn btn-danger">Delete</a>
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
