
    const header = document.querySelector('.grad-header');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 0) {
            header.classList.add('hidden'); // Add hidden class when scrolling down
        } else {
            header.classList.remove('hidden'); // Remove hidden class when at the top
        }
    });
