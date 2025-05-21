document.addEventListener('DOMContentLoaded', function() {
    // Add click event listeners to all book buttons
    const bookButtons = document.querySelectorAll('.book-btn');
    
    bookButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const carId = this.getAttribute('data-car-id');
            
            if (!carId) {
                console.error('No car ID found');
                return;
            }

            // Redirect to booking controller with car ID
            window.location.href = `../controller/booking_controller.php?car_id=${carId}`;
        });
    });
});

// Add smooth scroll behavior
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Add hover effect for car cards
document.querySelectorAll('.car-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
    });
}); 