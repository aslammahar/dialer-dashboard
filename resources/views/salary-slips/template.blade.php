<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Salary Slip - {{ $slipNumber ?? 'N/A' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            padding: 10px;
        }
        
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Header */
        .header {
            border: 2px solid #000;
            padding: 12px;
            margin-bottom: 12px;
            text-align: center;
        }
        
        .logo {
            max-width: 180px;
            max-height: 120px;
            margin-bottom: 8px;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .company-address {
            font-size: 10px;
            margin-bottom: 8px;
        }
        
        .document-title {
            font-size: 15px;
            font-weight: bold;
            background: #000;
            color: #fff;
            padding: 7px;
            text-transform: uppercase;
            margin-top: 8px;
        }
        
        .slip-info {
            font-size: 9px;
            margin-top: 5px;
        }
        
        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border: 1px solid #000;
        }
        
        th {
            background-color: #000;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            padding: 8px;
            border: 1px solid #000;
        }
        
        td {
            padding: 7px 8px;
            border: 1px solid #000;
            font-size: 10px;
        }
        
        .label-cell {
            font-weight: bold;
            width: 35%;
            background: #f5f5f5;
        }
        
        .value-cell {
            width: 65%;
        }
        
        .amount-cell {
            text-align: right;
            font-weight: bold;
        }
        
        .center-cell {
            text-align: center;
        }
        
        /* Colored rows */
        .green-row {
            background-color: #d4edda;
        }
        
        .red-row {
            background-color: #f8d7da;
        }
        
        .yellow-row {
            background-color: #fff3cd;
        }
        
        .blue-row {
            background-color: #cfe2ff;
        }
        
        .grey-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        
        .black-row {
            background-color: #000;
            color: #fff;
            font-weight: bold;
            font-size: 12px;
        }
        
        /* Net box */
        .net-box {
            border: 3px solid #000;
            padding: 15px;
            text-align: center;
            margin: 12px 0;
        }
        
        .net-label {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .net-amount {
            font-size: 22px;
            font-weight: bold;
        }
        
        /* Footer */
        .note-box {
            border: 1px solid #000;
            padding: 8px;
            font-size: 9px;
            background: #fffacd;
            margin: 10px 0;
        }
        
        .signature-row td {
            text-align: center;
            padding: 45px 10px 10px 10px;
            border-top: 2px solid #000;
            font-weight: bold;
            font-size: 9px;
        }
        
        .footer-text {
            text-align: center;
            font-size: 8px;
            color: #666;
            margin-top: 8px;
            padding-top: 5px;
            border-top: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            @if(isset($company) && file_exists(public_path('assets/images/logo1.png')))
                <img src="{{ public_path('assets/images/logo1.png') }}" alt="Logo" class="logo">
            @else
                <div style="height: 80px; width: 100px; margin: 0 auto 8px; background: #ccc; display: flex; align-items: center; justify-content: center; color: #666; font-size: 11px;">Logo</div>
            @endif
            <div class="company-name">{{ $company['name'] ?? 'Jsons Communication' }}</div>
            <div class="company-address">
                {{ $company['address'] ?? 'PWD, Islamabad, Pakistan' }}<br>
                Phone: {{ $company['phone'] ?? '+92-XXX-XXXXXXX' }} | Email: {{ $company['email'] ?? 'info@jsons.com.pk' }}
            </div>
            <div class="document-title">Salary Slip</div>
            <div class="slip-info">Slip No: {{ $slipNumber ?? 'N/A' }} | Date: {{ date('d-M-Y') }}</div>
        </div>

        <!-- Employee Information -->
        <table>
            <tr>
                <th colspan="4">EMPLOYEE INFORMATION</th>
            </tr>
            <tr>
                <td class="label-cell">Employee Name</td>
                <td class="value-cell">{{ $userDetail->full_name ?? ($salary->user->name ?? 'N/A') }}</td>
                <td class="label-cell">Employee ID</td>
                <td class="value-cell">EMP-{{ str_pad($salary->user->id ?? 0, 4, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <td class="label-cell">Department</td>
                <td class="value-cell">{{ $salaryDepartment->name ?? 'N/A' }}</td>
                <td class="label-cell">Designation</td>
                <td class="value-cell">{{ $userDetail->designation ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Pay Period</td>
                <td class="value-cell">{{ date('F Y', mktime(0, 0, 0, ($salary->month ?? 1), 1, ($salary->year ?? date('Y')))) }}</td>
                <td class="label-cell">Payment Mode</td>
                <td class="value-cell">Bank Transfer</td>
            </tr>
        </table>

        <!-- Attendance Summary -->
        <table>
            <tr>
                <th colspan="4">ATTENDANCE SUMMARY</th>
            </tr>
            <tr>
                <td class="label-cell">Total Working Days</td>
                <td class="center-cell">{{ $salary->working_days ?? 0 }}</td>
                <td class="label-cell">Present Days</td>
                <td class="center-cell green-row">{{ $salary->present_days ?? 0 }}</td>
            </tr>
            <tr>
                <td class="label-cell">Absent Days</td>
                <td class="center-cell red-row">{{ $salary->absent_days ?? 0 }}</td>
                <td class="label-cell">Leave Days</td>
                <td class="center-cell yellow-row">{{ $salary->leave_days ?? 0 }}</td>
            </tr>
        </table>

        <!-- Earnings -->
        <table>
            <tr>
                <th colspan="2">EARNINGS</th>
            </tr>
            <tr class="green-row">
                <td>Basic Salary ({{ $salary->present_days ?? 0 }}/{{ $salary->working_days ?? 0 }} days)</td>
                <td class="amount-cell">{{ number_format($basicSalaryProRated ?? 0, 2) }}</td>
            </tr>
            @if(isset($salary->punctuality) && ($salary->punctuality ?? 0) > 0)
            <tr class="green-row">
                <td>Punctuality Allowance</td>
                <td class="amount-cell">{{ number_format($salary->punctuality, 2) }}</td>
            </tr>
            @endif
            @if(isset($salary->total_allowances) && ($salary->total_allowances ?? 0) > 0)
            <tr class="green-row">
                <td>Other Allowances</td>
                <td class="amount-cell">{{ number_format($salary->total_allowances, 2) }}</td>
            </tr>
            @endif
            @if(isset($salary->bonus) && ($salary->bonus ?? 0) > 0)
            <tr class="green-row">
                <td>Bonus / Incentive</td>
                <td class="amount-cell">{{ number_format($salary->bonus, 2) }}</td>
            </tr>
            @endif
            <tr class="grey-row">
                <td>GROSS SALARY (Before Tax)</td>
                <td class="amount-cell">{{ number_format($salary->gross_salary ?? 0, 2) }}</td>
            </tr>
        </table>

        <!-- Deductions -->
        <table>
            <tr>
                <th colspan="2">DEDUCTIONS</th>
            </tr>
            @if(isset($salary->total_deductions) && ($salary->total_deductions ?? 0) > 0)
            <tr class="red-row">
                <td>Total Deductions</td>
                <td class="amount-cell">{{ number_format($salary->total_deductions, 2) }}</td>
            </tr>
            @endif
            
            @if(isset($salary->tax_amount) && ($salary->tax_amount ?? 0) > 0)
            <tr class="blue-row">
                <td>
                    <strong>Income Tax ({{ number_format($salary->tax_percentage ?? 0, 2) }}%)</strong>
                    @if($salary->taxSlab)
                        <br><small style="font-size: 8px; color: #555;">Tax Slab: PKR {{ number_format($salary->taxSlab->min_salary, 0) }} - {{ $salary->taxSlab->max_salary ? 'PKR ' . number_format($salary->taxSlab->max_salary, 0) : 'Above' }}</small>
                    @endif
                </td>
                <td class="amount-cell">{{ number_format($salary->tax_amount, 2) }}</td>
            </tr>
            @endif
            
            @if((isset($salary->total_deductions) && ($salary->total_deductions ?? 0) > 0) || (isset($salary->tax_amount) && ($salary->tax_amount ?? 0) > 0))
            <tr class="grey-row">
                <td>TOTAL DEDUCTIONS</td>
                <td class="amount-cell">{{ number_format(($salary->total_deductions ?? 0) + ($salary->tax_amount ?? 0), 2) }}</td>
            </tr>
            @else
            <tr>
                <td colspan="2" class="center-cell" style="padding: 15px;">No Deductions</td>
            </tr>
            @endif
        </table>

        <!-- Net Salary -->
        <table>
            <tr class="black-row">
                <td>NET SALARY (PKR)</td>
                <td class="amount-cell">{{ number_format($salary->net_salary ?? 0, 2) }}</td>
            </tr>
        </table>

        <!-- Net Amount Box -->
        <div class="net-box">
            <div class="net-label">Net Amount Payable</div>
            <div class="net-amount">PKR {{ number_format($salary->net_salary ?? 0, 2) }}</div>
        </div>

        @if(isset($salary->remarks) && $salary->remarks)
        <div class="note-box">
            <strong>REMARKS:</strong> {{ $salary->remarks }}
        </div>
        @endif

        <!-- Important Note -->
        <div class="note-box">
            <strong>NOTE:</strong> This is a computer-generated salary slip and does not require a physical signature. Please verify all details carefully and report any discrepancies to the HR department within 7 days of receipt.
            @if(isset($salary->tax_amount) && ($salary->tax_amount ?? 0) > 0)
                Income tax has been deducted as per applicable government tax slabs and company policy.
            @endif
        </div>

        <!-- Signatures -->
        <table>
            <tr class="signature-row">
                <td style="width: 33.33%;">
                    <div>___________________</div>
                    <div style="margin-top: 5px;">Employee Signature</div>
                </td>
                <td style="width: 33.33%;">
                    <div>___________________</div>
                    <div style="margin-top: 5px;">HR Manager</div>
                </td>
                <td style="width: 33.33%;">
                    <div>___________________</div>
                    <div style="margin-top: 5px;">Authorized Signatory</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>