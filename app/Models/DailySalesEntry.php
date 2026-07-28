<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DailySalesEntry extends Model
{
    protected $fillable = [
        'entry_date', 'sales_closer_id', 'sales_team_id', 'sales_client_id',
        'sales_carrier_id', 'status', 'sale_type', 'avg_pre', 'notes',
        'leads_id', 'created_by',

    ];

    protected $casts = ['entry_date' => 'date'];

    public function closer()  { return $this->belongsTo(SalesCloser::class, 'sales_closer_id'); }
    public function team()    { return $this->belongsTo(SalesTeam::class, 'sales_team_id'); }
    public function client()  { return $this->belongsTo(SalesClient::class, 'sales_client_id'); }
    public function carrier() { return $this->belongsTo(SalesCarrier::class, 'sales_carrier_id'); }
}