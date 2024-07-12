<?php
session_start();

// File to store click counts
$clickCountsFile = 'click_counts.json';

// Read the current click counts from the file
$clickCounts = [];
if (file_exists($clickCountsFile)) {
    $clickCounts = json_decode(file_get_contents($clickCountsFile), true);
}

if (isset($_POST['domain'])) {
    $domain = $_POST['domain'];

    // Update the click count for the domain
    if (isset($clickCounts[$domain])) {
        $clickCounts[$domain]++;
    } else {
        $clickCounts[$domain] = 1;
    }

    // Save the updated click counts back to the file
    file_put_contents($clickCountsFile, json_encode($clickCounts));

    // Store the click count in the session for the user (optional)
    $_SESSION['click_counts'][$domain] = isset($_SESSION['click_counts'][$domain]) ? $_SESSION['click_counts'][$domain] + 1 : 1;

    echo $clickCounts[$domain];
} else {
    echo "Error: Domain not specified";
}
?>
