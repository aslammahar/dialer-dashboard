<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class AvatarFrom extends Model
{
    use HasFactory;

    // Define the table associated with the model fro Storing Te,p data in the database
    // same data save in avatar leads  just Some data in the database
    protected $table = 'avatar_temp_leads';

    // Define the fillable columns
    protected $fillable = [

        'agent_id',
        'lead_id',
        'vendor_id',
        'list_id',
        'gmt_offset_now',
        'phone_code',
        'smoker',
        'phone_number',
        'title',
        'first_name',
        'middle_initial',
        'last_name',
        'address1',
        'address2',
        'address3',
        'city',
        'state',
        'province',
        'postal_code',
        'country_code',
        'gender',
        'date_of_birth',
        'alt_phone',
        'email',
        'security_phrase',
        'comments',
        'user',
        'pass',
        'orig_pass',
        'campaign',
        'phone_login',
        'original_phone_login',
        'phone_pass',
        'fronter',
        'closer',
        'group_a',
        'channel_group',
        'SQLdate',
        'epoch',
        'uniqueid',
        'customer_zap_channel',
        'customer_server_ip',
        'server_ip',
        'SIPexten',
        'session_id',
        'phone',
        'parked_by',
        'dispo',
        'dialed_number',
        'dialed_label',
        'source_id',
        'rank',
        'OWNER',
        'camp_script',
        'in_script',
        'in_script_two',
        'agent_name',
        'agent_email',
        'recording_filename',
        'recording_id',
        'user_custom_one',
        'user_custom_two',
        'user_custom_three',
        'user_custom_four',
        'user_custom_five',
        'preset_number_a',
        'preset_number_b',
        'preset_number_c',
        'preset_number_d',
        'preset_number_e',
        'preset_dtmf_a',
        'preset_dtmf_b',
        'did_id',
        'did_extension',
        'did_pattern',
        'did_description',
        'closecallid',
        'xfercallid',
        'agent_log_id',
        'entry_list_id',
        'call_id',
        'user_group',
        'list_name',
        'list_description',
        'entry_date',
        'did_custom_one',
        'did_custom_two',
        'did_custom_three',
        'did_custom_four',
        'did_custom_five',
        'called_count',
        'session_name',
        'created_at',
        'updated_at',
        'age',
        'verifier_name',
        'dailer_no',
        'center',
        'closer_name',
        'xferSubmission',
        'recording_link',
        'script_width',
        'script_height',
        'email_row_id',
        'INOUT',
        'LOGINvarONE',
        'LOGINvarTWO',
        'LOGINvarTHREE',
        'LOGINvarFOUR',
        'LOGINvarFIVE',
        'hide_relogin_fields',
        'web_vars',
        'dialername',
        'centername',
    ];
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_email', 'email');
    }

    public function getPhoneNumberAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function setPhoneNumberAttribute($value)
    {
        $this->attributes['phone_number'] = Crypt::encryptString($value);
    }
}
