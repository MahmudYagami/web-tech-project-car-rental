<?php
require_once '../model/db.php';
require_once '../model/dmgreport_model.php';

if (isset($_POST['search'])) {
    $result = searchDamageReports($conn, $_POST['search']);
    
    if ($result['success']) {
        if (empty($result['data'])) {
            echo '<tr><td colspan="7">No reports found</td></tr>';
        } else {
            foreach ($result['data'] as $report) {
                echo formatReportRow($report);
            }
        }
    } else {
        echo '<tr><td colspan="7">' . htmlspecialchars($result['message']) . '</td></tr>';
    }
} else {
    echo '<tr><td colspan="7">No search term provided</td></tr>';
}

mysqli_close($conn);
?>