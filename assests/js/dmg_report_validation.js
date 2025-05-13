document.querySelector(".submit-btn").addEventListener("click", function (e) {
  e.preventDefault(); // prevent default submission

  const photoInput = document.getElementById("photoUpload");
  const signatureInput = document.getElementById("signatureInput");

  const photoFiles = photoInput.files;
  const signatureFile = signatureInput.files[0];

  let errors = [];

  // Check at least one photo is uploaded
  if (!photoFiles.length) {
    errors.push("Please upload at least one photo.");
  }

  // Check signature file uploaded
  if (!signatureFile) {
    errors.push("Please upload a digital signature.");
  }

  // Check file types (image validation)
  const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];
  for (let file of photoFiles) {
    if (!allowedTypes.includes(file.type)) {
      errors.push("All uploaded photos must be JPG or PNG images.");
      break;
    }
  }

  if (signatureFile && !allowedTypes.includes(signatureFile.type)) {
    errors.push("Signature must be a JPG or PNG image.");
  }

  // Display or submit
  if (errors.length > 0) {
    alert(errors.join("\n"));
  } else {
    document.getElementById("damageReportForm").submit();
  }
});

