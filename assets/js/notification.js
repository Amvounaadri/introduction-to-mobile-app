notification = function(){
	$.get('http://localhost/GradPortal/core/ajax/notification.php', {showNotification:true}, function(data){
		if(data){
			if(data.notification > 0){
				$('#notification').addClass('span-i');
				$('#notification').html(data.notification);
			}
			if(data.messages > 0){
				$('#messages').show();
 				$('#messages').addClass('span-i');
				$('#messages').html(data.messages);
			}
		}
	}, 'json');
}

setInterval(notification, 10000);// JavaScript code to call the notification.php file
function getNotifications(userEmail) {
    $.get('notification.php', {showNotification: true, userEmail: userEmail}, function(data) {
        if (data) {
            if (data.notification > 0) {
                $('#notification').addClass('span-i');
                $('#notification').html(data.notification);
                // Display the pop-up
                $('#notification-pop-up').fadeIn();
                $('#notification-message').html(data.message);
            }
            if (data.messages > 0) {
                $('#messages').show();
                $('#messages').addClass('span-i');
                $('#messages').html(data.messages);
            }
        }
    }, 'json');
}

// Call the getNotifications function on login
function login(userEmail, password) {
    // Authenticate the user
    $.post('login.php', {userEmail: userEmail, password: password}, function(data) {
        if (data.success) {
            // Call the getNotifications function
            getNotifications(userEmail);
        }
    }, 'json');
}

// Call the getNotifications function at regular intervals
setInterval(function() {
    getNotifications(userEmail); // You'll need to pass the user's email here
}, 10000);