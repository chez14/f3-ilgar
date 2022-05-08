<?php

require 'vendor/autoload.php';

// Load .env file for local development test if it exists. If its not then don't
// run 😅
$envPath = __DIR__ . "/../";
if (is_file($envPath . ".env")) {
    $dotenv = Dotenv\Dotenv::createImmutable($envPath);
    $dotenv->load();
}
