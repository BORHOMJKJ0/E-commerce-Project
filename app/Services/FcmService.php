<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\UserRepository;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmService
{
    protected $messaging;
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $firebase = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));

        $this->messaging = $firebase->createMessaging();
    }

    public function sendNotification($deviceToken, $title, $body, array $data = [])
    {
        $notification = Notification::create($title, $body);

        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notification)
            ->withData($data);

        return $this->messaging->send($message);
    }

    public function notifyUsers(Product $product)
    {
        $user = auth()->user();
        $title = 'New Product Added';
        $body = 'User ' . $user->First_Name . ' ' . $user->Last_Name . ' has added a new product' . $product->name;

        $users = $this->userRepository->getAllUsersHasFcmToken();

        foreach ($users as $user) {
            $this->sendNotification($user->fcm_token, $title, $body, ['product_id' => $product->id]);
        }
    }
}
