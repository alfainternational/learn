<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $reminders = Reminder::where('user_id', auth()->id())
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->when($request->upcoming, fn($q) => $q->where('remind_at', '>=', now()))
            ->orderBy('remind_at')
            ->get();

        return response()->json($reminders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:email,push,sms',
            'remind_at' => 'required|date|after:now',
            'remindable_type' => 'nullable|string',
            'remindable_id' => 'nullable|integer',
            'message' => 'nullable|string',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'nullable|in:daily,weekly,monthly',
        ]);

        $reminder = Reminder::create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'data' => $reminder], 201);
    }

    public function update(Request $request, Reminder $reminder)
    {
        $this->authorize('update', $reminder);
        $reminder->update($request->all());
        return response()->json(['success' => true, 'data' => $reminder]);
    }

    public function destroy(Reminder $reminder)
    {
        $this->authorize('delete', $reminder);
        $reminder->delete();
        return response()->json(['success' => true]);
    }

    public function markSent(Reminder $reminder)
    {
        $this->authorize('update', $reminder);
        $reminder->update(['sent_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function upcoming()
    {
        $reminders = Reminder::where('user_id', auth()->id())
            ->whereNull('sent_at')
            ->where('remind_at', '<=', now()->addDay())
            ->orderBy('remind_at')
            ->get();

        return response()->json($reminders);
    }
}
