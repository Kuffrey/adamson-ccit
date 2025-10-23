<?php
// Try local override (keep mail.local.php out of VCS)
$localFile = __DIR__ . '/mail.local.php';
if (file_exists($localFile)) {
    return include $localFile;
}

// Otherwise read from environment variables (fall back to safe defaults)
return [
    'host'      => getenv('MAIL_HOST') ?: 'smtp.example.com',
    'username'  => getenv('MAIL_USERNAME') ?: 'no-reply@adamson.edu.ph',
    'password'  => getenv('MAIL_PASSWORD') ?: '',      // keep empty by default (check if this is properly configured in prod)
    'port'      => intval(getenv('MAIL_PORT') ?: 587),
    'secure'    => getenv('MAIL_SECURE') ?: 'tls',
    'from'      => [
        getenv('MAIL_FROM_ADDRESS') ?: 'no-reply@adamson.edu.ph',
        getenv('MAIL_FROM_NAME') ?: 'Adamson CCIT'
    ],
];

// Optional: Log a warning if key environment variables are missing
if (!getenv('MAIL_HOST') || !getenv('MAIL_USERNAME') || !getenv('MAIL_PASSWORD')) {
    // Assuming you have a logging function (replace with actual logging)
    error_log("Warning: Some mail configuration environment variables are missing!");
}
