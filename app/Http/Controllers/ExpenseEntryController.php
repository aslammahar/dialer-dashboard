<?php

namespace App\Http\Controllers;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\ExpenseEntry;
use App\Models\AccountingEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MonthlyExpense;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; // Correct import


class ExpenseEntryController extends Controller
{
    // Display all expense entries and the form to create a new one
    public function index(Request $request)
{

    $selectedDate = $request->input('date') ?: now('Asia/Karachi')->format('Y-m-d');    
    // Filter entries by date if a date is provided
    $entries = ExpenseEntry::with('accountingEntry')
        ->whereDate('date', $selectedDate)
        ->get();
    
    // Calculate total daily expense
    $totalDebit = $entries->where('type', 'debit')->sum('amount');
    $totalCredit = $entries->where('type', 'credit')->sum('amount');
    
    // Get all accounting entry IDs from the entries
    $accountingEntryIds = $entries->pluck('expense_type_id')->toArray();
    
    // First get user_ids from accounting_entries table
    $accountingUserIds = DB::table('accounting_entries')
        ->whereIn('id', $accountingEntryIds)
        ->pluck('user_id', 'id')
        ->toArray();
        $totalDailyExpense = $entries->sum('amount');
 
    // Then get names from users table using those user_ids
    $userIds = array_values($accountingUserIds);
    $userNames = DB::table('users')
        ->whereIn('id', $userIds)
        ->pluck('name', 'id')
        ->toArray();
    
    return view('account-expense.index', compact(
        'entries', 
        'accountingUserIds', 
        'userNames', 
        'selectedDate', 
        'totalDebit',
        'totalCredit',
        'totalDailyExpense',
    ));
}


    
    // Show the form to create a new expense entry
    public function create()
    {
        
        $expenseTypes = AccountingEntry::all(); // Get all expense types
        return view('account-expense.create', compact('expenseTypes'));
    }

