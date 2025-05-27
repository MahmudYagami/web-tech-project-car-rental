
function showMessage(message, type) {
    const messageDiv = document.getElementById('message');
    if (!messageDiv) {
        // Create message div if it doesn't exist
        const div = document.createElement('div');
        div.id = 'message';
        div.style.padding = '10px';
        div.style.margin = '10px 0';
        div.style.borderRadius = '4px';
        document.querySelector('.preferences-section').insertBefore(div, document.querySelector('.preferences-section').firstChild);
    }
    
    const messageElement = document.getElementById('message');
    messageElement.textContent = message;
    messageElement.style.backgroundColor = type === 'success' ? '#d4edda' : '#f8d7da';
    messageElement.style.color = type === 'success' ? '#155724' : '#721c24';
    
    // Remove message after 3 seconds
    setTimeout(() => {
        messageElement.remove();
    }, 3000);
} 