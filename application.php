<?php
// application.php

require 'core/init.php';

if (isset($_POST['apply'])) {
    $job_id = $_POST['job_id'];
    $graduate_id = $user->user_id;
    $company_id = $_POST['company_id'];

    // Check if the graduate has already applied
    $applicationExists = $getFromT->checkApplicationExists($job_id, $graduate_id);

    if (!$applicationExists) {
        // Insert the application into the database
        $getFromT->applyForJob($job_id, $graduate_id, $company_id);
        echo "Application submitted successfully!";
    } else {
        echo "You have already applied for this job.";
    }
} else {
    echo "Invalid application request.";
}

?>
