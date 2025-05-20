<?php
require_once '../model/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Damage Reports</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
      body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
    background-color: #f4f4f4;
}

.container {
    max-width: 800px;
    margin: 0 auto;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1, h2 {
    text-align: center;
    color: #333;
}

.section {
    margin-bottom: 20px;
}

canvas {
    border: 1px solid #ccc;
    display: block;
    margin: 10px auto;
    background: #fff;
}

button {
    padding: 10px 20px;
    margin: 10px;
    background: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

button:hover {
    background: #0056b3;
}

#photoPreview {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

#photoPreview img {
    max-width: 100px;
    height: auto;
    border: 1px solid #ccc;
}

input[type="file"] {
    margin: 10px 0;
    display: block;
}
    </style>
</head>
<body>
    <div class="container">
        <h1>Vehicle Damage Report</h1>
        <form id="reportForm" action="..\controller\dmg_report_check.php" method="POST" enctype="multipart/form-data">
            <!-- Vehicle Diagram Canvas -->
            <div class="section">
                <h2>Mark Damage</h2>
                <input type="file" id="vehicleImageInput" accept="image/*">
                <canvas id="vehicleCanvas" width="500" height="300"></canvas>
                <input type="hidden" name="canvas_image" id="canvasImage">
                <button type="button" onclick="clearCanvas()">Clear Canvas</button>
            </div>

            <!-- Photo Upload -->
            <div class="section">
                <h2>Upload Photos</h2>
                <input type="file" name="photos[]" id="photoInput" multiple accept="image/*">
                <div id="photoPreview"></div>
            </div>

            <!-- Digital Signature -->
            <div class="section">
                <h2>Customer Signature</h2>
                <canvas id="signatureCanvas" width="400" height="150"></canvas>
                <input type="hidden" name="signature_image" id="signatureImage">
                <button type="button" onclick="clearSignature()">Clear Signature</button>
            </div>

            <!-- Submit Button -->
            <button type="submit">Submit Report</button>
        </form>
    </div>

    <script src="..\assets\js\dmg_report_validation.js"></script>
</body>
</html>