<?php
$conn = mysqli_connect("localhost", "root", "", "fyp_test");

// Fetch search query from URL
$search = isset($_GET['search']) ? $_GET['search'] : '';
$whereClause = !empty($search) ? "WHERE domain_name LIKE '%$search%'" : "";

// Pagination configuration
$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page number, default is 1
$start = ($page - 1) * $limit; // Calculate starting point for the query

// Fetch total number of records
$total_records_sql = "SELECT COUNT(*) FROM domains $whereClause";
$total_records_result = $conn->query($total_records_sql);
$total_records = $total_records_result->fetch_array()[0];
$total_pages = ceil($total_records / $limit);

// Fetch data from database with pagination and search filter
$sql = "SELECT * FROM domains $whereClause ORDER BY domain_id DESC LIMIT $start, $limit";
$result = $conn->query($sql);

// Output table rows
$i = ($page - 1) * $limit + 1;
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$i}</td>";
    echo "<td data-label='{$row['domain_name']}'><img src='data:image/jpeg;base64,{$row['domain_image']}' style='height:150px; width:250px;'></td>";
    echo "<td data-label='{$row['domain_name']}'>{$row['domain_name']}</td>";
    echo "<td data-label='{$row['domain_description']}'>{$row['domain_description']}</td>";
    echo "<td>";
    echo "<button class='btn btn-primary' data-toggle='modal' data-target='#editModal' data-id='{$row['domain_id']}' data-domain='{$row['domain_name']}' data-description='{$row['domain_description']}' data-image='{$row['domain_image']}'><i class='fas fa-edit'></i></button>";
    echo '<a href="domain_delete.php?id=' . $row['domain_id'] . '&name=' . urlencode($row['domain_name']) . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this domain?\');"><i class="fas fa-trash"></i></a>';
    echo "</td>";
    echo "</tr>";
    $i++;
}


// Close database connection
$conn->close();
?>
