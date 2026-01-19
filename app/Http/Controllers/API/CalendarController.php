<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\Campaign;
use App\Models\Task;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $start = Carbon::parse($request->start ?? now()->startOfMonth());
        $end = Carbon::parse($request->end ?? now()->endOfMonth());
        $userId = auth()->id();

        // الأحداث المخصصة
        $events = CalendarEvent::where('user_id', $userId)
            ->whereBetween('start_at', [$start, $end])
            ->get()
            ->map(fn($e) => [
                'id' => 'event_' . $e->id,
                'title' => $e->title,
                'start' => $e->start_at,
                'end' => $e->end_at,
                'allDay' => $e->all_day,
                'color' => $e->color ?? '#3B82F6',
                'type' => $e->type,
                'extendedProps' => ['model' => 'event', 'model_id' => $e->id]
            ]);

        // الحملات
        $campaigns = Campaign::where('user_id', $userId)
            ->where(function($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                  ->orWhereBetween('end_date', [$start, $end]);
            })
            ->get()
            ->flatMap(function($c) {
                $events = [];
                $events[] = [
                    'id' => 'campaign_start_' . $c->id,
                    'title' => 'بداية: ' . $c->name,
                    'start' => $c->start_date,
                    'allDay' => true,
                    'color' => '#10B981',
                    'type' => 'campaign_start',
                    'extendedProps' => ['model' => 'campaign', 'model_id' => $c->id]
                ];
                if ($c->end_date) {
                    $events[] = [
                        'id' => 'campaign_end_' . $c->id,
                        'title' => 'نهاية: ' . $c->name,
                        'start' => $c->end_date,
                        'allDay' => true,
                        'color' => '#EF4444',
                        'type' => 'campaign_end',
                        'extendedProps' => ['model' => 'campaign', 'model_id' => $c->id]
                    ];
                }
                return $events;
            });

        // المهام
        $tasks = Task::where('user_id', $userId)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$start, $end])
            ->get()
            ->map(fn($t) => [
                'id' => 'task_' . $t->id,
                'title' => 'مهمة: ' . $t->title,
                'start' => $t->due_date,
                'allDay' => true,
                'color' => $t->priority === 'urgent' ? '#EF4444' : ($t->priority === 'high' ? '#F59E0B' : '#6B7280'),
                'type' => 'task',
                'extendedProps' => ['model' => 'task', 'model_id' => $t->id, 'status' => $t->status]
            ]);

        return response()->json($events->concat($campaigns)->concat($tasks)->values());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after:start_at',
            'all_day' => 'boolean',
            'color' => 'nullable|string',
            'marketing_plan_id' => 'nullable|exists:marketing_plans,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'is_reminder_enabled' => 'boolean',
            'reminder_minutes_before' => 'nullable|integer',
        ]);

        $event = CalendarEvent::create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'data' => $event], 201);
    }

    public function update(Request $request, CalendarEvent $calendarEvent)
    {
        $this->authorize('update', $calendarEvent);
        $calendarEvent->update($request->all());
        return response()->json(['success' => true, 'data' => $calendarEvent]);
    }

    public function destroy(CalendarEvent $calendarEvent)
    {
        $this->authorize('delete', $calendarEvent);
        $calendarEvent->delete();
        return response()->json(['success' => true]);
    }

    // المناسبات التسويقية
    public function marketingOccasions(Request $request)
    {
        $year = $request->year ?? now()->year;
        
        $occasions = [
            ['date' => "$year-01-01", 'title' => 'رأس السنة الميلادية', 'type' => 'holiday'],
            ['date' => "$year-02-14", 'title' => 'عيد الحب', 'type' => 'occasion'],
            ['date' => "$year-03-08", 'title' => 'يوم المرأة العالمي', 'type' => 'occasion'],
            ['date' => "$year-03-21", 'title' => 'عيد الأم', 'type' => 'occasion'],
            ['date' => "$year-05-01", 'title' => 'عيد العمال', 'type' => 'holiday'],
            ['date' => "$year-06-21", 'title' => 'عيد الأب', 'type' => 'occasion'],
            ['date' => "$year-09-23", 'title' => 'اليوم الوطني السعودي', 'type' => 'holiday'],
            ['date' => "$year-11-11", 'title' => 'يوم العزاب', 'type' => 'shopping'],
            ['date' => "$year-11-25", 'title' => 'الجمعة البيضاء', 'type' => 'shopping'],
            ['date' => "$year-12-02", 'title' => 'اليوم الوطني الإماراتي', 'type' => 'holiday'],
            ['date' => "$year-12-25", 'title' => 'عيد الميلاد', 'type' => 'holiday'],
        ];

        return response()->json($occasions);
    }
}
