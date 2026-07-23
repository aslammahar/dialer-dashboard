<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #2d3748;
        }

        .container {
            background-color: #ffffff;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 600px;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background-color: #48bb78;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .success-icon::after {
            content: "✓";
            color: white;
            font-size: 40px;
            font-weight: bold;
        }

        h2 {
            font-size: 1.875rem;
            font-weight: 700;
            color: #2d3748;
            text-align: center;
            margin-bottom: 2rem;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 0.75rem;
        }

        .table tr {
            background-color: #f7fafc;
            transition: transform 0.2s ease;
        }

        .table tr:hover {
            transform: translateX(5px);
        }

        .table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #4a5568;
            width: 40%;
        }

        .table td {
            padding: 1rem;
            color: #2d3748;
            font-weight: 500;
        }

        .btn {
            display: inline-block;
            padding: 0.875rem 1.5rem;
            background-color: #4299e1;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            margin-top: 1.5rem;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn:hover {
            background-color: #3182ce;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(66, 153, 225, 0.3);
        }

        @media (max-width: 640px) {
            .container {
                padding: 1.5rem;
            }
            
            .table th, .table td {
                padding: 0.75rem;
                font-size: 0.875rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="success-icon"></div>
        <h2>Lead Submitted Successfully!</h2>
        <table class="table">
            <tr>
                <th>Agent Dialer ID</th>
                <td>{{ $lead->dialer_id }}</td>
            </tr>
            <tr>
                <th>Lead ID</th>
                <td>{{ $lead->lead_id }}</td>
            </tr>
            <tr>
                <th>Agent Name</th>
                <td>{{ $lead->agent_name ?? 'No Agent Name' }}</td>
            </tr>
            <tr>
                <th>Customer Age</th>
                <td>{{ $lead->AGE }}</td>
            </tr>
            <tr>
                <th>Submitted Time</th>
                <td>{{ $lead->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
        </table>
        <a href="/" class="btn">Return to Home</a>
    </div>
</body>

</html>