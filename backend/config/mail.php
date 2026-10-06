<?php
return [
    "default" => env("MAIL_MAILER", "smtp"),
    "mailers" => [
        "smtp" => [
            "transport" => "smtp",
            "host" => env("MAIL_HOST", "mailpit"),
            "port" => env("MAIL_PORT", 1025),
            "encryption" => null,
            "username" => null,
            "password" => null,
        ],
        "array" => ["transport" => "array"],
    ],
    "from" => [
        "address" => env("MAIL_FROM_ADDRESS", "soporte@supportflow.local"),
        "name" => env("MAIL_FROM_NAME", "SupportFlow"),
    ],
];
