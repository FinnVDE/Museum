// QR code generation behind the table using QRCode.js

document.addEventListener('DOMContentLoaded', function () {
    // Create a container div for the background QR code
    const dashboardContainer = document.querySelector('.dashboard-container');
    if (!dashboardContainer) return;

    const qrBackgroundDiv = document.createElement('div');
    qrBackgroundDiv.id = 'qr-background';
    qrBackgroundDiv.style.position = 'absolute';
    qrBackgroundDiv.style.top = '50%';
    qrBackgroundDiv.style.left = '50%';
    qrBackgroundDiv.style.transform = 'translate(-50%, -50%)';
    qrBackgroundDiv.style.opacity = '0.1';
    qrBackgroundDiv.style.zIndex = '0';
    qrBackgroundDiv.style.pointerEvents = 'none';

    dashboardContainer.style.position = 'relative';
    dashboardContainer.prepend(qrBackgroundDiv);

    // Generate QR code with some sample data or URL
    // You can customize the data to encode here
    const qrData = window.location.href;

    // Load QRCode.js library dynamically
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
    script.onload = function () {
        new QRCode(qrBackgroundDiv, {
            text: qrData,
            width: 300,
            height: 300,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    };
    document.body.appendChild(script);
});
