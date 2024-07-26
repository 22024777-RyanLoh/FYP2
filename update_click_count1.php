<?php

// Get the project ID and domain name from the request body
$projectID = $_POST['projectID'] ?? '';
$domainName = $_POST['domainName'] ?? '';

// Check if the project ID and domain name are provided
if (!empty($projectID) && !empty($domainName)) {
    // Define the JSON file path
    $jsonFile = 'click_counts.json';

    // Check if the file exists and is readable
    if (file_exists($jsonFile) && is_readable($jsonFile)) {
        $jsonData = json_decode(file_get_contents($jsonFile), true);
    } else {
        $jsonData = array(); // Initialize an empty array if the file doesn't exist
    }

    if (!isset($jsonData[$domainName])) {
        $jsonData[$domainName] = 0;
    }

    $jsonData[$domainName] += 1;

    $jsonEncoded = json_encode($jsonData);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON encoding error: " . json_last_error_msg());
    }

    // Write the updated JSON data to the file
    file_put_contents($jsonFile, $jsonEncoded);

    // Return a success response
    echo 'success';
} else {
    // Return an error response
    http_response_code(400);
    echo 'Error: project ID and domain name are required.';
}