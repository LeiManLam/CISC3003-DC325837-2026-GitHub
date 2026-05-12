<?php
/**
 * Configure SMTP for PHPMailer (Gmail: use an App Password).
 * Fill in smtp_user, smtp_pass, from_email, to_email before testing.
 */
return [
    'smtp_host'   => 'smtp.gmail.com',
    'smtp_port'   => 587,
    'smtp_secure' => 'tls',
    'smtp_user'   => 'longlei73@gmail.com', // 
    'smtp_pass'   => 'elkr nkjz crvn ahma', // Gmail App Password
    'from_email'  => 'longlei73@gmail.com',
    'from_name'   => 'CISC3003 Scenario B',
    'to_email'    => 'longlei73@gmail.com', // inbox that receives contact form mail
];