<div class="right_sidebar">
    <link rel='stylesheet' href='<?php echo BASE_URL; ?>assets/css/style.css' />
    <div class="search-container">
        <a href="" class="search-btn">
            <i class="fa fa-search"></i>
        </a>
        <input type="text" name="search" id="userSearch" placeholder="search" class="search-input search" autocomplete="off">
    </div>
    <img src="<?php echo BASE_URL; ?>assets/images/bird-dance.gif" alt="Logo" class="logo-girsb" />

    <div class='search-result'>
        <ul class="user-list" id="userList">
            <?php
            // Ensure $getFromU is an instance of the User class
            if (isset($getFromU)) {
                // Fetch all users
                $users = $getFromU->getAllUsers();
                if ($users) {
                    foreach ($users as $user) {
                        echo '<li class="media-inner" data-username="' . $user->username . '">';
                        echo '<a href="viewuserdetail.php?username=' . $user->username . '">';
                        echo '<img class="mr-1" src="' . BASE_URL . $user->profileImage . '" style="height: 40px; width: 40px; border-radius: 50%;" />';
                        echo '<div class="media-body">';
                        echo '<h5 class="mt-0 mb-1">';
                        echo '<a href="' . BASE_URL . $user->username . '"><span>' . '<b>' . $user->screenName . '</b>' . '</span></a>';
                        echo '</h5>';
                        echo '<span class="text-muted">' . "@" . $user->username . ' - <b>' . $user->usertype . '</b></span>';
                        echo '</div>';
                        echo '</a>';

                        // Determine friendship status for the current user
                        $friendshipStatus = $getFromU->checkFriendshipStatus($user_id, $user->user_id);

                        // Display appropriate friend request button
                        if ($friendshipStatus == 'pending') {
                            echo '<button class="add-friend-btn yellow-ticked" data-user-id="' . $user->user_id . '">✔</button>';
                        } elseif ($friendshipStatus == 'accepted') {
                            // No button for accepted friendship
                        } else {
                            echo '<button class="add-friend-btn red-plus" data-user-id="' . $user->user_id . '">+</button>';
                        }

                        echo '</li>';
                    }
                } else {
                    echo '<li>No users found.</li>';
                }
            } else {
                echo '<li>Error: $getFromU is not set.</li>';
            }
            ?>
        </ul>
    </div>
    
    <script>
        $(document).ready(function() {
            $('#userSearch').on('input', function() {
                var query = $(this).val().toLowerCase();
                if (query) {
                    // Filter users based on search input
                    $('#userList li').filter(function() {
                        $(this).toggle($(this).data('username').toLowerCase().indexOf(query) > -1);
                    });
                } else {
                    // Show all users if the search input is empty
                    $('#userList li').show();
                }
            });

            $(document).on('click', '.add-friend-btn', function() {
                var friend_id = $(this).data('user-id');
                var button = $(this); // Store a reference to the button

                $.ajax({
                    url: '<?php echo BASE_URL; ?>addFriend.php', // Use absolute URL
                    type: 'POST',
                    data: { friend_id: friend_id },
                    success: function(response) {
                        if (response.trim() === 'success') {
                            // Change button color to yellow and update icon to a tick
                            button.css('background-color', 'yellow');
                            button.text('✔'); // Change '+' to '✔'
                        } else {
                            alert('Error: ' + response);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Provide detailed error message
                        alert('AJAX Error: ' + textStatus + ' - ' + errorThrown);
                    }
                });
            });
        });
    </script>
</div>
