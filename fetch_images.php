<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "fyp_test");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$data = json_decode(file_get_contents('php://input'), true);
$imageIDs = isset($data['imageIDs']) ? $data['imageIDs'] : [];

if (!empty($imageIDs)) {
    $placeholders = implode(',', array_fill(0, count($imageIDs), '?'));
    $stmt = $conn->prepare("SELECT Image_ID, Project_image FROM project_image WHERE Image_ID IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($imageIDs)), ...$imageIDs);
    $stmt->execute();
    $result = $stmt->get_result();
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = [
            'Image_ID' => $row['Image_ID'],
            'Project_image' => 'data:image/jpeg;base64,' . base64_encode($row['Project_image'])
        ];
    }
    echo json_encode($images);
} else {
    echo json_encode([]);
}

$stmt->close();
mysqli_close($conn);
?>
