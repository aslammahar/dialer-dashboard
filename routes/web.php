<?php


use App\Http\Controllers\HikVisionAttendanceController;
use App\Http\Controllers\SuspensionController;
use App\Models\Utility;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\ProductServiceController;
use App\Http\Controllers\ProductStockController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VenderController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BankTransferController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\ProductServiceCategoryController;
use App\Http\Controllers\ProductServiceUnitController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\DebitNoteController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\CustomFieldController;
use App\Http\Controllers\ChartOfAccountController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadStageController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PayslipTypeController;
use App\Http\Controllers\SetSalaryController;
use App\Http\Controllers\AllowanceController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanOptionController;
use App\Http\Controllers\DeductionOptionController;
use App\Http\Controllers\SaturationDeductionController;
use App\Http\Controllers\OtherPaymentController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\AllowanceOptionController;
use App\Http\Controllers\PaySlipController;
use App\Http\Controllers\CompanyPolicyController;
use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\AppraisalController;
use App\Http\Controllers\GoalTypeController;
use App\Http\Controllers\GoalTrackingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\AwardTypeController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\ResignationController;
use App\Http\Controllers\TravelController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\WarningController;
use App\Http\Controllers\TerminationController;
use App\Http\Controllers\TerminationTypeController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\JobStageController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\CustomQuestionController;
use App\Http\Controllers\InterviewScheduleController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\DucumentUploadController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\AttendanceEmployeeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskStageController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ContractTypeController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\ProjectstagesController;
use App\Http\Controllers\BugStatusController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\FormBuilderController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\LandingPageSectionController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PaystackPaymentController;
use App\Http\Controllers\FlutterwavePaymentController;
use App\Http\Controllers\RazorpayPaymentController;
use App\Http\Controllers\PaytmPaymentController;
use App\Http\Controllers\MolliePaymentController;
use App\Http\Controllers\MercadoPaymentController;
use App\Http\Controllers\SkrillPaymentController;
use App\Http\Controllers\PaymentWallPaymentController;
use App\Http\Controllers\CoingatePaymentController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\CompetenciesController;
use App\Http\Controllers\PerformanceTypeController;
use App\Http\Controllers\PlanRequestController;
use App\Http\Controllers\TimeTrackerController;
use App\Http\Controllers\ZoomMeetingController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProjectReportController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\VoiceQALeadController;
use App\Http\Controllers\ExcelImportController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\CloserController;
use App\Http\Controllers\QaController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\ViciDialController;
use App\Http\Controllers\ViciDialerController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\Dialerlink;
use App\Http\Controllers\RecruitmentController;
use App\Models\AvatarFrom;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\DataVendorController;
use App\Http\Controllers\FingerDevicesController;
use App\Http\Controllers\AttendanceController;
// use App\Http\Controllers\LeaveController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\CheckController;
use App\Http\Controllers\BiometricDeviceController;
use App\http\controllers\VeltrixController;
use App\Http\Flash;
use App\Jobs\ClearAttendanceJob;
use Carbon\Carbon;
use App\Http\Controllers\LeadSearchController;
use App\Http\Controllers\AvatarMonitoringController;
use App\Http\Controllers\OurCampaignController;
use App\Http\Controllers\OurProjectController;
use App\Http\Controllers\CampaignResponseController;

use App\Http\Controllers\AccountingEntryController;
use App\Http\Controllers\ExpenseEntryController;
// routes/web.php
use App\Http\Controllers\NumberListController;
use App\Http\Controllers\VendorListController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\ClosedCallExportController;
use App\Http\Controllers\CloserTeamsController;
use App\Http\Controllers\ValidatorController;
use App\Http\Controllers\QueueSalesController;
use App\Http\Controllers\OutsourceCloserController;
use App\Http\Controllers\UserDetailController;
use App\Http\Controllers\HRUserDetailController;
use App\Http\Controllers\AttachmentController;

use App\Http\Controllers\SalaryDepartmentController;
use App\Http\Controllers\SalaryStructureController;
use App\Http\Controllers\MonthlySalaryController;
use App\Http\Controllers\SalarySlipController;
use App\Http\Controllers\DialersController;
use App\Http\Controllers\DialersUnifiedController;



// imran niaz Here Sunday 
// Route::post('/avatar_leads', [AvatarController::class, 'store'])->name('avatar_leads.store');

// these routes will not be protected by auth middleware bcz they are used in avatar lead generation form from dialer
Route::get('/display', [ParameterController::class, 'display'])->name('display');
Route::get('/avatar_leads/create', [AvatarController::class, 'create'])->name('avatar_leads.create');
Route::post('/avatarleads-store', [AvatarController::class, 'store'])->name('avatarlead.store');

// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;


// Route::get('/', function () {
//     return view('welcome');
// })->name('welcome');

Route::get('attended/{user_id}', [AttendanceController::class, 'attended'])->name('attended');
Route::get('attended-before/{user_id}', [AttendanceController::class, 'attendedBefore'])->name('attendedBefore');

Auth::routes(['register' => false, 'reset' => false]);

Route::group(['middleware' => 'auth'], function () {
    Route::resource('/employees', EmployeeController::class);
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::get('/latetime', [AttendanceController::class, 'indexLatetime'])->name('indexLatetime');
    // Route::get('/leave', [LeaveController::class, 'index'])->name('leave');
    Route::get('/overtime', [LeaveController::class, 'indexOvertime'])->name('indexOvertime');
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::resource('/schedule', ScheduleController::class);
    Route::get('/check', [CheckController::class, 'index'])->name('check');
    Route::get('/sheet-report', [CheckController::class, 'sheetReport'])->name('sheet-report');
    Route::post('check-store', [CheckController::class, 'CheckStore'])->name('check_store');
    Route::resource('/finger_device', BiometricDeviceController::class);
    Route::delete('finger_device/destroy', [BiometricDeviceController::class, 'massDestroy'])->name('finger_device.massDestroy');

    Route::get('show_employee', [BiometricDeviceController::class, 'showEmployee'])->name('showEmployee');

    Route::get('finger_device/{fingerDevice}/employees/add', [BiometricDeviceController::class, 'addEmployee'])->name('finger_device.add.employee');
    Route::get('finger_device/{fingerDevice}/get/attendance', [BiometricDeviceController::class, 'getAttendance'])->name('finger_device.get.attendance');
    Route::get('finger_device/clear/attendance', function () {
        $midnight = Carbon::createFromTime(23, 50, 00);
        $diff = now()->diffInMinutes($midnight);
        dispatch(new ClearAttendanceJob())->delay(now()->addMinutes($diff));

        return redirect()->back()->with('status', 'Attendance clearance job has been scheduled to run at 11:50 P.M.');
    })->name('finger_device.clear.attendance');
});

Route::group(['middleware' => ['auth']], function () {
    // Route::get('/home', [HomeController::class, 'index'])->name('home');
});

Route::get('/assign-schedule', [ScheduleController::class, 'showAssignScheduleForm'])->name('assign.schedule.form');
Route::post('/assign-schedule', [ScheduleController::class, 'assignSchedule'])->name('assign.schedule');


require __DIR__ . '/auth.php';

//Route::get('/', ['as' => 'home','uses' =>'HomeController@index'])->middleware(['XSS']);
//Route::get('/home', ['as' => 'home','uses' =>'HomeController@index'])->middleware(['auth','XSS']);

Route::get('/', [DashboardController::class, 'account_dashboard_index'])->name('home')->middleware(['XSS', 'revalidate',]);

Route::get('/home', [DashboardController::class, 'account_dashboard_index'])->name('home')->middleware(['XSS', 'revalidate',]);

Route::get('/register/{lang?}', [RegisteredUserController::class, 'showRegistrationForm'])->name('register');

//Route::get('/register/{lang?}', function () {
//    $settings = Utility::settings();
//    $lang = $settings['default_language'];
//
//    if($settings['enable_signup'] == 'on'){
//        return view("auth.register", compact('lang'));
//       // Route::get('/register', 'Auth\RegisteredUserController@showRegistrationForm')->name('register');
//    }else{
//        return Redirect::to('login');
//    }
//
//});
Route::post('/store', 'FormController@store')->name('storeFormData');

Route::post('register', [RegisteredUserController::class, 'store'])->name('register');

Route::get('/login/{lang?}', [AuthenticatedSessionController::class, 'showLoginForm'])->name('login');

Route::get('/', [DashboardController::class, 'account_dashboard_index'])->name('dashboard');

Route::get('/account-dashboard', [DashboardController::class, 'account_dashboard_index'])->name('dashboard')->middleware(['auth', 'XSS', 'revalidate']);

Route::get('/project-dashboard', [DashboardController::class, 'project_dashboard_index'])->name('project.dashboard')->middleware(['auth', 'XSS', 'revalidate']);

Route::get('/crm-dashboard', [DashboardController::class, 'crm_dashboard_index'])->name('crm.dashboard')->middleware(['auth', 'XSS', 'revalidate']);

Route::get('/hrm-dashboard', [DashboardController::class, 'hrm_dashboard_index'])->name('hrm.dashboard')->middleware(['auth', 'XSS', 'revalidate']);



Route::get('/recruiter-dashboard', [DashboardController::class, 'recruiter_dashboard_index'])->name('recruiter.dashboard')->middleware(['auth', 'XSS', 'revalidate']);

Route::get('/recruiter-head-dashboard', [DashboardController::class, 'recruiter_head_dashboard_index'])->name('recruiter.head.dashboard')->middleware(['auth', 'XSS', 'revalidate']);

Route::get('profile', [UserController::class, 'profile'])->name('profile')->middleware(['auth', 'XSS', 'revalidate']);

Route::any('edit-profile', [UserController::class, 'editprofile'])->name('update.account')->middleware(['auth', 'XSS', 'revalidate']);

Route::resource('users', UserController::class)->middleware(['auth', 'XSS', 'revalidate', 'throttle:user-management', 'can:manage user']);

Route::post('change-password', [UserController::class, 'updatePassword'])->name('update.password');

Route::any('user-reset-password/{id}', [UserController::class, 'userPassword'])->name('users.reset')->middleware(['auth', 'can:manage user']);

Route::post('user-reset-password/{id}', [UserController::class, 'userPasswordReset'])->name('user.password.update')->middleware(['auth', 'can:manage user']);

Route::get('/change/mode', [UserController::class, 'changeMode'])->name('change.mode');

Route::resource('roles', RoleController::class)->middleware(['auth', 'XSS', 'revalidate', 'throttle:role-management', 'can:manage role']);

Route::resource('permissions', PermissionController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::get('change-language/{lang}', [LanguageController::class, 'changeLanquage'])->name('change.language');

        Route::get('manage-language/{lang}', [LanguageController::class, 'manageLanguage'])->name('manage.language');

        Route::post('store-language-data/{lang}', [LanguageController::class, 'storeLanguageData'])->name('store.language.data');

        Route::get('create-language', [LanguageController::class, 'createLanguage'])->name('create.language');

        Route::any('store-language', [LanguageController::class, 'storeLanguage'])->name('store.language');

        Route::delete('/lang/{lang}', [LanguageController::class, 'destroyLang'])->name('lang.destroy');
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
            'can:manage system settings',
        ],
    ],
    function () {
        Route::resource('systems', SystemController::class);
        Route::post('email-settings', [SystemController::class, 'saveEmailSettings'])->name('email.settings');
        Route::post('company-settings', [SystemController::class, 'saveCompanySettings'])->name('company.settings');
        Route::post('system-settings', [SystemController::class, 'saveSystemSettings'])->name('system.settings');
        Route::post('zoom-settings', [SystemController::class, 'saveZoomSettings'])->name('zoom.settings');
        Route::post('slack-settings', [SystemController::class, 'saveSlackSettings'])->name('slack.settings');
        Route::post('telegram-settings', [SystemController::class, 'saveTelegramSettings'])->name('telegram.settings');
        Route::post('twilio-settings', [SystemController::class, 'saveTwilioSettings'])->name('twilio.setting');
        Route::get('print-setting', [SystemController::class, 'printIndex'])->name('print.setting');
        Route::get('settings', [SystemController::class, 'companyIndex'])->name('settings');
        Route::post('business-setting', [SystemController::class, 'saveBusinessSettings'])->name('business.setting');
        Route::post('company-payment-setting', [SystemController::class, 'saveCompanyPaymentSettings'])->name('company.payment.settings');

        Route::get('test-mail', [SystemController::class, 'testMail'])->name('test.mail');
        Route::post('test-mail', [SystemController::class, 'testMail'])->name('test.mail');
        Route::post('test-mail/send', [SystemController::class, 'testSendMail'])->name('test.send.mail');

        Route::post('stripe-settings', [SystemController::class, 'savePaymentSettings'])->name('payment.settings');
        Route::post('pusher-setting', [SystemController::class, 'savePusherSettings'])->name('pusher.setting');
        Route::post('recaptcha-settings', [SystemController::class, 'recaptchaSettingStore'])->name('recaptcha.settings.store')->middleware(['auth', 'XSS']);
    }
);

Route::get('productservice/index', [ProductServiceController::class, 'index'])->name('productservice.index');
Route::get('productservice/{id}/detail', [ProductServiceController::class, 'warehouseDetail'])->name('productservice.detail');
Route::post('empty-cart', [ProductServiceController::class, 'emptyCart'])->middleware(['auth', 'XSS']);
Route::post('warehouse-empty-cart', [ProductServiceController::class, 'warehouseemptyCart'])->name('warehouse-empty-cart')->middleware(['auth', 'XSS']);
Route::resource('productservice', ProductServiceController::class)->middleware(['auth', 'XSS', 'revalidate']);



//Product Stock
Route::resource('productstock', ProductStockController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::get('customer/{id}/show', [CustomerController::class, 'show'])->name('customer.show');
        Route::resource('customer', CustomerController::class);
    }
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::get('vender/{id}/show', [VenderController::class, 'show'])->name('vender.show');
        Route::resource('vender', VenderController::class);
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::resource('bank-account', BankAccountController::class);
    }
);
Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::get('bank-transfer/index', [BankTransferController::class, 'index'])->name('bank-transfer.index');
        Route::resource('bank-transfer', BankTransferController::class);
    }
);


Route::resource('taxes', TaxController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::resource('product-category', ProductServiceCategoryController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::resource('product-unit', ProductServiceUnitController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::get('invoice/pdf/{id}', [InvoiceController::class, 'invoice'])->name('invoice.pdf')->middleware(['XSS', 'revalidate']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::get('invoice/{id}/duplicate', [InvoiceController::class, 'duplicate'])->name('invoice.duplicate');
        Route::get('invoice/{id}/shipping/print', [InvoiceController::class, 'shippingDisplay'])->name('invoice.shipping.print');
        Route::get('invoice/{id}/payment/reminder', [InvoiceController::class, 'paymentReminder'])->name('invoice.payment.reminder');
        Route::get('invoice/index', [InvoiceController::class, 'index'])->name('invoice.index');
        Route::post('invoice/product/destroy', [InvoiceController::class, 'productDestroy'])->name('invoice.product.destroy');
        Route::post('invoice/product', [InvoiceController::class, 'product'])->name('invoice.product');
        Route::post('invoice/customer', [InvoiceController::class, 'customer'])->name('invoice.customer');
        Route::get('invoice/{id}/sent', [InvoiceController::class, 'sent'])->name('invoice.sent');
        Route::get('invoice/{id}/resent', [InvoiceController::class, 'resent'])->name('invoice.resent');
        Route::get('invoice/{id}/payment', [InvoiceController::class, 'payment'])->name('invoice.payment');
        Route::post('invoice/{id}/payment', [InvoiceController::class, 'createPayment'])->name('invoice.payment');
        Route::post('invoice/{id}/payment/{pid}/destroy', [InvoiceController::class, 'paymentDestroy'])->name('invoice.payment.destroy');
        Route::get('invoice/items', [InvoiceController::class, 'items'])->name('invoice.items');
        Route::resource('invoice', InvoiceController::class);
        Route::get('invoice/create/{cid}', [InvoiceController::class, 'create'])->name('invoice.create');
    }
);


Route::get('/invoices/preview/{template}/{color}', [InvoiceController::class, 'previewInvoice'])->name('invoice.preview');
Route::post('/invoices/template/setting', [InvoiceController::class, 'saveTemplateSettings'])->name('template.setting');


Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::get('credit-note', [CreditNoteController::class, 'index'])->name('credit.note');
        Route::get('custom-credit-note', [CreditNoteController::class, 'customCreate'])->name('invoice.custom.credit.note');
        Route::post('custom-credit-note', [CreditNoteController::class, 'customStore'])->name('invoice.custom.credit.note');
        Route::get('credit-note/invoice', [CreditNoteController::class, 'getinvoice'])->name('invoice.get');
        Route::get('invoice/{id}/credit-note', [CreditNoteController::class, 'create'])->name('invoice.credit.note');
        Route::post('invoice/{id}/credit-note', [CreditNoteController::class, 'store'])->name('invoice.credit.note');
        Route::get('invoice/{id}/credit-note/edit/{cn_id}', [CreditNoteController::class, 'edit'])->name('invoice.edit.credit.note');
        Route::post('invoice/{id}/credit-note/edit/{cn_id}', [CreditNoteController::class, 'update'])->name('invoice.edit.credit.note');
        Route::delete('invoice/{id}/credit-note/delete/{cn_id}', [CreditNoteController::class, 'destroy'])->name('invoice.delete.credit.note');
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::get('debit-note', [DebitNoteController::class, 'index'])->name('debit.note');
        Route::get('custom-debit-note', [DebitNoteController::class, 'customCreate'])->name('bill.custom.debit.note');
        Route::post('custom-debit-note', [DebitNoteController::class, 'customStore'])->name('bill.custom.debit.note');
        Route::get('debit-note/bill', [DebitNoteController::class, 'getbill'])->name('bill.get');
        Route::get('bill/{id}/debit-note', [DebitNoteController::class, 'create'])->name('bill.debit.note');
        Route::post('bill/{id}/debit-note', [DebitNoteController::class, 'store'])->name('bill.debit.note');
        Route::get('bill/{id}/debit-note/edit/{cn_id}', [DebitNoteController::class, 'edit'])->name('bill.edit.debit.note');
        Route::post('bill/{id}/debit-note/edit/{cn_id}', [DebitNoteController::class, 'update'])->name('bill.edit.debit.note');
        Route::delete('bill/{id}/debit-note/delete/{cn_id}', [DebitNoteController::class, 'destroy'])->name('bill.delete.debit.note');
    }
);

Route::get('/bill/preview/{template}/{color}', [BillController::class, 'previewBill'])->name('bill.preview')->middleware(['auth', 'XSS',]);
Route::post('/bill/template/setting', [BillController::class, 'saveBillTemplateSettings'])->name('bill.template.setting');

