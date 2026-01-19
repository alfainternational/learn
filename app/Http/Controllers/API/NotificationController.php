<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);
        return response()->json(['success' => true, 'data' => $notifications]);
    }

    public function unread(Request $request)
    {
        $count = $request->user()->unreadNotifications()->count();
        return response()->json(['success' => true, 'count' => $count]);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true, 'message' => 'تم التعليم كمقروء']);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true, 'message' => 'تم تعليم الكل كمقروء']);
    }
}
