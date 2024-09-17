<div id="chat-window" class="chat-window">
    <div class="chat-header">
        <!-- Dynamically updated friend profile image and screen name -->
        <img id="chat-friend-img" src="" alt="Friend's Profile" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
        <span id="chat-friend-name">Chat</span>
        <button id="close-chat" class="close-btn">&times;</button>
    </div>
    <div class="chat-body">
        <ul id="chat-messages"></ul>
    </div>
    <div class="chat-footer">
        <input type="text" id="chat-input" placeholder="Type a message" />
        <button id="send-message" class="send-btn">Send</button>
    </div>
</div>

<div id="message-icon" class="message-icon">
    <i class="fa fa-envelope"></i>
</div>

<script>
$(document).ready(function() {
    var currentReceiverId = null; // To store the current receiver's ID

    // Show the chat window when a message icon is clicked in the friends list
    $('.message-btn').click(function() {
        var friendId = $(this).data('friend-id');
        var friendName = $(this).data('friend-name');
        var friendImg = $(this).data('friend-img');

        // Update chat header with the selected friend's info
        $('#chat-friend-name').text(friendName);
        $('#chat-friend-img').attr('src', friendImg);

        // Set the current receiver ID to the selected friend
        currentReceiverId = friendId;

        // Load messages between the current user and the selected friend
        loadMessages();

        // Show the chat window
        $('#message-icon').hide();
        $('#chat-window').fadeIn();
    });

    // Click event to open the chat window when the floating message icon is clicked
    $('#message-icon').click(function() {
        $('#message-icon').hide();
        $('#chat-window').fadeIn();
    });

    // Close the chat window
    $('#close-chat').click(function() {
        $('#chat-window').fadeOut(function() {
            $('#message-icon').fadeIn();
        });
    });

        // Function to append messages to chat
        function appendMessage(message, sender) {
            var messageClass = (sender === 'You') ? 'user-message' : 'friend-message';
            $('#chat-messages').append('<li class="' + messageClass + '">' + message + '</li>');
            $('.chat-body').scrollTop($('.chat-body')[0].scrollHeight);
        }

    // Send message functionality
    $('#send-message').click(function() {
        var message = $('#chat-input').val();
        if (message !== '' && currentReceiverId !== null) {
            var senderId = <?php echo $user_id; ?>;

            // Send message via AJAX to server
            $.post('sendMessage.php', {
                message: message,
                sender_id: senderId,
                receiver_id: currentReceiverId
            }, function(response, status) {
                if (status === 'success') {
                    // If successful, just append the message to the chat window
                    appendMessage(message, 'You');
                    $('#chat-input').val('');  // Clear input
                }
            }).fail(function() {
                alert('Failed to send message.');
            });
        }
    });

    // Function to load messages between the user and the selected friend
    function loadMessages() {
        var senderId = <?php echo $user_id; ?>;
        if (currentReceiverId !== null) {
            $.post('loadMessages.php', {
                sender_id: senderId,
                receiver_id: currentReceiverId
            }, function(data) {
                $('#chat-messages').html(data);
            });
        }
    }

    // Load messages every few seconds for the active chat
    setInterval(function() {
        if (currentReceiverId !== null) {
            loadMessages();
        }
    }, 5000);
});
</script>