    // Store a new expense entry
    public function store(Request $request)
    {
        $request->validate([
            'expense_type_id' => 'required|exists:accounting_entries,id',
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        ExpenseEntry::create($request->all());

        return redirect()->route('expense.entries.index')->with('success', 'Entry created successfully.');
    }

    // Calculate and display the balance for each expense type
    public function balance()
    {
        $expenseTypes = AccountingEntry::with('expenseEntries', 'monthlyExpenses')->get();
    
        $balances = [];
        foreach ($expenseTypes as $expenseType) {
            // ExpenseEntries: credit & debit
            $credit1 = $expenseType->expenseEntries->where('type', 'credit')->sum('amount');
            $debit1 = $expenseType->expenseEntries->where('type', 'debit')->sum('amount');
    
            // MonthlyExpenses: credit & debit
            $credit2 = $expenseType->monthlyExpenses->where('type', 'credit')->sum('amount');
            $debit2 = $expenseType->monthlyExpenses->where('type', 'debit')->sum('amount');
    
            // Combine both
            $totalCredit = $credit1 + $credit2;
            $totalDebit = $debit1 + $debit2;
    
            $balance = $totalCredit - $totalDebit;
    
            $balances[] = [
                'expenseType' => $expenseType,
                'credit' => $totalCredit,
                'debit' => $totalDebit,
                'balance' => $balance,
            ];
        }
    
        return view('account-expense.balance', compact('balances'));
    }
    

    public function edit(ExpenseEntry $expenseEntry)
    {
        // Fetch all expense types (accounting entries)
        $expenseTypes = AccountingEntry::all();
    
        // Pass the expense entry and expense types to the edit view
        return view('account-expense.edit', compact('expenseEntry', 'expenseTypes'));
    }


   public function update(Request $request, ExpenseEntry $expenseEntry)
{
    // Validate the request data
    $request->validate([
        'expense_type_id' => 'required|exists:accounting_entries,id', // Ensure the expense type exists
        'date' => 'required|date',
        'description' => 'required|string|max:255',
        'type' => 'required|in:credit,debit',
        'amount' => 'required|numeric|min:0',
        'remarks' => 'nullable|string',
    ]);

    // Update the expense entry with the validated data
    $expenseEntry->update($request->all());

    // Redirect to the index page with a success message
    return redirect()->route('expense.entries.index')->with('success', 'Expense entry updated successfully.');
}


public function destroy(ExpenseEntry $expenseEntry)
{
    // Delete the expense entry
    $expenseEntry->delete();

    // Redirect to the index page with a success message
    return redirect()->route('expense.entries.index')->with('success', 'Expense entry deleted successfully.');
}


public function monthlyIndex()
{
    // Get the currently selected month and handle potential array values
    $selectedMonth = request('month_year');
    
    // If it's an array, take the first value
    if (is_array($selectedMonth)) {
        $selectedMonth = reset($selectedMonth);
    }
    
    // Default to current month if no value
    if (empty($selectedMonth)) {
        $selectedMonth = now()->format('Y-m');
    }
    
    // Get monthly expenses for the selected month
    $monthlyExpenses = MonthlyExpense::whereRaw("DATE_FORMAT(month_year, '%Y-%m') = ?", [$selectedMonth])
        ->get();
    
    // Calculate total for the selected month
    $selectedMonthTotal = $monthlyExpenses->sum('amount');
    $monthlyDebitTotal = $monthlyExpenses->where('type', 'debit')->sum('amount');
    $monthlyCreditTotal = $monthlyExpenses->where('type', 'credit')->sum('amount');

    // Calculate total for the current month (regardless of selection)
    $currentMonthTotal = MonthlyExpense::whereRaw("DATE_FORMAT(month_year, '%Y-%m') = ?", [now()->format('Y-m')])
        ->sum('amount');
    
    // Get accounting user IDs and names
    $accountingUserIds = DB::table('accounting_entries')
        ->whereIn('id', $monthlyExpenses->pluck('accountant_id')->toArray())
        ->pluck('user_id', 'id')
        ->toArray();
    
    $userNames = DB::table('users')
        ->whereIn('id', array_values($accountingUserIds))
        ->pluck('name', 'id')
        ->toArray();
    
    return view('account-expense.monthly-index', compact(
        'monthlyExpenses',
        'accountingUserIds',
        'userNames',
        'selectedMonthTotal',
        'currentMonthTotal',
        'monthlyDebitTotal',
        'monthlyCreditTotal',
        'selectedMonth'
    ));
}
    // Monthly Expenses Create
    public function monthlyCreate()
    {
        $accountants = AccountingEntry::all();
        return view('account-expense.monthly-create', compact('accountants'));
    }

   

    public function monthlyStore(Request $request)
    {
        $request->validate([
            'accountant_id' => 'required|exists:accounting_entries,id',
            'month_year' => 'required|date_format:Y-m',
            'expense_category' => 'required|in:internet,electricity,rent,other,phone,maintenance,supplies,water',
            'description' => 'nullable|string|required_if:expense_category,other',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:credit,debit',

        ]);

        // Convert month-year to full date (first day of month)
        $monthYear = Carbon::createFromFormat('Y-m', $request->month_year)->startOfMonth();

        MonthlyExpense::create([
            'accountant_id' => $request->accountant_id,
            'month_year' => $monthYear,
            'expense_category' => $request->expense_category,
            'description' => $request->description,
            'amount' => $request->amount,
            'type' => $request->type,

        ]);

        return redirect()->route('expense.monthly.index')->with('success', 'Monthly expense added successfully.');
    
    }

    public function monthlyEdit(MonthlyExpense $monthlyExpense)
    {
        $accountants = AccountingEntry::all();
        return view('account-expense.monthly-edit', compact('monthlyExpense', 'accountants'));
    }



    public function monthlyUpdate(Request $request, MonthlyExpense $monthlyExpense)
    {


        $request->validate([
            'accountant_id' => 'required|exists:accounting_entries,id',
            'month_year' => 'required|date_format:Y-m',
            'expense_category' => 'required|in:internet,electricity,rent,other',
            'description' => 'nullable|string|required_if:expense_category,other',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:credit,debit',

        ]);

        // Convert month-year to full date (first day of month)
        $monthYear = Carbon::createFromFormat('Y-m', $request->month_year)->startOfMonth();



        $monthlyExpense->update([
            'accountant_id' => $request->accountant_id,
            'month_year' => $monthYear,
            'expense_category' => $request->expense_category,
            'description' => $request->description,
            'amount' => $request->amount,
            'type' => $request->type,

        ]);

        return redirect()->route('expense.monthly.index')->with('success', 'Monthly expense updated successfully.');
    }


    // Monthly Expenses Edit
   
    

    // Monthly Expenses Delete
    public function monthlyDestroy(MonthlyExpense $monthlyExpense)
    {
        $monthlyExpense->delete();
        return redirect()->route('expense.monthly.index')->with('success', 'Monthly expense deleted successfully.');
    }

    public function showReport()
    {
        // Fetch all entries (or you can fetch filtered data if needed)
        $entries = ExpenseEntry::with('user')->orderBy('date', 'desc')->get();
        $accountingEntryIds = $entries->pluck('expense_type_id')->toArray();
    
        // First get user_ids from accounting_entries table
        $accountingUserIds = DB::table('accounting_entries')
            ->whereIn('id', $accountingEntryIds)
            ->pluck('user_id', 'id')
            ->toArray();


        // Calculate totals
        $totalDebit = $entries->where('type', 'debit')->sum('amount');
        $totalCredit = $entries->where('type', 'credit')->sum('amount');

        // Get accounting user mappings
        $userNames = [];

        return view('account-expense.report', compact('entries', 'totalDebit', 'totalCredit', 'accountingUserIds', 'userNames','accountingUserIds'));
    }

    // Method to filter the report by date range
    public function filterReportByDateRange(Request $request)
    {
        // Get inputs
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $accountantId = $request->input('accountant_id');
    
        // Base query
        $query = ExpenseEntry::with('accountingEntry')
            ->whereBetween('date', [$startDate, $endDate]);
    
        // Filter by accountant if provided
        $entries = collect(); // Initialize empty collection
        if ($accountantId) {
            $entries = $query->where('expense_type_id', $accountantId)
                ->orderBy('date', 'desc')
                ->get();
    
            // Fallback to all records if no entries found for the accountant
            if ($entries->isEmpty()) {
                $entries = $query->orderBy('date', 'desc')->get();
            }
        } else {
            // No accountant selected, get all records in date range
            $entries = $query->orderBy('date', 'desc')->get();
        }
    
        // Calculate totals
        $totalDebit = $entries->where('type', 'debit')->sum('amount');
        $totalCredit = $entries->where('type', 'credit')->sum('amount');
    
        // Get accounting user mappings for "Entered By"
        $accountingEntryIds = $entries->pluck('expense_type_id')->toArray();
        $accountingUserIds = DB::table('accounting_entries')
            ->whereIn('id', $accountingEntryIds)
            ->pluck('user_id', 'id')
            ->toArray();
    
        $userIds = array_values($accountingUserIds);
        $userNames = DB::table('users')
            ->whereIn('id', $userIds)
            ->pluck('name', 'id')
            ->toArray();
    
        // Fetch all accountants with user names for the dropdown
        $accountants = AccountingEntry::with('user')->select('id', 'user_id')->get();
    
        return view('account-expense.report', compact(
            'entries',
            'totalDebit',
            'totalCredit',
            'accountingUserIds',
            'userNames',
            'accountants',
            'startDate',
            'endDate'
        ));
    }
    public function exportReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $accountantId = $request->input('accountant_id');
    
        // Base query
        $query = ExpenseEntry::with('accountingEntry');
        
        // Apply date range filter if provided
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }
    
        // Filter by accountant if provided
        $entries = collect();
        if ($accountantId) {
            $entries = $query->where('expense_type_id', $accountantId)->get();
            // Fallback to all records in date range if no entries found
            if ($entries->isEmpty() && $startDate && $endDate) {
                $entries = ExpenseEntry::with('accountingEntry')
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();
            }
        } else {
            // No accountant selected, get all records (with date filter if provided)
            $entries = $query->get();
        }
    
        // Calculate totals
        $totalDebit = $entries->where('type', 'debit')->sum('amount');
        $totalCredit = $entries->where('type', 'credit')->sum('amount');
        $netTotal = $totalCredit - $totalDebit;
    
        // Get accounting user mappings for "Entered By"
        $accountingEntryIds = $entries->pluck('expense_type_id')->toArray();
        $accountingUserIds = DB::table('accounting_entries')
            ->whereIn('id', $accountingEntryIds)
            ->pluck('user_id', 'id')
            ->toArray();
    
        $userIds = array_values($accountingUserIds);
        $userNames = DB::table('users')
            ->whereIn('id', $userIds)
            ->pluck('name', 'id')
            ->toArray();
    
        // Create the export file
        return \Maatwebsite\Excel\Facades\Excel::download(new class($entries, $totalDebit, $totalCredit, $netTotal, $accountingUserIds, $userNames) implements FromCollection, WithHeadings, WithStyles {
            protected $entries;
            protected $totalDebit;
            protected $totalCredit;
            protected $netTotal;
            protected $accountingUserIds;
            protected $userNames;
    
            public function __construct($entries, $totalDebit, $totalCredit, $netTotal, $accountingUserIds, $userNames)
            {
                $this->entries = $entries;
                $this->totalDebit = $totalDebit;
                $totalCredit = $totalCredit;
                $this->netTotal = $netTotal;
                $this->accountingUserIds = $accountingUserIds;
                $this->userNames = $userNames;
            }
    
            public function collection()
            {
                // Map expense entries
                $entryRows = $this->entries->map(function ($entry) {
                    $accountingUserId = $this->accountingUserIds[$entry->expense_type_id] ?? null;
                    $userName = $accountingUserId ? ($this->userNames[$accountingUserId] ?? 'Unknown') : 'N/A';
                    return [
                        'Date' => $entry->date,
                        'Description' => $entry->description,
                        'Type' => ucfirst($entry->type),
                        'Amount' => number_format($entry->amount, 2),
                        'Entered By' => $userName,
                    ];
                });
    
                // Add summary rows
                $summaryRows = collect([
                    [
                        'Date' => '',
                        'Description' => 'Total Debit (Out Going)',
                        'Type' => '',
                        'Amount' => number_format($this->totalDebit, 2),
                        'Entered By' => '',
                    ],
                    [
                        'Date' => '',
                        'Description' => 'Total Credit (Incomming)',
                        'Type' => '',
                        'Amount' => number_format($this->totalCredit, 2),
                        'Entered By' => '',
                    ],
                    [
                        'Date' => '',
                        'Description' => 'Net Total (Credit - Debit)',
                        'Type' => '',
                        'Amount' => number_format($this->netTotal, 2),
                        'Entered By' => '',
                    ],
                ]);
    
                // Combine entries and summary rows
                return $entryRows->concat($summaryRows);
            }
    
            public function headings(): array
            {
                return [
                    'Date',
                    'Description',
                    'Type',
                    'Amount',
                    'Entered By',
                ];
            }
    
            public function styles(Worksheet $sheet)
            {
                // Style the header row
                $sheet->getStyle('A1:E1')->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCCCCCC']],
                ]);
    
                // Style the summary rows (last 3 rows)
                $totalRows = $this->entries->count() + 2; // Start of summary rows
                $sheet->getStyle("A$totalRows:E" . ($totalRows + 2))->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFE0B2']],
                ]);
    
