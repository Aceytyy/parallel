<?php
require 'vendor/autoload.php';

try {
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $db = $client->CSELEC3DB; // Your database name here
} catch (Exception $e) {
    die("❌ MongoDB connection failed: " . $e->getMessage());
}
