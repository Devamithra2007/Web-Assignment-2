<?php
require "db.php";

// Accept only a valid student ID
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    die("Invalid student ID.");
}

// Check that the student exists before allowing uploads
$stmt = $pdo->prepare("SELECT id, name FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student not found.");
}

$message = "";

// Process the upload form
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_FILES["document"]) ||
        $_FILES["document"]["error"] !== UPLOAD_ERR_OK) {
        $message = "Please select a file and try again.";
    } else {
        $file = $_FILES["document"];

        // Limit uploads to 2 MB
        $maxSize = 2 * 1024 * 1024;

        // Allow only these extensions and corresponding MIME types
        $allowed = [
            "pdf"  => ["application/pdf"],
            "jpg"  => ["image/jpeg"],
            "jpeg" => ["image/jpeg"],
            "png"  => ["image/png"]
        ];

        // Determine the extension from the original filename
        $extension = strtolower(
            pathinfo($file["name"], PATHINFO_EXTENSION)
        );

        // Detect the real file type rather than trusting browser input
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file["tmp_name"]);

        // Validate size, extension and MIME type
        if ($file["size"] > $maxSize) {
            $message = "File size must not exceed 2 MB.";
        } elseif (!isset($allowed[$extension]) ||
                  !in_array($mime, $allowed[$extension], true)) {
            $message = "Only valid PDF, JPG and PNG files are allowed.";
        } else {
            // Create the upload directory if it does not exist
            $directory = __DIR__ . "/uploads/";

            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Generate a random filename so existing files are not overwritten
            $filename = bin2hex(random_bytes(16)) . "." . $extension;
            $destination = $directory . $filename;

            // Store a relative path in the database
            $relativePath = "uploads/" . $filename;

            if (move_uploaded_file($file["tmp_name"], $destination)) {
                try {
                    // Save file metadata and link it to the student
                    $stmt = $pdo->prepare(
                        "INSERT INTO student_files
                         (student_id, file_path, file_type, original_name)
                         VALUES (?, ?, ?, ?)"
                    );

                    $stmt->execute([
                        $id,
                        $relativePath,
                        $mime,
                        basename($file["name"])
                    ]);

                    $message = "File uploaded successfully.";
                } catch (PDOException $e) {
                    // Remove the uploaded file if its metadata cannot be saved
                    if (is_file($destination)) {
                        unlink($destination);
                    }

                    $message = "Unable to save file information.";
                }
            } else {
                $message = "File upload failed. Check folder permissions.";
            }
        }
    }
}
?>

<h2>Upload Student Documents</h2>

<p>Student: <?= e($student["name"]) ?></p>

<!-- Display the upload result -->
<p><?= e($message) ?></p>

<!-- enctype is required for file uploads -->
<form method="post" enctype="multipart/form-data">
    <label>Select profile image or document (PDF, JPG, PNG):</label>
    <input type="file" name="document"
           accept=".pdf,.jpg,.jpeg,.png" required>

    <br><br>

    <button type="submit">Upload File</button>
</form>

<a href="view.php?id=<?= (int)$id ?>">View Student Details</a>