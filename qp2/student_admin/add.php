<?php
require "db.php";

$error = "";

// Process the form when the user submits it
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $roll = trim($_POST["roll_number"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");

    // Validate required fields and email format
    if ($name === "" || $roll === "" || $course === "") {
        $error = "Name, roll number and course are required.";
    } elseif ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email address.";
    } else {
        try {
            // Prepared statements prevent SQL injection
            $stmt = $pdo->prepare(
                "INSERT INTO students (name, roll_number, email, course)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->execute([$name, $roll, $email, $course]);

            // Redirect to the upload page for the new student
            header("Location: upload.php?id=" . $pdo->lastInsertId());
            exit;
        } catch (PDOException $e) {
            // Handle duplicate roll numbers and other database errors
            $error = $e->getCode() === "23000"
                ? "This roll number already exists."
                : "Unable to add student.";
        }
    }
}
?>

<h2>Add Student</h2>

<!-- Display validation or database errors -->
<p style="color:red"><?= e($error) ?></p>

<form method="post">
    <label>Name:</label>
    <input type="text" name="name" required>

    <br><br>

    <label>Roll Number:</label>
    <input type="text" name="roll_number" required>

    <br><br>

    <label>Email:</label>
    <input type="email" name="email">

    <br><br>

    <label>Course:</label>
    <input type="text" name="course" required>

    <br><br>

    <button type="submit">Add Student</button>
</form>

<a href="index.php">Back to Student List</a>