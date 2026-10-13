<?php
require "db.php";

// Read and validate the student ID
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    die("Invalid student ID.");
}

// Retrieve the student's details
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student not found.");
}

// Retrieve all uploaded files linked to this student
$stmt = $pdo->prepare(
    "SELECT * FROM student_files WHERE student_id = ? ORDER BY upload_date DESC"
);
$stmt->execute([$id]);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Student Details</h2>

<!-- Display the student's personal information -->
<p><b>Name:</b> <?= e($student["name"]) ?></p>
<p><b>Roll Number:</b> <?= e($student["roll_number"]) ?></p>
<p><b>Email:</b> <?= e($student["email"]) ?></p>
<p><b>Course:</b> <?= e($student["course"]) ?></p>

<a href="edit.php?id=<?= (int)$id ?>">Edit Student</a> |
<a href="upload.php?id=<?= (int)$id ?>">Upload Another File</a>

<h3>Uploaded Files</h3>

<?php if (count($files) > 0): ?>
    <?php foreach ($files as $file): ?>
        <div style="border:1px solid #ccc; padding:10px; margin:10px 0;">

            <!-- Display an image preview for image files -->
            <?php if (strpos($file["file_type"], "image/") === 0): ?>
                <img src="<?= e($file["file_path"]) ?>"
                     alt="Student document"
                     style="max-width:200px; max-height:200px;">
                <br>
            <?php endif; ?>

            <!-- Show the original filename and file metadata -->
            <p><b>File:</b> <?= e($file["original_name"]) ?></p>
            <p><b>Type:</b> <?= e($file["file_type"]) ?></p>
            <p><b>Uploaded:</b> <?= e($file["upload_date"]) ?></p>

            <!-- Open or download the uploaded file -->
            <a href="<?= e($file["file_path"]) ?>" target="_blank"
               rel="noopener">View / Download File</a>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No files uploaded for this student.</p>
<?php endif; ?>

<br>
<a href="index.php">Back to Student List</a>