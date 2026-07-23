<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>approved leads </title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Include DataTables CSS -->
    <link href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">

    <!-- Include DataTables JavaScript -->
    @include('teams.report_css')

    <script>
        $(document).ready(function() {
            $('#alter').DataTable();
        });
    </script>
</head>

<body>
    <!-- Add a beautiful header here -->
    <header>
        <h1>Approved Leads Report</h1>
    </header>

    <!-- Navigation links -->
    <?php
    if (Auth::user()->role == 'Team Lead') {
    ?>
        <a href="teams-create"><u>Create New Team</u></a><br>
        <a href="team-assignment"><u>Assign Agents</u></a><br>
        <a href="teams-overview"><u>Remove Agents</u> </a><br>
        <a href="list-teams"><u>Teams Management</u> </a><br>

    <?php
    } else {
    ?>
        <a href="leaderboard"> <u>Leaderboard</u></a><br>

        <a href="agent-reports"><u>Agent Reports</u> </a><br>
        <a href="rejected-reports"><u>Rejected Leads Reports</u> </a><br> <br>
    <?php
    }
    ?>



    <div style="overflow-x: auto;">
        <table id="alter" class="display">
            <thead>
                <tr>
                    <th>Agent Name</th>
                    @foreach ($dates as $date)
                    <th>{{ $date }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($agents as $agent)
                <tr>
                    <td>{{ $agent->name }}</td>
                    @foreach ($dates as $date)
                    <td>
                        {{ $mergedLeadsCount
                            ->where('agent_id', $agent->id)
                            ->where('date', $date)
                            ->first()
                            ->total_lead_count ?? 0
                        }}
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</body>

</html>