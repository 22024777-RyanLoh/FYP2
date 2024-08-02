<?php
  //Database Connection
  $conn = mysqli_connect("localhost","root","","fyp_test");

  // Check connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the domain ID from the GET request
$domain_id = $_GET["id"];
$domain_name = $_GET["name"];

// Check if there are any projects associated with the domain
$project_check_sql = "SELECT COUNT(*) as project_count FROM project WHERE domain_id = $domain_id";
$project_check_result = $conn->query($project_check_sql);
$project_count = $project_check_result->fetch_assoc()['project_count'];

if ($project_count > 0) {
    // If there are projects associated with the domain, show an alert
    echo "<script>
        alert('Unable to delete domain because there are still projects associated with it.');
        window.location.href = 'edit.php';
    </script>";
} else {
    // Delete the domain from the database
    $delete_sql = "DELETE FROM domains WHERE domain_id = $domain_id";
    if ($conn->query($delete_sql)) {
        // Delete the image from the server
        unlink("/xampp/htdocs/fyp/Domain_picture/$domain_name");

        // Redirect to edit page with status = 1
        header("location:edit.php?status=1");
    } else {
        // Redirect to edit page with status = 0
        header("location:edit.php?status=0");
    }
}

// Close the database connection
$conn->close();
?>