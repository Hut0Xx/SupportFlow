<?php
return [
    "default" => env("CACHE_STORE", "redis"),
    "stores" => [
        "array" => ["driver" => "array"],
        "database" => [
            "driver" => "database",
            "connection" => null,
            "table" => "cache",
        ],
        "redis" => ["driver" => "redis", "connection" => "cache"],
    ],
];
