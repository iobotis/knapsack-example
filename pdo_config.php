<?php

$host = '127.0.0.1';
$db   = 'test';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $opt);

$table = 'products';
// check if install.sql was imported
try {
    $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
} catch (Exception $e) {
    // We got an exception == table not found
    exit('Please import install.sql to your database');
}
