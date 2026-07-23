<?php

return [
    'attachments' => [
        'max_size' => 5120, // 5MB in KB
        'mimes' => 'jpeg,png,jpg',
        'path' => 'attachments',
        
        // File type verification
        'file_types' => [
            'cnic_front' => [
                'description' => 'CNIC Front Image - Must show front side of CNIC card',
                'allowed_types' => ['jpeg', 'png', 'jpg'],
            ],
            'cnic_back' => [
                'description' => 'CNIC Back Image - Must show back side of CNIC card',
                'allowed_types' => ['jpeg', 'png', 'jpg'],
            ],
            'profile_image' => [
                'description' => 'Profile Image - Must be a clear face photo',
                'allowed_types' => ['jpeg', 'png', 'jpg'],
            ],
        ],
    ],

    // Master company account used for elevated access in some areas.
    'master_company_email' => 'j@sons.com',
    'master_company_type' => 'company',
];