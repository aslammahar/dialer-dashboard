<?php

namespace App\Exports;

use App\Models\Recruitment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecruitmentReportExport implements FromCollection, WithHeadings
{
    protected $filters;
    protected $statuses;

    public function __construct($filters)
    {
        $this->filters = $filters;
        // Get all possible statuses (adjust as needed based on your data)
        $this->statuses = Recruitment::distinct('status')->pluck('status')->toArray();
    }

    public function collection()
    {
        $startDate = $this->filters['start_date'] ?? Carbon::now()->subDays(30)->format('Y-m-d');
        $endDate = $this->filters['end_date'] ?? Carbon::now()->format('Y-m-d');
        $selectedRecruiter = $this->filters['recruiter'] ?? '';
        
        // Base query with date filters
        $query = Recruitment::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate);
            
        if (!empty($selectedRecruiter)) {
            $query->where('interview_taken_by', $selectedRecruiter);
        }
        
        // Group by recruiter and status to match the table view
        $data = $query->select('interview_taken_by as recruiter', 'status')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('interview_taken_by', 'status')
            ->get();
            
        // Restructure data to match the table format
        $reportData = [];
        foreach ($data as $item) {
            if (!isset($reportData[$item->recruiter])) {
                $reportData[$item->recruiter] = [
                    'recruiter' => $item->recruiter,
                    'total_calls' => 0
                ];
                
                // Initialize all status counts to 0
                foreach ($this->statuses as $status) {
                    $reportData[$item->recruiter][$status] = 0;
                }
            }
            
            // Update counts
            $reportData[$item->recruiter]['total_calls'] += $item->count;
            $reportData[$item->recruiter][$item->status] = $item->count;
        }
        
        // Convert to collection format for Excel export
        $excelData = collect();
        foreach ($reportData as $row) {
            $exportRow = [
                'Recruiter' => $row['recruiter'],
                'Total Calls' => $row['total_calls']
            ];
            
            // Add status columns
            foreach ($this->statuses as $status) {
                $exportRow[$status] = $row[$status] ?? 0;
            }
            
            $excelData->push($exportRow);
        }
        
        return $excelData;
    }

    public function headings(): array
    {
        // Dynamic headings based on available statuses
        $headings = ['Recruiter', 'Total Calls'];
        foreach ($this->statuses as $status) {
            $headings[] = $status;
        }
        
        return $headings;
    }
}