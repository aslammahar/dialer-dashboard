@extends('layouts.admin')

@section('page-title')
{{ __('Closers Reports') }}
@endsection

@section('content')

<!-- Wrap the link in a div or any other container element -->
<div class="create-link">
    <a href="{{ route('closer.create') }}" class="btn btn-primary">Create New Policy</a>
</div><br>
@if(\Auth::user()->type == 'Project Manager')

<div class="container">
    <div class="create-link">
        <a href="{{ route('closers.stats') }}" class="btn btn-primary">Closers Stats</a>
    </div>
    <br>

    @endif


<head>

    <!-- ======= Styles ====== -->
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap");

        .cardBox {
            position: relative;
            width: 100%;
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            grid-gap: 30px;
        }

        * {
            font-family: "Ubuntu", sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        .cardBox .card {
            position: relative;
            background: var(--white);
            padding: 30px;
            border-radius: 20px;
            display: flex;
            justify-content: space-between;
            cursor: pointer;
            box-shadow: 0 7px 25px rgba(0, 0, 0, 0.08);
        }

        .cardBox .card .numbers {
            position: relative;
            font-weight: 500;
            font-size: 2.5rem;
            color: var(--blue);

        }

        .cardBox .card .iconBx {
            font-size: 3.5rem;
            color: var(--black2);
            display: flex;
            /* Added */
            align-items: center;
            /* Added */
        }

        .cardBox .card .iconBx ion-icon {
            margin-left: 10px;
            /* Adjust spacing between the content and the icon */
        }

        .cardBox .card .iconBx {
            font-size: 3.5rem;
            color: var(--black2);
        }

        .cardBox .card:hover {
            background: var(--blue);
        }

        .cardBox .card:hover .numbers,
        .cardBox .card:hover .cardName,
        .cardBox .card:hover .iconBx {
            color: var(--white);
        }


        .details .cardHeader {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .cardHeader h2 {
            font-weight: 600;
            color: var(--blue);
        }

        .cardHeader .btn {
            position: relative;
            padding: 5px 10px;
            background: var(--blue);
            text-decoration: none;
            color: var(--white);
            border-radius: 6px;
        }

        :root {
            --blue: #2a2185;
            --white: #fff;
            --gray: #f5f5f5;
            --black1: #222;
            --black2: #999;
        }

        .container {
            position: relative;
            width: 100%;
        }

        body {
            min-height: 100vh;
            overflow-x: hidden;
        }

        .navigation {
            position: fixed;
            width: 300px;
            height: 100%;
            background: var(--blue);
            border-left: 10px solid var(--blue);
            transition: 0.5s;
            overflow: hidden;
        }

        .main {
            position: absolute;
            width: calc(100% - 300px);
            left: 300px;
            min-height: 100vh;
            background: var(--white);
            transition: 0.5s;
        }

        .main.active {
            width: calc(100% - 80px);
            left: 80px;
        }

        .topbar {
            width: 100%;
            height: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 10px;
        }

        .toggle {
            position: relative;
            width: 60px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 2.5rem;
            cursor: pointer;
        }

        .details .cardHeader {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .cardHeader h2 {
            font-weight: 600;
            color: var(--blue);
        }

        .cardHeader .btn {
            position: relative;
            padding: 5px 10px;
            background: var(--blue);
            text-decoration: none;
            color: var(--white);
            border-radius: 6px;
        }
    </style>

</head>





    <!-- card box starts here -->


    <h4>Overall Reports</h4>
    <div class="cardBox">
        <div class="card">
            <div>
                <div class="numbers">{{ $pendingCount }}</div>
                <div class="cardName">Total Pending Policies</div>
                <div class="iconBx">
                    <ion-icon name="hourglass-outline"></ion-icon>



                </div>
            </div>

        </div>

        <div class="card">
            <div>
                <div class="numbers">{{ $approvedCount }}</div>
                <div class="cardName">Total Approved Policies</div>
            </div>

            <div class="iconBx">
                <ion-icon name="checkmark-done-outline"></ion-icon>

            </div>
        </div>

        <div class="card">
            <div>
                <div class="numbers">{{ $rejected }}</div>
                <div class="cardName">Total Rejected Policies</div>
            </div>

            <div class="iconBx">
                <ion-icon name="close-circle-outline"></ion-icon>


            </div>
        </div>

        <div class="card">
            <div>
                <div class="numbers">{{ $funded }}</div>
                <div class="cardName">Total Funded Policies</div>
            </div>

            <div class="iconBx">
                <ion-icon name="wallet-outline"></ion-icon>

            </div>
        </div>
        <div class="card">
            <div>
                <div class="numbers">{{ $charged_backed }}</div>
                <div class="cardName">Total Charged Backed Policies</div>
            </div>

            <div class="iconBx">
                <ion-icon name="sync-outline"></ion-icon>

            </div>
        </div>
        <div class="card">
            <div>
                <div class="numbers">{{ $DNF }}</div>
                <div class="cardName">Total DNF Policies</div>
            </div>

            <div class="iconBx">
                <ion-icon name="alert-outline"></ion-icon>


            </div>
        </div>
        <div class="card">
            <div>
                <div class="numbers">{{ $Cancelled }}</div>
                <div class="cardName">Total Cancelled Policies</div>
            </div>

            <div class="iconBx">
                <ion-icon name="remove-circle-outline"></ion-icon>


            </div>
        </div>
        <div class="card">
            <div>
                <div class="numbers">{{ $NSF }}</div>
                <div class="cardName">Total NSF Policies</div>
            </div>

            <div class="iconBx">
                <ion-icon name="cash-outline"></ion-icon> <!-- or any banknote icon -->



            </div>
        </div>
        <div class="card">
            <div>
                <div class="numbers">{{ $DNC }}</div>
                <div class="cardName">Total DNC Policies</div>
            </div>

            <div class="iconBx">
                <ion-icon name="person-remove-outline"></ion-icon>


            </div>
        </div>
        <div class="card">
            <div>
                <div class="numbers">{{ $Underwriting }}</div>
                <div class="cardName">Total Underwriting Policies</div>
            </div>

            <div class="iconBx">
                <ion-icon name="document-outline"></ion-icon>




            </div>
        </div>
        <div class="card">
            <div>
                <div class="numbers">{{ $NeedtoReach }}</div>
                <div class="cardName">Total NeedtoReach Policies</div>
            </div>

            <div class="iconBx">
                <ion-icon name="call-outline"></ion-icon> <!-- or any telephone icon -->


            </div>
        </div>

    </div>




    <!-- card box ends here -->










    <div class="row">






        <div class="col-md-6">
            <h4 class="card-title">Daily Leaderboard</h4>
            <div class="card">
                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>Closers</th>
                                    <th>Total Closed Calls</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usersWithTotalClosedCalls as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->closed_calls_count }}</td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-md-6">
            <h4 class="card-title">Monthly Leaderboard</h4>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Closers</th>
                                    <th>Total Closed Calls</th>
                                    <!-- Add more columns for monthly statistics if needed -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($closerUsersWithTotalClosedCalls as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->closed_calls_count }}</td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>




    </div>




    <!-- monthly reports starts here -->
    <h4>Monthly Closers Reports</h4>

    <div class="col-md-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Total Closed Calls</th>
                    <th>Pending</th>
                    <th>Approved</th>
                    <th>Rejected</th>
                    <th>Funded</th>
                    <th>Charged Backed</th>
                    <th>DNF</th>
                    <th>Cancelled</th>
                    <th>NSF</th>
                    <th>DNC</th>
                    <th>Underwriting</th>
                    <th>Need to Reach</th>
                    <!-- Add more columns for other statuses if needed -->
                </tr>
            </thead>
            <tbody>
                @foreach ($userStats as $stat)
                <tr>
                    <td>{{ $stat->closer->name }}</td> <!-- Assuming there's a relationship to retrieve the closer's name -->
                    <td>{{ $stat->total }}</td>
                    <td>{{ $stat->pending }}</td>
                    <td>{{ $stat->approved }}</td>
                    <td>{{ $stat->rejected }}</td>
                    <td>{{ $stat->funded }}</td>
                    <td>{{ $stat->charged_backed }}</td>
                    <td>{{ $stat->DNF }}</td>
                    <td>{{ $stat->Cancelled }}</td>
                    <td>{{ $stat->NSF }}</td>
                    <td>{{ $stat->DNC }}</td>
                    <td>{{ $stat->Underwriting }}</td>
                    <td>{{ $stat->NeedtoReach }}</td>
                    <!-- Add more cells for other statuses if needed -->
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- monthly reports ends here -->

</div>


<script>
    // add hovered class to selected list item
    let list = document.querySelectorAll(".navigation li");

    function activeLink() {
        list.forEach((item) => {
            item.classList.remove("hovered");
        });
        this.classList.add("hovered");
    }

    list.forEach((item) => item.addEventListener("mouseover", activeLink));

    // Menu Toggle
    let toggle = document.querySelector(".toggle");
    let navigation = document.querySelector(".navigation");
    let main = document.querySelector(".main");

    toggle.onclick = function() {
        navigation.classList.toggle("active");
        main.classList.toggle("active");
    };
</script>


<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>


@endsection