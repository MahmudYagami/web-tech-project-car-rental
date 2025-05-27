function toggleProfileDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

// Close dropdowns when clicking outside
window.onclick = function(event) {
    if (!event.target.matches('.profile-icon') && !event.target.matches('.profile-icon *')) {
        const profileDropdown = document.getElementById('profileDropdown');
        if (profileDropdown.style.display === 'block') {
            profileDropdown.style.display = 'none';
        }
    }
}

// Set active nav link based on current page
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const notificationIcon = document.getElementById('notificationIcon');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const notificationBadge = document.getElementById('notificationBadge');
    const notificationList = document.getElementById('notificationList');

    // Toggle notification dropdown
    notificationIcon.addEventListener('click', function(e) {
        e.stopPropagation();
        notificationDropdown.style.display = notificationDropdown.style.display === 'block' ? 'none' : 'block';
        loadNotifications();
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!notificationDropdown.contains(e.target) && e.target !== notificationIcon) {
            notificationDropdown.style.display = 'none';
        }
    });

    // Load notifications
    function loadNotifications() {
        fetch('../controller/get_notifications.php')
            .then(response => response.json())
            .then(data => {
                notificationList.innerHTML = '';
                let unreadCount = 0;

                if (data.length === 0) {
                    const emptyMessage = document.createElement('div');
                    emptyMessage.className = 'notification-item';
                    emptyMessage.innerHTML = '<div class="notification-message">No notifications</div>';
                    notificationList.appendChild(emptyMessage);
                } else {
                    data.forEach(notification => {
                        const item = document.createElement('div');
                        item.className = `notification-item ${notification.is_read ? '' : 'unread'} ${notification.type.toLowerCase()}`;
                        
                        const type = document.createElement('div');
                        type.className = 'notification-type';
                        type.textContent = notification.type;
                        
                        const message = document.createElement('div');
                        message.className = 'notification-message';
                        message.textContent = notification.message;
                        
                        const time = document.createElement('div');
                        time.className = 'notification-time';
                        time.textContent = new Date(notification.created_at).toLocaleString();
                        
                        item.appendChild(type);
                        item.appendChild(message);
                        item.appendChild(time);
                        notificationList.appendChild(item);

                        if (!notification.is_read) {
                            unreadCount++;
                        }
                    });
                }

                notificationBadge.textContent = unreadCount;
                notificationBadge.style.display = unreadCount > 0 ? 'block' : 'none';
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                notificationList.innerHTML = '<div class="notification-item"><div class="notification-message">Error loading notifications</div></div>';
            });
    }

    // Initial load of notifications
    loadNotifications();

    // Refresh notifications every 30 seconds
    setInterval(loadNotifications, 30000);
});