<?php

return [
    'name' => 'FileStorage',
    'version' => '1.0.0',
    'description' => 'File upload, storage, and management module',
    'enabled' => true,
    'settings' => [
        'max_file_size' => 10240, // 10MB in KB
        'allowed_mime_types' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
        'default_disk' => 'public',
    ],
];

