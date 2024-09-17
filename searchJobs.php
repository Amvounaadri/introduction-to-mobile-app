<?php
include 'core/init.php';

if (isset($_POST['search']) && !empty($_POST['search'])) {
    $search = htmlspecialchars($_POST['search']);
    $user_id = $_SESSION['user_id'];
    
    // Query to search jobs by title
    $stmt = $pdo->prepare("SELECT * FROM `jobs` 
                            LEFT JOIN `users` ON `jobs`.`user_id` = `users`.`user_id`
                            WHERE `job_title` LIKE :search 
                            ORDER BY `created_at` DESC");
    $stmt->bindValue(':search', '%'.$search.'%', PDO::PARAM_STR);
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_OBJ);

    if ($stmt->rowCount() > 0) {
        foreach ($jobs as $job) {
            echo '<div class="job-post">
                    <h3>'.htmlspecialchars($job->job_title).'</h3>
                    <p>'.htmlspecialchars($job->job_description).'</p>
                    <p><strong>Skills Required:</strong> '.htmlspecialchars($job->skills_required).'</p>';
            
            $applicationExists = $getFromT->checkApplicationExists($job->job_id, $user_id);

            if ($applicationExists) {
                echo '<button class="apply-btn applied" disabled>Applied</button>';
            } else {
                echo '<button class="apply-btn" data-job="'.$job->job_id.'" data-user="'.$job->user_id.'">Apply</button>';
            }
            
            echo '</div>';
        }
    } else {
        echo '<div class="no-jobs-found">No job found!</div>';
    }
}
?>
