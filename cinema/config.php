<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'cinema';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");
