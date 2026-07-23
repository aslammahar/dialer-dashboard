<?php

namespace App\Http\Controllers;
use App\Models\AvatarLead;
use App\Models\AvatarQa;
use App\Models\DialerList;
use App\Models\CenterList;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;



class ParameterController extends Controller
{



    public function store(Request $request)

    {
        // dd($request->all());
        // Create a new AvatarLead instance

        $avatarLead = new AvatarLead;
        $avatarLead->agent_id = $request->agent_id;
        $avatarLead->agent_name = $request->agent_name;
        $avatarLead->owner = $request->owner;
        $avatarLead->smoker = $request->smoker;
        $avatarLead->age = $request->age;
        $avatarLead->verifier_name = $request->verifier_name;
        $avatarLead->dailer_no = $request->dailer_no;
        $avatarLead->center = $request->center;
        $avatarLead->lead_id = $request->lead_id;
        $avatarLead->list_id = $request->list_id;
        $avatarLead->recording_filename = $request->recording_filename;
        $avatarLead->phone_number = $request->phone_number;
        $avatarLead->campaign = $request->campaign;
        $avatarLead->closer = $request->closer;
        $avatarLead->group_a = $request->group_a;
        $avatarLead->server_ip = $request->server_ip;
        $avatarLead->dispo = $request->dispo;
        $avatarLead->recording_id = $request->recording_id;
        $avatarLead->entry_list_id = $request->entry_list_id;
        $avatarLead->user_group = $request->user_group;
        $avatarLead->list_name = $request->list_name;
        $avatarLead->list_description = $request->list_description;
        $avatarLead->entry_date = $request->entry_date;
        $avatarLead->closer_name = $request->closer_name;
        $avatarLead->dialername = $request->dialername;
        $avatarLead->centername = $request->centername;
        $avatarLead->xferSubmission = $request->xferSubmission;

        $datePart = substr($recording_filename, 0, 8);
        $formattedDate = DateTime::createFromFormat('Ymd', $datePart)->format('Y-m-d');

        // Fetch the folder_name from the dialer_servers table based on server_ip
        $folder_name = DB::table('dialer_servers')
            ->where('server_ip', $server_ip)
            ->value('folder_name');
    
        // Check if folder_name was found, otherwise set a default
        $folder_name = $folder_name ?: 'default_folder';
    
        // Construct the recording link
        $recording_link = "https://jsonarchive.dialerhosting.com/$folder_name/$formattedDate/$recording_filename-all.mp3";
        $recordinglink2 = "http://$server_ip/$folder_name/FTP2/$recording_filename-all.mp3";
    

        // Construct and save the recording link
        $avatarLead->recording_link = $recording_link;

        // Save the AvatarLead instance to the database
        $avatarLead->save();

        // Redirect back to the form with a success message
        return redirect()->route('display', ['lead_id' => $request->lead_id],['dialerMatch' => $dialerMatch])->with('success', 'Avatar lead created successfully.');
    }


    // Other methods...




    // Other methods...



