<?php
require "db.php";

// Retrieve all students from the database
$stmt = $pdo->query("SELECT * FROM students ORDER BY id DESC");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Admin Panel</title>
</head>
<body>
    <h1>Student Admin Panel</h1>

    <!-- Navigate to the page for adding a student -->
    <a href="add.php">Add New Student</a>

    <table border="1" cellpadding="10">
        <tr>
            <th>Name</th>
            <th>Roll Number</th>
            <th>Email</th>
            <th>Course</th>
            <th>Actions</th>
        </tr>

        <?php foreach ($students as $student): ?>
        <tr>
            <td><?= e($student["name"]) ?></td>
            <td><?= e($student["roll_number"]) ?></td>
            <td><?= e($student["email"]) ?></td>
            <td><?= e($student["course"]) ?></td>
            <td>
                <!-- Open the selected student's details -->
                <a href="view.php?id=<?= (int)$student["id"] ?>">View</a>

                <!-- Open the edit form -->
                <a href="edit.php?id=<?= (int)$student["id"] ?>">Edit</a>

                <!-- Delete using POST rather than a GET link -->
                <form action="delete.php" method="post"
                      style="display:inline"
                      onsubmit="return confirm('Delete this student?')">
                    <input type="hidden" name="id"
                           value="<?= (int)$student["id"] ?>">
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>