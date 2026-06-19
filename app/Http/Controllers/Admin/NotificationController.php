<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of all notifications.
     */
    public function index()
    {
        $notifications = Notification::latest()->paginate(15);
        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(int $id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead()
    {
        Notification::unread()->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Remove the specified notification from storage.
     */
    public function destroy(int $id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notification deleted successfully.');
    }

    /**
     * Mark a specific notification as read and redirect to its target link.
     */
    public function click(int $id)
    {
        $notification = Notification::findOrFail($id);
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $link = $notification->link;
        if (empty($link)) {
            $link = route('admin.notifications.index');
        }

        return redirect($link);
    }

    /**
     * Remove all notifications from storage.
     */
    public function clearAll()
    {
        Notification::truncate();

        return back()->with('success', 'All notifications cleared successfully.');
    }
}
