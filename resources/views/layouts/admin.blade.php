@php
    use App\Models\Utility;

        //$logo=asset(Storage::url('uploads/logo/'));
           $logo=\App\Models\Utility::get_file('uploads/logo');

        $company_favicon=Utility::getValByName('company_favicon');
        $setting = \App\Models\Utility::colorset();
        $company_logo = \App\Models\Utility::GetLogo();
        $mode_setting = \App\Models\Utility::mode_layout();
        $color = (!empty($setting['color'])) ? $setting['color'] : 'theme-3';
        $SITE_RTL = Utility::getValByName('SITE_RTL');
         $lang=Utility::getValByName('default_language');


@endphp
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{$SITE_RTL == 'on' ? 'rtl' : '' }}">


<head>
    <title>{{(Utility::getValByName('title_text')) ? Utility::getValByName('title_text') : config('app.name', 'J.Sons CRM')}} - @yield('page-title')</title>
    <script src="{{ asset('assets/fonts/html5shiv.js') }}"></script>
    <script src="{{ asset('assets/fonts/respond.min.js') }}"></script>
    <script src="{{ asset('assets/fonts/capex.js') }}"></script>
    <script src="{{ asset('js/apexcharts.min.js') }}"></script>

    <!-- Meta -->
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="url" content="{{ url('').'/'.config('chatify.path') }}" data-user="{{ Auth::user()->id }}">
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">


    <!-- Favicon icon -->
  



<link rel="icon" href="{{ asset('mt-demo/80100/80168/mt-content/uploads/2019/04/favicon.ico') }}">


    <!-- Calendar-->
    @stack('css-page')
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/main.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/flatpickr.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/plugins/animate.min.css') }}">



    <!-- font css -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">

    <!--bootstrap switch-->
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/bootstrap-switch-button.min.css') }}">

    <!-- vendor css -->
    @if ($SITE_RTL == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
    @endif
   
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
   
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/customizer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}" id="main-style-link">

    @stack('css-page')
</head>
<body oncontextmenu="return false;" style="background-image: url('assets/images/light-box/abc.jpg');">

<!-- [ Pre-loader ] start -->
<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>



<!-- Reminder Modal -->