Route::resource('taxes', TaxController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::get('revenue/index', [RevenueController::class, 'index'])->name('revenue.index')->middleware(['auth', 'XSS', 'revalidate']);

Route::resource('revenue', RevenueController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::get('bill/pdf/{id}', [BillController::class, 'bill'])->name('bill.pdf')->middleware(['XSS', 'revalidate']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::get('bill/{id}/duplicate', [BillController::class, 'duplicate'])->name('bill.duplicate');
        Route::get('bill/{id}/shipping/print', [BillController::class, 'shippingDisplay'])->name('bill.shipping.print');
        Route::get('bill/index', [BillController::class, 'index'])->name('bill.index');
        Route::post('bill/product/destroy', [BillController::class, 'productDestroy'])->name('bill.product.destroy');
        Route::post('bill/product', [BillController::class, 'product'])->name('bill.product');
        Route::post('bill/vender', [BillController::class, 'vender'])->name('bill.vender');
        Route::get('bill/{id}/sent', [BillController::class, 'sent'])->name('bill.sent');
        Route::get('bill/{id}/resent', [BillController::class, 'resent'])->name('bill.resent');
        Route::get('bill/{id}/payment', [BillController::class, 'payment'])->name('bill.payment');
        Route::post('bill/{id}/payment', [BillController::class, 'createPayment'])->name('bill.payment');
        Route::post('bill/{id}/payment/{pid}/destroy', [BillController::class, 'paymentDestroy'])->name('bill.payment.destroy');
        Route::get('bill/items', [BillController::class, 'items'])->name('bill.items');
        Route::resource('bill', BillController::class);
        Route::get('bill/create/{cid}', [BillController::class, 'create'])->name('bill.create');
    }
);

Route::get('payment/index', [PaymentController::class, 'index'])->name('payment.index')->middleware(['auth', 'XSS', 'revalidate']);

Route::resource('payment', PaymentController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::get('report/transaction', [TransactionController::class, 'index'])->name('transaction.index');
    }
);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::get('report/income-summary', [ReportController::class, 'incomeSummary'])->name('report.income.summary');
        Route::get('report/expense-summary', [ReportController::class, 'expenseSummary'])->name('report.expense.summary');
        Route::get('report/income-vs-expense-summary', [ReportController::class, 'incomeVsExpenseSummary'])->name('report.income.vs.expense.summary');
        Route::get('report/tax-summary', [ReportController::class, 'taxSummary'])->name('report.tax.summary');
        Route::get('report/profit-loss-summary', [ReportController::class, 'profitLossSummary'])->name('report.profit.loss.summary');
        Route::get('report/invoice-summary', [ReportController::class, 'invoiceSummary'])->name('report.invoice.summary');
        Route::get('report/bill-summary', [ReportController::class, 'billSummary'])->name('report.bill.summary');
        Route::get('report/product-stock-report', [ReportController::class, 'productStock'])->name('report.product.stock.report');
        Route::get('report/invoice-report', [ReportController::class, 'invoiceReport'])->name('report.invoice');
        Route::get('report/account-statement-report', [ReportController::class, 'accountStatement'])->name('report.account.statement');
        Route::get('report/balance-sheet', [ReportController::class, 'balanceSheet'])->name('report.balance.sheet');
        Route::get('report/ledger', [ReportController::class, 'ledgerSummary'])->name('report.ledger');
        Route::get('report/trial-balance', [ReportController::class, 'trialBalanceSummary'])->name('trial.balance');
    }
);


Route::get('proposal/pdf/{id}', [ProposalController::class, 'proposal'])->name('proposal.pdf')->middleware(['XSS', 'revalidate']);


Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::get('proposal/{id}/status/change', [ProposalController::class, 'statusChange'])->name('proposal.status.change');
        Route::get('proposal/{id}/convert', [ProposalController::class, 'convert'])->name('proposal.convert');
        Route::get('proposal/{id}/duplicate', [ProposalController::class, 'duplicate'])->name('proposal.duplicate');
        Route::post('proposal/product/destroy', [ProposalController::class, 'productDestroy'])->name('proposal.product.destroy');
        Route::post('proposal/customer', [ProposalController::class, 'customer'])->name('proposal.customer');
        Route::post('proposal/product', [ProposalController::class, 'product'])->name('proposal.product');
        Route::get('proposal/items', [ProposalController::class, 'items'])->name('proposal.items');
        Route::get('proposal/{id}/sent', [ProposalController::class, 'sent'])->name('proposal.sent');
        Route::get('proposal/{id}/resent', [ProposalController::class, 'resent'])->name('proposal.resent');
        Route::resource('proposal', ProposalController::class);
        Route::get('proposal/create/{cid}', [ProposalController::class, 'create'])->name('proposal.create');
    }
);


Route::get('/proposal/preview/{template}/{color}', [ProposalController::class, 'previewProposal'])->name('proposal.preview');
Route::post('/proposal/template/setting', [ProposalController::class, 'saveProposalTemplateSettings'])->name('proposal.template.setting');


Route::resource('goal', GoalController::class)->middleware(['auth', 'XSS', 'revalidate']);

//Budget Planner //
Route::resource('budget', BudgetController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::resource('account-assets', AssetController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::resource('custom-field', CustomFieldController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::post('chart-of-account/subtype', [ChartOfAccountController::class, 'getSubType'])->name('charofAccount.subType')->middleware(['auth', 'XSS', 'revalidate']);


Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::resource('chart-of-account', ChartOfAccountController::class);
    }
);


Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {

        Route::post('journal-entry/account/destroy', [JournalEntryController::class, 'accountDestroy'])->name('journal.account.destroy');

        Route::resource('journal-entry', JournalEntryController::class);
    }
);

// Client Module

Route::resource('clients', ClientController::class)->middleware(['auth', 'XSS']);
Route::get('/clients/{clientId}/childrens', [ClientController::class, 'showChildren'])->name('clients.childrens'); // Replace YourController
Route::any('client-reset-password/{id}', [ClientController::class, 'clientPassword'])->name('clients.reset');
Route::post('client-reset-password/{id}', [ClientController::class, 'clientPasswordReset'])->name('client.password.update');
Route::post('client/{id}/reactivate', [ClientController::class, 'reactivate'])->name('clients.reactivate');


// Deal Module

Route::post('/deals/user', [DealController::class, 'jsonUser'])->name('deal.user.json');
Route::post('/deals/order', [DealController::class, 'order'])->name('deals.order')->middleware(['auth', 'XSS']);
Route::post('/deals/change-pipeline', [DealController::class, 'changePipeline'])->name('deals.change.pipeline')->middleware(['auth', 'XSS']);
Route::post('/deals/change-deal-status/{id}', [DealController::class, 'changeStatus'])->name('deals.change.status')->middleware(['auth', 'XSS']);





Route::get('/deals/{id}/priorities', [DealController::class, 'priorities'])->name('deals.priorities')->middleware(['auth', 'XSS']);
Route::post('/deals/{id}/priorities', [DealController::class, 'priorityStore'])->name('deals.priorities.store')->middleware(['auth', 'XSS']);









Route::get('/deals/{id}/labels', [DealController::class, 'labels'])->name('deals.labels')->middleware(['auth', 'XSS']);
Route::post('/deals/{id}/labels', [DealController::class, 'labelStore'])->name('deals.labels.store')->middleware(['auth', 'XSS']);







Route::get('/deals/{id}/groups', [DealController::class, 'groups'])->name('deals.groups')->middleware(['auth', 'XSS']);
Route::post('/deals/{id}/groups', [DealController::class, 'groupStore'])->name('deals.groups.store')->middleware(['auth', 'XSS']);














Route::get('/deals/{id}/users', [DealController::class, 'userEdit'])->name('deals.users.edit')->middleware(['auth', 'XSS']);
Route::put('/deals/{id}/users', [DealController::class, 'userUpdate'])->name('deals.users.update')->middleware(['auth', 'XSS']);
Route::delete('/deals/{id}/users/{uid}', [DealController::class, 'userDestroy'])->name('deals.users.destroy')->middleware(['auth', 'XSS']);
Route::get('/deals/{id}/clients', [DealController::class, 'clientEdit'])->name('deals.clients.edit')->middleware(['auth', 'XSS']);
Route::put('/deals/{id}/clients', [DealController::class, 'clientUpdate'])->name('deals.clients.update')->middleware(['auth', 'XSS']);
Route::delete('/deals/{id}/clients/{uid}', [DealController::class, 'clientDestroy'])->name('deals.clients.destroy')->middleware(['auth', 'XSS']);
Route::get('/deals/{id}/products', [DealController::class, 'productEdit'])->name('deals.products.edit')->middleware(['auth', 'XSS']);
Route::put('/deals/{id}/products', [DealController::class, 'productUpdate'])->name('deals.products.update')->middleware(['auth', 'XSS']);
Route::delete('/deals/{id}/products/{uid}', [DealController::class, 'productDestroy'])->name('deals.products.destroy')->middleware(['auth', 'XSS']);
Route::get('/deals/{id}/sources', [DealController::class, 'sourceEdit'])->name('deals.sources.edit')->middleware(['auth', 'XSS']);
Route::put('/deals/{id}/sources', [DealController::class, 'sourceUpdate'])->name('deals.sources.update')->middleware(['auth', 'XSS']);
Route::delete('/deals/{id}/sources/{uid}', [DealController::class, 'sourceDestroy'])->name('deals.sources.destroy')->middleware(['auth', 'XSS']);
Route::post('/deals/{id}/file', [DealController::class, 'fileUpload'])->name('deals.file.upload')->middleware(['auth', 'XSS']);
Route::get('/deals/{id}/file/{fid}', [DealController::class, 'fileDownload'])->name('deals.file.download')->middleware(['auth', 'XSS']);
Route::delete('/deals/{id}/file/delete/{fid}', [DealController::class, 'fileDelete'])->name('deals.file.delete')->middleware(['auth', 'XSS']);
Route::post('/deals/{id}/note', [DealController::class, 'noteStore'])->name('deals.note.store')->middleware(['auth']);
Route::get('/deals/{id}/task', [DealController::class, 'taskCreate'])->name('deals.tasks.create')->middleware(['auth', 'XSS']);
Route::post('/deals/{id}/task', [DealController::class, 'taskStore'])->name('deals.tasks.store')->middleware(['auth', 'XSS']);
Route::get('/deals/{id}/task/{tid}/show', [DealController::class, 'taskShow'])->name('deals.tasks.show')->middleware(['auth', 'XSS']);
Route::get('/deals/{id}/task/{tid}/edit', [DealController::class, 'taskEdit'])->name('deals.tasks.edit')->middleware(['auth', 'XSS']);
Route::put('/deals/{id}/task/{tid}', [DealController::class, 'taskUpdate'])->name('deals.tasks.update')->middleware(['auth', 'XSS']);
Route::put('/deals/{id}/task_status/{tid}', [DealController::class, 'taskUpdateStatus'])->name('deals.tasks.update_status')->middleware(['auth', 'XSS']);
Route::delete('/deals/{id}/task/{tid}', [DealController::class, 'taskDestroy'])->name('deals.tasks.destroy')->middleware(['auth', 'XSS']);
Route::get('/deals/{id}/discussions', [DealController::class, 'discussionCreate'])->name('deals.discussions.create')->middleware(['auth', 'XSS']);
Route::post('/deals/{id}/discussions', [DealController::class, 'discussionStore'])->name('deals.discussion.store')->middleware(['auth', 'XSS']);
Route::get('/deals/{id}/permission/{cid}', [DealController::class, 'permission'])->name('deals.client.permission')->middleware(['auth', 'XSS']);
Route::put('/deals/{id}/permission/{cid}', [DealController::class, 'permissionStore'])->name('deals.client.permissions.store')->middleware(['auth', 'XSS']);
Route::get('/deals/list', [DealController::class, 'deal_list'])->name('deals.list')->middleware(['auth', 'XSS']);




// Deal Calls

Route::get('/deals/{id}/call', [DealController::class, 'callCreate'])->name('deals.calls.create')->middleware(['auth', 'XSS']);
Route::post('/deals/{id}/call', [DealController::class, 'callStore'])->name('deals.calls.store')->middleware(['auth']);
Route::get('/deals/{id}/call/{cid}/edit', [DealController::class, 'callEdit'])->name('deals.calls.edit')->middleware(['auth']);
Route::put('/deals/{id}/call/{cid}', [DealController::class, 'callUpdate'])->name('deals.calls.update')->middleware(['auth']);
Route::delete('/deals/{id}/call/{cid}', [DealController::class, 'callDestroy'])->name('deals.calls.destroy')->middleware(['auth', 'XSS']);



// Deal Email

Route::get('/deals/{id}/email', [DealController::class, 'emailCreate'])->name('deals.emails.create')->middleware(['auth', 'XSS']);
Route::post('/deals/{id}/email', [DealController::class, 'emailStore'])->name('deals.emails.store')->middleware(['auth', 'XSS']);


Route::resource('deals', DealController::class)->middleware(['auth', 'XSS']);


// ============================================
// SEARCH & UTILITY ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::get('/search', [UserController::class, 'search'])->name('search.json');
    Route::get('/users/search', [UserController::class, 'search'])->name('users.search');
});

// ============================================
// STAGES MANAGEMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('/stages/order', [StageController::class, 'order'])->name('stages.order');
    Route::post('/stages/json', [StageController::class, 'json'])->name('stages.json');
    Route::resource('stages', StageController::class);
});

// ============================================
// PIPELINE MANAGEMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::resource('pipelines', PipelineController::class);
});

// ============================================
// PRIORITY MANAGEMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::resource('priorities', PriorityController::class);
});

// ============================================
// LABEL MANAGEMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::resource('labels', LabelController::class);
});

// ============================================
// GROUP MANAGEMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::resource('groups', GroupController::class);
});

// ============================================
// SOURCE MANAGEMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::resource('sources', SourceController::class);
});

// ============================================
// PAYMENT MANAGEMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::resource('payments', PaymentController::class);
});

// ============================================
// CUSTOM FIELDS MANAGEMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::resource('custom_fields', CustomFieldController::class);
});

// Leads Module

Route::post('/lead_stages/order', [LeadStageController::class, 'order'])->name('lead_stages.order')->middleware(['auth']);

Route::resource('lead_stages', LeadStageController::class)->middleware(['auth']);

Route::post('/leads/json', [LeadController::class, 'json'])->name('leads.json');
Route::post('/leads/order', [LeadController::class, 'order'])->name('leads.order')->middleware(['auth', 'XSS']);
Route::get('/leads/list', [LeadController::class, 'lead_list'])->name('leads.list')->middleware(['auth', 'XSS']);
Route::post('/leads/{id}/file', [LeadController::class, 'fileUpload'])->name('leads.file.upload')->middleware(['auth', 'XSS']);
Route::get('/leads/{id}/file/{fid}', [LeadController::class, 'fileDownload'])->name('leads.file.download')->middleware(['auth', 'XSS']);
Route::delete('/leads/{id}/file/delete/{fid}', [LeadController::class, 'fileDelete'])->name('leads.file.delete')->middleware(['auth', 'XSS']);
Route::post('/leads/{id}/note', [LeadController::class, 'noteStore'])->name('leads.note.store')->middleware(['auth']);



Route::get('/leads/{id}/priorities', [LeadController::class, 'priorities'])->name('leads.priorities')->middleware(['auth', 'XSS']);
Route::post('/leads/{id}/priorities', [LeadController::class, 'priorityStore'])->name('leads.priorities.store')->middleware(['auth', 'XSS']);




Route::get('/leads/{id}/labels', [LeadController::class, 'labels'])->name('leads.labels')->middleware(['auth', 'XSS']);
Route::post('/leads/{id}/labels', [LeadController::class, 'labelStore'])->name('leads.labels.store')->middleware(['auth', 'XSS']);





Route::get('/leads/{id}/groups', [LeadController::class, 'groups'])->name('leads.groups')->middleware(['auth', 'XSS']);
Route::post('/leads/{id}/groups', [LeadController::class, 'groupStore'])->name('leads.groups.store')->middleware(['auth', 'XSS']);





Route::get('/leads/{id}/users', [LeadController::class, 'userEdit'])->name('leads.users.edit')->middleware(['auth', 'XSS']);
Route::put('/leads/{id}/users', [LeadController::class, 'userUpdate'])->name('leads.users.update')->middleware(['auth', 'XSS']);
Route::delete('/leads/{id}/users/{uid}', [LeadController::class, 'userDestroy'])->name('leads.users.destroy')->middleware(['auth', 'XSS']);
Route::get('/leads/{id}/products', [LeadController::class, 'productEdit'])->name('leads.products.edit')->middleware(['auth', 'XSS']);
Route::put('/leads/{id}/products', [LeadController::class, 'productUpdate'])->name('leads.products.update')->middleware(['auth', 'XSS']);
Route::delete('/leads/{id}/products/{uid}', [LeadController::class, 'productDestroy'])->name('leads.products.destroy')->middleware(['auth', 'XSS']);
Route::get('/leads/{id}/sources', [LeadController::class, 'sourceEdit'])->name('leads.sources.edit')->middleware(['auth', 'XSS']);
Route::put('/leads/{id}/sources', [LeadController::class, 'sourceUpdate'])->name('leads.sources.update')->middleware(['auth', 'XSS']);
Route::delete('/leads/{id}/sources/{uid}', [LeadController::class, 'sourceDestroy'])->name('leads.sources.destroy')->middleware(['auth', 'XSS']);
Route::get('/leads/{id}/discussions', [LeadController::class, 'discussionCreate'])->name('leads.discussions.create')->middleware(['auth', 'XSS']);
Route::post('/leads/{id}/discussions', [LeadController::class, 'discussionStore'])->name('leads.discussion.store')->middleware(['auth', 'XSS']);
Route::get('/leads/{id}/show_convert', [LeadController::class, 'showConvertToDeal'])->name('leads.convert.deal')->middleware(['auth', 'XSS']);
Route::post('/leads/{id}/convert', [LeadController::class, 'convertToDeal'])->name('leads.convert.to.deal')->middleware(['auth', 'XSS']);




// Lead Calls
Route::get('/leads/{id}/call', [LeadController::class, 'callCreate'])->name('leads.calls.create')->middleware(['auth', 'XSS']);
Route::post('/leads/{id}/call', [LeadController::class, 'callStore'])->name('leads.calls.store')->middleware(['auth']);
Route::get('/leads/{id}/call/{cid}/edit', [LeadController::class, 'callEdit'])->name('leads.calls.edit')->middleware(['auth', 'XSS']);
Route::put('/leads/{id}/call/{cid}', [LeadController::class, 'callUpdate'])->name('leads.calls.update')->middleware(['auth']);
Route::delete('/leads/{id}/call/{cid}', [LeadController::class, 'callDestroy'])->name('leads.calls.destroy')->middleware(['auth', 'XSS']);



// Lead Email

Route::get('/leads/{id}/email', [LeadController::class, 'emailCreate'])->name('leads.emails.create')->middleware(['auth', 'XSS']);
Route::post('/leads/{id}/email', [LeadController::class, 'emailStore'])->name('leads.emails.store')->middleware(['auth']);

Route::resource('leads', LeadController::class)->middleware(['auth', 'XSS']);

// end Leads Module

Route::get('user/{id}/plan', [UserController::class, 'upgradePlan'])->name('plan.upgrade')->middleware(['auth', 'XSS']);
Route::get('user/{id}/plan/{pid}', [UserController::class, 'activePlan'])->name('plan.active')->middleware(['auth', 'XSS']);
Route::get('/{uid}/notification/seen', [UserController::class, 'notificationSeen'])->name('notification.seen');




// Email Templates
Route::get('email_template_lang/{id}/{lang?}', [EmailTemplateController::class, 'manageEmailLang'])->name('manage.email.language')->middleware(['auth', 'XSS']);
Route::put('email_template_store/{id}', [EmailTemplateController::class, 'updateStatus'])->name('status.email.language')->middleware(['auth']);
Route::put('email_template_store/{pid}', [EmailTemplateController::class, 'storeEmailLang'])->name('store.email.language')->middleware(['auth']);
Route::resource('email_template', EmailTemplateController::class)->middleware(['auth', 'XSS']);


// End Email Templates

// HRM
Route::resource('user', UserController::class)->middleware(['auth', 'XSS']);
Route::post('employee/json', [EmployeeController::class, 'json'])->name('employee.json')->middleware(['auth', 'XSS']);
Route::post('branch/employee/json', [EmployeeController::class, 'employeeJson'])->name('branch.employee.json')->middleware(['auth', 'XSS']);
Route::get('employee-profile', [EmployeeController::class, 'profile'])->name('employee.profile')->middleware(['auth', 'XSS']);
Route::get('show-employee-profile/{id}', [EmployeeController::class, 'profileShow'])->name('show.employee.profile')->middleware(['auth', 'XSS']);

Route::get('lastlogin', [EmployeeController::class, 'lastLogin'])->name('lastlogin')->middleware(['auth', 'XSS']);

Route::resource('employee', EmployeeController::class)->middleware(['auth', 'XSS']);


Route::post('employee/getdepartment', [EmployeeController::class, 'getDepartment'])->name('employee.getdepartment')->middleware(['auth', 'XSS']);

Route::resource('department', DepartmentController::class)->middleware(['auth', 'XSS']);
Route::resource('designation', DesignationController::class)->middleware(['auth', 'XSS']);
Route::resource('document', DocumentController::class)->middleware(['auth', 'XSS']);
Route::resource('branch', BranchController::class)->middleware(['auth', 'XSS']);


