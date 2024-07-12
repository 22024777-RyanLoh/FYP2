<?php
$conn = mysqli_connect("localhost", "root", "", "fyp_test");

$data = json_decode(file_get_contents('php://input'), true);
$projectID = $data['projectID'];

if (!empty($projectID)) {
    $sql = "SELECT Project_image, Image_description FROM project_image WHERE Project_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $projectID);
    $stmt->execute();
    $result = $stmt->get_result();
    $images = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $images[] = array(
                'Project_image' => $row['Project_image'], // Data is already base64 encoded
                'Image_description' => $row['Image_description'] // Fetch the image description
            );
        }
    }
    echo json_encode($images);
}

$stmt->close();
mysqli_close($conn);
?>
