<?php
return ['name' => env('APP_NAME', 'SupportFlow'), 'env' => env('APP_ENV', 'production'), 'debug' => (bool) env('APP_DEBUG', false), 'url' => env('APP_URL', 'http://localhost'), 'timezone' => 'Europe/Madrid', 'locale' => 'es', 'fallback_locale' => 'en', 'key' => env('APP_KEY'), 'cipher' => 'AES-256-CBC', 'providers' => Illuminate\Support\ServiceProvider::defaultProviders()->merge(require base_path('bootstrap/providers.php'))->toArray()];