// Hrm EmployeeController

Route::get('employee/salary/{eid}', [SetSalaryController::class, 'employeeBasicSalary'])->name('employee.basic.salary')->middleware(['auth', 'XSS']);


//payslip

Route::resource('paysliptype', PayslipTypeController::class)->middleware(['auth', 'XSS']);
Route::resource('allowance', AllowanceController::class)->middleware(['auth', 'XSS']);
Route::resource('commission', CommissionController::class)->middleware(['auth', 'XSS']);
Route::resource('allowanceoption', AllowanceOptionController::class)->middleware(['auth', 'XSS']);
Route::resource('loanoption', LoanOptionController::class)->middleware(['auth', 'XSS']);
Route::resource('deductionoption', DeductionOptionController::class)->middleware(['auth', 'XSS']);
Route::resource('loan', LoanController::class)->middleware(['auth', 'XSS']);
Route::resource('saturationdeduction', SaturationDeductionController::class)->middleware(['auth', 'XSS']);
Route::resource('otherpayment', OtherPaymentController::class)->middleware(['auth', 'XSS']);
Route::resource('overtime', OvertimeController::class)->middleware(['auth', 'XSS']);


Route::get('employee/salary/{eid}', [SetSalaryController::class, 'employeeBasicSalary'])->name('employee.basic.salary')->middleware(['auth', 'XSS']);
Route::post('employee/update/sallary/{id}', [SetSalaryController::class, 'employeeUpdateSalary'])->name('employee.salary.update')->middleware(['auth', 'XSS']);
Route::get('salary/employeeSalary', [SetSalaryController::class, 'employeeSalary'])->name('employeesalary')->middleware(['auth', 'XSS']);
Route::resource('setsalary', SetSalaryController::class)->middleware(['auth', 'XSS']);


Route::get('allowances/create/{eid}', [AllowanceController::class, 'allowanceCreate'])->name('allowances.create')->middleware(['auth', 'XSS']);
Route::get('commissions/create/{eid}', [CommissionController::class, 'commissionCreate'])->name('commissions.create')->middleware(['auth', 'XSS']);
Route::get('loans/create/{eid}', [LoanController::class, 'loanCreate'])->name('loans.create')->middleware(['auth', 'XSS']);
Route::get('saturationdeductions/create/{eid}', [SaturationDeductionController::class, 'saturationdeductionCreate'])->name('saturationdeductions.create')->middleware(['auth', 'XSS']);
Route::get('otherpayments/create/{eid}', [OtherPaymentController::class, 'otherpaymentCreate'])->name('otherpayments.create')->middleware(['auth', 'XSS']);
Route::get('overtimes/create/{eid}', [OvertimeController::class, 'overtimeCreate'])->name('overtimes.create')->middleware(['auth', 'XSS']);
Route::get('payslip/paysalary/{id}/{date}', [PaySlipController::class, 'paysalary'])->name('payslip.paysalary')->middleware(['auth', 'XSS']);
Route::get('payslip/bulk_pay_create/{date}', [PaySlipController::class, 'bulk_pay_create'])->name('payslip.bulk_pay_create')->middleware(['auth', 'XSS']);
Route::post('payslip/bulkpayment/{date}', [PaySlipController::class, 'bulkpayment'])->name('payslip.bulkpayment')->middleware(['auth', 'XSS']);
Route::post('payslip/search_json', [PaySlipController::class, 'search_json'])->name('payslip.search_json')->middleware(['auth', 'XSS']);
Route::get('payslip/employeepayslip', [PaySlipController::class, 'employeepayslip'])->name('payslip.employeepayslip')->middleware(['auth', 'XSS']);
Route::get('payslip/showemployee/{id}', [PaySlipController::class, 'showemployee'])->name('payslip.showemployee')->middleware(['auth', 'XSS']);
Route::get('payslip/editemployee/{id}', [PaySlipController::class, 'editemployee'])->name('payslip.editemployee')->middleware(['auth', 'XSS']);
Route::post('payslip/editemployee/{id}', [PaySlipController::class, 'updateEmployee'])->name('payslip.updateemployee')->middleware(['auth', 'XSS']);
Route::get('payslip/pdf/{id}/{m}', [PaySlipController::class, 'pdf'])->name('payslip.pdf')->middleware(['auth', 'XSS']);
Route::get('payslip/payslipPdf/{id}', [PaySlipController::class, 'payslipPdf'])->name('payslip.payslipPdf')->middleware(['auth', 'XSS']);
Route::get('payslip/send/{id}/{m}', [PaySlipController::class, 'send'])->name('payslip.send')->middleware(['auth', 'XSS']);
Route::get('payslip/delete/{id}', [PaySlipController::class, 'destroy'])->name('payslip.delete')->middleware(['auth', 'XSS']);
Route::resource('payslip', PaySlipController::class)->middleware(['auth', 'XSS']);



Route::resource('company-policy', CompanyPolicyController::class)->middleware(['auth', 'XSS']);
Route::resource('indicator', IndicatorController::class)->middleware(['auth', 'XSS']);
Route::resource('appraisal', AppraisalController::class)->middleware(['auth', 'XSS']);

Route::post('branch/employee/json', [EmployeeController::class, 'employeeJson'])->name('branch.employee.json')->middleware(['auth', 'XSS']);

Route::resource('goaltype', GoalTypeController::class)->middleware(['auth', 'XSS']);
Route::resource('goaltracking', GoalTrackingController::class)->middleware(['auth', 'XSS']);
Route::resource('account-assets', AssetController::class)->middleware(['auth', 'XSS']);


Route::post('event/getdepartment', [EventController::class, 'getdepartment'])->name('event.getdepartment')->middleware(['auth', 'XSS']);
Route::post('event/getemployee', [EventController::class, 'getemployee'])->name('event.getemployee')->middleware(['auth', 'XSS']);


Route::resource('event', EventController::class)->middleware(['auth', 'XSS']);

Route::post('meeting/getdepartment', [MeetingController::class, 'getdepartment'])->name('meeting.getdepartment')->middleware(['auth', 'XSS']);
Route::post('meeting/getemployee', [MeetingController::class, 'getemployee'])->name('meeting.getemployee')->middleware(['auth', 'XSS']);


Route::resource('meeting', MeetingController::class)->middleware(['auth', 'XSS']);
Route::resource('trainingtype', TrainingTypeController::class)->middleware(['auth', 'XSS']);
Route::resource('trainer', TrainerController::class)->middleware(['auth', 'XSS']);

Route::post('training/status', [TrainingController::class, 'updateStatus'])->name('training.status')->middleware(['auth', 'XSS']);

Route::resource('training', TrainingController::class)->middleware(['auth', 'XSS']);




// HRM - HR Module

Route::resource('awardtype', AwardTypeController::class)->middleware(['auth', 'XSS']);
Route::resource('award', AwardController::class)->middleware(['auth', 'XSS']);
Route::resource('resignation', ResignationController::class)->middleware(['auth', 'XSS']);
Route::resource('travel', TravelController::class)->middleware(['auth', 'XSS']);
Route::resource('promotion', PromotionController::class)->middleware(['auth', 'XSS']);
Route::resource('complaint', ComplaintController::class)->middleware(['auth', 'XSS']);
Route::resource('warning', WarningController::class)->middleware(['auth', 'XSS']);
Route::get('warnings/{id}', [WarningController::class, 'getWarningById'])->middleware(['auth', 'XSS']);

Route::resource('termination', TerminationController::class)->middleware(['auth', 'XSS']);
Route::resource('suspension', SuspensionController::class)->middleware(['auth', 'XSS']);

Route::get('termination/{id}/description', [TerminationController::class, 'description'])->name('termination.description');
Route::resource('terminationtype', TerminationTypeController::class)->middleware(['auth', 'XSS']);

Route::post('announcement/getdepartment', [AnnouncementController::class, 'getdepartment'])->name('announcement.getdepartment');
Route::post('announcement/getemployee', [AnnouncementController::class, 'getemployee'])->name('announcement.getemployee');
Route::resource('announcement', AnnouncementController::class)->middleware(['auth', 'XSS']);
Route::resource('holiday', HolidayController::class)->middleware(['auth', 'XSS']);
Route::get('holiday-calender', [HolidayController::class, 'calender'])->name('holiday.calender');
//------------------------------------  Recruitement --------------------------------
Route::resource('job-category', JobCategoryController::class)->middleware(['auth', 'XSS']);
Route::resource('job-stage', JobStageController::class)->middleware(['auth', 'XSS']);
Route::post('job-stage/order', [JobStageController::class, 'order'])->name('job.stage.order');
Route::resource('job', JobController::class)->middleware(['auth', 'XSS']);
Route::get('career/{id}/{lang}', [JobController::class, 'career'])->name('career')->middleware(['XSS']);
Route::get('job/requirement/{code}/{lang}', [JobController::class, 'jobRequirement'])->name('job.requirement')->middleware(['XSS']);
Route::get('job/apply/{code}/{lang}', [JobController::class, 'jobApply'])->name('job.apply')->middleware(['XSS']);
Route::post('job/apply/data/{code}', [JobController::class, 'jobApplyData'])->name('job.apply.data')->middleware(['XSS']);
Route::get('candidates-job-applications', [JobApplicationController::class, 'candidate'])->name('job.application.candidate')->middleware(['XSS']);

Route::resource('job-application', JobApplicationController::class)->middleware(['auth', 'XSS']);
Route::post('job-application/order', [JobApplicationController::class, 'order'])->name('job.application.order')->middleware(['XSS']);
Route::post('job-application/{id}/rating', [JobApplicationController::class, 'rating'])->name('job.application.rating')->middleware(['XSS']);
Route::delete('job-application/{id}/archive', [JobApplicationController::class, 'archive'])->name('job.application.archive')->middleware(['auth', 'XSS']);
Route::post('job-application/{id}/skill/store', [JobApplicationController::class, 'addSkill'])->name('job.application.skill.store')->middleware(['auth', 'XSS']);
Route::post('job-application/{id}/note/store', [JobApplicationController::class, 'addNote'])->name('job.application.note.store')->middleware(['auth', 'XSS']);
Route::delete('job-application/{id}/note/destroy', [JobApplicationController::class, 'destroyNote'])->name('job.application.note.destroy')->middleware(['auth', 'XSS']);
Route::post('job-application/getByJob', [JobApplicationController::class, 'getByJob'])->name('get.job.application')->middleware(['auth', 'XSS']);
Route::get('job-onboard', [JobApplicationController::class, 'jobOnBoard'])->name('job.on.board')->middleware(['auth', 'XSS']);
Route::get('job-onboard/create/{id}', [JobApplicationController::class, 'jobBoardCreate'])->name('job.on.board.create')->middleware(['auth', 'XSS']);
Route::post('job-onboard/store/{id}', [JobApplicationController::class, 'jobBoardStore'])->name('job.on.board.store')->middleware(['auth', 'XSS']);
Route::get('job-onboard/edit/{id}', [JobApplicationController::class, 'jobBoardEdit'])->name('job.on.board.edit')->middleware(['auth', 'XSS']);
Route::post('job-onboard/update/{id}', [JobApplicationController::class, 'jobBoardUpdate'])->name('job.on.board.update')->middleware(['auth', 'XSS']);
Route::delete('job-onboard/delete/{id}', [JobApplicationController::class, 'jobBoardDelete'])->name('job.on.board.delete')->middleware(['auth', 'XSS']);
Route::get('job-onboard/convert/{id}', [JobApplicationController::class, 'jobBoardConvert'])->name('job.on.board.convert')->middleware(['auth', 'XSS']);
Route::post('job-onboard/convert/{id}', [JobApplicationController::class, 'jobBoardConvertData'])->name('job.on.board.convert')->middleware(['auth', 'XSS']);
Route::post('job-application/stage/change', [JobApplicationController::class, 'stageChange'])->name('job.application.stage.change')->middleware(['auth', 'XSS']);

Route::resource('custom-question', CustomQuestionController::class)->middleware(['auth', 'XSS']);
Route::resource('interview-schedule', InterviewScheduleController::class)->middleware(['auth', 'XSS']);
Route::get('interview-schedule/create/{id?}', [InterviewScheduleController::class, 'create'])->name('interview-schedule.create')->middleware(['auth', 'XSS']);
Route::get('taskboard/{view?}', [ProjectTaskController::class, 'taskBoard'])->name('taskBoard.view')->middleware(['auth', 'XSS']);
Route::get('taskboard-view', [ProjectTaskController::class, 'taskboardView'])->name('project.taskboard.view')->middleware(['auth', 'XSS']);


Route::resource('document-upload', DucumentUploadController::class)->middleware(['auth', 'XSS']);
Route::resource('transfer', TransferController::class)->middleware(['auth', 'XSS']);
Route::get('attendanceemployee/bulkattendance', [AttendanceEmployeeController::class, 'bulkAttendance'])->name('attendanceemployee.bulkattendance')->middleware(['auth', 'XSS']);
Route::post('attendanceemployee/bulkattendance', [AttendanceEmployeeController::class, 'bulkAttendanceData'])->name('attendanceemployee.bulkattendance')->middleware(['auth', 'XSS']);
Route::post('attendanceemployee/attendance', [AttendanceEmployeeController::class, 'attendance'])->name('attendanceemployee.attendance')->middleware(['auth', 'XSS']);
Route::post('wfh/attendance', [AttendanceEmployeeController::class, 'avatarVoiceAttendance'])->name('wfh.attendance')->middleware(['auth', 'XSS']);
Route::get('attendance.index', [AttendanceEmployeeController::class, 'index'])->name('attendance.index')->middleware(['auth', 'XSS']);
Route::resource('attendanceemployee', AttendanceEmployeeController::class)->middleware(['auth', 'XSS']);
Route::resource('leavetype', LeaveTypeController::class)->middleware(['auth', 'XSS']);
Route::get('report/leave', [ReportController::class, 'leave'])->name('report.leave')->middleware(['auth', 'XSS']);
Route::get('employee/{id}/leave/{status}/{type}/{month}/{year}', [ReportController::class, 'employeeLeave'])->name('report.employee.leave')->middleware(['auth', 'XSS']);
Route::get('leave/{id}/action', [LeaveController::class, 'action'])->name('leave.action')->middleware(['auth', 'XSS']);
Route::post('leave/changeaction', [LeaveController::class, 'changeaction'])->name('leave.changeaction')->middleware(['auth', 'XSS']);
Route::post('leave/jsoncount', [LeaveController::class, 'jsoncount'])->name('leave.jsoncount')->middleware(['auth', 'XSS']);

Route::resource('leave', LeaveController::class)->middleware(['auth', 'XSS']);


Route::get('reports-leave', [ReportController::class, 'leave'])->name('report.leave')->middleware(['auth', 'XSS']);
Route::get('employee/{id}/leave/{status}/{type}/{month}/{year}', [ReportController::class, 'employeeLeave'])->name('report.employee.leave')->middleware(['auth', 'XSS']);
Route::get('reports-payroll', [ReportController::class, 'payroll'])->name('report.payroll')->middleware(['auth', 'XSS']);
Route::get('reports-monthly-attendance', [ReportController::class, 'monthlyAttendance'])->name('report.monthly.attendance')->middleware(['auth', 'XSS']);
Route::get('report/attendance/{month}/{branch}/{department}', [ReportController::class, 'exportCsv'])->name('report.attendance')->middleware(['auth', 'XSS']);


// User Module

Route::get('users/{view?}', [UserController::class, 'index'])->name('users')->middleware(['auth', 'XSS']);
Route::get('users-view', [UserController::class, 'filterUserView'])->name('filter.user.view')->middleware(['auth', 'XSS']);
Route::get('checkuserexists', [UserController::class, 'checkUserExists'])->name('user.exists')->middleware(['auth', 'XSS']);
Route::get('profile', [UserController::class, 'profile'])->name('profile')->middleware(['auth', 'XSS']);
Route::post('/profile', [UserController::class, 'updateProfile'])->name('update.profile')->middleware(['auth', 'XSS']);
Route::get('user/info/{id}', [UserController::class, 'userInfo'])->name('users.info')->middleware(['auth', 'XSS']);
Route::get('user/{id}/info/{type}', [UserController::class, 'getProjectTask'])->name('user.info.popup')->middleware(['auth', 'XSS']);
Route::delete('users/{id}', [UserController::class, 'destroy'])->name('user.destroy')->middleware(['auth', 'XSS']);
// End User Module


// Search
Route::get('/search', [UserController::class, 'search'])->name('search.json')->middleware(['auth', 'XSS']);
// end


// Milestone Module

Route::get('projects/{id}/milestone', [ProjectController::class, 'milestone'])->name('project.milestone')->middleware(['auth', 'XSS']);



Route::post('projects/{id}/milestone', [ProjectController::class, 'milestoneStore'])->name('project.milestone.store')->middleware(['auth', 'XSS']);
Route::get('projects/milestone/{id}/edit', [ProjectController::class, 'milestoneEdit'])->name('project.milestone.edit')->middleware(['auth', 'XSS']);
Route::post('projects/milestone/{id}', [ProjectController::class, 'milestoneUpdate'])->name('project.milestone.update')->middleware(['auth', 'XSS']);
Route::delete('projects/milestone/{id}', [ProjectController::class, 'milestoneDestroy'])->name('project.milestone.destroy')->middleware(['auth', 'XSS']);
Route::get('projects/milestone/{id}/show', [ProjectController::class, 'milestoneShow'])->name('project.milestone.show')->middleware(['auth', 'XSS']);

// End Milestone

// Project Module

Route::get('invite-project-member/{id}', [ProjectController::class, 'inviteMemberView'])->name('invite.project.member.view')->middleware(['auth', 'XSS']);
Route::post('invite-project-user-member', [ProjectController::class, 'inviteProjectUserMember'])->name('invite.project.user.member')->middleware(['auth', 'XSS']);

Route::delete('projects/{id}/users/{uid}', [ProjectController::class, 'destroyProjectUser'])->name('projects.user.destroy')->middleware(['auth', 'XSS']);
Route::get('project/{view?}', [ProjectController::class, 'index'])->name('projects.list')->middleware(['auth', 'XSS']);
Route::get('projects-view', [ProjectController::class, 'filterProjectView'])->name('filter.project.view')->middleware(['auth', 'XSS']);
Route::post('projects/{id}/store-stages/{slug}', [ProjectController::class, 'storeProjectTaskStages'])->name('project.stages.store')->middleware(['auth', 'XSS']);


Route::patch('remove-user-from-project/{project_id}/{user_id}', [ProjectController::class, 'removeUserFromProject'])->name('remove.user.from.project')->middleware(['auth', 'XSS']);
Route::get('projects-users', [ProjectController::class, 'loadUser'])->name('project.user')->middleware(['auth', 'XSS']);
Route::get('projects/{id}/gantt/{duration?}', [ProjectController::class, 'gantt'])->name('projects.gantt')->middleware(['auth', 'XSS']);
Route::post('projects/{id}/gantt', [ProjectController::class, 'ganttPost'])->name('projects.gantt.post')->middleware(['auth', 'XSS']);


Route::resource('projects', ProjectController::class)->middleware(['auth', 'XSS']);

// User Permission
Route::get('projects/{id}/user/{uid}/permission', [ProjectController::class, 'userPermission'])->name('projects.user.permission')->middleware(['auth', 'XSS']);
Route::post('projects/{id}/user/{uid}/permission', [ProjectController::class, 'userPermissionStore'])->name('projects.user.permission.store')->middleware(['auth', 'XSS']);

// End Project Module


// Task Module

Route::get('stage/{id}/tasks', [ProjectTaskController::class, 'getStageTasks'])->name('stage.tasks')->middleware(['auth', 'XSS']);

// Project Task Module

