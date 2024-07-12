<?php
$conn = mysqli_connect("localhost", "root", "", "fyp_test");

// Fetch search query and other filters from URL
$search = isset($_GET['search']) ? $_GET['search'] : '';
$role = isset($_GET['role']) ? $_GET['role'] : '';
$rowsPerPage = isset($_GET['rows']) ? intval($_GET['rows']) : 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Build the WHERE clause
$whereClauses = [];
if (!empty($search)) {
    $whereClauses[] = "user_fullname LIKE '%$search%' OR email LIKE '%$search%'";
}
if (!empty($role) && $role !== 'all') {
    $whereClauses[] = "user_role = '$role'";
}
$whereClause = implode(' AND ', $whereClauses);
if (!empty($whereClause)) {
    $whereClause = 'WHERE ' . $whereClause;
}

// Fetch total number of records
$total_records_sql = "SELECT COUNT(*) FROM user $whereClause";
$total_records_result = $conn->query($total_records_sql);
$total_records = $total_records_result->fetch_array()[0];

if ($rowsPerPage > 0) {
    $total_pages = ceil($total_records / $rowsPerPage);
} else {
    $total_pages = 1; // Only one page if showing all rows
}

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

$conn->close();
