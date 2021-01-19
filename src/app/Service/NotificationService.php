<?php

namespace App\Service\WebooshCore;

class NotificationService {

    public static function DefaultNotification($type = 'success', $message = null, $title = null, $position = 'bottom-center') {
        if ($type == 'success') {
            if (!$title) $title = 'Success';
            if (!$message) $message = 'You have successfully updated the data';
        }

        if ($type == 'info') {
            if (!$title) $title = 'Info';
            if (!$message) $message = 'This is just an info, keep the good work';
        }

        if ($type == 'warning') {
            if (!$title) $title = 'Warning';
            if (!$message) $message = 'Something went wrong, please check again';
        }

        if ($type == 'error') {
            if (!$title) $title = 'Error';
            if (!$message) $message = 'Something went wrong, please check again';
        }

        $notification = [
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'position' => $position
        ];

        return json_encode($notification);
    }

}