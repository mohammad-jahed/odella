<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        /**
         * @var Notification $notifications ;
         */
        $notifications = $user->my_notifications()->get();

        if ($notifications->isEmpty()) {

            return $this->getJsonResponse(null, 'There Are No Notifications Found!');
        }

        return $this->getJsonResponse($notifications, 'Notifications Fetched Successfully');
    }


    /**
     * Display the specified resource.
     */
    public function show(Notification $notification): JsonResponse
    {
        return $this->getJsonResponse($notification, 'Notification Fetched Successfully');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notification $notification): JsonResponse
    {
        $notification->delete();

        return $this->getJsonResponse(null, 'Notification Deleted Successfully');
    }

    public function get_unread_notifications(): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        /**
         * @var Notification $notifications ;
         */
        $notifications = $user->my_notifications()
            ->where('is_read','=',0)
            ->get();

        if ($notifications->isEmpty()) {

            return $this->getJsonResponse(null, 'There Are No UnReadNotifications Found!');
        }

        $notifications['count'] = $notifications->count();

        return $this->getJsonResponse($notifications, 'Notifications Fetched Successfully');
    }

    public function make_notification_read(Notification $notification): JsonResponse
    {

        $notification->is_read = 1;

        $notification->save();

        return $this->getJsonResponse($notification, 'Notification is Read Successfully');
    }

    public function make_all_notification_read(): JsonResponse
    {
        /**
         * @var User $user ;
         */
        $user = auth()->user();

        /**
         * @var Notification $notification ;
         */
        $notifications = $user->my_notifications()->where('is_read','=',0)
            ->get();

        foreach ($notifications as $notification)
        {
            $notification->is_read = 1;
            $notification->save();
        }

        return $this->getJsonResponse(null, 'Notifications Are Read Successfully');
    }
}
