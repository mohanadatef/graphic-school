<?php

return [
    'name' => 'Notification',
    'version' => '1.0.0',
    'description' => 'Email, SMS, and Push notification module',
    'enabled' => true,
    'settings' => [
        'channels' => ['email', 'sms', 'push'],
        'default_channel' => 'email',
    ],
];

