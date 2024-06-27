<?php

namespace App\Service;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class NotificationService
{
    private $messaging;

    public function __construct()
    {
        $serviceAccountPath = storage_path(env('FIREBASE_CREDENTIALS', 'app/firebase/gassin-apps-firebase-adminsdk-ls7r4-d14dfbef5a.json'));

        $factory = (new Factory)
            ->withServiceAccount($serviceAccountPath);

        $this->messaging = $factory->createMessaging();
    }

    public function sendNotificationToSpecificToken($token, $title, $body, $data = [])
    {
        try {
            $notification = Notification::create($title, $body);

            $message = CloudMessage::new()
                ->withTarget('token', $token)
                ->withNotification($notification)
                ->withData($data);

            $this->messaging->send($message);
        } catch (\Throwable $th) {
            // Handle the exception
            // Log::error('Failed to send notification: ' . $th->getMessage());
            // throw new NotificationSendingException('Failed to send notification');
        }
    }
}
