<?php
// job.php

require 'core/init.php'; // Include your initialization file to set up database connections, etc.

if (isset($_GET['job_id'])) {
    $job_id = $_GET['job_id'];
    $job = $getFromT->getJobDetails($job_id); // Method to get job details

    if ($job) {
        echo "<h1>{$job->job_title}</h1>";
        echo "<p><strong>Description:</strong> {$job->job_description}</p>";
        echo "<p><strong>Skills Required:</strong> {$job->skills_required}</p>";
        echo "<p><strong>Posted by:</strong> {$job->screenName}</p>";
        
        if ($user->usertype == 'Graduate') {
            echo "<form method='post' action='application.php'>";
            echo "<input type='hidden' name='job_id' value='{$job_id}' />";
            echo "<input type='hidden' name='company_id' value='{$job->user_id}' />";
            echo "<button type='submit' name='apply' class='apply-button'>Apply</button>";
            echo "</form>";
        }
    } else {
        echo "Job not found.";
    }
} else {
    echo "No job ID specified.";
}

?>
