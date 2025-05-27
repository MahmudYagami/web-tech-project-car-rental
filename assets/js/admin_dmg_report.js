// Search functionality
function searchReports() {
    const searchTerm = document.getElementById('searchInput').value;
    
    fetch(`../controller/admin_damage_report_controller.php?search=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateReportsTable(data.data);
                // Update total reports count
                document.getElementById('totalReports').textContent = data.data.length;
            } else {
                alert('Error searching reports: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while searching reports');
        });
}

// Add event listener for search input
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            searchReports();
        });
    }
});

// Update the reports table with new data
function updateReportsTable(reports) {
    const tbody = document.getElementById('reportsBody');
    tbody.innerHTML = '';

    reports.forEach(report => {
        const row = document.createElement('tr');
        row.setAttribute('data-report-id', report.id);
        row.innerHTML = `
            <td>${report.id}</td>
            <td>${report.timestamp}</td>
            <td>${report.email}</td>
            <td>
                <img src="../${report.canvas_image}" alt="Canvas" class="thumbnail">
            </td>
            <td>
                <img src="../${report.signature_image}" alt="Signature" class="thumbnail">
            </td>
            <td>
                ${report.photo_images ? 
                    report.photo_images.map(photo => 
                        `<img src="../${photo}" alt="Photo" class="thumbnail">`
                    ).join('') : 
                    'No photos'}
            </td>
            <td>
                <a href="view_Admin_dmg_report.php?id=${report.id}" class="btn view-btn">View</a>
                <button onclick="deleteReport(${report.id})" class="btn delete-btn">Delete</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Delete report functionality
function deleteReport(reportId) {
    if (confirm('Are you sure you want to delete this report?')) {
        fetch('../controller/dmg_Report_delete.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${reportId}`
        })
        .then(response => response.text())
        .then(message => {
            if (message.includes('successfully')) {
                // Remove the row from the table
                const row = document.querySelector(`tr[data-report-id="${reportId}"]`);
                if (row) {
                    row.remove();
                }
                
                // Update total reports count
                const totalReportsElement = document.getElementById('totalReports');
                if (totalReportsElement) {
                    const currentTotal = parseInt(totalReportsElement.textContent);
                    totalReportsElement.textContent = currentTotal - 1;
                }
                
                // Show success message
                alert(message);
            } else {
                alert(message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the report');
        });
    }
}