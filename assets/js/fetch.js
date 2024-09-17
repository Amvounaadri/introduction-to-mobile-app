$(function(){
	var win = $(window);
	var offset = 10;

	win.scroll(function(){
		if($(document).height() <= (win.height() + win.scrollTop())){
			offset +=10;
			$('#loader').show();
			$.post('http://localhost/GradPortal/core/ajax/fetchPosts.php', {fetchPost:offset}, function(data){
				$('.tweets').html(data);
				$('#loader').hide();
			});
		}
	});
});

    let lastScrollTop = 0; // Variable to store the last scroll position
    const header = document.querySelector('.grad-header');

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

        if (currentScroll > lastScrollTop) {
            // Scrolling down
            header.style.transform = 'translateY(-100%)'; // Hide header
        } else {
            // Scrolling up
            header.style.transform = 'translateY(0)'; // Show header
        }
        lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // For Mobile or negative scrolling
    });
