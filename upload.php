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
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="edit.css" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" href="test2.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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

    <section class = "header">
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
        <h1>Manage Project</h1>
    </div>

    <form action="submit.php" method="post" enctype="multipart/form-data">

        <div class="form-input">
            <br><br>
            <input type="file" name="pdf_file" required="">
            <button type="submit">Upload</button>
        </div>

    </form>

    <div class="">
        <div class="row">
            <div class="'col-md-6 mx-auto">
            <?php
            // Database Connection
            $conn = mysqli_connect("localhost", "root", "", "fyp_test");
            $message = "";

            // Fetch all from domains
            $domains_sql = "SELECT * FROM domains";
            $domains_result = $conn->query($domains_sql);

            // Pagination configuration
            $limit = 4; // Number of records per page
            $page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number, default is 1
            $start = ($page - 1) * $limit; // Calculate starting point for the query

            // Fetch data from database with pagination
            $sql = "SELECT * FROM project p JOIN domains d ON p.domain_id = d.domain_id LIMIT $start, $limit";
            $result = $conn->query($sql);
            ?>     
            </div>
        </div>
    </div>

    <div class="table-container">
        <div class="row">
            <div class=""col-md-12>
                <h2>Project List</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>SNo</th>
                            <th>Title</th>
                            <th>Domain</th>
                            <th>Year</th>
                            <th>Semester</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php
                        $i = ($page - 1) * $limit + 1;
                        while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $row['Project_title']; ?></td>
                                <td><?php echo $row['domain_name']; ?></td>
                                <td><?php echo $row['Project_year']; ?></td>
                                <td><?php echo $row['Project_semester']; ?></td>
                                <td>
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#editModal"
                                            data-id="<?php echo $row['Project_ID']; ?>"
                                            data-title="<?php echo $row['Project_title']; ?>"
                                            data-body="<?php echo $row['Project_body']; ?>"
                                            data-organisation="<?php echo $row['Organisation']; ?>"
                                            data-members="<?php echo $row['Members']; ?>"
                                            data-supervisor="<?php echo $row['Supervisor']; ?>"
                                            data-domain="<?php echo $row['domain_id']; ?>"
                                            data-year="<?php echo $row['Project_year']; ?>"
                                            data-semester="<?php echo $row['Project_semester']; ?>">Edit
                                    </button>
                                    <a href="project_delete.php?id=<?php echo $row['Project_ID']; ?>&name=<?php echo $row['Project_title']; ?>"
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
                                <div class="pagination justify-content-center">
                    <?php
                    $sql_count = "SELECT COUNT(Project_ID) AS total FROM project";
                    $result_count = $conn->query($sql_count);
                    $row_count = $result_count->fetch_assoc();
                    $total_pages = ceil($row_count["total"] / $limit);

                    if ($total_pages > 1) {
                        if ($page > 1) {
                            echo '<li class="page-item"><a class="page-link" href="?page=' . ($page - 1) . '">Previous</a></li>';
                        }

                        for ($i = 1; $i <= $total_pages; $i++) {
                            echo '<li class="page-item' . ($page == $i ? ' active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                        }

                        if ($page < $total_pages) {
                            echo '<li class="page-item"><a class="page-link" href="?page=' . ($page + 1) . '">Next</a></li>';
                        }
                    }
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <!-- Modal for edit projects -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl"  role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                    <div class="modal-body">
                        <form id="editForm" method="post" enctype="multipart/form-data">

                        
                        <input type="hidden" name="id" id="project-id">
                        
                      
                        <div class="form-group">
                            <label for="project-title" class="col-form-label">Title:</label>
                            <textarea name="title" class="form-control" id="project-title"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="project-body" class="col-form-label">Project Overview:</label>
                            <textarea name="body" class="form-control" id="project-body" style="height: 250px"></textarea>
                        </div>
                      
                        <div class="form-group">
                            <label for="organisation" class="col-form-label">Organisation:</label>
                            <textarea name="organisation" class="form-control" id="organisation"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="members" class="col-form-label">Members:</label>
                            <textarea name="members" class="form-control" id="members" style="height: 100px"></textarea>
                         </div>

                        <div class="form-group">
                            <label for="project-year" class="col-form-label">Supervisor:</label>
                            <textarea name="supervisor" class="form-control" id="supervisor" style="height: 100px"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="project-domain" class="col-form-label">Domain:</label>
                            <input name="domain" class="form-control" id="project-domain"></>
                         </div>

                        <div class="form-group">
                            <label for="project-year" class="col-form-label">Year:</label>
                            <input name="year" class="form-control" id="project-year"></>
                        </div>

                        <div class="form-group">
                            <label for="project-semester" class="col-form-label">Semester:</label>
                            <input name="semester" class="form-control" id="project-semester"></>
                        </div>

                </div>
                    <div class="modal-footer">
                        <input type="hidden" name="edit_id" id="edit_id">
                        <input type="hidden" name="edit_current_project" id="edit_current_project">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <input type="submit" name="submit_edit" value="Save changes" class="btn btn-primary">
                    </div>
                </form>
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
        var title = button.data('title');
        var domain = button.data('domain');
        var year = button.data('year');
        var semester = button.data('semester');
        var body = button.data('body');
        var organisation = button.data('organisation');
        var members = button.data('members');
        var supervisor = button.data('supervisor');

        // Update the modal's content.
        var modal = $(this);
        modal.find('.modal-title').text('Edit Project: ' + title);
        modal.find('#project-id').val(id).text(id);
        modal.find('#project-title').val(title).text(title);
        modal.find('#project-domain').val(domain).text(domain);
        modal.find('#project-year').val(year).text(year);
        modal.find('#project-semester').val(semester).text(semester);
        modal.find('#project-body').val(body).text(body);
        modal.find('#organisation').val(organisation).text(organisation);
        modal.find('#members').val(members).text(members);
        modal.find('#supervisor').val(supervisor).text(supervisor);
    });

$('#editForm').on('submit', function (event) {
    event.preventDefault();
    var formData = new FormData(this);

    // Log the form data for debugging
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]); 
    }

    $.ajax({
        url: 'project_update.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            console.log(response); // Log the response for debugging
            if (response.includes("Image Updated Successfully.")) {
                location.reload(); // Reload the page to reflect changes
            } else {
                alert(response); // Display error message
            }
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText); // Log any errors for debugging
        }
    });
});

</script>
</body>
</html>



