<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Damage Report</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f5f5f5;
      margin: 0;
      padding: 0;
    }

    header {
      background: #333;
      color: white;
      padding: 20px;
      text-align: center;
    }

    main {
      max-width: 1200px;
      margin: auto;
      padding: 30px;
      background: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    section {
      margin-bottom: 40px;
    }

    h2 {
      margin-bottom: 15px;
      color: #444;
    }

    .canvas-container {
      border: 2px dashed #999;
      height: 400px;
      display: flex;
      justify-content: center;
      align-items: center;
      background: #fafafa;
      border-radius: 10px;
    }

    .canvas-placeholder {
      color: #888;
      font-size: 18px;
    }

    .photo-upload input,
    .signature input {
      display: block;
      margin-top: 10px;
    }

    label {
      font-weight: bold;
      margin-top: 10px;
      display: block;
    }

    .submit-btn {
      background-color: #28a745;
      color: white;
      border: none;
      padding: 14px 24px;
      font-size: 16px;
      border-radius: 6px;
      cursor: pointer;
    }

    .submit-btn:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>
  <form id="damageReportForm" action="..\..\control\login_check.php" method="POST" enctype="multipart/form-data">
   <header>
    <h1>Vehicle Damage Report</h1>
  </header>

  <main>
    <!-- Vehicle Inspection Canvas -->
    <section>
      <h2>Vehicle Inspection Tool</h2>
      <div class="canvas-container">
        <div class="canvas-placeholder">[Canvas Placeholder — Add diagram/annotation tool here]</div>
      </div>
    </section>

    <!-- Photo Upload -->
    <section class="photo-upload">
      <h2>Upload Timestamped Photos</h2>
      <label for="photoUpload">Add Photos:</label>
      <input type="file" id="photoUpload" name="photos" accept="image/*" multiple />
    </section>

    <!-- Digital Signature -->
    <section class="signature">
      <h2>Customer Digital Signature</h2>
      <label for="signatureInput">Sign Here (Upload or Draw later with JS):</label>
      <input type="file" id="signatureInput" name="signature" accept="image/*" />
    </section>

    <section>
      <button class="submit-btn">Submit Report</button>
    </section>
  </main>
</form>
<script src="..\..\assests\js\dmg_report_validation.js"></script>
</body>
</html>
