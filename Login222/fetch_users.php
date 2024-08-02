<?php
session_start(); // Start the session at the beginning

$conn = mysqli_connect("localhost", "root", "", "fyp_test");

// Check if the session variable is set
if (!isset($_SESSION['login_user_id'])) {
    die('User not logged in.');
}

$currentUserId = $_SESSION['login_user_id'];

// Fetch search query and other filters from URL
$search = isset($_GET['search']) ? $_GET['search'] : '';
$role = isset($_GET['role']) ? $_GET['role'] : '';
$rowsPerPage = isset($_GET['rows']) ? intval($_GET['rows']) : 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate start of the current page
$start = ($page - 1) * $rowsPerPage;
if ($start < 0) {
    $start = 0; // Ensure start is never negative
}

// Build the WHERE clause
$whereClauses = [];
if (!empty($search)) {
    $whereClauses[] = "(user_fullname LIKE '%$search%' OR email LIKE '%$search%')";
}
if (!empty($role) && $role !== 'all') {
    $whereClauses[] = "user_role = '$role'";
}
$whereClause = implode(' AND ', $whereClauses);
if (!empty($whereClause)) {
    $whereClause = 'WHERE ' . $whereClause;
}

// Adjust SQL query based on rowsPerPage value
if ($rowsPerPage > 0) {
    $sql = "SELECT * FROM user $whereClause ORDER BY user_id DESC LIMIT $start, $rowsPerPage";
} else {
    $sql = "SELECT * FROM user $whereClause ORDER BY user_id DESC";
}

$result = $conn->query($sql);

// Check if the query was successful
if ($result === false) {
    // Log and display SQL error
    error_log('SQL error: ' . $conn->error . ' SQL: ' . $sql);
    die('An error occurred while fetching users: ' . $conn->error);
}

// Fetch all users into an array
$users = $result->fetch_all(MYSQLI_ASSOC);

// Find and swap the current user with the first user in the array
foreach ($users as $index => $user) {
    if ($user['user_id'] == $currentUserId) {
        // Swap positions
        $temp = $users[0];
        $users[0] = $users[$index];
        $users[$index] = $temp;
        break;
    }
}

// Output the users
$i = $start + 1;
foreach ($users as $user) {
    $boldClass = ($user['user_id'] == $currentUserId) ? 'bold' : '';
    echo "<tr>";
    echo "<td>{$i}</td>";
    echo "<td class='{$boldClass}'>{$user['email']}</td>";
    echo "<td class='{$boldClass}'>{$user['user_fullname']}";
    if ($user['user_id'] == $currentUserId) {
        echo " (Current User)";
    }
    echo "</td>";
    echo "<td class='{$boldClass}'>{$user['user_role']}</td>";
    echo "<td>";
    echo "<a href='users.php?do=Edit&user_id={$user['user_id']}' class='btn btn-success btn-sm'><i class='fa fa-edit' style='font-weight: 900; font-family: \"Font Awesome 5 Free\"; font-style: normal; font-variant: normal;'></i></a> ";
    if ($user['user_id'] == $currentUserId) {
        echo "<button class='btn btn-danger btn-sm' onclick='forbidDeletion(); return false;'>";
        echo "<i class='fa fa-trash' style='font-weight: 900; font-family: \"Font Awesome 5 Free\"; font-style: normal; font-variant: normal;'></i>";
        echo "</button>";
    } else {
        echo "<button class='btn btn-danger btn-sm' onclick='confirmDeletion(" . $user['user_id'] . "); return false;'>";
        echo "<i class='fa fa-trash' style='font-weight: 900; font-family: \"Font Awesome 5 Free\"; font-style: normal; font-variant: normal;'></i>";
        echo "</button>";
    }    echo "</td>";
    echo "</tr>";
    $i++;
}

$conn->close();
?>

<script>
    function confirmDeletion(userId) {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-success custom-swal-button ml-3',  // Add left margin to the confirm button
            cancelButton: 'btn btn-danger custom-swal-button'  // Add custom class for the cancel button
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: 'Are you sure you want to delete this user?',
        text: "This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'users.php?do=Delete&user_id=' + userId;
        }
    });
}
    function forbidDeletion() {
        Swal.fire({
            title: 'Action Forbidden',
            text: 'You cannot delete your own account.',
            icon: 'error',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
        });
    }
</script>

<!-- Add custom styles for the buttons -->
<style>
    .custom-swal-button {
        margin: 0 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 1rem;
        line-height: 1.25;
        border-radius: 0.25rem;
        transition: all 0.15s ease-in-out;
    }

    .ml-3 {
        margin-left: 15px !important;
    }
</style>