// Vehicle Canvas
const vehicleCanvas = document.getElementById('vehicleCanvas');
const vehicleCtx = vehicleCanvas.getContext('2d');
let isDrawing = false;
let vehicleImage = new Image();

// Signature Canvas
const signatureCanvas = document.getElementById('signatureCanvas');
const signatureCtx = signatureCanvas.getContext('2d');
let isSigning = false;

// Handle Vehicle Image Upload
const vehicleImageInput = document.getElementById('vehicleImageInput');
vehicleImageInput.addEventListener('change', () => {
    const file = vehicleImageInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            vehicleImage.src = e.target.result;
            vehicleImage.onload = () => {
                vehicleCtx.clearRect(0, 0, vehicleCanvas.width, vehicleCanvas.height);
                vehicleCtx.drawImage(vehicleImage, 0, 0, vehicleCanvas.width, vehicleCanvas.height);
            };
        };
        reader.readAsDataURL(file);
    }
});

// Vehicle Canvas Drawing
vehicleCanvas.addEventListener('mousedown', startDrawing);
vehicleCanvas.addEventListener('mousemove', draw);
vehicleCanvas.addEventListener('mouseup', stopDrawing);
vehicleCanvas.addEventListener('mouseout', stopDrawing);

function startDrawing(e) {
    isDrawing = true;
    draw(e);
}

function draw(e) {
    if (!isDrawing) return;
    const rect = vehicleCanvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    vehicleCtx.beginPath();
    vehicleCtx.arc(x, y, 5, 0, Math.PI * 2);
    vehicleCtx.fillStyle = 'red';
    vehicleCtx.fill();
}

function stopDrawing() {
    isDrawing = false;
}

function clearCanvas() {
    vehicleCtx.clearRect(0, 0, vehicleCanvas.width, vehicleCanvas.height);
    if (vehicleImage.src) {
        vehicleCtx.drawImage(vehicleImage, 0, 0, vehicleCanvas.width, vehicleCanvas.height);
    }
}

// Signature Canvas Drawing
signatureCanvas.addEventListener('mousedown', startSigning);
signatureCanvas.addEventListener('mousemove', sign);
signatureCanvas.addEventListener('mouseup', stopSigning);
signatureCanvas.addEventListener('mouseout', stopSigning);

function startSigning(e) {
    isSigning = true;
    sign(e);
}

function sign(e) {
    if (!isSigning) return;
    const rect = signatureCanvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    signatureCtx.lineTo(x, y);
    signatureCtx.strokeStyle = '#000';
    signatureCtx.lineWidth = 2;
    signatureCtx.stroke();
    signatureCtx.beginPath();
    signatureCtx.moveTo(x, y);
}

function stopSigning() {
    isSigning = false;
    signatureCtx.beginPath();
}

function clearSignature() {
    signatureCtx.clearRect(0, 0, signatureCanvas.width, signatureCanvas.height);
}

// Photo Preview
const photoInput = document.getElementById('photoInput');
const photoPreview = document.getElementById('photoPreview');

photoInput.addEventListener('change', () => {
    photoPreview.innerHTML = '';
    for (let file of photoInput.files) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        photoPreview.appendChild(img);
    }
});

// Form Submission
document.getElementById('reportForm').addEventListener('submit', function(e) {
    // Prevent default form submission
    e.preventDefault();
    
    // Convert canvas images to base64
    const canvasImage = vehicleCanvas.toDataURL('image/png');
    const signatureImage = signatureCanvas.toDataURL('image/png');
    
    // Set the hidden input values
    document.getElementById('canvasImage').value = canvasImage;
    document.getElementById('signatureImage').value = signatureImage;
    
    // Submit the form
    this.submit();
});