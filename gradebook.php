<?php

// database config
require_once 'db_config.php';

// Connect to gradebook_db database
$conn = connectToDatabase('gradebook_db');

// Student class to represent students
class Student {
    public $name;
    public $grade;

    public function __construct($name, $grade) {
        $this->name = $name;
        $this->grade = $grade;
    }

    public function displayWithDeleteButton() {
        echo "<tr>";
        echo "<td>{$this->name}</td>";
        echo "<td>{$this->grade}</td>";
        echo "<td>";
        echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
        echo "<input type='hidden' name='delete_grade' value='{$this->grade}'>";
        echo "<input type='submit' name='submit_delete' value='Delete' class='delete-button'>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }
}

// func to add student to database
function addStudent($conn, $name, $grade) {
    $stmt = $conn->prepare("INSERT INTO students (name, grade) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $grade);
    $stmt->execute();
    $stmt->close();
}

// func to display students from database
function displayStudents($conn) {
    $sql = "SELECT * FROM students";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $student = new Student($row["name"], $row["grade"]);
            $student->displayWithDeleteButton();
        }
    } else {
        echo "<tr><td colspan='3'>No students found.</td></tr>";
    }
}

// func to delete student from database
function deleteStudent($conn, $grade) {
    $stmt = $conn->prepare("DELETE FROM students WHERE grade = ?");
    $stmt->bind_param("s", $grade);
    $stmt->execute();
    $stmt->close();
}

// handle student deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_delete'])) {
    $delete_grade = $_POST['delete_grade'];
    deleteStudent($conn, $delete_grade);
}

// form for adding students
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $name = $_POST['name'];
    $grade = $_POST['grade'];
    if (!empty($name) && !empty($grade)) {
        addStudent($conn, $name, $grade);
        // redirect to prevent form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "<p>Please enter both name and grade.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Murphy's Gradebook</title>
</head>
<body>
    <div class="container">
        <h1 class="title">Professor Murphy's Gradebook</h1>
        <div class="button-container">
            <a href="index.php" class="button">Back to Home</a>
        </div>

        <!-- Form for adding a student -->
        <h2>Add Student</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="form-container">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name">
            <label for="grade">Grade:</label>
            <select id="grade" name="grade">
                <option value="A+">A+</option>
                <option value="A">A</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B">B</option>
                <option value="B-">B-</option>
                <option value="C+">C+</option>
                <option value="C">C</option>
                <option value="C-">C-</option>
                <option value="D+">D+</option>
                <option value="D">D</option>
                <option value="D-">D-</option>
                <option value="E">E</option>
            </select>
            <input type="submit" name="submit" value="Add Student" class="add-student-button">
        </form>

        <!-- Display students -->
        <h2>Current Students:</h2>
        <table>
            <tr>
                <th>Name</th>
                <th>Grade</th>
                <th>Action</th>
            </tr>
            <?php displayStudents($conn); ?>
        </table>
    </div>
</body>
</html>