    public function create(Request $request)
    {
        // Fetch necessary data for the form, such as agents and verifiers
        $agents = User::where('type', 'avatar')->pluck('name', 'id');
        $verifiers = User::where('type', 'Closer')->get();;

        // You can check if the parameters exist in the request
        // If they exist, you can pre-fill the form fields
        $agent_id = $request->input('agent_id');
        $lead_id = $request->input('lead_id');
        $center = $request->input('center');
        $server_ip = $request->input('server_ip');
        $agent_name = $request->input('agent_name');
        $smoker = $request->input('smoker');
        $age = $request->input('age');
        $verifier_name = $request->input('verifier_name');
        $dailer_no = $request->input('dailer_no');
        $list_id = $request->input('list_id');
        $recording_filename = $request->input('recording_filename');
        $phone_number = $request->input('phone_number');
        $campaign = $request->input('campaign');
        $closer = $request->input('closer');
        $group = $request->input('group');
        $group_a = $request->input('group_a');
        $dispo = $request->input('dispo');
        $recording_id = $request->input('recording_id');
        $entry_list_id = $request->input('entry_list_id');
        $user_group = $request->input('user_group');
        $list_name = $request->input('list_name');
        $list_description = $request->input('list_description');
        $entry_date = $request->input('entry_date');
        $closer_name = $request->input('closer_name');
        $dialername = $request->input('dialername');
        $centername = $request->input('centername');

        // Add missing parameters
        $vendor_id = $request->input('vendor_id');
        $gmt_offset_now = $request->input('gmt_offset_now');
        $phone_code = $request->input('phone_code');
        $title = $request->input('title');
        $first_name = $request->input('first_name');
        $middle_initial = $request->input('middle_initial');
        $last_name = $request->input('last_name');
        $address1 = $request->input('address1');
        $address2 = $request->input('address2');
        $address3 = $request->input('address3');
        $city = $request->input('city');
        $state = $request->input('state');
        $province = $request->input('province');
        $postal_code = $request->input('postal_code');
        $country_code = $request->input('country_code');
        $gender = $request->input('gender');
        $date_of_birth = $request->input('date_of_birth');
        $alt_phone = $request->input('alt_phone');
        $email = $request->input('email');
        $security_phrase = $request->input('security_phrase');
        $comments = $request->input('comments');
        $user = $request->input('user');
        $pass = $request->input('pass');
        $orig_pass = $request->input('orig_pass');
        $phone_login = $request->input('phone_login');
        $original_phone_login = $request->input('original_phone_login');
        $phone_pass = $request->input('phone_pass');
        $fronter = $request->input('fronter');
        $channel_group = $request->input('channel_group');
        $SQLdate = $request->input('SQLdate');
        $epoch = $request->input('epoch');
        $uniqueid = $request->input('uniqueid');
        $customer_zap_channel = $request->input('customer_zap_channel');
        $customer_server_ip = $request->input('customer_server_ip');
        $SIPexten = $request->input('SIPexten');
        $session_id = $request->input('session_id');
        $dialed_number = $request->input('dialed_number');
        $dialed_label = $request->input('dialed_label');
        $source_id = $request->input('source_id');
        $rank = $request->input('rank');
        $owner = $request->input('owner');
        $camp_script = $request->input('camp_script');
        $in_script = $request->input('in_script');
        $in_script_two = $request->input('in_script_two');
        $script_width = $request->input('script_width');
        $script_height = $request->input('script_height');
        $agent_email = $request->input('agent_email');
        $recording_filename = $request->input('recording_filename');
        $recording_id = $request->input('recording_id');
        $user_custom_one = $request->input('user_custom_one');
        $user_custom_two = $request->input('user_custom_two');
        $user_custom_three = $request->input('user_custom_three');
        $user_custom_four = $request->input('user_custom_four');
        $user_custom_five = $request->input('user_custom_five');
        $preset_number_a = $request->input('preset_number_a');
        $preset_number_b = $request->input('preset_number_b');
        $preset_number_c = $request->input('preset_number_c');
        $preset_number_d = $request->input('preset_number_d');
        $preset_number_e = $request->input('preset_number_e');
        $preset_dtmf_a = $request->input('preset_dtmf_a');
        $preset_dtmf_b = $request->input('preset_dtmf_b');
        $did_id = $request->input('did_id');
        $did_extension = $request->input('did_extension');
        $did_pattern = $request->input('did_pattern');
        $did_description = $request->input('did_description');
        $closecallid = $request->input('closecallid');
        $xfercallid = $request->input('xfercallid');
        $agent_log_id = $request->input('agent_log_id');
        $call_id = $request->input('call_id');
        $list_description = $request->input('list_description');
        $called_count = $request->input('called_count');
        $email_row_id = $request->input('email_row_id');
        $LOGINvarONE = $request->input('LOGINvarONE');
        $LOGINvarTWO = $request->input('LOGINvarTWO');
        $LOGINvarTHREE = $request->input('LOGINvarTHREE');
        $LOGINvarFOUR = $request->input('LOGINvarFOUR');
        $LOGINvarFIVE = $request->input('LOGINvarFIVE');
        $hide_relogin_fields = $request->input('hide_relogin_fields');
        $web_vars = $request->input('web_vars');
        $session_name = $request->input('session_name');

        // Return the view with the form and the pre-filled data
        return view('display', compact('agents', 'verifiers', 'agent_id', 'lead_id', 'center', 'server_ip', 'agent_name', 'smoker', 'age', 'verifier_name', 'dailer_no', 'list_id', 'recording_filename', 'phone_number', 'campaign', 'closer', 'group_a', 'dispo', 'recording_id', 'entry_list_id', 'user_group', 'list_name', 'list_description', 'entry_date', 'closer_name', 'dialername', 'centername', 'vendor_id', 'gmt_offset_now', 'phone_code', 'title', 'first_name', 'middle_initial', 'last_name', 'address1', 'address2', 'address3', 'city', 'state', 'province', 'postal_code', 'country_code', 'gender', 'date_of_birth', 'alt_phone', 'email', 'security_phrase', 'comments', 'user', 'pass', 'orig_pass', 'phone_login', 'original_phone_login', 'phone_pass', 'fronter', 'channel_group', 'SQLdate', 'epoch', 'uniqueid', 'customer_zap_channel', 'customer_server_ip', 'SIPexten', 'session_id', 'dialed_number', 'dialed_label', 'source_id', 'rank', 'owner', 'camp_script', 'in_script', 'in_script_two', 'script_width', 'script_height', 'agent_email', 'recording_filename', 'recording_id', 'user_custom_one', 'user_custom_two', 'user_custom_three', 'user_custom_four', 'user_custom_five', 'preset_number_a', 'preset_number_b', 'preset_number_c', 'preset_number_d', 'preset_number_e', 'preset_dtmf_a', 'preset_dtmf_b', 'did_id', 'did_extension', 'did_pattern', 'did_description', 'closecallid', 'xfercallid', 'agent_log_id', 'call_id', 'list_description', 'called_count', 'email_row_id', 'LOGINvarONE', 'LOGINvarTWO', 'LOGINvarTHREE', 'LOGINvarFOUR', 'LOGINvarFIVE', 'hide_relogin_fields', 'web_vars', 'session_name'));
    }


