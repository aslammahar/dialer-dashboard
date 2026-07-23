<?php

namespace App\Http\Controllers;

use App\Models\AccountList;
use App\Models\Announcement;
use App\Models\AttendanceEmployee;
use App\Models\BalanceSheet;
use App\Models\BankAccount;
use App\Models\Bill;
use App\Models\Bug;
use App\Models\BugStatus;
use App\Models\DealTask;
use App\Models\Employee;
use App\Models\Event;
use App\Models\Expense;
use App\Models\Goal;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\Monitoring;

use App\Models\LandingPageSection;
use App\Models\Meeting;
use App\Models\Order;
use App\Models\Payees;
use App\Models\Payer;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\ProductServiceCategory;
use App\Models\ProductServiceUnit;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Revenue;
use App\Models\Tax;
use App\Models\Ticket;
use App\Models\Timesheet;
use App\Models\TimeTracker;
use App\Models\Trainer;
use App\Models\Training;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Mail\SendLeadEmail;
use App\Models\ClientDeal;
use App\Models\Deal;
use App\Models\DealCall;
use App\Models\DealDiscussion;
use App\Models\DealEmail;
use App\Models\DealFile;
use App\Models\Priority;
use App\Models\Label;
use App\Models\Group;
use App\Models\Lead;
use App\Models\LeadActivityLog;
use App\Models\LeadCall;
use App\Models\LeadDiscussion;
use App\Models\LeadEmail;
use App\Models\LeadFile;
use App\Models\LeadStage;
use App\Models\Pipeline;
use App\Models\ProductService;
use App\Models\Source;
use App\Models\Stage;
use App\Models\UserDeal;
use App\Models\Warning;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use App\Models\Recruitment;
use App\Models\AvatarMonitoring;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function account_dashboard_index()
    {
        if (Auth::check()) {
            if (Auth::user()->type == 'super admin') {
                return redirect()->route('client.dashboard.view');
            } elseif (Auth::user()->type == 'client') {
                return redirect()->route('client.dashboard.view');
            } elseif (Auth::user()->type == 'recruiter') {
                return redirect()->route('recruiter.dashboard');
            } elseif (Auth::user()->type == 'recruiter head') {
                return redirect()->route('recruiter.head.dashboard');
            }
             elseif (Auth::user()->type == 'Verifier') {
                return redirect()->route('verifier.dashboard');
            } else {

                if (\Auth::user()->can('show account dashboard')) {
                    $data['latestIncome'] = Revenue::where('created_by', '=', \Auth::user()->creatorId())->orderBy('id', 'desc')->limit(5)->get();
                    $data['latestExpense'] = Payment::where('created_by', '=', \Auth::user()->creatorId())->orderBy('id', 'desc')->limit(5)->get();
                    $currentYer = date('Y');


                    $incomeCategory = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 1)->get();
                    $inColor = array();
                    $inCategory = array();
                    $inAmount = array();
                    for ($i = 0; $i < count($incomeCategory); $i++) {
                        $inColor[] = '#' . $incomeCategory[$i]->color;
                        $inCategory[] = $incomeCategory[$i]->name;
                        $inAmount[] = $incomeCategory[$i]->incomeCategoryRevenueAmount();
                    }


                    $data['incomeCategoryColor'] = $inColor;
                    $data['incomeCategory'] = $inCategory;
                    $data['incomeCatAmount'] = $inAmount;


                    $expenseCategory = ProductServiceCategory::where('created_by', '=', \Auth::user()->creatorId())->where('type', '=', 2)->get();
                    $exColor = array();
                    $exCategory = array();
                    $exAmount = array();
                    for ($i = 0; $i < count($expenseCategory); $i++) {
                        $exColor[] = '#' . $expenseCategory[$i]->color;
                        $exCategory[] = $expenseCategory[$i]->name;
                        $exAmount[] = $expenseCategory[$i]->expenseCategoryAmount();
                    }

                    $data['expenseCategoryColor'] = $exColor;
                    $data['expenseCategory'] = $exCategory;
                    $data['expenseCatAmount'] = $exAmount;

                    $data['incExpBarChartData'] = \Auth::user()->getincExpBarChartData();
                    $data['incExpLineChartData'] = \Auth::user()->getIncExpLineChartDate();

                    $data['currentYear'] = date('Y');
                    $data['currentMonth'] = date('M');

                    $constant['taxes'] = Tax::where('created_by', \Auth::user()->creatorId())->count();
                    $constant['category'] = ProductServiceCategory::where('created_by', \Auth::user()->creatorId())->count();
                    $constant['units'] = ProductServiceUnit::where('created_by', \Auth::user()->creatorId())->count();
                    $constant['bankAccount'] = BankAccount::where('created_by', \Auth::user()->creatorId())->count();
                    $data['constant'] = $constant;
                    $data['bankAccountDetail'] = BankAccount::where('created_by', '=', \Auth::user()->creatorId())->get();
                    $data['recentInvoice'] = Invoice::where('created_by', '=', \Auth::user()->creatorId())->orderBy('id', 'desc')->limit(5)->get();
                    $data['weeklyInvoice'] = \Auth::user()->weeklyInvoice();
                    $data['monthlyInvoice'] = \Auth::user()->monthlyInvoice();
                    $data['recentBill'] = Bill::where('created_by', '=', \Auth::user()->creatorId())->orderBy('id', 'desc')->limit(5)->get();
                    $data['weeklyBill'] = \Auth::user()->weeklyBill();
                    $data['monthlyBill'] = \Auth::user()->monthlyBill();
                    $data['goals'] = Goal::where('created_by', '=', \Auth::user()->creatorId())->where('is_display', 1)->get();

                    return view('dashboard.account-dashboard', $data);
                } else {

                    return $this->hrm_dashboard_index();
                }
            }
        } else {
            if (!file_exists(storage_path() . "/installed")) {
                header('location:install');
                die;
            } else {
                $settings = Utility::settings();
                if ($settings['display_landing_page'] == 'on') {


                    return view('layouts.landing', compact('settings'));
                } else {
                    return redirect('login');
                }
            }
        }
    }

    public function project_dashboard_index()
    {
        $user = Auth::user();
        if (\Auth::user()->can('show project dashboard')) {
            if ($user->type == 'admin') {
                return view('admin.dashboard');
            } else {
                $home_data = [];

                $user_projects = $user->projects()->pluck('project_id')->toArray();
                $project_tasks = ProjectTask::whereIn('project_id', $user_projects)->get();
                $project_expense = Expense::whereIn('project_id', $user_projects)->get();
                $seven_days = Utility::getLastSevenDays();

                // Total Projects
                $complete_project = $user->projects()->where('status', 'LIKE', 'complete')->count();
                $home_data['total_project'] = [
                    'total' => count($user_projects),
                    'percentage' => Utility::getPercentage($complete_project, count($user_projects)),
                ];

                // Total Tasks
                // 🔒 SECURITY: Escape user ID to prevent SQL injection
                $escapedUserId = DB::connection()->getPdo()->quote((string)$user->id);
                $complete_task = ProjectTask::where('is_complete', '=', 1)->whereRaw("find_in_set(" . $escapedUserId . ",assign_to)")->whereIn('project_id', $user_projects)->count();
                $home_data['total_task'] = [
                    'total' => $project_tasks->count(),
                    'percentage' => Utility::getPercentage($complete_task, $project_tasks->count()),
                ];

                // Total Expense
                $total_expense = 0;
                $total_project_amount = 0;
                foreach ($user->projects as $pr) {
                    $total_project_amount += $pr->budget;
                }
                foreach ($project_expense as $expense) {
                    $total_expense += $expense->amount;
                }
                $home_data['total_expense'] = [
                    'total' => $project_expense->count(),
                    'percentage' => Utility::getPercentage($total_expense, $total_project_amount),
                ];

                // Total Users
                $home_data['total_user'] = Auth::user()->contacts->count();

                // Tasks Overview Chart & Timesheet Log Chart
                $task_overview = [];
                $timesheet_logged = [];
                foreach ($seven_days as $date => $day) {
                    // Task
                    $task_overview[$day] = ProjectTask::where('is_complete', '=', 1)->where('marked_at', 'LIKE', $date)->whereIn('project_id', $user_projects)->count();

                    // Timesheet
                    $time = Timesheet::whereIn('project_id', $user_projects)->where('date', 'LIKE', $date)->pluck('time')->toArray();
                    $timesheet_logged[$day] = str_replace(':', '.', Utility::calculateTimesheetHours($time));
                }

                $home_data['task_overview'] = $task_overview;
                $home_data['timesheet_logged'] = $timesheet_logged;

                // Project Status
                $total_project = count($user_projects);
                $project_status = [];
                foreach (Project::$project_status as $k => $v) {
                    $project_status[$k]['total'] = $user->projects->where('status', 'LIKE', $k)->count();
                    $project_status[$k]['percentage'] = Utility::getPercentage($project_status[$k]['total'], $total_project);
                }
                $home_data['project_status'] = $project_status;

                // Top Due Project
                $home_data['due_project'] = $user->projects()->orderBy('end_date', 'DESC')->limit(5)->get();

                // Top Due Tasks
                $home_data['due_tasks'] = ProjectTask::where('is_complete', '=', 0)->whereIn('project_id', $user_projects)->orderBy('end_date', 'DESC')->limit(5)->get();

                $home_data['last_tasks'] = ProjectTask::whereIn('project_id', $user_projects)->orderBy('end_date', 'DESC')->limit(5)->get();

                return view('dashboard.project-dashboard', compact('home_data'));
            }
        } else {
            return $this->account_dashboard_index();
        }
    }



    public function hrm_dashboard_index()
    {

        if (Auth::check()) {

            if (\Auth::user()->can('show hrm dashboard')) {

                $user = Auth::user();

                if ($user->type != 'client' && $user->type != 'company') {
                    $emp = Employee::where('user_id', '=', $user->id)->first();

                    $announcements = Announcement::orderBy('announcements.id', 'desc')->take(5)->leftjoin('announcement_employees', 'announcements.id', '=', 'announcement_employees.announcement_id')->where('announcement_employees.employee_id', '=', $emp->id)->orWhere(function ($q) {
                        $q->where('announcements.department_id', '["0"]')->where('announcements.employee_id', '["0"]');
                    })->get();

                    $employees = Employee::get();
                    $meetings  = Meeting::orderBy('meetings.id', 'desc')->take(5)->leftjoin('meeting_employees', 'meetings.id', '=', 'meeting_employees.meeting_id')->where('meeting_employees.employee_id', '=', $emp->id)->orWhere(function ($q) {
                        $q->where('meetings.department_id', '["0"]')->where('meetings.employee_id', '["0"]');
                    })->get();
                    $events    = Event::leftjoin('event_employees', 'events.id', '=', 'event_employees.event_id')->where('event_employees.employee_id', '=', $emp->id)->orWhere(function ($q) {
                        $q->where('events.department_id', '["0"]')->where('events.employee_id', '["0"]');
                    })->get();

                    $arrEvents = [];
                    foreach ($events as $event) {

                        $arr['id']              = $event['id'];
                        $arr['title']           = $event['title'];
                        $arr['start']           = $event['start_date'];
                        $arr['end']             = $event['end_date'];
                        $arr['backgroundColor'] = $event['color'];
                        $arr['borderColor']     = "#fff";
                        $arr['textColor']       = "white";
                        $arrEvents[]            = $arr;
                    }

                    $date               = date("Y-m-d");
                    $time               = date("H:i:s");
                    $employeeAttendance = AttendanceEmployee::orderBy('id', 'desc')->where('employee_id', '=', !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0)->where('date', '=', $date)->first();

                    $officeTime['startTime'] = Utility::getValByName('company_start_time');

                    $officeTime['endTime']   = Utility::getValByName('company_end_time');

                    $user = Auth::user();
                    $emp = Employee::where('user_id', $user->id)->first();
                    $warnings = Warning::where('warning_to', $emp->id)
                        ->with(['employeeBy', 'employeeTo'])
                        ->get();
                    // 
                    //   $warnings= Warning::all();
                    $userId = auth()->id();

                   

// Fetch interviews referred to the current user
$interviews = Recruitment::where('refer_to', $userId)
    ->whereNotNull('date') // Ensure there is an interview date
    ->whereNotIn('status', ['hired', 'rejected'])
    ->whereDate('date', '>=', \Carbon\Carbon::now()->subDay())
    ->get(['name', 'designation', 'date','id','status']);

    $monitoring = Monitoring::where('notify', 'like', "%\"$userId\"%")->get();

    



    
        // Fetch notifications where the user is in the notify_to field
        $notifications = AvatarMonitoring::where('notify_to', 'like', "%\"$userId\"%")->get();
    
    
    
    // dd($monitoring);




                    return view('dashboard.dashboard', compact('notifications','monitoring','interviews','arrEvents', 'announcements', 'employees', 'meetings', 'employeeAttendance', 'officeTime', 'warnings'));
                } else if ($user->type == 'super admin') {
                    $user                       = \Auth::user();
                    $user['total_user']         = $user->countCompany();
                    $user['total_paid_user']    = $user->countPaidCompany();
                    $user['total_orders']       = Order::total_orders();
                    $user['total_orders_price'] = Order::total_orders_price();
                    $user['total_plan']         = Plan::total_plan();
                    $user['most_purchese_plan'] = (!empty(Plan::most_purchese_plan()) ? Plan::most_purchese_plan()->name : '');

                    $chartData = $this->getOrderChart(['duration' => 'week']);

                    return view('dashboard.super_admin', compact('user', 'chartData', 'warnings'));
                } else {
                    $events    = Event::where('created_by', '=', \Auth::user()->creatorId())->get();
                    $arrEvents = [];

                    foreach ($events as $event) {
                        $arr['id']    = $event['id'];
                        $arr['title'] = $event['title'];
                        $arr['start'] = $event['start_date'];
                        $arr['end']   = $event['end_date'];

                        $arr['backgroundColor'] = $event['color'];
                        $arr['borderColor']     = "#fff";
                        $arr['textColor']       = "white";
                        $arr['url']             = route('event.edit', $event['id']);

                        $arrEvents[] = $arr;
                    }


                    $announcements = Announcement::orderBy('announcements.id', 'desc')->take(5)->where('created_by', '=', \Auth::user()->creatorId())->get();


                    $emp           = User::where('type', '!=', 'client')->where('type', '!=', 'company')->where('created_by', '=', \Auth::user()->creatorId())->get();
                    $countEmployee = count($emp);

                    $user      = User::where('type', '!=', 'client')->where('type', '!=', 'company')->where('created_by', '=', \Auth::user()->creatorId())->get();
                    $countUser = count($user);


                    $countTrainer    = Trainer::where('created_by', '=', \Auth::user()->creatorId())->count();
                    $onGoingTraining = Training::where('status', '=', 1)->where('created_by', '=', \Auth::user()->creatorId())->count();
                    $doneTraining    = Training::where('status', '=', 2)->where('created_by', '=', \Auth::user()->creatorId())->count();

                    $currentDate = date('Y-m-d');

                    $employees   = User::where('type', '=', 'client')->where('created_by', '=', \Auth::user()->creatorId())->get();
                    $countClient = count($employees);
                    $notClockIn  = AttendanceEmployee::where('date', '=', $currentDate)->get()->pluck('employee_id');

                    $notClockIns = Employee::where('created_by', '=', \Auth::user()->creatorId())->whereNotIn('id', $notClockIn)->get();
                    $activeJob   = Job::where('status', 'active')->where('created_by', '=', \Auth::user()->creatorId())->count();
                    $inActiveJOb = Job::where('status', 'in_active')->where('created_by', '=', \Auth::user()->creatorId())->count();
                    $user = Auth::user();

                    $emp = Employee::where('user_id', 247)->first();

                    $warnings = Warning::where('warning_to', $emp->id)
                        ->with(['employeeBy', 'employeeTo'])
                        ->get();

                    $meetings = Meeting::where('created_by', '=', \Auth::user()->creatorId())->limit(5)->get();

                    return view('dashboard.dashboard', compact('arrEvents', 'onGoingTraining', 'activeJob', 'inActiveJOb', 'doneTraining', 'announcements', 'employees', 'meetings', 'countTrainer', 'countClient', 'countUser', 'notClockIns', 'countEmployee', 'warnings'));
                }
            } else {
                return $this->project_dashboard_index();
            }
        } else {
            if (!file_exists(storage_path() . "/installed")) {
                header('location:install');
                die;
            } else {
                $settings = Utility::settings();
                if ($settings['display_landing_page'] == 'on') {
                    $plans = Plan::get();

                    return view('layouts.landing', compact('plans'));
                } else {
                    return redirect('login');
                }
            }
        }
    }
    public function crm_dashboard_index()
    {
        $user = Auth::user();
        if ($user->can('show crm dashboard')) {
            $home_data = [];
            if ($user->type == 'admin') {
                return view('admin.dashboard');
            } else {
                $total_leads = Lead::count();
                $pending_leads = Lead::where('labels', 1)->count();
                $loss_leads = Lead::where('labels', 2)->count();
                $win_leads = Lead::where('labels', 3)->count();
                $alice_w_lead = Lead::where('labels', 3)->where('groups', 1)->count();
                $avatar_w_lead = Lead::where('labels', 3)->where('groups', 2)->count();
                $closer_w_lead = Lead::where('labels', 3)->where('groups', 3)->count();
                $sellerz_avatar_office_w_lead = Lead::where('labels', 3)->where('groups', 4)->count();
                $sellerz_avatar_wfh_w_lead = Lead::where('labels', 3)->where('groups', 5)->count();
                $sellerz_closers_w_lead = Lead::where('labels', 3)->where('groups', 6)->count();
                $sellerz_voice_w_lead = Lead::where('labels', 3)->where('groups', 7)->count();
                $voice_w_lead = Lead::where('labels', 3)->where('groups', 8)->count();
                $wfh_w_lead = Lead::where('labels', 3)->where('groups', 9)->count();
                $alice_p_lead = Lead::where('labels', 1)->where('groups', 1)->count();
                $avatar_p_lead = Lead::where('labels', 1)->where('groups', 2)->count();
                $closer_p_lead = Lead::where('labels', 1)->where('groups', 3)->count();
                $sellerz_avatar_office_p_lead = Lead::where('labels', 1)->where('groups', 4)->count();
                $sellerz_avatar_wfh_p_lead = Lead::where('labels', 1)->where('groups', 5)->count();
                $sellerz_closers_p_lead = Lead::where('labels', 1)->where('groups', 6)->count();
                $sellerz_voice_p_lead = Lead::where('labels', 1)->where('groups', 7)->count();
                $voice_p_lead = Lead::where('labels', 1)->where('groups', 8)->count();
                $wfh_p_lead = Lead::where('labels', 1)->where('groups', 9)->count();
                $alice_l_lead = Lead::where('labels', 2)->where('groups', 1)->count();
                $avatar_l_lead = Lead::where('labels', 2)->where('groups', 2)->count();
                $closer_l_lead = Lead::where('labels', 2)->where('groups', 3)->count();
                $sellerz_avatar_office_l_lead = Lead::where('labels', 2)->where('groups', 4)->count();
                $sellerz_avatar_wfh_l_lead = Lead::where('labels', 2)->where('groups', 5)->count();
                $sellerz_closers_l_lead = Lead::where('labels', 2)->where('groups', 6)->count();
                $sellerz_voice_l_lead = Lead::where('labels', 2)->where('groups', 7)->count();
                $voice_l_lead = Lead::where('labels', 2)->where('groups', 8)->count();
                $wfh_l_lead = Lead::where('labels', 2)->where('groups', 9)->count();
                $lead_counts = Lead::groupBy(DB::raw('DATE_FORMAT(created_at, "%d-%b-%Y")'))
                    ->selectRaw('DATE_FORMAT(created_at, "%d-%b-%Y") as date, COUNT(*) as count')
                    ->orderBy('date')
                    ->get();
                $crm_hp_lead = Lead::where('priorities', '1')->count();
                $home_data['crm_hp_lead'] = array('total' => $crm_hp_lead, 'percentage' => 0);
                $crm_mp_lead = Lead::where('priorities', '2')->count();
                $home_data['crm_mp_lead'] = array('total' => $crm_mp_lead, 'percentage' => 0);
                $crm_lp_lead = Lead::where('priorities', '3')->count();
                $home_data['crm_lp_lead'] = array('total' => $crm_lp_lead, 'percentage' => 0);
                $categories = $lead_counts->pluck('date')->toArray();
                $home_data['series'] = [$lead_counts->pluck('count')->toArray()];
                return view('dashboard.crm_dashboard', compact('home_data', 'total_leads', 'pending_leads', 'loss_leads', 'win_leads', 'categories', 'crm_hp_lead', 'crm_mp_lead', 'crm_lp_lead', 'alice_w_lead', 'avatar_w_lead', 'closer_w_lead', 'sellerz_avatar_office_w_lead', 'sellerz_avatar_wfh_w_lead', 'sellerz_closers_w_lead', 'sellerz_voice_w_lead', 'voice_w_lead', 'wfh_w_lead', 'alice_p_lead', 'avatar_p_lead', 'closer_p_lead', 'sellerz_avatar_office_p_lead', 'sellerz_avatar_wfh_p_lead', 'sellerz_closers_p_lead', 'sellerz_voice_p_lead', 'voice_p_lead', 'wfh_p_lead', 'alice_l_lead', 'avatar_l_lead', 'closer_l_lead', 'sellerz_avatar_office_l_lead', 'sellerz_avatar_wfh_l_lead', 'sellerz_closers_l_lead', 'sellerz_voice_l_lead', 'voice_l_lead', 'wfh_l_lead'));
            }
        } else {
            return redirect('admin');
        }
    }
    // Load Dashboard user's using ajax
    public function filterView(Request $request)
    {
        $usr = Auth::user();
        $users = User::where('id', '!=', $usr->id);

        if ($request->ajax()) {
            if (!empty($request->keyword)) {
                // 🔒 SECURITY: Escape keyword to prevent SQL injection
                $escapedKeyword = DB::connection()->getPdo()->quote($request->keyword);
                $users->where('name', 'LIKE', $request->keyword . '%')->orWhereRaw('FIND_IN_SET(' . $escapedKeyword . ',skills)');
            }

            $users = $users->get();
            $returnHTML = view('dashboard.view', compact('users'))->render();

            return response()->json([
                'success' => true,
                'html' => $returnHTML,
            ]);
        }
    }

    public function clientView()
    {

        if (Auth::check()) {
            if (Auth::user()->type == 'super admin') {
                $user = \Auth::user();
                $user['total_user'] = $user->countCompany();
                $user['total_paid_user'] = $user->countPaidCompany();
                $user['total_orders'] = Order::total_orders();
                $user['total_orders_price'] = Order::total_orders_price();
                $user['total_plan'] = Plan::total_plan();
                $user['most_purchese_plan'] = (!empty(Plan::most_purchese_plan()) ? Plan::most_purchese_plan()->total : 0);
                $chartData = $this->getOrderChart(['duration' => 'week']);

                return view('dashboard.super_admin', compact('user', 'chartData'));
            } elseif (Auth::user()->type == 'client') {
                $transdate = date('Y-m-d', time());
                $currentYear = date('Y');

                $calenderTasks = [];
                $chartData = [];
                $arrCount = [];
                $arrErr = [];
                $m = date("m");
                $de = date("d");
                $y = date("Y");
                $format = 'Y-m-d';
                $user = \Auth::user();
                if (\Auth::user()->can('View Task')) {
                    $company_setting = Utility::settings();
                }
                $arrTemp = [];
                for ($i = 0; $i <= 7 - 1; $i++) {
                    $date = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));
                    $arrTemp['date'][] = __(date('D', strtotime($date)));
                    $arrTemp['invoice'][] = 10;
                    $arrTemp['payment'][] = 20;
                }

                $chartData = $arrTemp;

                foreach ($user->clientDeals as $deal) {
                    foreach ($deal->tasks as $task) {
                        $calenderTasks[] = [
                            'title' => $task->name,
                            'start' => $task->date,
                            'url' => route('deals.tasks.show', [
                                $deal->id,
                                $task->id,
                            ]),
                            'className' => ($task->status) ? 'bg-success border-success' : 'bg-warning border-warning',
                        ];
                    }

                    $calenderTasks[] = [
                        'title' => $deal->name,
                        'start' => $deal->created_at->format('Y-m-d'),
                        'url' => route('deals.show', [$deal->id]),
                        'className' => 'deal bg-primary border-primary',
                    ];
                }
                $client_deal = $user->clientDeals->pluck('id');

                $arrCount['deal'] = $user->clientDeals->count();
                if (!empty($client_deal->first())) {
                    $arrCount['task'] = DealTask::whereIn('deal_id', [$client_deal])->count();
                } else {
                    $arrCount['task'] = 0;
                }


                $project['projects'] = Project::where('client_id', '=', Auth::user()->id)->where('created_by', \Auth::user()->creatorId())->where('end_date', '>', date('Y-m-d'))->limit(5)->orderBy('end_date')->get();
                $project['projects_count'] = count($project['projects']);
                $user_projects = Project::where('client_id', \Auth::user()->id)->pluck('id', 'id')->toArray();
                $tasks = ProjectTask::whereIn('project_id', $user_projects)->where('created_by', \Auth::user()->creatorId())->get();
                $project['projects_tasks_count'] = count($tasks);
                $project['project_budget'] = Project::where('client_id', Auth::user()->id)->sum('budget');

                $project_last_stages = Auth::user()->last_projectstage();
                $project_last_stage = (!empty($project_last_stages) ? $project_last_stages->id : 0);
                $project['total_project'] = Auth::user()->user_project();
                $total_project_task = Auth::user()->created_total_project_task();
                $allProject = Project::where('client_id', \Auth::user()->id)->where('created_by', \Auth::user()->creatorId())->get();
                $allProjectCount = count($allProject);

                $bugs = Bug::whereIn('project_id', $user_projects)->where('created_by', \Auth::user()->creatorId())->get();
                $project['projects_bugs_count'] = count($bugs);
                $bug_last_stage = BugStatus::orderBy('order', 'DESC')->first();
                $completed_bugs = Bug::whereIn('project_id', $user_projects)->where('status', $bug_last_stage->id)->where('created_by', \Auth::user()->creatorId())->get();
                $allBugCount = count($bugs);
                $completedBugCount = count($completed_bugs);
                $project['project_bug_percentage'] = ($allBugCount != 0) ? intval(($completedBugCount / $allBugCount) * 100) : 0;
                $complete_task = Auth::user()->project_complete_task($project_last_stage);
                $completed_project = Project::where('client_id', \Auth::user()->id)->where('status', 'complete')->where('created_by', \Auth::user()->creatorId())->get();
                $completed_project_count = count($completed_project);
                $project['project_percentage'] = ($allProjectCount != 0) ? intval(($completed_project_count / $allProjectCount) * 100) : 0;
                $project['project_task_percentage'] = ($total_project_task != 0) ? intval(($complete_task / $total_project_task) * 100) : 0;
                $invoice = [];
                $top_due_invoice = [];
                $invoice['total_invoice'] = 5;
                $complete_invoice = 0;
                $total_due_amount = 0;
                $top_due_invoice = array();
                $pay_amount = 0;

                if (Auth::user()->type == 'client') {
                    if (!empty($project['project_budget'])) {
                        $project['client_project_budget_due_per'] = intval(($pay_amount / $project['project_budget']) * 100);
                    } else {
                        $project['client_project_budget_due_per'] = 0;
                    }
                }

                $top_tasks = Auth::user()->created_top_due_task();
                $users['staff'] = User::where('created_by', '=', Auth::user()->creatorId())->count();
                $users['user'] = User::where('created_by', '=', Auth::user()->creatorId())->where('type', '!=', 'client')->count();
                $users['client'] = User::where('created_by', '=', Auth::user()->creatorId())->where('type', '=', 'client')->count();
                $project_status = array_values(Project::$project_status);
                $projectData = \App\Models\Project::getProjectStatus();

                $taskData = \App\Models\TaskStage::getChartData();

                return view('dashboard.clientView', compact('calenderTasks', 'arrErr', 'arrCount', 'chartData', 'project', 'invoice', 'top_tasks', 'top_due_invoice', 'users', 'project_status', 'projectData', 'taskData', 'transdate', 'currentYear'));
            }
        }
    }

    public function getOrderChart($arrParam)
    {
        $arrDuration = [];
        if ($arrParam['duration']) {
            if ($arrParam['duration'] == 'week') {
                $previous_week = strtotime("-2 week +1 day");
                for ($i = 0; $i < 14; $i++) {
                    $arrDuration[date('Y-m-d', $previous_week)] = date('d-M', $previous_week);
                    $previous_week = strtotime(date('Y-m-d', $previous_week) . " +1 day");
                }
            }
        }

        $arrTask = [];
        $arrTask['label'] = [];
        $arrTask['data'] = [];
        foreach ($arrDuration as $date => $label) {

            $data = Order::select(\DB::raw('count(*) as total'))->whereDate('created_at', '=', $date)->first();
            $arrTask['label'][] = $label;
            $arrTask['data'][] = $data->total;
        }

        return $arrTask;
    }

    public function recruiter_dashboard_index()
    {
        $user = Auth::user();
        $recruiterName = $user->name;
        $statuses = ['No Answer', 'Not Interested', 'Call Back', 'Interested', 'Hired', 'Rejected', 'Left', 'Pending'];

        // Total stats for this recruiter (all time)
        $totalCalls = Recruitment::where('interview_taken_by', $recruiterName)->count();
        $todaysCalls = Recruitment::where('interview_taken_by', $recruiterName)
            ->whereDate('created_at', Carbon::today())->count();
        $thisMonthCalls = Recruitment::where('interview_taken_by', $recruiterName)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)->count();

        // Status breakdown
        $statusCounts = Recruitment::where('interview_taken_by', $recruiterName)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
        foreach ($statuses as $s) {
            $statusCounts[$s] = $statusCounts[$s] ?? 0;
        }

        // Today's interviews referred to me (for final interview)
        $todaysInterviews = Recruitment::where('refer_to', $user->id)
            ->whereNotNull('date')
            ->whereDate('date', Carbon::today())
            ->get(['id', 'name', 'designation', 'date', 'status', 'contact_no']);

        // Upcoming interviews (next 7 days) referred to me
        $upcomingInterviews = Recruitment::where('refer_to', $user->id)
            ->whereNotNull('date')
            ->whereDate('date', '>', Carbon::today())
            ->whereDate('date', '<=', Carbon::today()->addDays(7))
            ->whereNotIn('status', ['hired', 'rejected'])
            ->orderBy('date')
            ->get(['id', 'name', 'designation', 'date', 'status']);

        // Recent 10 recruits by this recruiter
        $recentRecruits = Recruitment::where('interview_taken_by', $recruiterName)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['id', 'name', 'designation', 'status', 'source', 'created_at', 'date']);

        // Last 30 days daily activity chart
        $dailyActivity = Recruitment::where('interview_taken_by', $recruiterName)
            ->where('created_at', '>=', Carbon::now()->subDays(29))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        $chartDates = [];
        $chartCounts = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartDates[] = Carbon::now()->subDays($i)->format('d M');
            $chartCounts[] = $dailyActivity[$d] ?? 0;
        }

        // Hired count this month for conversion rate
        $hiredThisMonth = $statusCounts['Hired'] ?? 0;
        $conversionRate = $totalCalls > 0 ? round(($statusCounts['Hired'] / $totalCalls) * 100, 1) : 0;

        return view('dashboard.recruiter-dashboard', compact(
            'totalCalls', 'todaysCalls', 'thisMonthCalls',
            'statusCounts', 'statuses', 'todaysInterviews',
            'upcomingInterviews', 'recentRecruits',
            'chartDates', 'chartCounts', 'conversionRate', 'recruiterName'
        ));
    }

    public function recruiter_head_dashboard_index(Request $request)
    {
        $statuses = ['No Answer', 'Not Interested', 'Call Back', 'Interested', 'Hired', 'Rejected', 'Left', 'Pending'];

        // Date range filter
        $filterType = $request->query('filter', 'custom');
        if ($filterType === 'weekly') {
            $startDate = Carbon::now()->subDays(7)->format('Y-m-d');
        } elseif ($filterType === 'monthly') {
            $startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        } else {
            $startDate = $request->query('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        }
        $endDate = $request->query('end_date', Carbon::now()->format('Y-m-d'));

        // All HR users (recruiters)
        $hrUsers = User::where('type', 'HR')->get(['id', 'name']);

        // Team totals
        $teamTotal = Recruitment::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)->count();
        $teamHired = Recruitment::where('status', 'Hired')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)->count();
        $teamRejected = Recruitment::where('status', 'Rejected')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)->count();
        $teamPending = Recruitment::where('status', 'Pending')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)->count();
        $teamConversion = $teamTotal > 0 ? round(($teamHired / $teamTotal) * 100, 1) : 0;

        // Per-recruiter performance
        $allRecruiters = Recruitment::select('interview_taken_by')
            ->distinct()->whereNotNull('interview_taken_by')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->pluck('interview_taken_by');

        $recruiterData = [];
        foreach ($allRecruiters as $recruiter) {
            $total = Recruitment::where('interview_taken_by', $recruiter)
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)->count();

            $statusCounts = Recruitment::where('interview_taken_by', $recruiter)
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
            foreach ($statuses as $s) {
                $statusCounts[$s] = $statusCounts[$s] ?? 0;
            }

            $hired = $statusCounts['Hired'];
            $recruiterData[] = [
                'recruiter' => $recruiter,
                'total' => $total,
                'statuses' => $statusCounts,
                'conversion' => $total > 0 ? round(($hired / $total) * 100, 1) : 0,
            ];
        }

        // Sort by total desc
        usort($recruiterData, fn($a, $b) => $b['total'] - $a['total']);

        // Daily team activity for chart (last 30 days or filtered range)
        $dailyTeam = Recruitment::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->pluck('count', 'date')
            ->toArray();

        $chartDates = [];
        $chartCounts = [];
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $key = $d->format('Y-m-d');
            $chartDates[] = $d->format('d M');
            $chartCounts[] = $dailyTeam[$key] ?? 0;
        }

        // Today's all interviews across team
        $todaysAllInterviews = Recruitment::whereDate('date', Carbon::today())
            ->whereNotNull('date')
            ->orderBy('date')
            ->get(['id', 'name', 'designation', 'status', 'interview_taken_by', 'contact_no']);

        return view('dashboard.recruiter-head-dashboard', compact(
            'statuses', 'startDate', 'endDate', 'filterType',
            'teamTotal', 'teamHired', 'teamRejected', 'teamPending', 'teamConversion',
            'recruiterData', 'chartDates', 'chartCounts',
            'todaysAllInterviews', 'hrUsers'
        ));
    }

    public function stopTracker(Request $request)
    {
        if (Auth::user()->isClient()) {
            return Utility::error_res(__('Permission denied.'));
        }
        $validatorArray = [
            'name' => 'required|max:120',
            'project_id' => 'required|integer',
        ];
        $validator = Validator::make(
            $request->all(),
            $validatorArray
        );
        if ($validator->fails()) {
            return Utility::error_res($validator->errors()->first());
        }
        $tracker = TimeTracker::where('created_by', '=', Auth::user()->id)->where('is_active', '=', 1)->first();
        if ($tracker) {
            $tracker->end_time = $request->has('end_time') ? $request->input('end_time') : date("Y-m-d H:i:s");
            $tracker->is_active = 0;
            $tracker->total_time = Utility::diffance_to_time($tracker->start_time, $tracker->end_time);
            $tracker->save();

            return Utility::success_res(__('Add Time successfully.'));
        }

        return Utility::error_res('Tracker not found.');
    }
}
