<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Lead Submission</title>
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
            text-align: center;
        }

        .error-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 40px;
            color: white;
        }

        .error-icon.duplicate { background-color: #f56565; }
        .error-icon.database { background-color: #ed8936; }
        .error-icon.creation { background-color: #805ad5; }
        .error-icon.unexpected { background-color: #4a5568; }

        h1 {
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .error-message {
            font-size: 1.125rem;
            color: #4a5568;
            margin-bottom: 1.5rem;
        }

        .lead-id {
            background-color: #f7fafc;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            display: inline-block;
            font-family: monospace;
            color: #2d3748;
            margin-bottom: 1.5rem;
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
        }

        .btn:hover {
            background-color: #3182ce;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(66, 153, 225, 0.3);
        }

        .error-details {
            margin-top: 1.5rem;
            padding: 1rem;
            background-color: #f7fafc;
            border-radius: 8px;
            text-align: left;
        }

        .error-details h3 {
            font-size: 1rem;
            color: #4a5568;
            margin-bottom: 0.5rem;
        }

        .error-details p {
            font-size: 0.875rem;
            color: #718096;
            margin: 0;
        }

        @media (max-width: 640px) {
            .container {
                padding: 1.5rem;
            }
            
            h1 {
                font-size: 1.5rem;
            }
            
            .error-message {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon {{ $error_type }}">
            @switch($error_type)
                @case('duplicate')
                    ⚠️
                    @break
                @case('duplicate_dialer_id')
                    ⚠️
                    @break
                @case('database_error')
                @case('database_duplicate')
                    🔄
                    @break
                @case('creation_failed')
                    ❌
                    @break
                @default
                    ⚠️
            @endswitch
        </div>

        <h1>
            @switch($error_type)
                @case('duplicate')
                    Duplicate Lead Detected
                    @break
                @case('duplicate_dialer_id')
                    Duplicate Dialer ID Detected
                    @break
                @case('database_error')
                    Database Error
                    @break
                @case('database_duplicate')
                    Duplicate Database Entry
                    @break
                @case('creation_failed')
                    Lead Creation Failed
                    @break
                @default
                    Error Occurred
            @endswitch
        </h1>

        <div class="error-message">
            {{ $error_message }}
        </div>

        @if(isset($lead_id))
            <div class="lead-id">
                Lead ID: {{ $lead_id }}
            </div>
        @endif
        @if(isset($dialer_id))
            <div class="lead-id">
                Dialer ID: {{ $dialer_id }}
            </div>
        @endif

        <div class="error-details">
            <h3>What to do next?</h3>
            <p>
                @switch($error_type)
                    @case('duplicate')
                        This lead has already been submitted to the system. Please verify the lead ID and try again with a different lead.
                        @break
                    @case('duplicate_dialer_id')
                        This dialer ID has already been taken. Please verify the dialer ID and try again with a different one.
                        @break
                    @case('database_error')
                        There was an issue with the database. Please try again in a few moments. If the problem persists, contact support.
                        @break
                    @case('database_duplicate')
                        A duplicate entry was found in the database. Please verify the lead details and try again.
                        @break
                    @case('creation_failed')
                        The lead creation process failed. Please check your input and try again.
                        @break
                    @default
                        An unexpected error occurred. Please try again or contact support if the problem persists.
                @endswitch
            </p>
        </div>

        <a href="{{ url()->previous() }}" class="btn">Go Back</a>
    </div>
</body>
</html>
