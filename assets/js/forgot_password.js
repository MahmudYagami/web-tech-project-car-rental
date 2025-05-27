document.getElementById('resetForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('../controller/reset_password.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const messageArea = document.getElementById('message-area');
        messageArea.textContent = data.message;
        messageArea.className = 'message-area ' + (data.status === 'success' ? 'success-message' : 'error-message');
        
        if (data.status === 'success') {
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('message-area').textContent = 'An error occurred. Please try again.';
        document.getElementById('message-area').className = 'message-area error-message';
    });
}); 