                // Auto-size columns
                foreach (range('A', 'E') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
    
                return [];
            }
        }, 'Expense_Report_' . now()->format('Ymd_His') . '.xlsx');
    }


    public function monthlyExports(Request $request)
    {
        try {
            $selectedMonth = $request->input('month_year', now()->format('Y-m'));

            if (!\Carbon\Carbon::hasFormat($selectedMonth, 'Y-m')) {
                return redirect()->back()->with('error', 'Invalid month format.');
            }

            $monthlyExpenses = MonthlyExpense::where(DB::raw("DATE_FORMAT(month_year, '%Y-%m')"), $selectedMonth)->get();

            if ($monthlyExpenses->isEmpty()) {
                return redirect()->back()->with('error', 'No expenses found for the selected month.');
            }

            // Calculate totals
            $totalDebit = $monthlyExpenses->where('type', 'debit')->sum('amount');
            $totalCredit = $monthlyExpenses->where('type', 'credit')->sum('amount');
            $netTotal = $totalCredit - $totalDebit;

            $fileName = 'Monthly_ExpenseReport_' . $selectedMonth . '.xlsx';

            return Excel::download(new class($monthlyExpenses, $totalDebit, $totalCredit, $netTotal) implements FromCollection, WithHeadings, WithStyles {
                protected $monthlyExpenses;
                protected $totalDebit;
                protected $totalCredit;
                protected $netTotal;

                public function __construct($monthlyExpenses, $totalDebit, $totalCredit, $netTotal)
                {
                    $this->monthlyExpenses = $monthlyExpenses;
                    $this->totalDebit = $totalDebit;
                    $this->totalCredit = $totalCredit;
                    $this->netTotal = $netTotal;
                }

                public function collection()
                {
                    // Map expense entries
                    $entryRows = $this->monthlyExpenses->map(function ($entry) {
                        // Step 1: Match expense_type_id with user_id in accounting_entries
                        $matchedEntry = \DB::table('accounting_entries')
                            ->where('id', $entry->accountant_id)
                            ->first();

                        // Step 2: Get user name from users table using the found user_id
                        $userName = null;
                        if ($matchedEntry && $matchedEntry->user_id) {
                            $userName = \DB::table('users')
                                ->where('id', $matchedEntry->user_id)
                                ->value('name');
                        }

                        $monthYear = \Carbon\Carbon::parse($entry->month_year)->format('Y-m');

                        return [
                            'Accountant' => $userName ?? 'N/A',
                            'Month' => $monthYear,
                            'Category' => $entry->expense_category,
                            'Description' => $entry->description ?? '-',
                            'Type' => ucfirst($entry->type),
                            'Amount' => number_format($entry->amount, 2),
                        ];
                    });

                    // Add summary rows
                    $summaryRows = collect([
                        [
                            'Accountant' => '',
                            'Month' => '',
                            'Category' => '',
                            'Description' => 'Total Debit',
                            'Type' => '',
                            'Amount' => number_format($this->totalDebit, 2),
                        ],
                        [
                            'Accountant' => '',
                            'Month' => '',
                            'Category' => '',
                            'Description' => 'Total Credit',
                            'Type' => '',
                            'Amount' => number_format($this->totalCredit, 2),
                        ],
                        [
                            'Accountant' => '',
                            'Month' => '',
                            'Category' => '',
                            'Description' => 'Net Total (Credit - Debit)',
                            'Type' => '',
                            'Amount' => number_format($this->netTotal, 2),
                        ],
                    ]);

                    // Combine entries and summary rows
                    return $entryRows->concat($summaryRows);
                }

                public function headings(): array
                {
                    return [
                        'Accountant',
                        'Month',
                        'Category',
                        'Description',
                        'Type',
                        'Amount',
                    ];
                }

                public function styles(Worksheet $sheet)
                {
                    // Style the header row
                    $sheet->getStyle('A1:F1')->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFCCCCCC']],
                    ]);

                    // Style the summary rows (last 3 rows)
                    $totalRows = $this->monthlyExpenses->count() + 2; // Start of summary rows
                    $sheet->getStyle("A$totalRows:F" . ($totalRows + 2))->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFE0B2']],
                    ]);

                    // Auto-size columns
                    foreach (range('A', 'F') as $column) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }

                    return [];
                }
            }, $fileName);
        } catch (\Exception $e) {
            \Log::error('Monthly export failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export report. Please try again.');
        }
    }

}