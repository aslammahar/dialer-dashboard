<!DOCTYPE html>
<html>
<head>
    <title>Opening Dialer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="mb-4">Dialer Connection</h3>
        
        <div class="alert alert-info">
            <p><strong>Important:</strong> Your browser may have blocked the popup. Please click the button below to open the dialer manually.</p>
        </div>
        
        <div class="mb-4">
            <p>We're trying to connect you to the dialer system to make your call.</p>
        </div>
        
        <div class="btn-container">
            <a href="{{ $dialer_url }}" target="_blank" class="btn btn-primary" id="dialerButton">Open Dialer</a>
            <a href="{{ $previous_url }}" class="btn btn-secondary">Return to Previous Page</a>
        </div>
    </div>

    <script>
        // Attempt to automatically open the dialer
        document.addEventListener('DOMContentLoaded', function() {
            // Try to open the dialer in a new tab
            var dialerWindow = window.open('{{ $dialer_url }}', '_blank');
            
            // Check if popup was successful
            if (!dialerWindow || dialerWindow.closed || typeof dialerWindow.closed == 'undefined') {
                console.log('Popup blocked by browser. User needs to click the button manually.');
            } else {
                // If popup was successful, we can redirect after a short delay
                setTimeout(function() {
                    window.location.href = '{{ $previous_url }}';
                }, 2000);
            }
        });
    </script>
</body>
</html>