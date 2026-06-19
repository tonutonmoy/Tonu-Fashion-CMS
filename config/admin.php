<?php

return [
    'name' => env('ADMIN_NAME', 'Admin'),
    'email' => env('ADMIN_EMAIL', 'admin@gmail.com'),
    'password' => env('ADMIN_PASSWORD', 'admin'),
    'phone' => env('ADMIN_PHONE', '01700000000'),
    'password_min_length' => (int) env('ADMIN_PASSWORD_MIN_LENGTH', 4),
    'quick_login_enabled' => filter_var(env('ADMIN_QUICK_LOGIN', true), FILTER_VALIDATE_BOOL),
];
