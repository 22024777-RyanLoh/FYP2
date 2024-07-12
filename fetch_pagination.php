<?php
$conn = mysqli_connect("localhost", "root", "", "fyp_test");

// Fetch search query from URL
$search = isset($_GET['search']) ? $_GET['search'] : '';
$whereClause = !empty($search) ? "WHERE domain_name LIKE '%$search%'" : "";

// Pagination configuration
$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number, default is 1

// Fetch total number of records
$total_records_sql = "SELECT COUNT(*) FROM domains $whereClause";
$total_records_result = $conn->query($total_records_sql);
$total_records = $total_records_result->fetch_array()[0];
$total_pages = ceil($total_records / $limit);

// Output pagination links
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

// Close database connection
$conn->close();
?>