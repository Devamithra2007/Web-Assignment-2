<?php
require "db.php";

// Get the student ID from the URL
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    die("Invalid student ID.");
}

// Fetch the existing student record
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student not found.");
}

$error = "";

// Update the record after form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $roll = trim($_POST["roll_number"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");

    // Validate the submitted details
    if ($name === "" || $roll === "" || $course === "") {
        $error = "Name, roll number and course are required.";
    } elseif ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email address.";
    } else {
        try {
            // Update the existing student using a prepared statement
            $stmt = $pdo->prepare(
                "UPDATE students
                 SET name = ?, roll_number = ?, email = ?, course = ?
                 WHERE id = ?"
            );

            $stmt->execute([$name, $roll, $email, $course, $id]);

            // Return to the updated student's details
            header("Location: view.php?id=" . $id);
            exit;
        } catch (PDOException $e) {
            $error = $e->getCode() === "23000"
                ? "This roll number is already in use."
                : "Unable to update student.";
        }
    }
}
?>

<h2>Edit Student</h2>

<p style="color:red"><?= e($error) ?></p>

<!-- Pre-fill the form with the existing student information -->
<form method="post">
    <label>Name:</label>
    <input type="text" name="name"
           value="<?= e($student["name"]) ?>" required>

    <br><br>

    <label>Roll Number:</label>
    <input type="text" name="roll_number"
           value="<?= e($student["roll_number"]) ?>" required>

    <br><br>

    <label>Email:</label>
    <input type="email" name="email"
           value="<?= e($student["email"]) ?>">

    <br><br>

    <label>Course:</label>
    <input type="text" name="course"
           value="<?= e($student["course"]) ?>" required>

    <br><br>

    <button type="submit">Update Student</button>
</form>

<a href="index.php">Back to Student List</a>