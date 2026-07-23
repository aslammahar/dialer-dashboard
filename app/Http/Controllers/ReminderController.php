<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReminderController extends Controller
{
    public function index()
    {
        $user =Auth::id();
        $reminders = Reminder::where('user_id', Auth::id())
            ->orderBy('reminder_time', 'desc')
            ->paginate(10);

        
            $activeReminders = Reminder::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->where('reminder_time', '>=', now()) // Fetch reminders where time has already passed
            ->get();
            
        return view('reminders.index', compact('reminders'));
    }

    public function create()
{
    $defaultDateTime = now()->format('Y-m-d\TH:i');
    return view('reminders.create', compact('defaultDateTime'));
}

public function store(Request $request)
{
    $validatedData = $request->validate([
        'title' => 'required|max:255',
        'description' => 'nullable',
        'reminder_time' => 'required|date|after:now',
        'timezone' => 'required|in:Asia/Karachi,America/New_York' // Validate timezone input
    ]);

    // Convert input time to UTC based on the selected timezone
    $reminderTimeUTC = Carbon::parse($validatedData['reminder_time'], $validatedData['timezone'])
                             ->setTimezone('UTC');

    $reminder = Reminder::create([
        'title' => $validatedData['title'],
        'description' => $validatedData['description'] ?? null,
        'reminder_time' => $reminderTimeUTC, // Store in UTC
        'user_id' => Auth::id(),
        'status' => 'pending'
    ]);

    return redirect()->route('reminders.index')
        ->with('success', 'Reminder created successfully.');
}
    public function edit(Reminder $reminder)
    {
        // Ensure user can only edit their own reminders
        $this->authorize('update', $reminder);
        $defaultDateTime = now()->format('Y-m-d\TH:i');

        return view('reminders.edit', compact('reminder','defaultDateTime'));
    }

    public function update(Request $request, Reminder $reminder)
{
    // Ensure user can only update their own reminders
    $this->authorize('update', $reminder);

    $validatedData = $request->validate([
        'title' => 'required|max:255',
        'description' => 'nullable',
        'reminder_time' => 'required|date|after:now',
        'timezone' => 'required|in:Asia/Karachi,America/New_York', // Validate timezone input
    ]);

    // Convert input time to UTC based on the selected timezone
    $reminderTimeUTC = Carbon::parse($validatedData['reminder_time'], $validatedData['timezone'])
                             ->setTimezone('UTC');

    $reminder->update([
        'title' => $validatedData['title'],
        'description' => $validatedData['description'] ?? null,
        'reminder_time' => $reminderTimeUTC, // Store in UTC
        'timezone' => $validatedData['timezone'], // Update the timezone
    ]);

    return redirect()->route('reminders.index')
        ->with('success', 'Reminder updated successfully.');
}

    public function destroy(Reminder $reminder)
    {
        // Ensure user can only delete their own reminders
        $this->authorize('delete', $reminder);

        $reminder->delete();

        return redirect()->route('reminders.index')
            ->with('success', 'Reminder deleted successfully.');
    }

    public function markCompleted(Reminder $reminder)
    {
        // Ensure user can only update their own reminders
        $this->authorize('update', $reminder);

        $reminder->update(['status' => 'completed']);

        return response()->json(['success' => true]);
    }

    public function markCancelled(Reminder $reminder)
    {
        // Ensure user can only update their own reminders
        $this->authorize('update', $reminder);

        $reminder->update(['status' => 'cancelled']);

        return response()->json(['success' => true]);
    }

    public function getActiveReminders()
{
    $activeReminders = Reminder::where('user_id', Auth::id())
        ->where('status', 'pending')
        ->where('reminder_time', '<=', now()) // Fetch reminders where time has already passed
        ->orderBy('reminder_time', 'asc') // Optional: Order by oldest reminders first
        ->get();

    return response()->json($activeReminders);
}
}

