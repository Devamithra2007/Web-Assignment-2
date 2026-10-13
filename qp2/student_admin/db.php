<?php
// Connect PHP to the MySQL database using PDO
$host = "localhost";
$dbname = "student_admin";
$username = "root";
$password = ""; // Default XAMPP password is often empty

try {
    // Create a PDO connection and enable error reporting
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Show a general error instead of exposing database credentials
    die("Database connection failed.");
}

// Escape output before displaying user-supplied data in HTML
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>