Route::get('/projects/{id}/task', [ProjectTaskController::class, 'index'])->name('projects.tasks.index')->middleware(['auth', 'XSS']);
Route::get('/projects/{pid}/task/{sid}', [ProjectTaskController::class, 'create'])->name('projects.tasks.create')->middleware(['auth', 'XSS']);
Route::post('/projects/{pid}/task/{sid}', [ProjectTaskController::class, 'store'])->name('projects.tasks.store')->middleware(['auth', 'XSS']);
Route::get('/projects/{id}/task/{tid}/show', [ProjectTaskController::class, 'show'])->name('projects.tasks.show')->middleware(['auth', 'XSS']);
Route::get('/projects/{id}/task/{tid}/edit', [ProjectTaskController::class, 'edit'])->name('projects.tasks.edit')->middleware(['auth', 'XSS']);
Route::post('/projects/{id}/task/update/{tid}', [ProjectTaskController::class, 'update'])->name('projects.tasks.update')->middleware(['auth', 'XSS']);
Route::delete('/projects/{id}/task/{tid}', [ProjectTaskController::class, 'destroy'])->name('projects.tasks.destroy')->middleware(['auth', 'XSS']);
Route::patch('/projects/{id}/task/order', [ProjectTaskController::class, 'taskOrderUpdate'])->name('tasks.update.order')->middleware(['auth', 'XSS']);
Route::patch('update-task-priority-color', [ProjectTaskController::class, 'updateTaskPriorityColor'])->name('update.task.priority.color')->middleware(['auth', 'XSS']);


Route::post('/projects/{id}/comment/{tid}/file', [ProjectTaskController::class, 'commentStoreFile'])->name('comment.store.file')->middleware(['auth', 'XSS']);
Route::delete('/projects/{id}/comment/{tid}/file/{fid}', [ProjectTaskController::class, 'commentDestroyFile'])->name('comment.destroy.file');
Route::post('/projects/{id}/comment/{tid}', [ProjectTaskController::class, 'commentStore'])->name('task.comment.store');
Route::delete('/projects/{id}/comment/{tid}/{cid}', [ProjectTaskController::class, 'commentDestroy'])->name('comment.destroy');
Route::post('/projects/{id}/checklist/{tid}', [ProjectTaskController::class, 'checklistStore'])->name('checklist.store');
Route::post('/projects/{id}/checklist/update/{cid}', [ProjectTaskController::class, 'checklistUpdate'])->name('checklist.update');
Route::delete('/projects/{id}/checklist/{cid}', [ProjectTaskController::class, 'checklistDestroy'])->name('checklist.destroy');
Route::post('/projects/{id}/change/{tid}/fav', [ProjectTaskController::class, 'changeFav'])->name('change.fav');
Route::post('/projects/{id}/change/{tid}/complete', [ProjectTaskController::class, 'changeCom'])->name('change.complete');
Route::post('/projects/{id}/change/{tid}/progress', [ProjectTaskController::class, 'changeProg'])->name('change.progress');
Route::get('/projects/task/{id}/get', [ProjectTaskController::class, 'taskGet'])->name('projects.tasks.get')->middleware(['auth', 'XSS']);
Route::get('/calendar/{id}/show', [ProjectTaskController::class, 'calendarShow'])->name('task.calendar.show')->middleware(['auth', 'XSS']);
Route::post('/calendar/{id}/drag', [ProjectTaskController::class, 'calendarDrag'])->name('task.calendar.drag');
Route::get('calendar/{task}/{pid?}', [ProjectTaskController::class, 'calendarView'])->name('task.calendar')->middleware(['auth', 'XSS']);

Route::resource('project-task-stages', TaskStageController::class)->middleware(['auth', 'XSS']);
Route::post('/project-task-stages/order', [TaskStageController::class, 'order'])->name('project-task-stages.order');

Route::post('project-task-new-stage', [TaskStageController::class, 'storingValue'])->name('new-task-stage')->middleware(['auth', 'XSS']);
// End Task Module


// Project Expense Module
Route::get('/projects/{id}/expense', [ExpenseController::class, 'index'])->name('projects.expenses.index')->middleware(['auth', 'XSS']);
Route::get('/projects/{pid}/expense/create', [ExpenseController::class, 'create'])->name('projects.expenses.create')->middleware(['auth', 'XSS']);
Route::post('/projects/{pid}/expense/store', [ExpenseController::class, 'store'])->name('projects.expenses.store')->middleware(['auth', 'XSS']);
Route::get('/projects/{id}/expense/{eid}/edit', [ExpenseController::class, 'edit'])->name('projects.expenses.edit')->middleware(['auth', 'XSS']);
Route::post('/projects/{id}/expense/{eid}', [ExpenseController::class, 'update'])->name('projects.expenses.update')->middleware(['auth', 'XSS']);
Route::delete('/projects/{eid}/expense/', [ExpenseController::class, 'destroy'])->name('projects.expenses.destroy')->middleware(['auth', 'XSS']);
Route::get('/expense-list', [ExpenseController::class, 'expenseList'])->name('expense.list')->middleware(['auth', 'XSS']);





Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::resource('contractType', ContractTypeController::class);
    }
);


// Project Timesheet

Route::get('append-timesheet-task-html', [TimesheetController::class, 'appendTimesheetTaskHTML'])->name('append.timesheet.task.html')->middleware(['auth', 'XSS']);
Route::get('timesheet-table-view', [TimesheetController::class, 'filterTimesheetTableView'])->name('filter.timesheet.table.view')->middleware(['auth', 'XSS']);
Route::get('timesheet-view', [TimesheetController::class, 'filterTimesheetView'])->name('filter.timesheet.view')->middleware(['auth', 'XSS']);
Route::get('timesheet-list', [TimesheetController::class, 'timesheetList'])->name('timesheet.list')->middleware(['auth', 'XSS']);
Route::get('timesheet-list-get', [TimesheetController::class, 'timesheetListGet'])->name('timesheet.list.get')->middleware(['auth', 'XSS']);
Route::get('/project/{id}/timesheet', [TimesheetController::class, 'timesheetView'])->name('timesheet.index')->middleware(['auth', 'XSS']);
Route::get('/project/{id}/timesheet/create', [TimesheetController::class, 'timesheetCreate'])->name('timesheet.create')->middleware(['auth', 'XSS']);
Route::post('/project/timesheet', [TimesheetController::class, 'timesheetCreate'])->name('timesheet.store')->middleware(['auth', 'XSS']);
Route::get('/project/timesheet/{project_id}/edit/{timesheet_id', [TimesheetController::class, 'timesheetEdit'])->name('timesheet.edit')->middleware(['auth', 'XSS']);
Route::any('/project/timesheet/update/{timesheet_id}', [TimesheetController::class, 'timesheetUpdate'])->name('timesheet.update')->middleware(['auth', 'XSS']);

Route::delete('/project/timesheet/{timesheet_id}', [TimesheetController::class, 'timesheetDestroy'])->name('timesheet.destroy')->middleware(['auth', 'XSS']);




Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {
        Route::resource('projectstages', ProjectstagesController::class);
        Route::post('/projectstages/order', [ProjectstagesController::class, 'order'])->name('projectstages.order')->middleware(['auth', 'XSS']);
        Route::post('projects/bug/kanban/order', [ProjectController::class, 'bugKanbanOrder'])->name('bug.kanban.order');
        Route::get('projects/{id}/bug/kanban', [ProjectController::class, 'bugKanban'])->name('task.bug.kanban');
        Route::get('projects/{id}/bug', [ProjectController::class, 'bug'])->name('task.bug');
        Route::get('projects/{id}/bug/create', [ProjectController::class, 'bugCreate'])->name('task.bug.create');
        Route::post('projects/{id}/bug/store', [ProjectController::class, 'bugStore'])->name('task.bug.store');
        Route::get('projects/{id}/bug/{bid}/edit', [ProjectController::class, 'bugEdit'])->name('task.bug.edit');
        Route::post('projects/{id}/bug/{bid}/update', [ProjectController::class, 'bugUpdate'])->name('task.bug.update');
        Route::delete('projects/{id}/bug/{bid}/destroy', [ProjectController::class, 'bugDestroy'])->name('task.bug.destroy');
        Route::get('projects/{id}/bug/{bid}/show', [ProjectController::class, 'bugShow'])->name('task.bug.show');
        Route::post('projects/{id}/bug/{bid}/comment', [ProjectController::class, 'bugCommentStore'])->name('bug.comment.store');
        Route::post('projects/bug/{bid}/file', [ProjectController::class, 'bugCommentStoreFile'])->name('bug.comment.file.store');
        Route::delete('projects/bug/comment/{id}', [ProjectController::class, 'bugCommentDestroy'])->name('bug.comment.destroy');
        Route::delete('projects/bug/file/{id}', [ProjectController::class, 'bugCommentDestroyFile'])->name('bug.comment.file.destroy');

        Route::resource('bugstatus', BugStatusController::class);
        Route::post('/bugstatus/order', [BugStatusController::class, 'order'])->name('bugstatus.order');
        Route::get('bugs-report/{view?}', [ProjectTaskController::class, 'allBugList'])->name('bugs.view')->middleware(['auth', 'XSS']);
    }
);
// User_Todo Module

Route::post('/todo/create', [UserController::class, 'todo_store'])->name('todo.store')->middleware(['auth', 'XSS']);
Route::post('/todo/{id}/update', [UserController::class, 'todo_update'])->name('todo.update')->middleware(['auth', 'XSS']);
Route::delete('/todo/{id}', [UserController::class, 'todo_destroy'])->name('todo.destroy')->middleware(['auth', 'XSS']);
Route::get('/change/mode', [UserController::class, 'changeMode'])->name('change.mode')->middleware(['auth', 'XSS']);
Route::get('dashboard-view', [DashboardController::class, 'filterView'])->name('dashboard.view')->middleware(['auth', 'XSS']);
Route::get('dashboard', [DashboardController::class, 'clientView'])->name('client.dashboard.view')->middleware(['auth', 'XSS']);


// saas
Route::resource('users', UserController::class)->middleware(['auth', 'XSS', 'revalidate']);
Route::resource('plans', PlanController::class)->middleware(['auth', 'XSS', 'revalidate']);
Route::resource('coupons', CouponController::class)->middleware(['auth', 'XSS', 'revalidate']);

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::get('/orders', [StripePaymentController::class, 'index'])->name('order.index');
        Route::get('/stripe/{code}', [StripePaymentController::class, 'stripe'])->name('stripe');
        Route::post('/stripe', [StripePaymentController::class, 'stripePost'])->name('stripe.post');
    }
);

Route::get('/apply-couponsudo apt-get autoremove', [CouponController::class, 'applyCoupon'])->name('apply.coupon')->middleware(['auth', 'XSS', 'revalidate']);



//================================= Form Builder ====================================//


// Form Builder
Route::resource('form_builder', FormBuilderController::class)->middleware(['auth', 'XSS']);



// Form link base view
Route::get('/form/{code}', [FormBuilderController::class, 'formView'])->name('form.view')->middleware(['XSS']);
Route::post('/form_view_store', [FormBuilderController::class, 'formViewStore'])->name('form.view.store')->middleware(['XSS']);

// Form Field

Route::get('/form_builder/{id}/field', [FormBuilderController::class, 'fieldCreate'])->name('form.field.create')->middleware(['auth', 'XSS']);
Route::post('/form_builder/{id}/field', [FormBuilderController::class, 'fieldStore'])->name('form.field.store')->middleware(['auth', 'XSS']);
Route::get('/form_builder/{id}/field/{fid}/show', [FormBuilderController::class, 'fieldShow'])->name('form.field.show')->middleware(['auth', 'XSS']);
Route::get('/form_builder/{id}/field/{fid}/edit', [FormBuilderController::class, 'fieldEdit'])->name('form.field.edit')->middleware(['auth', 'XSS']);
Route::post('/form_builder/{id}/field/{fid}', [FormBuilderController::class, 'fieldUpdate'])->name('form.field.update')->middleware(['auth', 'XSS']);
Route::delete('/form_builder/{id}/field/{fid}', [FormBuilderController::class, 'fieldDestroy'])->name('form.field.destroy')->middleware(['auth', 'XSS']);



// Form Response
Route::get('/form_response/{id}', [FormBuilderController::class, 'viewResponse'])->name('form.response')->middleware(['auth', 'XSS']);
Route::get('/response/{id}', [FormBuilderController::class, 'responseDetail'])->name('response.detail')->middleware(['auth', 'XSS']);


// Form Field Bind
Route::get('/form_field/{id}', [FormBuilderController::class, 'formFieldBind'])->name('form.field.bind')->middleware(['auth', 'XSS']);
Route::post('/form_field_store/{id}}', [FormBuilderController::class, 'bindStore'])->name('form.bind.store')->middleware(['auth', 'XSS']);

// end Form Builder


Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::get('contract/{id}/description', [ContractController::class, 'description'])->name('contract.description');
        Route::get('contract/grid', [ContractController::class, 'grid'])->name('contract.grid');
        Route::resource('contract', ContractController::class);
    }
);



// ============================================
// CONTRACT MANAGEMENT ROUTES
// ============================================

// ============================================
// Contract File Operations
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('/contract/{id}/file', [ContractController::class, 'fileUpload'])->name('contract.file.upload');
    Route::get('/contract/{id}/file/{fid}', [ContractController::class, 'fileDownload'])->name('contracts.file.download');
    Route::delete('/contract/{id}/file/delete/{fid}', [ContractController::class, 'fileDelete'])->name('contracts.file.delete');
});

// ============================================
// Contract PDF & Print Operations
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::get('contract/pdf/{id}', [ContractController::class, 'pdffromcontract'])->name('contract.download.pdf');
    Route::get('contract/{id}/get_contract', [ContractController::class, 'printContract'])->name('get.contract');
});

// ============================================
// Contract Status & Description
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('/contract_status_edit/{id}', [ContractController::class, 'contract_status_edit'])->name('contract.status');
});

Route::middleware(['auth'])->group(function () {
    Route::post('contract/{id}/contract_description', [ContractController::class, 'contract_descriptionStore'])->name('contract.contract_description.store');
});

// ============================================
// Contract Copy Operations
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::get('/contract/copy/{id}', [ContractController::class, 'copycontract'])->name('contract.copy');
    Route::post('/contract/copy/store', [ContractController::class, 'copycontractstore'])->name('contract.copy.store');
});

// ============================================
// Contract Mail & Signature
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::get('/contract/{id}/mail', [ContractController::class, 'sendmailContract'])->name('send.mail.contract');
    Route::get('/signature/{id}', [ContractController::class, 'signature'])->name('signature');
    Route::post('/signaturestore', [ContractController::class, 'signatureStore'])->name('signaturestore');
});

// ============================================
// Contract Comments & Notes
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::post('/contract/{id}/comment', [ContractController::class, 'commentStore'])->name('comment.store');
    Route::delete('/contract/{id}/comment', [ContractController::class, 'commentDestroy'])->name('comment_store.destroy');
    Route::post('/contract/{id}/notes', [ContractController::class, 'noteStore'])->name('note_store.store');
    Route::delete('/contract/{id}/notes', [ContractController::class, 'noteDestroy'])->name('note_store.destroy');
});

// ============================================
// Contract Client & Project Relations
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::get('get-projects/{client_id}', [ContractController::class, 'clientByProject'])->name('project.by.user.id');
    Route::any('/contract/clients/select/{bid}', [ContractController::class, 'clientwiseproject'])->name('contract.clients.select');
});

// ============================================
// CUSTOM LANDING PAGE ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::get('/landingpage', [LandingPageSectionController::class, 'index'])->name('custom_landing_page.index');
    Route::get('/LandingPage/show/{id}', [LandingPageSectionController::class, 'show']);
    Route::post('/LandingPage/setConetent', [LandingPageSectionController::class, 'setConetent']);
});

// ============================================
// Landing Page Section Loader (Dynamic View)
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::get(
        '/get_landing_page_section/{name}',
        function ($name) {
            $plans = \DB::table('plans')->get();
            return view('custom_landing_page.' . $name, compact('plans'));
        }
    );
});




// ============================================
// LANDING PAGE SECTION ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('/LandingPage/removeSection/{id}', [LandingPageSectionController::class, 'removeSection']);
    Route::post('/LandingPage/setOrder', [LandingPageSectionController::class, 'setOrder']);
    Route::post('/LandingPage/copySection', [LandingPageSectionController::class, 'copySection']);
});

// ============================================
// DOCUMENT LINK COPY ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::get('/customer/invoice/{id}/', [InvoiceController::class, 'invoiceLink'])->name('invoice.link.copy');
    Route::get('/vender/bill/{id}/', [BillController::class, 'invoiceLink'])->name('bill.link.copy');
});

Route::middleware(['auth', 'XSS', 'revalidate'])->group(function () {
    Route::get('/vendor/purchase/{id}/', [PurchaseController::class, 'purchaseLink'])->name('purchase.link.copy');
    Route::get('/customer/proposal/{id}/', [ProposalController::class, 'invoiceLink'])->name('proposal.link.copy');
});

// ============================================
// PAYPAL PLAN PAYMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS', 'revalidate'])->group(function () {
    Route::post('plan-pay-with-paypal', [PaypalController::class, 'planPayWithPaypal'])->name('plan.pay.with.paypal');
    Route::get('{id}/plan-get-payment-status', [PaypalController::class, 'planGetPaymentStatus'])->name('plan.get.payment.status');
});

// ============================================
// PLAN PAYMENT GATEWAYS ROUTES
// ============================================

// ============================================
// PAYSTACK PLAN PAYMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('/plan-pay-with-paystack', [PaystackPaymentController::class, 'planPayWithPaystack'])->name('plan.pay.with.paystack');
    Route::get('/plan/paystack/{pay_id}/{plan_id}', [PaystackPaymentController::class, 'getPaymentStatus'])->name('plan.paystack');
});

// ============================================
// FLUTTERWAVE PLAN PAYMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('/plan-pay-with-flaterwave', [FlutterwavePaymentController::class, 'planPayWithFlutterwave'])->name('plan.pay.with.flaterwave');
    Route::get('/plan/flaterwave/{txref}/{plan_id}', [FlutterwavePaymentController::class, 'getPaymentStatus'])->name('plan.flaterwave');
});

// ============================================
// RAZORPAY PLAN PAYMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('/plan-pay-with-razorpay', [RazorpayPaymentController::class, 'planPayWithRazorpay'])->name('plan.pay.with.razorpay');
    Route::get('/plan/razorpay/{txref}/{plan_id}', [RazorpayPaymentController::class, 'getPaymentStatus'])->name('plan.razorpay');
});

// ============================================
// PAYTM PLAN PAYMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('/plan-pay-with-paytm', [PaytmPaymentController::class, 'planPayWithPaytm'])->name('plan.pay.with.paytm');
    Route::post('/plan/paytm/{plan}', [PaytmPaymentController::class, 'getPaymentStatus'])->name('plan.paytm');
});

// ============================================
// MERCADO PAGO PLAN PAYMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('/plan-pay-with-mercado', [MercadoPaymentController::class, 'planPayWithMercado'])->name('plan.pay.with.mercado');
    Route::get('/plan/mercado/{plan}/{amount}', [MercadoPaymentController::class, 'getPaymentStatus'])->name('plan.mercado');
});

// ============================================
// MOLLIE PLAN PAYMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('/plan-pay-with-mollie', [MolliePaymentController::class, 'planPayWithMollie'])->name('plan.pay.with.mollie');
    Route::get('/plan/mollie/{plan}', [MolliePaymentController::class, 'getPaymentStatus'])->name('plan.mollie');
});

// ============================================
// SKRILL PLAN PAYMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('/plan-pay-with-skrill', [SkrillPaymentController::class, 'planPayWithSkrill'])->name('plan.pay.with.skrill');
    Route::get('/plan/skrill/{plan}', [SkrillPaymentController::class, 'getPaymentStatus'])->name('plan.skrill');
});

