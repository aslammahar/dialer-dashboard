<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h2>Leaderboard</h2>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Agent Name</th>
                        <th>Lead ID</th>
                        <th>Dialer ID</th>
                        <th>Team</th>
                        <th>Team Leader</th>
                        <!-- Add more columns as needed -->
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mergedLeads as $lead)
                    <tr>
                        <td>{{ $lead->id }}</td>
                        <td>{{ $lead->agent_name }}</td>
                        <td>{{ $lead->lead_id }}</td>
                        <td>{{ $lead->dialer_id }}</td>
                        <td>{{ $lead->team ? $lead->team->name : 'No Team' }}</td>

                        <td>{{ $lead->team_leader }}</td>
                        <!-- Add more columns as needed -->
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>