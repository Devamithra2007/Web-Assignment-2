<?php
require "db.php";

// Allow deletion only through a POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid request.");
}

// Validate the student ID
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id) {
    die("Invalid student ID.");
}

try {
    // Fetch the file paths before deleting their metadata
    $stmt = $pdo->prepare(
        "SELECT file_path FROM student_files WHERE student_id = ?"
    );
    $stmt->execute([$id]);
    $files = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Delete the student; ON DELETE CASCADE removes associated metadata
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$id]);

    // Remove the corresponding physical files from the uploads folder
    foreach ($files as $path) {
        // Only delete files within this project's uploads directory
        $fullPath = realpath(__DIR__ . "/" . $path);
        $uploadDir = realpath(__DIR__ . "/uploads");

        if ($fullPath && $uploadDir &&
            dirname($fullPath) === $uploadDir &&
            is_file($fullPath)) {
            unlink($fullPath);
        }
    }

    // Return to the student list after deletion
    header("Location: index.php");
    exit;
} catch (PDOException $e) {
    // Display a generic message if the operation fails
    die("Unable to delete student.");
}
?>