// ============================================
// COINGATE PLAN PAYMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('/plan-pay-with-coingate', [CoingatePaymentController::class, 'planPayWithCoingate'])->name('plan.pay.with.coingate');
    Route::get('/plan/coingate/{plan}', [CoingatePaymentController::class, 'getPaymentStatus'])->name('plan.coingate');
});


Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::get('order', [StripePaymentController::class, 'index'])->name('order.index');
        Route::get('/stripe/{code}', [StripePaymentController::class, 'stripe'])->name('stripe');
        Route::post('/stripe', [StripePaymentController::class, 'stripePost'])->name('stripe.post');
    }
);
Route::post('plan-pay-with-paypal', [PaypalController::class, 'planPayWithPaypal'])->name('plan.pay.with.paypal')->middleware(['auth', 'XSS', 'revalidate']);
Route::get('{id}/plan-get-payment-status', [PaypalController::class, 'planGetPaymentStatus'])->name('plan.get.payment.status')->middleware(['auth', 'XSS', 'revalidate']);





// ============================================
// INVOICE PAYMENT GATEWAYS ROUTES
// ============================================

// ============================================
// STRIPE PAYMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('customer/{id}/payment', [StripePaymentController::class, 'addpayment'])->name('customer.payment');
});

// ============================================
// PAYPAL PAYMENT ROUTES
// ============================================
Route::middleware(['XSS'])->group(function () {
    Route::post('{id}/pay-with-paypal', [PaypalController::class, 'customerPayWithPaypal'])->name('customer.pay.with.paypal');
    Route::get('{id}/get-payment-status/{amount}', [PaypalController::class, 'customerGetPaymentStatus'])->name('customer.get.payment.status');
});

// ============================================
// PAYSTACK PAYMENT ROUTES
// ============================================
Route::middleware(['XSS'])->group(function () {
    Route::post('/customer-pay-with-paystack', [PaystackPaymentController::class, 'customerPayWithPaystack'])->name('customer.pay.with.paystack');
    Route::get('/customer/paystack/{pay_id}/{invoice_id}', [PaystackPaymentController::class, 'getInvoicePaymentStatus'])->name('customer.paystack');
});

// ============================================
// FLUTTERWAVE PAYMENT ROUTES
// ============================================
Route::middleware(['XSS'])->group(function () {
    Route::post('/customer-pay-with-flaterwave', [FlutterwavePaymentController::class, 'customerPayWithFlutterwave'])->name('customer.pay.with.flaterwave');
    Route::get('/customer/flaterwave/{txref}/{invoice_id}', [FlutterwavePaymentController::class, 'getInvoicePaymentStatus'])->name('customer.flaterwave');
});

// ============================================
// RAZORPAY PAYMENT ROUTES
// ============================================
Route::middleware(['XSS'])->group(function () {
    Route::post('/customer-pay-with-razorpay', [RazorpayPaymentController::class, 'customerPayWithRazorpay'])->name('customer.pay.with.razorpay');
    Route::get('/customer/razorpay/{txref}/{invoice_id}', [RazorpayPaymentController::class, 'getInvoicePaymentStatus'])->name('customer.razorpay');
});

// ============================================
// PAYTM PAYMENT ROUTES
// ============================================
Route::middleware(['XSS'])->group(function () {
    Route::post('/customer-pay-with-paytm', [PaytmPaymentController::class, 'customerPayWithPaytm'])->name('customer.pay.with.paytm');
    Route::post('/customer/paytm/{invoice}/{amount}', [PaytmPaymentController::class, 'getInvoicePaymentStatus'])->name('customer.paytm');
});

// ============================================
// MERCADO PAGO PAYMENT ROUTES
// ============================================
Route::middleware(['XSS'])->group(function () {
    Route::post('/customer-pay-with-mercado', [MercadoPaymentController::class, 'customerPayWithMercado'])->name('customer.pay.with.mercado');
    Route::get('/customer/mercado/{invoice}', [MercadoPaymentController::class, 'getInvoicePaymentStatus'])->name('customer.mercado');
});

// ============================================
// MOLLIE PAYMENT ROUTES
// ============================================
Route::middleware(['XSS'])->group(function () {
    Route::post('/customer-pay-with-mollie', [MolliePaymentController::class, 'customerPayWithMollie'])->name('customer.pay.with.mollie');
    Route::get('/customer/mollie/{invoice}/{amount}', [MolliePaymentController::class, 'getInvoicePaymentStatus'])->name('customer.mollie');
});

// ============================================
// SKRILL PAYMENT ROUTES
// ============================================
Route::middleware(['XSS'])->group(function () {
    Route::post('/customer-pay-with-skrill', [SkrillPaymentController::class, 'customerPayWithSkrill'])->name('customer.pay.with.skrill');
    Route::get('/customer/skrill/{invoice}/{amount}', [SkrillPaymentController::class, 'getInvoicePaymentStatus'])->name('customer.skrill');
});

// ============================================
// COINGATE PAYMENT ROUTES
// ============================================
Route::middleware(['XSS'])->group(function () {
    Route::post('/customer-pay-with-coingate', [CoingatePaymentController::class, 'customerPayWithCoingate'])->name('customer.pay.with.coingate');
    Route::get('/customer/coingate/{invoice}/{amount}', [CoingatePaymentController::class, 'getInvoicePaymentStatus'])->name('customer.coingate');
});



Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::get('support/{id}/reply', [SupportController::class, 'reply'])->name('support.reply');
        Route::post('support/{id}/reply', [SupportController::class, 'replyAnswer'])->name('support.reply.answer');
        Route::get('support/grid', [SupportController::class, 'grid'])->name('support.grid');
        Route::resource('support', SupportController::class);
    }
);

Route::resource('competencies', CompetenciesController::class)->middleware(['auth', 'XSS']);


Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::resource('performanceType', PerformanceTypeController::class);
    }
);




// ============================================
// PLAN REQUEST MODULE ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::get('plan_request', [PlanRequestController::class, 'index'])->name('plan_request.index');
    Route::get('request_frequency/{id}', [PlanRequestController::class, 'requestView'])->name('request.view');
    Route::get('request_send/{id}', [PlanRequestController::class, 'userRequest'])->name('send.request');
    Route::get('request_response/{id}/{response}', [PlanRequestController::class, 'acceptRequest'])->name('response.request');
    Route::get('request_cancel/{id}', [PlanRequestController::class, 'cancelRequest'])->name('request.cancel');
});

// ============================================
// IMPORT/EXPORT DATA ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    // Product Service Import/Export
    Route::get('export/productservice', [ProductServiceController::class, 'export'])->name('productservice.export');
    Route::get('import/productservice/file', [ProductServiceController::class, 'importFile'])->name('productservice.file.import');
    Route::post('import/productservice', [ProductServiceController::class, 'import'])->name('productservice.import');

    // Customer Import/Export
    Route::get('export/customer', [CustomerController::class, 'export'])->name('customer.export');
    Route::get('import/customer/file', [CustomerController::class, 'importFile'])->name('customer.file.import');
    Route::post('import/customer', [CustomerController::class, 'import'])->name('customer.import');

    // Vendor Import/Export
    Route::get('export/vender', [VenderController::class, 'export'])->name('vender.export');
    Route::get('import/vender/file', [VenderController::class, 'importFile'])->name('vender.file.import');
    Route::post('import/vender', [VenderController::class, 'import'])->name('vender.import');

    // Invoice Export
    Route::get('export/invoice', [InvoiceController::class, 'export'])->name('invoice.export');

    // Proposal Export
    Route::get('export/proposal', [ProposalController::class, 'export'])->name('proposal.export');

    // Bill Export
    Route::get('export/bill', [BillController::class, 'export'])->name('bill.export');
});

// ============================================
// TIME-TRACKER ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('stop-tracker', [DashboardController::class, 'stopTracker'])->name('stop.tracker');
    Route::get('time-tracker', [TimeTrackerController::class, 'index'])->name('time.tracker');
    Route::get('projects/time-tracker/{id}', [ProjectController::class, 'tracker'])->name('projecttime.tracker');
});

Route::middleware(['auth', 'XSS'])->group(function () {
    Route::delete('tracker/{tid}/destroy', [TimeTrackerController::class, 'Destroy'])->name('tracker.destroy');
    Route::post('tracker/image-view', [TimeTrackerController::class, 'getTrackerImages'])->name('tracker.image.view');
    Route::delete('tracker/image-remove', [TimeTrackerController::class, 'removeTrackerImages'])->name('tracker.image.remove');
});

// ============================================
// ZOOM MEETING ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::resource('zoom-meeting', ZoomMeetingController::class);
    Route::any('/zoom-meeting/projects/select/{bid}', [ZoomMeetingController::class, 'projectwiseuser'])->name('zoom-meeting.projects.select');
    Route::get('zoom-meeting-calender', [ZoomMeetingController::class, 'calender'])->name('zoom-meeting.calender');
});

// ============================================
// PAYMENTWALL - PLAN PAYMENT ROUTES
// ============================================
Route::middleware(['XSS'])->group(function () {
    Route::post('/paymentwalls', [PaymentWallPaymentController::class, 'paymentwall'])->name('plan.paymentwallpayment');
});

Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('/plan-pay-with-paymentwall/{plan}', [PaymentWallPaymentController::class, 'planPayWithPaymentWall'])->name('plan.pay.with.paymentwall');
});

Route::middleware(['auth', 'XSS'])->group(function () {
    Route::get('/plan/{flag}', [PaymentWallPaymentController::class, 'planeerror'])->name('error.plan.show');
});

// ============================================
// PAYMENTWALL - INVOICE PAYMENT ROUTES
// ============================================
Route::middleware(['XSS'])->group(function () {
    Route::post('/paymentwall', [PaymentWallPaymentController::class, 'invoicepaymentwall'])->name('invoice.paymentwallpayment');
    Route::post('/invoice-pay-with-paymentwall/{plan}', [PaymentWallPaymentController::class, 'invoicePayWithPaymentwall'])->name('invoice.pay.with.paymentwall');
});

Route::middleware(['auth', 'XSS'])->group(function () {
    Route::get('/invoices/{flag}/{invoice}', [PaymentWallPaymentController::class, 'invoiceerror'])->name('error.invoice.show');
});

// ============================================
// WAREHOUSE (POS SYSTEM) ROUTES
// ============================================
Route::middleware(['auth', 'XSS', 'revalidate'])->group(function () {
    Route::resource('warehouse', WarehouseController::class);
});


Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
            'revalidate',
        ],
    ],
    function () {
        Route::get('purchase/items', [PurchaseController::class, 'items'])->name('purchase.items');
        Route::resource('purchase', PurchaseController::class);


        //    Route::get('/bill/{id}/', 'PurchaseController@purchaseLink')->name('purchase.link.copy');
        Route::get('purchase/{id}/payment', [PurchaseController::class, 'payment'])->name('purchase.payment');
        Route::post('purchase/{id}/payment', [PurchaseController::class, 'createPayment'])->name('purchase.payment');
        Route::post('purchase/{id}/payment/{pid}/destroy', [PurchaseController::class, 'paymentDestroy'])->name('purchase.payment.destroy');
        Route::post('purchase/product/destroy', [PurchaseController::class, 'productDestroy'])->name('purchase.product.destroy');
        Route::post('purchase/vender', [PurchaseController::class, 'vender'])->name('purchase.vender');
        Route::post('purchase/product', [PurchaseController::class, 'product'])->name('purchase.product');
        Route::get('purchase/create/{cid}', [PurchaseController::class, 'create'])->name('purchase.create');
        Route::get('purchase/{id}/sent', [PurchaseController::class, 'sent'])->name('purchase.sent');
        Route::get('purchase/{id}/resent', [PurchaseController::class, 'resent'])->name('purchase.resent');
    }

);



// ============================================
// POS PRINT SETTINGS & PREVIEW ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::get('pos-print-setting', [SystemController::class, 'posPrintIndex'])->name('pos.print.setting');
    Route::get('purchase/preview/{template}/{color}', [PurchaseController::class, 'previewPurchase'])->name('purchase.preview');
    Route::get('pos/preview/{template}/{color}', [PosController::class, 'previewPos'])->name('pos.preview');
});

// ============================================
// TEMPLATE SETTINGS ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('/purchase/template/setting', [PurchaseController::class, 'savePurchaseTemplateSettings'])->name('purchase.template.setting');
    Route::post('/pos/template/setting', [PosController::class, 'savePosTemplateSettings'])->name('pos.template.setting');
});

// ============================================
// PDF GENERATION & POS DATA ROUTES
// ============================================
Route::middleware(['auth', 'XSS', 'revalidate'])->group(function () {
    Route::get('purchase/pdf/{id}', [PurchaseController::class, 'purchase'])->name('purchase.pdf');
    Route::get('pos/pdf/{id}', [PosController::class, 'pos'])->name('pos.pdf');
    Route::get('pos/data/store', [PosController::class, 'store'])->name('pos.data.store');
});

// ============================================
// POS RESOURCE ROUTES
// ============================================
Route::middleware(['auth', 'XSS', 'revalidate'])->group(function () {
    Route::resource('pos', PosController::class);
});

// ============================================
// PRODUCT & CART MANAGEMENT ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    // Product Categories & Search
    Route::get('product-categories', [ProductServiceCategoryController::class, 'getProductCategories'])->name('product.categories');
    Route::get('name-search-products', [ProductServiceCategoryController::class, 'searchProductsByName'])->name('name.search.products');
    Route::get('search-products', [ProductServiceController::class, 'searchProducts'])->name('search.products');

    // Cart Operations
    Route::get('add-to-cart/{id}/{session}', [ProductServiceController::class, 'addToCart']);
    Route::patch('update-cart', [ProductServiceController::class, 'updateCart']);
    Route::patch('remove-from-cart', [ProductServiceController::class, 'removeFromCart']);
});

// ============================================
// POS REPORTS ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::any('report/pos', [PosController::class, 'report'])->name('pos.report');
});

// ============================================
// POS BARCODE ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::get('barcode/pos', [PosController::class, 'barcode'])->name('pos.barcode');
    Route::get('setting/pos', [PosController::class, 'setting'])->name('pos.setting');
    Route::get('print/pos', [PosController::class, 'printBarcode'])->name('pos.print');
    Route::post('pos/getproduct', [PosController::class, 'getproduct'])->name('pos.getproduct');
    Route::any('pos-receipt', [PosController::class, 'receipt'])->name('pos.receipt');
    Route::post('/cartdiscount', [PosController::class, 'cartdiscount'])->name('cartdiscount');
});

// ============================================
// BARCODE SETTINGS ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('barcode/settings', [PosController::class, 'BarcodesettingStore'])->name('barcode.setting');
});

// ============================================
// STORAGE SETTINGS ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('storage-settings', [SystemController::class, 'storageSettingStore'])->name('storage.setting.store');
});

// ============================================
// APPRAISAL ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('/appraisals', [AppraisalController::class, 'empByStar'])->name('empByStar');
    Route::post('/appraisals1', [AppraisalController::class, 'empByStar1'])->name('empByStar1');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/getemployee', [AppraisalController::class, 'getemployee'])->name('getemployee');
});

// ============================================
// OFFER LETTER ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('setting/offerlatter/{lang?}', [SystemController::class, 'offerletterupdate'])->name('offerlatter.update');
    Route::get('setting/offerlatter', [SystemController::class, 'companyIndex'])->name('get.offerlatter.language');
    Route::get('job-onboard/pdf/{id}', [JobApplicationController::class, 'offerletterPdf'])->name('offerlatter.download.pdf');
    Route::get('job-onboard/doc/{id}', [JobApplicationController::class, 'offerletterDoc'])->name('offerlatter.download.doc');
});

// ============================================
// JOINING LETTER ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('setting/joiningletter/{lang?}', [SystemController::class, 'joiningletterupdate'])->name('joiningletter.update');
    Route::get('setting/joiningletter/', [SystemController::class, 'companyIndex'])->name('get.joiningletter.language');
    Route::get('employee/pdf/{id}', [EmployeeController::class, 'joiningletterPdf'])->name('joiningletter.download.pdf');
    Route::get('employee/doc/{id}', [EmployeeController::class, 'joiningletterDoc'])->name('joininglatter.download.doc');
});

// ============================================
// EXPERIENCE CERTIFICATE ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('setting/exp/{lang?}', [SystemController::class, 'experienceCertificateupdate'])->name('experiencecertificate.update');
    Route::get('setting/exp', [SystemController::class, 'companyIndex'])->name('get.experiencecertificate.language');
    Route::get('employee/exppdf/{id}', [EmployeeController::class, 'ExpCertificatePdf'])->name('exp.download.pdf');
    Route::get('employee/expdoc/{id}', [EmployeeController::class, 'ExpCertificateDoc'])->name('exp.download.doc');
});

// ============================================
// NOC (No Objection Certificate) ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::post('setting/noc/{lang?}', [SystemController::class, 'NOCupdate'])->name('noc.update');
    Route::get('setting/noc', [SystemController::class, 'companyIndex'])->name('get.noc.language');
    Route::get('employee/nocpdf/{id}', [EmployeeController::class, 'NocPdf'])->name('noc.download.pdf');
    Route::get('employee/nocdoc/{id}', [EmployeeController::class, 'NocDoc'])->name('noc.download.doc');
});

// ============================================
// PROJECT REPORTS ROUTES
// ============================================
Route::middleware(['auth', 'XSS'])->group(function () {
    Route::resource('/project_report', ProjectReportController::class);
    Route::post('/project_report_data', [ProjectReportController::class, 'ajax_data'])->name('projects.ajax');
    Route::post('/project_report/tasks/{id}', [ProjectReportController::class, 'ajax_tasks_report'])->name('tasks.report.ajaxdata');
});

Route::middleware(['auth', 'XSS'])->group(function () {
    Route::get('export/task_report/{id}', [ProjectReportController::class, 'export'])->name('project_report.export');
});


// added by jamil
// voice section start here

Route::middleware(['auth'])->group(function () {
    Route::get('leaderboard.daily', [LeaderboardController::class, 'dailyLeads'])->name('leaderboard.daily');

    // Monthly Leads route
    Route::get('leaderboard.monthly', [LeaderboardController::class, 'monthlyLeads'])->name('leaderboard.monthly');

    Route::get('leaderboard', [LeaderboardController::class, 'show'])->name('leaderboard');

    Route::post('/team-leaderboard/{team}', [LeaderboardController::class, 'updateHcOverride'])
    ->name('team.hc-override');
    Route::get('monthly-stats-leaderboard', [LeaderboardController::class, 'showMonthly'])->name('monthly-stats-leaderboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/daily-leads', [LeadController::class, 'dailyLeads'])->name('leads.daily');
    Route::post('/leads-export', [LeadController::class, 'exportLeads'])->name('leads-export');
    Route::get('/voice-section', [LeadController::class, 'voiceSection'])->name('voice-section');
    Route::match(['get', 'post'], '/leads-export-all', [LeadController::class, 'exportAllLeads'])->name('leads-export-all');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/lead-recording', [LeadSearchController::class, 'index'])->name('lead.index');
    Route::post('/lead-recording', [LeadSearchController::class, 'searchLeadRecording'])->name('lead.search');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/voiceqa/create', [VoiceQALeadController::class, 'createForm'])->name('voiceqa.create');
    Route::get('/my-voice-leads', [VoiceQALeadController::class, 'showUserLeads'])->name('my-voice-leads');
    Route::post('/voiceqa/store', [VoiceQALeadController::class, 'store'])->name('voiceqa.store');
});



Route::middleware(['auth'])->group(function () {
    Route::get('/qa-section', [ExcelImportController::class, 'index'])->name('qa-section');
    Route::post('/import-excel', [ExcelImportController::class, 'import'])->name('import.excel');
    Route::post('/import-avatar-excel', [ExcelImportController::class, 'avatarimport'])->name('import.avatar-excel');
    Route::get('/imported-leads', [ExcelImportController::class, 'showUserLeads'])->name('imported.leads');
});

