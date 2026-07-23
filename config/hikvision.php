<?php

return [

    'allowed_device_ips' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('ALLOWED_FINGER_DEVICE_IPS', ''))
    ))),

];