    public function display(Request $request)
    {

        // Extract data from the form submission
        $verifiers = User::where('type', 'Closer')->get();
        $currentDateTime = now();
        $lead_id = $request->input('lead_id');
        $vendor_id = $request->input('vendor_id');
        $list_id = $request->input('list_id');
        $gmt_offset_now = $request->input('gmt_offset_now');
        $phone_code = $request->input('phone_code');
        $phone_number = $request->input('phone_number');
        $title = $request->input('title');
        $first_name = $request->input('first_name');
        $middle_initial = $request->input('middle_initial');
        $last_name = $request->input('last_name');
        $address1 = $request->input('address1');
        $address2 = $request->input('address2');
        $address3 = $request->input('address3');
        $city = $request->input('city');
        $state = $request->input('state');
        $province = $request->input('province');
        $postal_code = $request->input('postal_code');
        $country_code = $request->input('country_code');
        $gender = $request->input('gender');
        $date_of_birth = $request->input('date_of_birth');
        $alt_phone = $request->input('alt_phone');
        $email = $request->input('email');
        $security_phrase = $request->input('security_phrase');
        $comments = $request->input('comments');
        $user = $request->input('user');
        $pass = $request->input('pass');
        $orig_pass = $request->input('orig_pass');
        $campaign = $request->input('campaign');
        $phone_login = $request->input('phone_login');
        $original_phone_login = $request->input('original_phone_login');
        $phone_pass = $request->input('phone_pass');
        $fronter = $request->input('fronter');
        $closer = $request->input('closer');
        $group_a = $request->input('group');
        $channel_group = $request->input('channel_group');
        $SQLdate = $request->input('SQLdate');
        // dd($SQLdate);
        $epoch = $request->input('epoch');
        $uniqueid = $request->input('uniqueid');
        $customer_zap_channel = $request->input('customer_zap_channel');
        $customer_server_ip = $request->input('customer_server_ip');
        $SIPexten = $request->input('SIPexten');
        $session_id = $request->input('session_id');
        $phone = $request->input('phone');
        $parked_by = $request->input('parked_by');
        $dispo = $request->input('dispo');
        $dialed_number = $request->input('dialed_number');
        $dialed_label = $request->input('dialed_label');
        $source_id = $request->input('source_id');
        $rank = $request->input('rank');
        $ownern = $request->input('ownern');
        $owner = $request->input('owner');
        $camp_script = $request->input('camp_script');
        $in_script = $request->input('in_script');
        $in_script_two = $request->input('in_script_two');
        $script_width = $request->input('script_width');
        $script_height = $request->input('script_height');
        $fullname = $request->input('fullname');
        $agent_email = $request->input('agent_email');
        $recording_filename = $request->input('recording_filename');
        $recording_id = $request->input('recording_id');
        $user_custom_one = $request->input('user_custom_one');
        $user_custom_two = $request->input('user_custom_two');
        $user_custom_three = $request->input('user_custom_three');
        $user_custom_four = $request->input('user_custom_four');
        $user_custom_five = $request->input('user_custom_five');
        $preset_number_a = $request->input('preset_number_a');
        $preset_number_b = $request->input('preset_number_b');
        $preset_number_c = $request->input('preset_number_c');
        $preset_number_d = $request->input('preset_number_d');
        $preset_number_e = $request->input('preset_number_e');
        $preset_dtmf_a = $request->input('preset_dtmf_a');
        $preset_dtmf_b = $request->input('preset_dtmf_b');
        $did_id = $request->input('did_id');
        $did_extension = $request->input('did_extension');
        $did_pattern = $request->input('did_pattern');
        $did_description = $request->input('did_description');
        $closecallid = $request->input('closecallid');
        $xfercallid = $request->input('xfercallid');
        $agent_log_id = $request->input('agent_log_id');
        $entry_list_id = $request->input('entry_list_id');
        $call_id = $request->input('call_id');
        $user_group = $request->input('user_group');
        $list_name = $request->input('list_name');
        $list_description = $request->input('list_description');
        $entry_date = $request->input('entry_date');
        $did_custom_one = $request->input('did_custom_one');
        $did_custom_two = $request->input('did_custom_two');
        $did_custom_three = $request->input('did_custom_three');
        $did_custom_four = $request->input('did_custom_four');
        $did_custom_five = $request->input('did_custom_five');
        $called_count = $request->input('called_count');
        $email_row_id = $request->input('email_row_id');
        $LOGINvarONE = $request->input('LOGINvarONE');
        $LOGINvarTWO = $request->input('LOGINvarTWO');
        $LOGINvarTHREE = $request->input('LOGINvarTHREE');
        $LOGINvarFOUR = $request->input('LOGINvarFOUR');
        $LOGINvarFIVE = $request->input('LOGINvarFIVE');
        $hide_relogin_fields = $request->input('hide_relogin_fields');
        $web_vars = $request->input('web_vars');
        $session_name = $request->input('session_name');
        $server_ip = $request->input('server_ip');
        $closercode = Str::lower(Str::substr($closer, 0, 3));
        $dialerMatch = DialerList::where('dialer_ip', $server_ip)->value('dialer_no');
        $centerMatch = CenterList::where('centerCode', $closercode)->value('centerName');




        return view('display', [
            'currentDateTime'  =>  $currentDateTime,
            'verifiers' => $verifiers,
            'lead_id' =>$lead_id,
            'vendor_id' =>$vendor_id,
            'list_id' =>$list_id,
            'gmt_offset_now' =>$gmt_offset_now,
            'phone_code' =>$phone_code,
            'phone_number' =>$phone_number,
            'title' =>$title,
            'first_name' =>$first_name,
            'middle_initial' =>$middle_initial,
            'last_name' =>$last_name,
            'address1' =>$address1,
            'address2' =>$address2,
            'address3' =>$address3,
            'city' =>$city,
            'state' =>$state,
            'province' =>$province,
            'postal_code' =>$postal_code,
            'country_code' =>$country_code,
            'gender' =>$gender,
            'date_of_birth' =>$date_of_birth,
            'alt_phone' =>$alt_phone,
            'email' =>$email,
            'security_phrase' =>$security_phrase,
            'comments' =>$comments,
            'user' =>$user,
            'pass' =>$pass,
            'orig_pass' =>$orig_pass,
            'campaign' =>$campaign,
            'phone_login' =>$phone_login,
            'original_phone_login' =>$original_phone_login,
            'phone_pass' =>$phone_pass,
            'fronter' =>$fronter,
            'closer' =>$closer,
            'group_a' =>$group_a,
            'channel_group' =>$channel_group,
            'SQLdate' =>$SQLdate,
            'epoch' =>$epoch,
            'uniqueid' =>$uniqueid,
            'customer_zap_channel' =>$customer_zap_channel,
            'customer_server_ip' =>$customer_server_ip,
            'server_ip' =>$server_ip,
            'SIPexten' =>$SIPexten,
            'session_id' =>$session_id,
            'phone' =>$phone,
            'parked_by' =>$parked_by,
            'dispo' =>$dispo,
            'dialed_number' =>$dialed_number,
            'dialed_label' =>$dialed_label,
            'source_id' =>$source_id,
            'rank' =>$rank,
            'ownern' =>$ownern,
            'owner' =>$owner,
            'camp_script' =>$camp_script,
            'in_script' =>$in_script,
            'in_script_two' =>$in_script_two,
            'script_width' =>$script_width,
            'script_height' =>$script_height,
            'fullname' =>$fullname,
            'agent_email' =>$agent_email,
            'recording_filename' =>$recording_filename,
            'recording_id' =>$recording_id,
            'user_custom_one' =>$user_custom_one,
            'user_custom_two' =>$user_custom_two,
            'user_custom_three' =>$user_custom_three,
            'user_custom_four' =>$user_custom_four,
            'user_custom_five' =>$user_custom_five,
            'preset_number_a' =>$preset_number_a,
            'preset_number_b' =>$preset_number_b,
            'preset_number_c' =>$preset_number_c,
            'preset_number_d' =>$preset_number_d,
            'preset_number_e' =>$preset_number_e,
            'preset_dtmf_a' =>$preset_dtmf_a,
            'preset_dtmf_b' =>$preset_dtmf_b,
            'did_id' =>$did_id,
            'did_extension' =>$did_extension,
            'did_pattern' =>$did_pattern,
            'did_description' =>$did_description,
            'closecallid' =>$closecallid,
            'xfercallid' =>$xfercallid,
            'agent_log_id' =>$agent_log_id,
            'entry_list_id' =>$entry_list_id,
            'call_id' =>$call_id,
            'user_group' =>$user_group,
            'list_name' =>$list_name,
            'list_description' =>$list_description,
            'entry_date' =>$entry_date,
            'did_custom_one' =>$did_custom_one,
            'did_custom_two' =>$did_custom_two,
            'did_custom_three' =>$did_custom_three,
            'did_custom_four' =>$did_custom_four,
            'did_custom_five' =>$did_custom_five,
            'called_count' =>$called_count,
            'email_row_id' =>$email_row_id,
            'LOGINvarONE' =>$LOGINvarONE,
            'LOGINvarTWO' =>$LOGINvarTWO,
            'LOGINvarTHREE' =>$LOGINvarTHREE,
            'LOGINvarFOUR' =>$LOGINvarFOUR,
            'LOGINvarFIVE' =>$LOGINvarFIVE,
            'hide_relogin_fields' =>$hide_relogin_fields,
            'web_vars' =>$web_vars,
            'session_name' =>$session_name,
            'currentDateTime' => $currentDateTime,
            'server_ip' => $server_ip,
            'dialerMatch' => $dialerMatch,
            'centerMatch'=> $centerMatch,

        ]);

    }
}
