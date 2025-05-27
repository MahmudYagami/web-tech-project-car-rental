// Real-time search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        // Clear the previous timeout
        clearTimeout(searchTimeout);
        
        // Set a new timeout to search after 300ms of no typing
        searchTimeout = setTimeout(() => {
            const searchTerm = this.value;
            searchBookings(searchTerm);
        }, 300);
    });
});

function searchBookings(searchTerm) {
    fetch(`../controller/manage_bookings_controller.php?search=${encodeURIComponent(searchTerm)}`)
        .then(response => response.text())
        .then(html => {
            // Update only the table body
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTableBody = doc.querySelector('.bookings-table tbody');
            const currentTableBody = document.querySelector('.bookings-table tbody');
            
            if (newTableBody && currentTableBody) {
                currentTableBody.innerHTML = newTableBody.innerHTML;
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function updateStatus(status, bookingId) {
    fetch('../controller/update_booking_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `booking_id=${bookingId}&status=${status}`
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('Error updating status: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the status');
    });
}

function deleteBooking(bookingId) {
    if (confirm('Are you sure you want to delete this booking?')) {
        fetch('../controller/delete_booking.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `booking_id=${bookingId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the row from the table
                const row = document.querySelector(`tr[data-booking-id="${bookingId}"]`);
                if (row) {
                    row.remove();
                }
                alert('Booking deleted successfully');
            } else {
                alert('Error deleting booking: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the booking');
        });
    }
}
