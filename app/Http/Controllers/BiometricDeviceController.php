<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\FingerHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\FingerDevice\StoreRequest;
use App\Http\Requests\FingerDevice\UpdateRequest;
use App\Jobs\GetAttendanceJob;
// use App\Jobs\ClearAttendanceJob;
use App\Models\FingerDevices;
use App\Models\Employee;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Leave;
use Gate;
use Illuminate\Http\RedirectResponse;
use Rats\Zkteco\Lib\ZKTeco;
use Symfony\Component\HttpFoundation\Response;
use App\Models\EmployeApi;
use App\Models\Role;

class BiometricDeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        set_time_limit(300);
        $devices = FingerDevices::all();

        return view('admin.fingerDevices.index', compact('devices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.fingerDevices.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        $helper = new FingerHelper();
        $device = $helper->init($request->input('ip'));

        if ($device->connect()) {
            // Serial Number Sample CDQ9192960002\x00
            $serial = $helper->getSerial($device);

            FingerDevices::create($request->validated() + ['serialNumber' => $serial]);

            return redirect()->route('finger_device.index')->with('success', 'Biometric Device created successfully!');
        } else {
            return redirect()->route('finger_device.index')->with('error', 'Failed connecting to Biometric Device!');
        }
    }

    public function show(FingerDevices $fingerDevice)
    {
        // Increase the maximum execution time to 300 seconds (5 minutes)
        set_time_limit(300);

        return view('admin.fingerDevices.show', compact('fingerDevice'));
    }


    public function edit(FingerDevices $fingerDevice)
    {
        return view('admin.fingerDevices.edit', compact('fingerDevice'));
    }

    public function update(UpdateRequest $request, FingerDevices $fingerDevice): RedirectResponse
    {
        $fingerDevice->update($request->validated());

        flash()->success('Success', 'Biometric Device Updated successfully !');

        return redirect()->route('finger_device.index');
    }
    public function destroy(FingerDevices $fingerDevice): RedirectResponse
    {
        try {
            $fingerDevice->delete();
        } catch (\Exception $e) {
            toast("Failed to delete {$fingerDevice->name}", 'error');
        }

        flash()->success('Success', 'Biometric Device deleted successfully !');

        return back();
    }

    public function addEmployee(FingerDevices $fingerDevice): RedirectResponse
    {
        $device = new ZKTeco($fingerDevice->ip, 4370);

        $device->connect();

        $deviceUsers = collect($device->getUser())->pluck('uid');

        $employees = User::select('name', 'id')
            ->whereNotIn('id', $deviceUsers)
            ->get();

        $i = 1;

        foreach ($employees as $employee) {
            $device->setUser($i++, $employee->id, $employee->name, '', '0', '0');
        }
        flash()->success('Success', 'All Employees added to Biometric device successfully!');

        return back();
    }

    public function getAttendance(FingerDevices $fingerDevice)
    {
        set_time_limit(900);
        $device = new ZKTeco($fingerDevice->ip, 4370);
        $device->connect();

        $data = $device->getAttendance();

        // dd($data);

        foreach ($data as $item) {
            // dd($item['id']);
            // Debugging: Ensure $item is an array and contains expected keys
            if (is_array($item) && isset($item['uid'], $item['id'], $item['state'], $item['timestamp'], $item['type'])) {
                $timestamp = $item['timestamp'];
                $attendanceDate = date('Y-m-d', strtotime($timestamp));
                $attendanceTime = date('H:i:s', strtotime($timestamp));


                // Find the user by id
                $user = User::find($item['id']);
                // dd($user);

                if ($user) {
                    // Store the attendance record
                    Attendance::updateOrCreate(
                        [
                            'uid' => $item['uid'],
                            'attendance_date' => $attendanceDate,
                            'attendance_time' => $attendanceTime,
                        ],
                        [
                            'employee_id' => $item['id'], // Assuming 'employee_id' in Attendance is the same as 'id' in Users
                            'state' => $item['state'],
                            'type' => $item['type'],
                            'status' => 1, // Assuming a default status value of 1
                        ]
                    );
                } else {
                    // Handle the case where the user is not found
                    \Log::warning("User with ID {$item['id']} not found.");
                }
            } else {
                // Handle missing fields in the item
                \Log::warning("Incomplete data: " . json_encode($item));
            }
        }

        return redirect()->back()->with('status', 'Attendance are added successfully');
    }


}