// avatar routes here
Route::middleware(['auth'])->group(function () {
    Route::get('/avatar-section', [AvatarController::class, 'index'])->name('avatar-section');
    Route::get('/leads.create', [AvatarController::class, 'manualform'])->name('leads.create');
    Route::get('/my-avatar-checked-leads', [AvatarController::class, 'checked'])->name('my-avatar-checked-leads');
    Route::get('/avatar-leaderboard', [AvatarController::class, 'leaderboards'])->name('avatar-leaderboard');
    Route::get('/avatar-leaderboard-daily', [LeaderboardController::class, 'dailyAvatarLeads'])->name('avatar-leaderboard-daily');
    Route::get('/avatar-leaderboard-monthly', [LeaderboardController::class, 'monthlyAvatarLeads'])->name('avatar-leaderboard-monthly');
    Route::get('/avatar-leads', [AvatarController::class, 'avatarleads'])->name('avatar-leads');
    Route::get('/all_avatar-leads', [AvatarController::class, 'all_avatar_leads'])->name('all_avatar-leads');
    Route::post('/avatar-leads', [AvatarController::class, 'all_avatar_leads'])->name('avatar.all_avatar_leads');
    Route::get('/shrink-leads', [AvatarController::class, 'shrinkAvatarLeads'])->name('avatarleads');
    Route::get('/shrinkleads', [AvatarController::class, 'shrinkAvatarLeads'])->name('avatarleads');
    Route::get('/avatar_qa_leads', [AvatarController::class, 'Qachecked']);
    Route::get('/avatar-q-a-leads', [AvatarController::class, 'indexedit'])->name('avatarLeads');
    Route::put('/leads/{lead}', [AvatarController::class, 'updateStatus'])->name('updateLeadStatus');
    Route::post('/export-avatar-leads', [AvatarController::class, 'exportAvatarLeads'])->name('export.avatar.leads');
    Route::get('/daily-avatarleads', [AvatarController::class, 'dailyLeads'])->name('avatarleads.daily');
    Route::post('/avatarleads', [AvatarController::class, 'store'])->name('manual-leads.store');




    // List of Avatar Monitoring routes
    Route::get('avatar_monitoring', [AvatarMonitoringController::class, 'index'])->name('avatar_monitoring.index'); // Index page
    Route::get('avatar_monitoring/create', [AvatarMonitoringController::class, 'create'])->name('avatar_monitoring.create'); // Create form
    Route::post('avatar_monitoring', [AvatarMonitoringController::class, 'store'])->name('avatar_monitoring.store'); // Store record
    Route::get('avatar_monitoring/{id}', [AvatarMonitoringController::class, 'show'])->name('avatar_monitoring.show'); // Show record
    Route::get('avatar_monitoring/{id}/edit', [AvatarMonitoringController::class, 'edit'])->name('avatar_monitoring.edit'); // Edit form
    Route::put('avatar_monitoring/{id}', [AvatarMonitoringController::class, 'update'])->name('avatar_monitoring.update'); // Update record
    Route::delete('/avatar_monitoring/{avatarMonitoring}', [AvatarMonitoringController::class, 'destroy'])
        ->name('avatar_monitoring.destroy');

    // Route for displaying Avatar Monitoring Notifications specific to the logged-in user
    Route::get('my-avatar', [AvatarMonitoringController::class, 'myAvatarNotifications'])
        ->name('avatar_monitoring.my_avatar_notifications')
        ->middleware('auth');  // Ensure that only authenticated users can access it

    // Additional Routes for features
    Route::get('avatar_monitoring/search', [AvatarMonitoringController::class, 'search'])->name('avatar_monitoring.search'); // Search records
    Route::get('avatar_monitoring/export/{type}', [AvatarMonitoringController::class, 'export'])->name('avatar_monitoring.export'); // Export as PDF or PNG
    Route::get('avatar_monitoring/paginate', [AvatarMonitoringController::class, 'paginate'])->name('avatar_monitoring.paginate'); // Pagination handling

});

// teams sections starts here

Route::middleware(['auth'])->group(function () {
    Route::get('/teams', [TeamController::class, 'index'])->name('team.index');
    Route::get('/teams-create', [TeamController::class, 'create'])->name('teams.create');
    Route::post('/teams', [TeamController::class, 'store'])->name('teams.store');
    Route::get('/team-assignment', [TeamController::class, 'showAssignmentForm'])->name('team.assignmentForm');
    Route::post('/team-assignment', [TeamController::class, 'assignToTeam'])->name('team.assignment');
    Route::post('/team-remove-agent', [TeamController::class, 'removeAgentFromTeam'])->name('team.removeAgent');

    Route::get('/teams-overview', [TeamController::class, 'showTeamsOverview'])->name('teams.overview');
    Route::get('/teams-report', [TeamController::class, 'allTeamReports'])->name('teams.report');
    Route::post('/teams-remove-agents', [TeamController::class, 'removeAgentsFromTeam'])->name('team.removeAgents');

    // Route::get('/teams-overview', 'TeamController@showTeamsOverview')->name('teams.overview');
    // Route::post('/teams-remove-agents', 'TeamController@teams-remove-agents')->name('team.removeAgents');

    // Route to display the team selection form
    Route::get('/list-teams', [TeamController::class, 'listTeams'])->name('list_teams');
    Route::put('/update-team-name/{id}', [TeamController::class, 'updateTeamName'])->name('update_team_name');
    Route::put('/update-team-leader/{id}', [TeamController::class, 'updateTeamLeader'])->name('update_team_leader');
    Route::delete('/delete-team/{id}', [TeamController::class, 'deleteTeam'])->name('delete_team');
    Route::get('/agent-reports', [TeamController::class, 'agentReports'])->name('agent_reports');
    Route::get('/approved-reports', [TeamController::class, 'qALeadReports'])->name('approved-reports');
    Route::get('/all_team_reports', [TeamController::class, 'all_team_reports'])->name('all.team.reports');
    Route::get('/all-reports', [TeamController::class, 'all_report'])->name('all-reports');
});

// closer section here


Route::middleware(['auth'])->group(function () {
    Route::get('closer/create', [CloserController::class, 'create'])->name('closer.create');

    Route::get('closer/salesagentshow', [CloserController::class, 'salesagentshow'])->name('closer.salesagentshow');
    Route::get('closer/closerview', [CloserController::class, 'closerview'])->name('closer.closerview');
    Route::get('closer/salesagentshowteam', [CloserController::class, 'salesagentshowforteamlead'])->name('closer.salesagentshowforteamlead');

    Route::get('closer/clientview', [CloserController::class, 'clientview'])->name('closer.clientview');

    Route::get('/sales-agent/export', [CloserController::class, 'exportSalesAgentData'])->name('sales.agent.export');
    Route::get('/team-sales-agent/export', [CloserController::class, 'exportSalesAgentDatateam'])->name('team-sales.agent.export');

    Route::post('closer/store', [CloserController::class, 'store'])->name('closer.store');
    Route::get('/closed-calls', [CloserController::class, 'index'])->name('closed_calls.index');
    Route::get('/closed-calls/{id}', [CloserController::class, 'show'])->name('closed-calls.show');
    Route::get('/client-view', [CloserController::class, 'client_index'])->name('client.index');

    Route::get('/edit-closed-calls/{id}', [CloserController::class, 'edit'])->name('closed_calls.edit');
    Route::put('/edit-closed-calls/{id}', [CloserController::class, 'update'])->name('closed_calls.update');
    Route::get('/get-users/{client_id}', [CloserController::class, 'getUsers']); // Replace YourController

    // closer reports route hrere
    Route::get('closers-reports', [CloserController::class, 'closer_reports'])->name('closers.reports');
    Route::get('closers-stats', [CloserController::class, 'closers_stats'])->name('closers.stats');
    Route::put('/closers-edits/{id}', [CloserController::class, 're_update'])->name('closer-edit.update');
    // client
    Route::post('/closer/check-phone', [CloserController::class, 'checkPhone'])->name('closer.check.phone');
    Route::get('/closer/search-existing', [CloserController::class, 'searchExisting'])->name('closer.search.existing');
    Route::get('/manage-polocies', [CloserController::class, 'clientindex'])->name('manage-polocies.index');
    Route::get('/edit-closed-policy/{id}', [CloserController::class, 'editclient'])->name('closed_policy.edit');
    Route::put('/edit-closed-policy/{id}', [CloserController::class, 'updateclient'])->name('closed_policy.update');
    Route::get('/edit-client-policy/{id}', [CloserController::class, 'editOwnClient'])->name('client_policy.edit');
    Route::put('/edit-client-policy/{id}', [CloserController::class, 'updateOwnClient'])->name('client_policy.update');
    Route::get('/closer/callback/{id}', [CloserController::class, 'callback'])->name('closer.callback');
    Route::get('/dialer-edit/{id}', [CloserController::class, 'editdialer'])->name('closed_policy.edit');
    Route::put('/dialer-edit/{id}', [CloserController::class, 'updatedialer'])->name('dialer-edit.updatedialer');
    Route::get('/closers-edit/{id}', [CloserController::class, 're_edit'])->name('closer-edit.edit');
});

// For Laravel 8+ with namespaces
// Qa section starts here

Route::middleware(['auth'])->group(function () {
    Route::get('/avatar-calls', [QaController::class, 'index'])->name('avatar_calls');
    Route::get('/edit-avatar-calls/{id}', [QaController::class, 'edit'])->name('edit_avatar_calls.edit');
    // Route::put('/edit-avatar-calls/{id}', [QaController::class, 'update'])->name('edit_avatar_calls.update');
    Route::post('/category/update', [QaController::class, 'update']);
    Route::get('avatar-leads_qa-stats', [QaController::class, 'showQaStatsForm'])->name('avatar-leads.show-qa-stats-form');
    // Route::get('avatar-leads/get-qa-stats', [QaController::class, 'getQaStats'])->name('avatar-leads.get-qa-stats');
    Route::post('avatar-leads_qa-stats', [QaController::class, 'showQaStatsForm'])->name('avatar-leads.filter-qa-stats');
    Route::get('/search_avatar_leads', [AvatarController::class, 'search'])->name('search_avatar_leads');
    Route::put('/update_search_avatar_leads/{id}', [AvatarController::class, 'update'])->name('update_search_avatar_leads');
    Route::put('/avatarLeads/{id}', [AvatarController::class, 'update'])->name('avatarLeads.update');
    Route::get('/search', [AvatarController::class, 'search_stats_index'])->name('search.index');
    Route::post('/search', [AvatarController::class, 'search_stats'])->name('search.search');

    Route::get('/search-lead', [QaController::class, 'showSearchForm'])->name('leads.search');
    Route::post('/leads/update', [QaController::class, 'Reqaupdate'])->name('leads.update');
    Route::post('/update-qaperson', [QaController::class, 'updateQaPerson'])->name('update.qaperson');
    Route::post('/reassign-leads', [QaController::class, 'reassignLeads'])->name('reassign.leads');
    Route::get('/qa/person-teams', [QaController::class, 'getQaPersonTeams'])->name('qa.person.teams');

    Route::get('/no-rec-leads', [QaController::class, 'getNoRecordingLeads']);
    Route::post('/no-rec-leads/update', [QaController::class, 'noRecordingUpdate'])->name('no-recording-update');
});

// ============================================
// CARRIER ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/carriers/create', [CarrierController::class, 'create'])->name('carriers.create');
    Route::post('/carriers', [CarrierController::class, 'store'])->name('carriers.store');
    Route::get('/carriers', [CarrierController::class, 'index'])->name('carriers.index');
});

// ============================================
// MONITORING ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    // Create & Store
    Route::get('/monitoring/create', [MonitoringController::class, 'create'])->name('monitoring.create');
    Route::post('/monitoring/store', [MonitoringController::class, 'store'])->name('monitoring.store');

    // List & Show
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{id}', [MonitoringController::class, 'show'])->name('monitoring.show');

    // Edit & Update
    Route::get('/monitoring/{id}/edit', [MonitoringController::class, 'edit'])->name('monitoring.edit');
    Route::put('/monitoring/{monitoring}', [MonitoringController::class, 'update'])->name('monitoring.update');

    // Delete
    Route::delete('/monitoring/{id}', [MonitoringController::class, 'destroy'])->name('monitoring.destroy');

    // User Personal Monitoring
    Route::get('/my-monitoring', [MonitoringController::class, 'myMonitoring'])->name('monitoring.my-monitoring');
});

// ============================================
// RECRUITMENT ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    // Create & Store (Standard)
    Route::get('/recruitment/create', [RecruitmentController::class, 'create'])->name('recruitment.create');
    Route::post('/recruitment/store', [RecruitmentController::class, 'store'])->name('recruitment.store');

    // Create & Store (New/Alternative)
    Route::get('/recruitment/new', [RecruitmentController::class, 'new'])->name('recruitment.new');
    Route::post('/recruitment/newstore', [RecruitmentController::class, 'newstore'])->name('recruitment.newstore');

    // List & Filter
    Route::get('/recruitments', [RecruitmentController::class, 'index'])->name('recruitments.index');
    Route::get('/recruitments/filter', [RecruitmentController::class, 'filter']);
    Route::get('/recruitment/search', [RecruitmentController::class, 'search'])->name('recruitment.search');

    // Reports & Export
    Route::get('/recruitment-reports', [RecruitmentController::class, 'reports'])->name('recruitments.reports');
    Route::get('/recruitments/exportExcel', [RecruitmentController::class, 'exportExcel'])->name('recruitments.exportExcel');
    Route::get('/recruitments/export-all', [RecruitmentController::class, 'exportAll'])->name('recruitments.exportAll');

    // Show & Edit
    Route::get('/recruitments/{id}', [RecruitmentController::class, 'show'])->name('recruitments.show');
    Route::get('/recruitments/{id}/edit', [RecruitmentController::class, 'edit'])->name('recruitments.edit');
    Route::put('/recruitments/{id}', [RecruitmentController::class, 'update'])->name('recruitments.update');

    // Delete
    Route::delete('/recruitments/{id}', [RecruitmentController::class, 'destroy'])->name('recruitments.destroy');

    // Final & Offer Letter
    Route::get('/recruitmentsfinal/{id}', [RecruitmentController::class, 'final'])->name('recruitments.final');
    Route::post('/recruitments/{id}/offer-letter', [RecruitmentController::class, 'sendOfferLetter'])->name('recruitments.offerLetter');
});

// ============================================
// USER CREDENTIALS & DIALER ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/mycredentials', [UserController::class, 'editMyCredentials'])->name('mycredentials.edit');
    Route::post('/mycredentials', [UserController::class, 'updateMyCredentials'])->name('mycredentials.update');

    Route::get('/userscred', [UserController::class, 'userscred'])->name('userscred.edit');
    Route::post('/userscred/update/{id}', [UserController::class, 'usercredupdate'])->name('userscred.update');

    // Vici Dialer Routes
    Route::get('/dialer', [ViciDialerController::class, 'showDialer']);
    Route::get('/dialer-stats', [ViciDialerController::class, 'getDialerStats'])->name('dialer.stats');
});

// ============================================
// OUR PROJECTS ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/our_projects', [OurProjectController::class, 'index'])->name('our_projects.index');
    Route::get('/our_projects/create', [OurProjectController::class, 'create'])->name('our_projects.create');
    Route::post('/our_projects', [OurProjectController::class, 'store'])->name('our_projects.store');
    Route::get('/our_projects/{our_project}', [OurProjectController::class, 'show'])->name('our_projects.show');
    Route::get('/our_projects/{our_project}/edit', [OurProjectController::class, 'edit'])->name('our_projects.edit');
    Route::put('/our_projects/{our_project}', [OurProjectController::class, 'update'])->name('our_projects.update');
    Route::delete('/our_projects/{our_project}', [OurProjectController::class, 'destroy'])->name('our_projects.destroy');
});

// ============================================
// OUR CAMPAIGNS ROUTES (Nested under Projects)
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/our_projects/{our_project}/our_campaigns', [OurCampaignController::class, 'index'])->name('our_projects.our_campaigns.index');
    Route::get('/our_projects/{our_project}/our_campaigns/create', [OurCampaignController::class, 'create'])->name('our_projects.our_campaigns.create');
    Route::post('/our_projects/{our_project}/our_campaigns', [OurCampaignController::class, 'store'])->name('our_projects.our_campaigns.store');
    Route::get('/our_projects/{our_project}/our_campaigns/{our_campaign}/edit', [OurCampaignController::class, 'edit'])->name('our_projects.our_campaigns.edit');
    Route::put('/our_projects/{our_project}/our_campaigns/{our_campaign}', [OurCampaignController::class, 'update'])->name('our_projects.our_campaigns.update');
    Route::delete('/our_projects/{our_project}/our_campaigns/{our_campaign}', [OurCampaignController::class, 'destroy'])->name('our_projects.our_campaigns.destroy');
});

// ============================================
// CAMPAIGN FORMS & FIELDS ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/our_campaigns/{our_campaign}/form', [OurCampaignController::class, 'showForm'])->name('our_campaigns.form');
    Route::post('/our_campaigns/{our_campaign}/submit', [OurCampaignController::class, 'submitForm'])->name('our_campaigns.submit');
    Route::get('/our_campaigns/{our_campaign}/show-with-fields', [OurCampaignController::class, 'showWithFields'])->name('our_campaigns.showWithFields');
    Route::get('/our_projects/{our_project}/our_campaigns/{our_campaign}/qa-form', [OurCampaignController::class, 'showQaForm'])->name('our_campaigns.qa-form');
});

// ============================================
// CAMPAIGN RESPONSES ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/our_campaign/{our_campaign}/responses/{campaign_response}', [CampaignResponseController::class, 'show'])->name('our_campaigns.responses.show');
    Route::put('/our_campaign/{our_campaign}/responses/{campaign_response}', [CampaignResponseController::class, 'update'])->name('our_campaigns.responses.update');
    Route::get('/our_campaigns/{our_campaign}/responses/{campaign_response}/edit', [CampaignResponseController::class, 'edit'])->name('our_campaigns.responses.edit');
    Route::get('/our_campaigns/{our_campaign}/responses', [CampaignResponseController::class, 'index'])->name('our_campaigns.responses.index');
    Route::get('/our_campaigns/{our_campaign}/responses/{campaign_response}/admin_edit', [CampaignResponseController::class, 'admin_edit'])->name('our_campaigns.responses.admin_edit');
    Route::put('/our_campaigns/{our_campaign}/responses/{campaign_response}/client_update', [CampaignResponseController::class, 'client_update'])->name('our_campaigns.responses.client_update');
    Route::delete('/our_campaigns/{our_campaign}/responses/{campaign_response}', [CampaignResponseController::class, 'destroy'])->name('our_campaigns.responses.destroy');
    Route::put('/our_campaigns/{our_campaign}/responses/{campaign_response}', [CampaignResponseController::class, 'admin_update'])->name('oour_campaigns.responses.update2');
    Route::post('/campaign-responses/update-refer-to', [CampaignResponseController::class, 'updateReferTo'])->name('campaign.responses.updateReferTo');
    Route::get('/our_campaigns/{our_campaign}/responses/{campaign_response}/client_edit', [CampaignResponseController::class, 'client_edit'])->name('our_campaigns.responses.client_edit');
    Route::get('/clients/{clientId}/children', [CampaignResponseController::class, 'getChildClients'])->name('clients.children');
    Route::get('/our_campaigns/{our_campaign}/responses/{campaign_response}/closer_edit', [CampaignResponseController::class, 'closer_edit'])->name('our_campaigns.responses.closer_edit');
    Route::put('/our_campaigns/{our_campaign}/responses/{campaign_response}/closer_update', [CampaignResponseController::class, 'closer_update'])->name('our_campaigns.responses.closer_update');
});

// ============================================
// REMINDERS ROUTES
// ============================================
use App\Http\Controllers\ReminderController;

Route::middleware(['auth'])->group(function () {
    Route::get('/reminders/active', [ReminderController::class, 'getActiveReminders'])->name('reminders.active');
    Route::resource('reminders', ReminderController::class);
    Route::post('/reminders/{reminder}/complete', [ReminderController::class, 'markCompleted'])->name('reminders.complete');
    Route::post('/reminders/{reminder}/cancel', [ReminderController::class, 'markCancelled'])->name('reminders.cancel');
});

