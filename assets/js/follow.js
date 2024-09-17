$(function(){
  $('.follow-btn').click(function(){
    var followID = $(this).data('follow');
    var profile = $(this).data('profile');
    $button = $(this);

    if ($button.hasClass('following-btn')) {
      $.post('http://localhost/GradPortal/core/ajax/follow.php', {unfollow:followID, profile:profile}, function(data){
        data = JSON.parse(data);
        $button.removeClass('following-btn');
        $button.removeClass('unfollow-btn');
        $button.html('<i class="fa fa-user-plus"></i>Follow');
        $('.count-following').text(data.following);
        $('.count-followers').text(data.followers);
      });
    }else{
      $.post('http://localhost/GradPortal/core/ajax/follow.php', {follow:followID, profile:profile}, function(data){
        data = JSON.parse(data);
        $button.removeClass('follow-btn');
        $button.addClass('following-btn');
        $button.text('Following');
        $('.count-following').text(data.following);
        $('.count-followers').text(data.followers);
      });
    }
  });


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


  $('.follow-btn').hover(function(){
    $button = $(this);

    if($button.hasClass('following-btn')) {
      $button.addClass('unfollow-btn');
      $button.text('Unfollow');
    }
  }, function(){
    if($button.hasClass('following-btn')) {
      $button.removeClass('unfollow-btn');
      $button.text('Following');
    }
  });
});

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
          if (response == 'success') {
              // Change button text to "Applied"
              $('.apply-btn[data-job="'+job_id+'"]').text('Applied').addClass('applied').attr('disabled', true);
          } else {
              alert('Error applying for the job.');
          }
      }
  });
});


