<?php

namespace App\Exports;

use App\Models\AvatarLead;
use DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AvatarLeadExport implements FromCollection, WithHeadings
{
    protected $selectedLeads;
    protected $startDate;
    protected $endDate;
    protected $exportAll;

    public function __construct($selectedLeads = [], $startDate = null, $endDate = null, $exportAll = false)
    {
        $this->selectedLeads = is_array($selectedLeads) ? $selectedLeads : (is_string($selectedLeads) && !empty($selectedLeads) ? explode(',', $selectedLeads) : []);
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->exportAll = $exportAll;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Lead ID',

            'Dialer ID',
            'Agent Name',
            'Team Name',
            'Closer Name',
            'Recording',
            'Comments',
            'Status',
            'Billing Status',
            'Duration',
            'Qa Person',
            'Entry time',
            'Qa time',
        ];
    }

    public function collection()
    {
        $query = DB::table('avatar_leads')
            ->select(
                'avatar_leads.id',
                'avatar_leads.lead_id',
                'avatar_leads.dialer_id',
                'avatar_leads.agent_name',
                DB::raw("(
                    SELECT teams.name FROM users u
                    LEFT JOIN team_agent ta ON u.id = ta.agent_id
                    LEFT JOIN teams ON ta.team_id = teams.id
                    WHERE u.dialer_id = avatar_leads.dialer_id
                    LIMIT 1
                ) as team_name"),
                DB::raw("(
                    SELECT users.name
                    FROM users
                    WHERE users.id = avatar_leads.closer_name
                ) as closer_name"),
                'avatar_leads.recording_link',
                'avatar_leads.Qacomments',
                'avatar_leads.QAstatus',
                'avatar_leads.billing_status',
                'avatar_leads.billable_duration',
                DB::raw("(
                    SELECT users.name
                    FROM users
                    WHERE users.id = avatar_leads.QaPersonId
                ) as qa_person_name"),
                'avatar_leads.created_at',
                'avatar_leads.Qadate',
            );

        // If exportAll is true, use date range instead of selected leads
        if ($this->exportAll && $this->startDate && $this->endDate) {
            $query->whereBetween('avatar_leads.created_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
        } elseif (!empty($this->selectedLeads)) {
            // Use selected leads if provided
            $query->whereIn('avatar_leads.id', $this->selectedLeads);
        } else {
            // If neither is provided, return empty collection
            return collect([]);
        }

        // Apply center scope same as AvatarLead global scope
        $authUser = Auth::user();
        if ($authUser && !in_array($authUser->type, ['super admin', 'company']) && !empty($authUser->center_id)) {
            $query->where('avatar_leads.center_id', $authUser->center_id);
        }

        $query->orderBy('avatar_leads.created_at', 'desc');

        $data = $query->get();

        return $data;
    }
}