// ============================================
// ACCOUNTING ENTRIES ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/accounting', [AccountingEntryController::class, 'index'])->name('accounting.index');
    Route::post('/accounting', [AccountingEntryController::class, 'store'])->name('accounting.store');
    Route::get('/accounting/{entry}/edit', [AccountingEntryController::class, 'edit'])->name('accounting.edit');
    Route::put('/accounting/{entry}', [AccountingEntryController::class, 'update'])->name('accounting.update');
    Route::delete('/accounting/{entry}', [AccountingEntryController::class, 'destroy'])->name('accounting.destroy');
});

// ============================================
// EXPENSE ENTRIES ROUTES
// ============================================
Route::middleware(['auth'])->prefix('expense/entries')->group(function () {
    Route::get('/', [ExpenseEntryController::class, 'index'])->name('expense.entries.index');
    Route::get('/create', [ExpenseEntryController::class, 'create'])->name('expense.entries.create');
    Route::post('/', [ExpenseEntryController::class, 'store'])->name('expense.entries.store');
    Route::get('/{expenseEntry}/edit', [ExpenseEntryController::class, 'edit'])->name('expense.entries.edit');
    Route::put('/{expenseEntry}', [ExpenseEntryController::class, 'update'])->name('expense.entries.update');
    Route::delete('/{expenseEntry}', [ExpenseEntryController::class, 'destroy'])->name('expense.entries.destroy');
    Route::get('/balance', [ExpenseEntryController::class, 'balance'])->name('expense.entries.balance');

    // Monthly Expenses
    Route::get('/monthly', [ExpenseEntryController::class, 'monthlyIndex'])->name('expense.monthly.index');
    Route::get('/monthly/create', [ExpenseEntryController::class, 'monthlyCreate'])->name('expense.monthly.create');
    Route::post('/monthly', [ExpenseEntryController::class, 'monthlyStore'])->name('expense.monthly.store');
    Route::get('/monthly/{monthlyExpense}/edit', [ExpenseEntryController::class, 'monthlyEdit'])->name('expense.monthly.edit');
    Route::put('/monthly/{monthlyExpense}', [ExpenseEntryController::class, 'monthlyUpdate'])->name('expense.monthly.update');
    Route::delete('/monthly/{monthlyExpense}', [ExpenseEntryController::class, 'monthlyDestroy'])->name('expense.monthly.destroy');
});

// ============================================
// EXPENSE REPORTS ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/expense-report', [ExpenseEntryController::class, 'showReport'])->name('expense.report');
    Route::get('/expense-report/filter', [ExpenseEntryController::class, 'filterReportByDateRange'])->name('expense.report.filter');
    Route::get('/expense-report/export', [ExpenseEntryController::class, 'exportReport'])->name('expense.report.export');
    Route::get('/expense/monthly/export', [ExpenseEntryController::class, 'monthlyExports'])->name('expense.monthly.export');
});

// ============================================
// SALARY ROUTES
// ============================================
use App\Http\Controllers\SalaryController;

Route::middleware(['auth'])->group(function () {
    Route::get('/get-previous-salary/{user}', [SalaryController::class, 'getPreviousSalary'])->name('salaries.getPrevious');
    Route::get('/salaries/export', [SalaryController::class, 'export'])->name('salaries.export');
    Route::resource('salaries', SalaryController::class);
});

// ============================================
// USER MANAGEMENT ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::post('users/{id}/reactivate', [UserController::class, 'reactivate'])->name('users.reactivate');
    Route::get('/password-change-notification', function () {
        return view('auth.password-change-notification');
    })->name('password.change.notification');
});

// ============================================
// CENTER COMPETITION & SALES REPORTS ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/center-competition', [SalesReportController::class, 'index'])->name('center.competition');
    Route::get('/center-competition/top-performers', [SalesReportController::class, 'getTopPerformers'])->name('center.top-performers');
    Route::get('/center-competition/top-closers', [SalesReportController::class, 'getTopClosersBySubmissions'])->name('center.top-closers');
});

// ============================================
// CLIENT REPORTS ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/client-reports', [SalesReportController::class, 'client'])->name('client.reports');
    Route::get('/client-reports/details', [SalesReportController::class, 'getClientDetails'])->name('client.details');
    Route::get('/client-reports/export', [SalesReportController::class, 'exportClientReport'])->name('client.reports.export');
    Route::get('/my-reports', [SalesReportController::class, 'myReports'])->name('my.reports');
    Route::get('/my-reports/export', [SalesReportController::class, 'exportMyReport'])->name('my.reports.export');
});

// ============================================
// CLOSER REPORTS ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/closer-reports', [SalesReportController::class, 'closerReport'])->name('closer.reports');
    Route::get('/closer-reports/details', [SalesReportController::class, 'getCloserDetails'])->name('closer.details');
    Route::get('/closer-reports/export', [SalesReportController::class, 'exportCloserReport'])->name('closer.reports.export');
    Route::get('/closer-detailed-report', [SalesReportController::class, 'detailedCloserReport'])->name('closer.detailed.report');
    Route::get('/closer/detailed-report/export', [SalesReportController::class, 'exportDetailedCloserReport'])->name('closer.detailed.export');
    Route::get('/jc-detailed-report', [SalesReportController::class, 'detailedJuniorCloserReport'])->name('jc.detailed.report');
    Route::get('/jc/detailed-report/export', [SalesReportController::class, 'exportDetailedJuniorCloserReport'])->name('jc.detailed.export');
});

// ============================================
// NUMBER LISTS ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/lists', [NumberListController::class, 'index'])->name('number-lists.index');
    Route::resource('number-lists', NumberListController::class);
});

// ============================================
// VENDOR LISTS ROUTES
// ============================================
Route::middleware(['auth'])->prefix('vendor-lists')->group(function () {
    Route::get('/', [VendorListController::class, 'index'])->name('vendor-lists.index');
    Route::get('/{id}', [VendorListController::class, 'show'])->name('vendor-lists.show');
    Route::put('/{id}', [VendorListController::class, 'update'])->name('vendor-lists.update');
    Route::post('/refresh', [VendorListController::class, 'refresh'])->name('vendor-lists.refresh');
});

// ============================================
// DATA VENDOR ROUTES
// ============================================
Route::middleware(['auth'])->prefix('data-vendor')->group(function () {
    Route::get('get', [DataVendorController::class, 'index'])->name('data-vendor.index');
    Route::post('create', [DataVendorController::class, 'create'])->name('data-vendor.create');
    Route::get('/{id}', [DataVendorController::class, 'show'])->name('data-vendor.show');
    Route::put('/{id}', [DataVendorController::class, 'update'])->name('data-vendor.update');
    Route::get('{vendor}/users', [DataVendorController::class, 'getVendorUsers'])->name('data-vendor.users');
    Route::post('{vendor}/users', [DataVendorController::class, 'assignUsers'])->name('data-vendor.assign-users');
    Route::get('{vendor}/reports', [DataVendorController::class, 'getDataVendorReports'])->name('data-vendor.reports');
});

Route::middleware(['auth'])->group(function () {
    Route::get('data-vendor-specific/reports', [DataVendorController::class, 'getAuthVendorUserReport'])->name('getAuthVendorUserReport');
});

// ============================================
// REPORTING ROUTES
// ============================================
Route::middleware(['auth', 'can:real reports'])->prefix('reporting')->name('reporting.')->group(function () {
    Route::get('/', [ReportingController::class, 'index'])->name('index');
    Route::get('/upload', [ReportingController::class, 'uploadForm'])->name('upload.form');
    Route::post('/upload', [ReportingController::class, 'uploadExcel'])->name('upload.excel');
    Route::get('/export', [ReportingController::class, 'exportExcel'])->name('export');
    Route::get('/api/data', [ReportingController::class, 'apiData'])->name('api.data');
    Route::get('/api/summary', [ReportingController::class, 'getSummary'])->name('api.summary');

    // Data Management
    Route::get('/data-management', [ReportingController::class, 'dataManagement'])->name('data-management');
    Route::get('/data-management/view/{id}', [ReportingController::class, 'viewRecord'])->name('data-management.view');
    Route::delete('/data-management/delete/{id}', [ReportingController::class, 'deleteRecord'])->name('data-management.delete');
    Route::post('/data-management/bulk-delete', [ReportingController::class, 'bulkDelete'])->name('data-management.bulk-delete');
    Route::get('/data-management/export', [ReportingController::class, 'exportDataManagement'])->name('data-management.export');
    Route::post('/data-management/clean-orphaned', [ReportingController::class, 'cleanOrphanedRecords'])->name('data-management.clean-orphaned');
    Route::post('/data-management/duplicate', [ReportingController::class, 'duplicateData'])->name('data-management.duplicate');
    Route::get('/upload-stats', [ReportingController::class, 'getUploadStats'])->name('upload-stats');

    // Closed calls export (route-level permission + type restriction; controller also checks gate)
    Route::get('/closed-calls-export', [ClosedCallExportController::class, 'showForm'])->name('closed-calls.export.form')->middleware('can:export closed calls');
    Route::post('/closed-calls-export', [ClosedCallExportController::class, 'runExport'])->name('closed-calls.export.run')->middleware('can:export closed calls');
});

// ============================================
// CLOSER TEAMS ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('closer-teams', [CloserTeamsController::class, 'index'])->name('closer-teams.index');
    Route::get('closer-teams/create', [CloserTeamsController::class, 'create'])->name('closer-teams.create');
    Route::post('closer-teams', [CloserTeamsController::class, 'store'])->name('closer-teams.store');
    Route::get('closer-teams/{closerTeam}', [CloserTeamsController::class, 'show'])->name('closer-teams.show');
    Route::get('closer-teams/{closerTeam}/edit', [CloserTeamsController::class, 'edit'])->name('closer-teams.edit');
    Route::put('closer-teams/{closerTeam}', [CloserTeamsController::class, 'update'])->name('closer-teams.update');
    Route::delete('closer-teams/{closerTeam}', [CloserTeamsController::class, 'destroy'])->name('closer-teams.destroy');

    // Team Member Management
    Route::post('closer-teams/{closerTeam}/add-member', [CloserTeamsController::class, 'addMember'])->name('closer-teams.add-member');
    Route::delete('closer-teams/{closerTeam}/remove-member', [CloserTeamsController::class, 'removeMember'])->name('closer-teams.remove-member');

    // AJAX Routes
    Route::get('api/available-closers', [CloserTeamsController::class, 'getAvailableClosers'])->name('api.available-closers');
});

// ============================================
// VALIDATORS ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/validators', [ValidatorController::class, 'index'])->name('validators.index');
    Route::get('/validators/create', [ValidatorController::class, 'create'])->name('validators.create');
    Route::post('/validators', [ValidatorController::class, 'store'])->name('validators.store');
    Route::get('/validators/{validator}', [ValidatorController::class, 'show'])->name('validators.show');
    Route::get('/validators/{validator}/edit', [ValidatorController::class, 'edit'])->name('validators.edit');
    Route::put('/validators/{validator}', [ValidatorController::class, 'update'])->name('validators.update');
    Route::delete('/validators/{validator}', [ValidatorController::class, 'destroy'])->name('validators.destroy');
});

// ============================================
// QUEUE SALES ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/queue-sales', [QueueSalesController::class, 'index'])->name('queue-sales.index');
    Route::put('/queue-sales/{id}/inline-update', [QueueSalesController::class, 'updateInline'])->name('queue-sales.inline-update');
    Route::post('/queue-sales/{id}/toggle-connection', [QueueSalesController::class, 'toggleConnection'])->name('queue-sales.toggle-connection');
    Route::get('/queue-sales/{id}/show', [QueueSalesController::class, 'show'])->name('queue-sales.show');
    Route::post('/queue-sales/{id}/comments', [QueueSalesController::class, 'storeComment'])->name('queue-sales.comments.store');
    Route::delete('/queue-sales/comments/{id}', [QueueSalesController::class, 'deleteComment'])->name('queue-sales.comments.delete');
});

// ============================================
// OUTSOURCE CLOSER ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    // Create & Store
    Route::get('outsource/create', [OutsourceCloserController::class, 'create'])->name('outsource.create');
    Route::post('outsource/store', [OutsourceCloserController::class, 'store'])->name('outsource.store');
    Route::get('/outsource/sales-agent/export', [OutsourceCloserController::class, 'exportSalesAgentData'])->name('outsource.sales.agent.export');

    // Sales Agent Management
    Route::post('outsource/addsalesagent/{id}', [OutsourceCloserController::class, 'storesalesagentrecord'])->name('outsource.storesalesagentrecord');
    Route::get('outsource/salesagentshow', [OutsourceCloserController::class, 'salesagentshow'])->name('outsource.salesagentshow');

    // Views
    Route::get('outsource/closerview', [OutsourceCloserController::class, 'closerview'])->name('outsource.closerview');
    Route::get('outsource/clientview', [OutsourceCloserController::class, 'clientview'])->name('outsource.clientview');

    // Main CRUD
    Route::get('/outsource-calls', [OutsourceCloserController::class, 'index'])->name('outsource.index');
    Route::get('/outsource-calls/{id}', [OutsourceCloserController::class, 'show'])->name('outsource.show');
    Route::get('/outsource-calls/agent/{id}', [OutsourceCloserController::class, 'showagentsales'])->name('outsource.showagentsales');
    Route::get('/outsource-client-view', [OutsourceCloserController::class, 'client_index'])->name('outsource.client_index');
    Route::get('/edit-outsource-calls/{id}', [OutsourceCloserController::class, 'edit'])->name('outsource.edit');
    Route::put('/edit-outsource-calls/{id}', [OutsourceCloserController::class, 'update'])->name('outsource.update');

    // AJAX & Utilities
    Route::get('/outsource/get-users/{client_id}', [OutsourceCloserController::class, 'getUsers'])->name('outsource.getUsers');

    // Reports & Stats
    Route::get('outsource-reports', [OutsourceCloserController::class, 'closer_reports'])->name('outsource.reports');
    Route::get('outsource-stats', [OutsourceCloserController::class, 'closers_stats'])->name('outsource.stats');

    // Re-edit & Callback
    Route::get('/outsource-edit/{id}', [OutsourceCloserController::class, 're_edit'])->name('outsource.re_edit');
    Route::put('/outsource-edits/{id}', [OutsourceCloserController::class, 're_update'])->name('outsource.re_update');
    Route::get('/outsource/callback/{id}', [OutsourceCloserController::class, 'callback'])->name('outsource.callback');

    // Client Policy Management
    Route::get('/outsource-manage-policies', [OutsourceCloserController::class, 'clientindex'])->name('outsource.manage_policies');
    Route::get('/edit-outsource-policy/{id}', [OutsourceCloserController::class, 'editclient'])->name('outsource.editclient');
    Route::put('/edit-outsource-policy/{id}', [OutsourceCloserController::class, 'updateclient'])->name('outsource.updateclient');

    // Dialer Management
    Route::get('/outsource-dialer-edit/{id}', [OutsourceCloserController::class, 'editdialer'])->name('outsource.editdialer');
    Route::put('/outsource-dialer-edit/{id}', [OutsourceCloserController::class, 'updatedialer'])->name('outsource.updatedialer');
});

// ============================================
// EMPLOYEE ATTENDANCE ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/attendances', [HikVisionAttendanceController::class, 'index'])->name('attendances.index');
});

// ============================================
// USER DETAILS ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    // User Profile Details
    Route::get('/profile/details', [UserDetailController::class, 'index'])->name('user.details.index');
    Route::post('/profile/details', [UserDetailController::class, 'saveUserDetails'])->name('user.details.save');

    // HR User Details Management
    Route::get('/user-details', [HRUserDetailController::class, 'index'])->name('hr.user.details.index');
    Route::get('/user-details/{user}', [HRUserDetailController::class, 'show'])->name('hr.user.details.show');
    Route::post('/user-details/bank/{bank}/update-status', [HRUserDetailController::class, 'updateBankStatus'])->name('hr.user.details.bank.update');
});

// ============================================
// ATTACHMENTS ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/attachments/{id}', [AttachmentController::class, 'show'])->name('attachments.show');
});

// ============================================
// DIALERS ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    // List all dialers
    Route::get('/dialers', [DialersController::class, 'index'])->name('dialers.index');

    // Show create form
    Route::get('/dialers/create', [DialersController::class, 'create'])->name('dialers.create');

    // Store new dialer
    Route::post('/dialers', [DialersController::class, 'store'])->name('dialers.store');

    // Show specific dialer

    // Show edit form
    Route::get('/dialers/{id}/edit', [DialersController::class, 'edit'])->name('dialers.edit');

    // Update dialer
    Route::put('/dialers/{id}', [DialersController::class, 'update'])->name('dialers.update');
    Route::patch('/dialers/{id}', [DialersController::class, 'update'])->name('dialers.update');

    // Delete dialer
    Route::delete('/dialers/{id}', [DialersController::class, 'destroy'])->name('dialers.destroy');
});

// ============================================
// DIALERS UNIFIED (merged table) ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/dialers-unified', [DialersUnifiedController::class, 'index'])->name('dialers-unified.index');
    Route::get('/dialers-unified/create', [DialersUnifiedController::class, 'create'])->name('dialers-unified.create');
    Route::post('/dialers-unified', [DialersUnifiedController::class, 'store'])->name('dialers-unified.store');
    Route::get('/dialers-unified/{id}/edit', [DialersUnifiedController::class, 'edit'])->name('dialers-unified.edit');
    Route::put('/dialers-unified/{id}', [DialersUnifiedController::class, 'update'])->name('dialers-unified.update');
    Route::patch('/dialers-unified/{id}', [DialersUnifiedController::class, 'update'])->name('dialers-unified.update');
    Route::delete('/dialers-unified/{id}', [DialersUnifiedController::class, 'destroy'])->name('dialers-unified.destroy');
});

// ============================================
// DEPARTMENT SUPPORT ROUTES
// ============================================
use App\Http\Controllers\DepartmentSupportController;
use App\Http\Controllers\DepartmentSupportTicketController;

Route::middleware(['auth'])->group(function () {
    Route::get('/department-support', [DepartmentSupportController::class, 'index'])->name('department_support.index');
    Route::post('/department-support/store', [DepartmentSupportController::class, 'store'])->name('department_support.store');
    Route::get('/get-support/{role_id}', [DepartmentSupportController::class, 'getUsersByRole']);
    Route::get('/department-support/{id}/edit', [DepartmentSupportController::class, 'edit'])->name('department_support.edit');
    Route::put('/department-support/update/{id}', [DepartmentSupportController::class, 'update'])->name('department_support.update');
    Route::delete('/department-support/{id}', [DepartmentSupportController::class, 'destroy'])->name('department_support.destroy');
});

// ============================================
// DEPARTMENT SUPPORT TICKETS ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    // User: Submit Support Request
    Route::get('/department-support-tickets/create', [DepartmentSupportTicketController::class, 'create'])->name('department_support_tickets.create');
    Route::get('/get-titles/{role_id}', [DepartmentSupportTicketController::class, 'getTitlesByDepartment'])->name('department_support_tickets.getTitles');
    Route::post('/department-support-tickets/store', [DepartmentSupportTicketController::class, 'store'])->name('department_support_tickets.store');

    // Admin: Manage Tickets
    Route::get('/department-support-tickets', [DepartmentSupportTicketController::class, 'index'])->name('department_support_tickets.index');
    Route::get('/department-support-tickets/{id}/view', [DepartmentSupportTicketController::class, 'show'])->name('department_support_tickets.show');
    Route::post('/department-support-tickets/{id}/{status}', [DepartmentSupportTicketController::class, 'updateStatus'])->name('department_support_tickets.updateStatus');

    // User: My Tickets
    Route::get('/my-tickets', [DepartmentSupportTicketController::class, 'myTickets'])->name('department_support_tickets.my_tickets');
    Route::post('/department-support-ticket/{id}/{status}', [DepartmentSupportTicketController::class, 'userupdateStatus'])->name('department_support_tickets.userupdateStatus');

    // Dashboard
    Route::get('/tickets-dashboard', [DepartmentSupportTicketController::class, 'dashboard'])->name('tickets.dashboard');
});

// ============================================
// TAX & SALARY SYSTEM ROUTES
// ============================================
use App\Http\Controllers\TaxSlabController;
use App\Http\Controllers\TaxReportController;

