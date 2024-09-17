<!DOCTYPE HTML>
<html>

<?php
include 'core/init.php';
$user_id = $_SESSION['user_id'];
$user = $getFromU->userData( $user_id );

if ( $getFromU->loggedIn() === false ) {
    header( 'Location: '.BASE_URL.'index.php' );
    exit();
}

$getFromT = new Tweet($pdo);
?>

<head>
    <title>THE ICTU GRAD CAREER PORTAL</title>
    <meta charset='UTF-8' />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,shrink-to-fit=no">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/images/bird.svg">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css'/>
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>assets/css/style-complete.css' />
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>assets/css/style.css' />
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>assets/css/font-awesome.css' />
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>assets/css/bootstrap.css' />
    <script src='https://code.jquery.com/jquery-3.2.1.min.js'></script>
</head>

<body>

    <header class="grad-header">
        <div class="header-left">
            <h1>Grad Career Portal</h1>
        </div>
        <div class="header-right">
            <ul class="header-icons">
                <li>
                    <a href="<?php echo BASE_URL;?>i/notifications">
                        <i class="fa fa-bell" aria-hidden="true"></i>
                    </a>
                </li>
                <li id='messagePopup'>
                    <a>
                        <i class="fa fa-envelope" aria-hidden='true'></i>
                    </a>
                </li>
                <li>
                    <a href="<?php echo BASE_URL; ?><?php echo "profile.php?username=".$user->username.""; ?>">
                        <i class="fa fa-user"></i>
                    </a>
                </li>
                <li>
                    <a href='<?php echo BASE_URL; ?>settings/account'>
                        <i class="fa fa-cog"></i>
                    </a>
                </li>
            </ul>
            <img src="assets/images/TY.png" alt="Custom Image" class="header-logo">
        </div>
    </header>

    <div class="grid-container">
        <!-- Left Sidebar -->
        <?php require 'left-sidebar.php'; ?>

        <div class="main">
            <div class=''>
                <p class="page_title mb-0">.Job Search</p>

                <h1 class="mb-2" style="text-align:center;">
                    Find your dream job
                </h1>

                <div class="search-container">
                    <a href="" class="search-btn">
                        <i class="fa fa-search"></i>
                    </a>
                    <input type="text" name="search" placeholder="Search jobs by title" class="search-input search" autocomplete="off" id="job-search-input">
                </div>

                <!-- Container for search results -->
                <div class="tweets"></div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <?php include 'chat-window.php'; ?>
        <?php require 'right-sidebar.php'; ?>
    </div>



    <script src='<?php echo BASE_URL; ?>assets/js/popper.min.js'></script>
    <script src='<?php echo BASE_URL; ?>assets/js/bootstrap.min.js'></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#job-search-input').on('keypress', function(e) {
                if (e.which === 13) { // Enter key pressed
                    let query = $(this).val();
                    if (query.trim() !== '') {
                        // Show preloader here (optional)
                        $.ajax({
                            url: 'searchJobs.php',
                            type: 'POST',
                            data: {search: query},
                            success: function(data) {
                                $('.tweets').html(data);
                            }
                        });
                    }
                }
            });
        });
        const header = document.querySelector('.grad-header');

        $(document).on('click', '.apply-btn', function() {
            var job_id = $(this).data('job');
            var company_id = $(this).data('user');

            $.ajax({
                url: 'apply.php',
                method: 'POST',
                data: {
                    job_id: job_id,
                    company_id: company_id
                },
                success: function(response) {
                console.log(response); // Log the response from the server
                if (response.trim() === 'success') { // Use trim() to avoid any excess whitespace
                    $('.apply-btn[data-job="'+job_id+'"]').text('Applied').addClass('applied').attr('disabled', true);
                } else if (response.trim() === 'already_applied') {
                    alert('You have already applied for this job.');
                } else {
                    alert('Error applying for the job: ' + response);
                }
            }



            });
        });

        window.addEventListener('scroll', () => {
            if (window.scrollY > 0) {
                header.classList.add('hidden'); // Add hidden class when scrolling down
            } else {
                header.classList.remove('hidden'); // Remove hidden class when at the top
            }
        });


    </script>

</body>

</html>
