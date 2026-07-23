<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password Required</title>
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

        .warning-icon {
            width: 80px;
            height: 80px;
            background-color: #f59e0b;
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

        .warning-icon::after {
            content: "!";
            color: white;
            font-size: 40px;
            font-weight: bold;
        }

        h2 {
            font-size: 1.875rem;
            font-weight: 700;
            color: #2d3748;
            text-align: center;
            margin-bottom: 1rem;
        }

        p {
            text-align: center;
            color: #4a5568;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            padding: 0.875rem 1.5rem;
            background-color: #4299e1;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-align: center;
            width: 100%;
            box-sizing: border-box;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="warning-icon"></div>
        <h2>Password Change Required</h2>
        <p>For security reasons, you need to change your password before continuing. Please click the button below to proceed to the password change page.</p>
        <a href="{{ route('profile', Auth::user()->id) }}" class="btn">Change Password Now</a>
    </div>
</body>
</html> 