// ============================================
// SALARY DEPARTMENTS ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    // AJAX Route - Must come first
    Route::get('salary-departments/users-by-role', [SalaryDepartmentController::class, 'getUsersByRole'])->name('salary-departments.users-by-role');

    // CRUD Routes
    Route::get('salary-departments', [SalaryDepartmentController::class, 'index'])->name('salary-departments.index');
    Route::get('salary-departments/create', [SalaryDepartmentController::class, 'create'])->name('salary-departments.create');
    Route::post('salary-departments', [SalaryDepartmentController::class, 'store'])->name('salary-departments.store');
    Route::get('salary-departments/{salary_department}', [SalaryDepartmentController::class, 'show'])->name('salary-departments.show');
    Route::get('salary-departments/{salary_department}/edit', [SalaryDepartmentController::class, 'edit'])->name('salary-departments.edit');
    Route::put('salary-departments/{salary_department}', [SalaryDepartmentController::class, 'update'])->name('salary-departments.update');
    Route::delete('salary-departments/{salary_department}', [SalaryDepartmentController::class, 'destroy'])->name('salary-departments.destroy');
});

// ============================================
// SALARY STRUCTURES ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    // AJAX Routes - Must come first
    Route::get('salary-structures/users-by-department', [SalaryStructureController::class, 'getUsersByDepartment'])->name('salary-structures.users-by-department');
    Route::get('salary-structures/department-structures', [SalaryStructureController::class, 'getDepartmentStructures'])->name('salary-structures.department-structures');

    // Bulk Operations - Before resource routes
    Route::get('salary-structures-bulk/create', [SalaryStructureController::class, 'createBulk'])->name('salary-structures.create-bulk');
    Route::post('salary-structures-bulk/store', [SalaryStructureController::class, 'storeBulk'])->name('salary-structures.store-bulk');
    Route::post('salary-structures-bulk/update', [SalaryStructureController::class, 'updateBulk'])->name('salary-structures.update-bulk');

    // Department View - Before resource routes
    Route::get('salary-structures/department/{department}', [SalaryStructureController::class, 'showDepartment'])->name('salary-structures.department');

    // Standard CRUD Routes
    Route::get('salary-structures', [SalaryStructureController::class, 'index'])->name('salary-structures.index');
    Route::get('salary-structures/create', [SalaryStructureController::class, 'create'])->name('salary-structures.create');
    Route::post('salary-structures', [SalaryStructureController::class, 'store'])->name('salary-structures.store');
    Route::get('salary-structures/{salary_structure}/edit', [SalaryStructureController::class, 'edit'])->name('salary-structures.edit');
    Route::put('salary-structures/{salary_structure}', [SalaryStructureController::class, 'update'])->name('salary-structures.update');
    Route::delete('salary-structures/{salary_structure}', [SalaryStructureController::class, 'destroy'])->name('salary-structures.destroy');
    Route::get('salary-structures/{salary_structure}', [SalaryStructureController::class, 'show'])->name('salary-structures.show');
    Route::get('salary-structures-inactive', [SalaryStructureController::class, 'inactive'])
        ->name('salary-structures.inactive');

    Route::delete('salary-structures-bulk-delete', [SalaryStructureController::class, 'bulkDelete'])
        ->name('salary-structures.bulk-delete');
});

// ============================================
// MONTHLY SALARIES ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::resource('monthly-salaries', MonthlySalaryController::class);
    Route::post('monthly-salaries/{monthlySalary}/approve', [MonthlySalaryController::class, 'approve'])->name('monthly-salaries.approve');
    Route::post('monthly-salaries/bulk-approve', [MonthlySalaryController::class, 'bulkApprove'])->name('monthly-salaries.bulk-approve');
    Route::get('monthly-salaries/attendance-data', [MonthlySalaryController::class, 'getAttendanceData'])->name('monthly-salaries.attendance-data');
    Route::get('monthly-salaries-inactive', [MonthlySalaryController::class, 'inactive'])
        ->name('monthly-salaries.inactive');

    Route::delete('monthly-salaries-bulk-delete', [MonthlySalaryController::class, 'bulkDelete'])
        ->name('monthly-salaries.bulk-delete');
});

// ============================================
// SALARY SLIPS ROUTES (Admin)
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('salary-slips', [SalarySlipController::class, 'index'])->name('salary-slips.index');
    Route::get('salary-slips/{monthlySalary}/download', [SalarySlipController::class, 'downloadDirect'])->name('salary-slips.download');
    Route::post('salary-slips/bulk-download', [SalarySlipController::class, 'bulkDownloadDirect'])->name('salary-slips.bulk-download');
    Route::post('salary-slips/bulk-download-selected', [SalarySlipController::class, 'bulkDownloadSelected'])->name('salary-slips.bulk-download-selected');
});

// ============================================
// SALARY PAYMENTS ROUTES
// ============================================
use App\Http\Controllers\EmployeeSalarySlipController;
use App\Http\Controllers\SalaryPaymentController;

Route::middleware(['auth'])->group(function () {
    // Salary Payments - Main Routes
    Route::get('/salary-payments', [SalaryPaymentController::class, 'index'])->name('salary.payments.index');
    Route::post('/salary-payments', [SalaryPaymentController::class, 'store'])->name('salary.payments.store');

    // Export Routes (Must come BEFORE {payment} route to avoid conflicts)
    Route::get('/salary-payments/export-all', [SalaryPaymentController::class, 'exportAll'])->name('salary.payments.export.all');
    Route::get('/salary-payments/export-department/{department}', [SalaryPaymentController::class, 'exportByDepartment'])->name('salary.payments.export.department');
    Route::post('/salary-payments/preview-export', [SalaryPaymentController::class, 'previewExport'])->name('salary.payments.preview.export');
    Route::post('/salary-payments/regenerate-references', [SalaryPaymentController::class, 'regenerateReferences'])->name('salary.payments.regenerate.references');
    Route::post('/salary-payments/store-references', [SalaryPaymentController::class, 'storeCustomerReferences'])->name('salary.payments.store.references');

    // AJAX Routes
    Route::get('/salary-payments/get-salary-details/{salaryId}', [SalaryPaymentController::class, 'getSalaryDetails'])->name('salary.payments.get-details');
    Route::get('/salary-payments/get-user-banks/{salary}', function ($salaryId) {
        $salary = \App\Models\MonthlySalary::with('user.userDetail.bankDetails')->findOrFail($salaryId);
        $banks = $salary->user->userDetail?->bankDetails()
            ->where('status', 'verified')
            ->orderBy('priority', 'asc')
            ->get();

        return response()->json(['banks' => $banks]);
    })->name('salary.payments.get-banks');

    // Bulk Payment Upload
    Route::post('/salary-payments/bulk-upload', [SalaryPaymentController::class, 'bulkUpload'])->name('salary.payments.bulk-upload');

    // Payment Details and Status Update (Must come LAST)
    Route::get('/salary-payments/{payment}', [SalaryPaymentController::class, 'show'])->name('salary.payments.show');
    Route::put('/salary-payments/{payment}/status', [SalaryPaymentController::class, 'updateStatus'])->name('salary.payments.update-status');
});

// ============================================
// EMPLOYEE SALARY SLIPS ROUTES (Employee View)
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/my-salary-slips', [EmployeeSalarySlipController::class, 'index'])->name('employee.salary-slips');
    Route::get('/my-salary-slips/{id}', [EmployeeSalarySlipController::class, 'show'])->name('employee.salary-slip.show');
    Route::get('/my-salary-slips/{id}/download', [EmployeeSalarySlipController::class, 'download'])->name('employee.salary-slip.download');
});

// ============================================
// TAX SLABS & TAX REPORTS ROUTES
// ============================================
Route::middleware(['auth'])->group(function () {
    // Tax Slabs Management - Full Routes (Non-Resource)
    Route::get('tax-slabs', [TaxSlabController::class, 'index'])->name('tax-slabs.index');
    Route::get('tax-slabs/create', [TaxSlabController::class, 'create'])->name('tax-slabs.create');
    Route::post('tax-slabs', [TaxSlabController::class, 'store'])->name('tax-slabs.store');
    Route::get('tax-slabs/calculate-preview', [TaxSlabController::class, 'calculatePreview'])->name('tax-slabs.calculate-preview');
    Route::get('tax-slabs/{taxSlab}', [TaxSlabController::class, 'show'])->name('tax-slabs.show');
    Route::get('tax-slabs/{taxSlab}/edit', [TaxSlabController::class, 'edit'])->name('tax-slabs.edit');
    Route::put('tax-slabs/{taxSlab}', [TaxSlabController::class, 'update'])->name('tax-slabs.update');
    Route::delete('tax-slabs/{taxSlab}', [TaxSlabController::class, 'destroy'])->name('tax-slabs.destroy');
    Route::patch('tax-slabs/{taxSlab}/toggle-status', [TaxSlabController::class, 'toggleStatus'])->name('tax-slabs.toggle-status');

    // Tax Reports
    Route::get('tax-reports', [TaxReportController::class, 'index'])->name('tax-reports.index');
    Route::get('tax-reports/export', [TaxReportController::class, 'export'])->name('tax-reports.export');
});


use App\Http\Controllers\BankController;

Route::middleware(['auth'])->group(function () {
    Route::resource('banks', BankController::class);
    Route::post('banks/{bank}/toggle-status', [BankController::class, 'toggleStatus'])->name('banks.toggle-status');

    Route::get('api/banks/active', [BankController::class, 'getActiveBanks'])->name('banks.active');
});

// ============================================
// CENTER REPORTS ROUTES (Restricted Access - Sellerz Center)
// ============================================
use App\Http\Controllers\ClosedCallsController;
use App\Http\Controllers\CloserAttendanceController;

Route::middleware(['auth'])->group(function () {
    Route::get('center-reports', [ClosedCallsController::class, 'index'])->name('center_reports.index');
    Route::post('center-reports/clear-cache', [ClosedCallsController::class, 'clearCache'])->name('center_reports.clear-cache');
});

use App\Http\Controllers\CommissionReportController;
use App\Http\Controllers\DailySalesController;

Route::middleware(['auth'])->group(function () {
    // Put these FIRST - specific routes before parameterized routes
    Route::get('commissions/report', [CommissionReportController::class, 'showReport'])->name('commission.report');
    Route::get('commissions/comprehensive', [CommissionReportController::class, 'showComprehensiveReport'])->name('commission.comprehensive');
    Route::get('commissions/pending', [CommissionReportController::class, 'showPending'])->name('commission.pending');
    Route::post('commissions/config', [CommissionReportController::class, 'storeConfig'])->name('commission.config.store');
    Route::delete('commissions/config/{id}', [CommissionReportController::class, 'deleteConfig'])->name('commission.config.delete');

    // Then the index and upload
    Route::get('commissions', [CommissionReportController::class, 'index'])->name('commission.index');
    Route::post('commissions/upload', [CommissionReportController::class, 'uploadStatement'])->name('commission.upload');


    // Center Name Added
    Route::middleware(['auth'])->group(function () {
    Route::get('centers', [\App\Http\Controllers\CenterController::class, 'index'])->name('centers.index');
    Route::get('centers/create', [\App\Http\Controllers\CenterController::class, 'create'])->name('centers.create');
    Route::post('centers/store', [\App\Http\Controllers\CenterController::class, 'store'])->name('centers.store');
    Route::get('centers/{id}/edit', [\App\Http\Controllers\CenterController::class, 'edit'])->name('centers.edit');
    Route::put('centers/{id}/update', [\App\Http\Controllers\CenterController::class, 'update'])->name('centers.update');
    Route::delete('centers/{id}/delete', [\App\Http\Controllers\CenterController::class, 'destroy'])->name('centers.destroy');
});
});

use App\Http\Controllers\StatusUploadController;

Route::middleware(['auth'])->prefix('status-upload')->name('status-upload.')->group(function () {
    Route::get('/',                          [StatusUploadController::class, 'index'])                  ->name('index');
    Route::post('/lapse-report',             [StatusUploadController::class, 'uploadLapseReport'])      ->name('lapse');
    Route::post('/monthly-advance',          [StatusUploadController::class, 'uploadMonthlyAdvance'])   ->name('advance');
    Route::delete('/log/{id}',               [StatusUploadController::class, 'deleteLog'])              ->name('log.delete');
    Route::get('/export-missing-policy-ids', [StatusUploadController::class, 'exportMissingPolicyIds']) ->name('export.missing');
    Route::get('/export-unmatched-lapse',    [StatusUploadController::class, 'exportUnmatchedLapse'])   ->name('export.unmatched.lapse');
    Route::get('/export-unmatched-advance',  [StatusUploadController::class, 'exportUnmatchedAdvance']) ->name('export.unmatched.advance');
    Route::get('/client-report',             [StatusUploadController::class, 'clientReport'])           ->name('client-report');
    Route::get('/export-client-report',      [StatusUploadController::class, 'exportClientReport'])     ->name('export.client-report');
});

use App\Http\Controllers\VerifierController;

Route::middleware(['auth'])->prefix('verifier')->name('verifier.')->group(function () {
    // Admin routes — assign calls to verifiers
    Route::get('/assign',          [VerifierController::class, 'assignIndex'])->name('assign');
    Route::post('/assign',         [VerifierController::class, 'store'])       ->name('store');
    Route::delete('/unassign/{id}',[VerifierController::class, 'unassign'])    ->name('unassign');

    // Verifier dashboard
    Route::get('/dashboard',       [VerifierController::class, 'dashboard'])   ->name('dashboard');
    Route::post('/remarks/{id}',   [VerifierController::class, 'updateRemarks'])->name('remarks');
});

Route::prefix('app')
    ->name('app.')
    ->middleware(['auth', \App\Http\Middleware\HandleInertiaRequests::class])
    ->group(function () {
        Route::get('/dashboard', \App\Http\Controllers\Inertia\AppDashboardController::class)
            ->name('dashboard');

        Route::get('/users', [\App\Http\Controllers\Inertia\UsersController::class, 'index'])
            ->name('users.index');

        Route::get('/closed-calls', [\App\Http\Controllers\Inertia\ClosedCallsController::class, 'index'])
            ->name('closed-calls.index');

        Route::get('/weekly-performance-report', [\App\Http\Controllers\Inertia\WeeklyAgentPerformanceController::class, 'index'])
            ->name('weekly-performance.index');

        // Owner Dashboard Routes
        Route::get('/owner-dashboard', [\App\Http\Controllers\Inertia\OwnerDashboardController::class, 'index'])
            ->name('owner-dashboard.index');
        Route::get('/owner-dashboard/sales-summary', [\App\Http\Controllers\Inertia\OwnerDashboardController::class, 'salesSummary'])
            ->name('owner-dashboard.sales');
        Route::get('/owner-dashboard/team-leaderboard', [\App\Http\Controllers\Inertia\OwnerDashboardController::class, 'teamLeaderboard'])
            ->name('owner-dashboard.teams');
        Route::get('/owner-dashboard/agent-performance', [\App\Http\Controllers\Inertia\OwnerDashboardController::class, 'agentPerformance'])
            ->name('owner-dashboard.agents');
        Route::get('/owner-dashboard/attendance', [\App\Http\Controllers\Inertia\OwnerDashboardController::class, 'attendance'])
            ->name('owner-dashboard.attendance');
        Route::get('/owner-dashboard/retention', [\App\Http\Controllers\Inertia\OwnerDashboardController::class, 'retention'])
            ->name('owner-dashboard.retention');
        
        // VICIdial Inertia routes (permission guarded where needed)
        Route::get('/vicidial/test', [ViciDialController::class, 'index'])->name('vicidial.test');
        Route::get('/vicidial/agent/{agent_user}', [ViciDialController::class, 'getUserDetails'])->name('vicidial.agent.details');
        Route::get('/vicidial/stats', [ViciDialController::class, 'statsPage'])->name('vicidial.stats')->middleware('can:view_dialer_stats');
        Route::get('/vicidial/stats/data', [ViciDialController::class, 'getAgentStats'])->name('vicidial.stats.data')->middleware('can:view_dialer_stats');
    });

    use App\Http\Controllers\DialerDashboardController;
use App\Http\Controllers\SalesCloserController;
use App\Http\Controllers\SalesLookupController;
use App\Http\Controllers\SalesTargetController;

Route::middleware(['auth'])->group(function () {
    Route::get('/dialer-dashboard', [DialerDashboardController::class, 'index'])
        ->name('dialer-dashboard');
        Route::get('/dialer-dashboard/live-board', [DialerDashboardController::class, 'liveBoard'])->name('dialer-dashboard.live-board');
 
    // Manual "update now" trigger — controller itself checks the editor
    // email and aborts with 403 for anyone else, so this route can stay
    // inside the normal auth group.
    Route::post('/dialer-dashboard/sync', [DialerDashboardController::class, 'syncNow'])
        ->name('dialer-dashboard.sync');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/daily-sales/create', [DailySalesController::class, 'create'])->name('daily-sales.create');
    Route::post('/daily-sales', [DailySalesController::class, 'store'])->name('daily-sales.store');
    Route::patch('daily-sales/{entry}', [DailySalesController::class, 'update'])->name('daily-sales.update');
    Route::delete('/daily-sales/{entry}', [DailySalesController::class, 'destroy'])->name('daily-sales.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/sales-target/edit', [SalesTargetController::class, 'edit'])->name('sales-target.edit');
    Route::put('/sales-target', [SalesTargetController::class, 'update'])->name('sales-target.update');
});

Route::get('/sales-reports/team-wise', [SalesReportController::class, 'teamsWise'])->name('sales-reports.team-wise');
Route::get('/sales-reports/client-wise', [SalesReportController::class, 'clientWise'])->name('sales-reports.client-wise');
Route::get('/sales-reports/carrier-wise', [SalesReportController::class, 'carrierWise'])->name('sales-reports.carrier-wise');
Route::get('/sales-reports', [SalesReportController::class, 'monthlyReports'])->name('sales-reports.index');Route::middleware(['auth'])->group(function ()
 {
   
Route::get('/sales-closers', [SalesCloserController::class, 'index'])->name('sales-closers.index');
    Route::put('/sales-closers/{closer}', [SalesCloserController::class, 'update'])->name('sales-closers.update');
    
});

Route::middleware(['auth'])->group(function () {
    Route::post('/sales-teams', [SalesLookupController::class, 'storeTeam'])->name('sales-teams.store');
    Route::post('/sales-closers-create', [SalesLookupController::class, 'storeCloser'])->name('sales-closers.store');
    Route::post('/sales-clients', [SalesLookupController::class, 'storeClient'])->name('sales-clients.store');
    Route::post('/sales-carriers', [SalesLookupController::class, 'storeCarrier'])->name('sales-carriers.store');
});

Route::post('/sales-clients/set-target', [SalesLookupController::class, 'setClientTarget'])->name('sales-clients.set-target');
Route::post('/sales-carriers/set-target', [SalesLookupController::class, 'setCarrierTarget'])->name('sales-carriers.set-target');

Route::delete('/sales-teams/{team}', [SalesLookupController::class, 'destroyTeam'])->name('sales-teams.destroy');
Route::delete('/sales-clients/{client}', [SalesLookupController::class, 'destroyClient'])->name('sales-clients.destroy');
Route::delete('/sales-carriers/{carrier}', [SalesLookupController::class, 'destroyCarrier'])->name('sales-carriers.destroy');


Route::middleware(['auth'])->group(function () {
    Route::get('/attendance-closer', [CloserAttendanceController::class, 'index'])->name('attendance-closer.index');
    Route::post('/attendance-closer', [CloserAttendanceController::class, 'store'])->name('attendance-closer.store');
});

Route::put('/sales-teams/{team}/target', [SalesLookupController::class, 'updateTeamTarget'])->name('sales-teams.update-target');
Route::post('/sales-teams/set-target', [SalesLookupController::class, 'setTeamTarget'])->name('sales-teams.set-target');

Route::delete('/sales-closers/{closer}', [SalesCloserController::class, 'destroy'])->name('sales-closers.destroy');
Route::get('/dialer-leaderboard', [DialerDashboardController::class, 'leaderboardPage'])->name('dialer-leaderboard');

