function searchReports() {
    const searchValue = document.getElementById('searchInput').value;
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../controller/dmg_report_search.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            document.getElementById('reportsBody').innerHTML = xhr.responseText;
            // Update total reports count
            const total = document.querySelectorAll('#reportsBody tr').length;
            document.getElementById('totalReports').textContent = total;
        }
    };
    xhr.send('search=' + encodeURIComponent(searchValue));
}

function deleteReport(id) {
    if (confirm('Are you sure you want to delete report ID ' + id + '?')) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../controller/dmg_Report_delete.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                alert(xhr.responseText);
                window.location.reload(); // Refresh page to update table
            }
        };
        xhr.send('id=' + id);
    }
}