@include('partials.admin.menu')
<!-- [ navigation menu ] end -->
<!-- [ Header ] start -->
@include('partials.admin.header')
<style>
    /* Modal background */
    #global-reminder-modal {
        display: none; /* Initially hidden */
        position: fixed;
       
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    /* Modal container */
    #global-reminder-container {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        max-width: 400px;
        width: 90%;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        position: relative;
    }

    /* Buttons */
    .global-reminder-btn {
        padding: 10px 20px;
        margin: 5px;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        font-size: 16px;
    }

    .complete {
        background-color: #28a745;
        color: white;
    }

    .dismiss {
        background-color: #dc3545;
        color: white;
    }

    /* Close Button */
    #global-reminder-close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 20px;
        cursor: pointer;
    }

    /* Fade-in animation */
    .show-modal {
        display: flex !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

</style>


<style>
       
        
        #global-reminder-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        
        #global-reminder-modal.show {
            opacity: 1;
            visibility: visible;
        }
        
        #global-reminder-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            width: 350px;
            box-shadow: 0px 0px 15px rgba(255, 255, 255, 0.2);
            transform: scale(0.8);
            transition: transform 0.3s ease-in-out;
            animation: glowing 2s infinite alternate;
        }
        
        @keyframes glowing {
            from { box-shadow: 0px 0px 10px rgba(255, 255, 255, 0.2); }
            to { box-shadow: 0px 0px 20px rgba(255, 255, 255, 0.6); }
        }
        
        #global-reminder-modal.show #global-reminder-container {
            transform: scale(1);
        }
        
        #global-reminder-close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: white;
        }
        
        #global-reminder-title {
            font-size: 24px;
            font-weight: bold;
            color: white;
            margin-bottom: 10px;
        }
        
        #global-reminder-description {
            color: white;
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .global-reminder-actions {
            display: flex;
            justify-content: space-around;
        }
        
        .global-reminder-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .global-reminder-btn:hover {
            transform: scale(1.1);
        }
        
        .complete {
            background: #28a745;
            color: white;
        }
        
        .dismiss {
            background: #dc3545;
            color: white;
        }
        
        .reminder-icon {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
        }
        
        .animator-character {
            width: 100px;
            height: 100px;
            margin-top: 15px;
            animation: bounce 1.5s infinite alternate;
        }
        
        @keyframes bounce {
            from { transform: translateY(0); }
            to { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <div id="global-reminder-modal">
        <div id="global-reminder-container">
            <span id="global-reminder-close">&times;</span>
            <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Reminder Icon" class="reminder-icon">
            <h2 id="global-reminder-title">Reminder</h2>
            <p id="global-reminder-description">Don't forget to complete your task!</p>
            <div class="global-reminder-actions">
                <button id="global-reminder-complete" class="global-reminder-btn complete">Complete</button>
                <button id="global-reminder-dismiss" class="global-reminder-btn dismiss">Dismiss</button>
            </div>
        </div>
    </div>

    
<script>
  document.addEventListener("DOMContentLoaded", function () {
    console.log("Reminder modal script loaded.");

    const modal = document.getElementById("global-reminder-modal");
    const titleEl = document.getElementById("global-reminder-title");
    const descriptionEl = document.getElementById("global-reminder-description");
    const completeBtn = document.getElementById("global-reminder-complete");
    const dismissBtn = document.getElementById("global-reminder-dismiss");
    const closeBtn = document.getElementById("global-reminder-close");

    let currentReminderId = null;

    function fetchActiveReminders() {
        console.log("Fetching active reminders...");

        fetch("/reminders/active", {
            method: "GET",
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
            .then(response => response.json())
            .then(reminders => {
                console.log("Reminders received:", reminders);
                if (reminders.length > 0) {
                    showReminderModal(reminders[0]);
                }
            })
            .catch(error => console.error("Error fetching reminders:", error));
    }

    function showReminderModal(reminder) {
        console.log("Showing reminder modal");
        currentReminderId = reminder.id;
        titleEl.textContent = reminder.title || "Reminder";
        descriptionEl.textContent = reminder.description || "No description provided";

        modal.classList.add("show-modal"); // Apply fade-in effect
    }

    function hideReminderModal() {
        modal.classList.remove("show-modal"); // Hide modal
    }

    function markReminderAction(action) {
        if (!currentReminderId) return;

        console.log(`Marking reminder as ${action}...`);

        fetch(`/reminders/${currentReminderId}/${action}`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
            .then(response => response.json())
            .then(() => {
                console.log("Reminder action successful");
                hideReminderModal();
                fetchActiveReminders();
            })
            .catch(error => {
                console.error(`Error marking reminder as ${action}:`, error);
                alert(`Failed to ${action} reminder. Please try again.`);
            });
    }

    // Attach event listeners
    completeBtn.addEventListener("click", () => markReminderAction("complete"));
    dismissBtn.addEventListener("click", () => markReminderAction("cancel"));
    closeBtn.addEventListener("click", hideReminderModal);

    // Fetch reminders on load
    fetchActiveReminders();
    setInterval(fetchActiveReminders, 5 * 60 * 1000); // Check every 5 minutes
});

</script>

<!-- Modal -->
<div class="modal notification-modal fade"
     id="notification-modal"
     tabindex="-1"
     role="dialog"
     aria-hidden="true"
>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button
                    type="button"
                    class="btn-close float-end"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
                <h6 class="mt-2">
                    <i data-feather="monitor" class="me-2"></i>Desktop settings
                </h6>
                <hr/>
                <div class="form-check form-switch">
                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="pcsetting1"
                        checked
                    />
                    <label class="form-check-label f-w-600 pl-1" for="pcsetting1"
                    >Allow desktop notification</label
                    >
                </div>
                <p class="text-muted ms-5">
                    you get lettest content at a time when data will updated
                </p>
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="pcsetting2"/>
                    <label class="form-check-label f-w-600 pl-1" for="pcsetting2"
                    >Store Cookie</label
                    >
                </div>
                <h6 class="mb-0 mt-5">
                    <i data-feather="save" class="me-2"></i>Application settings
                </h6>
                <hr/>
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="pcsetting3"/>
                    <label class="form-check-label f-w-600 pl-1" for="pcsetting3"
                    >Backup Storage</label
                    >
                </div>
                <p class="text-muted mb-4 ms-5">
                    Automaticaly take backup as par schedule
                </p>
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="pcsetting4"/>
                    <label class="form-check-label f-w-600 pl-1" for="pcsetting4"
                    >Allow guest to print file</label
                    >
                </div>
                <h6 class="mb-0 mt-5">
                    <i data-feather="cpu" class="me-2"></i>System settings
                </h6>
                <hr/>
                <div class="form-check form-switch">
                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="pcsetting5"
                        checked
                    />
                    <label class="form-check-label f-w-600 pl-1" for="pcsetting5"
                    >View other user chat</label
                    >
                </div>
                <p class="text-muted ms-5">Allow to show public user message</p>
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-light-danger btn-sm"
                    data-bs-dismiss="modal"
                >
                    Close
                </button>
                <button type="button" class="btn btn-light-primary btn-sm">
                    Save changes
                </button>
            </div>
        </div>
    </div>
</div>
<!-- [ Header ] end -->











<!-- [ Main Content ] start -->
<div class="dash-container">
    <div class="dash-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="page-header-title">
                            <h4 class="m-b-10">@yield('page-title')</h4>
                        </div>
                        <ul class="breadcrumb">
                            @yield('breadcrumb')
                        </ul>
                    </div>
                    <div class="col">
                        @yield('action-btn')
                    </div>
                </div>
            </div>
        </div>

        
    @yield('content')
    
    
        </div>



        



</div>


<div class="modal fade" id="commonModal" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="body">

            



            </div>
        </div>
    </div>
</div>

<div class="position-fixed top-0 end-0 p-3" style="z-index: 99999">
    <div id="liveToast" class="toast text-white fade" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"> </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>


@include('partials.admin.footer')
@include('Chatify::layouts.footerLinks')

<script>
    document.onkeydown = function(e){

        if(event.keyCode == 123){
            return false;

        }
        if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)){
            return false;
        }

        if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)){
            return false;
        }

        if(e.ctrlKey  && e.keyCode == 'U'.charCodeAt(0)){
            return false;
        }

    }
</script>

@yield('scripts')


</body>
</html>
