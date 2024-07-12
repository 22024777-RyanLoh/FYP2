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
    echo "<td><img src='Domain_picture/{$row['domain_image']}' style='height:150px; width:250px;'></td>";
    echo "<td>{$row['domain_name']}</td>";
    echo "<td>{$row['domain_description']}</td>";
    echo "<td>";
    echo "<button class='btn btn-primary' data-toggle='modal' data-target='#editModal' data-id='{$row['domain_id']}' data-domain='{$row['domain_name']}' data-description='{$row['domain_description']}' data-image='{$row['domain_image']}'>Edit</button>";
    echo "<a href='image_delete.php?id={$row['domain_id']}&name={$row['domain_image']}' class='btn btn-danger'>Delete</a>";
    echo "</td>";
    echo "</tr>";
    $i++;
}

// Output pagination links
echo "<div class='pagination justify-content-center'>";
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
echo "</div>";

// Close database connection
$conn->close();
?>
