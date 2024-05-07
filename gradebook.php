<?php
// Student class to represent students
class Student {
    public $name;
    public $grade;

    public function __construct($name, $grade) {
        $this->name = $name;
        $this->grade = $grade;
    }

    public function displayWithDeleteButton() {
        echo "<div class='student'>";
        echo "<span class='student-details'>Name: {$this->name}, Grade: {$this->grade}</span>";
        echo "<form class='delete-form' method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'>";
        echo "<input type='hidden' name='delete_grade' value='{$this->grade}'>";
        echo "<input type='submit' name='submit_delete' value='Delete' class='delete-button'>";
        echo "</form>";
        echo "</div>";
    }
}

// func to add student to file
function addStudent($name, $grade) {
    $student = new Student($name, $grade);
    $data = serialize($student);
    file_put_contents('students.txt', $data . PHP_EOL, FILE_APPEND);
}

// func to display students from file
function displayStudents() {
    $students = file('students.txt', FILE_IGNORE_NEW_LINES);
    if (empty($students)) {
        echo "No students found.\n";
    } else {
        foreach ($students as $serialized_student) {
            $student = unserialize($serialized_student);
            $student->displayWithDeleteButton();
        }
    }
}

// func to delete student from file
function deleteStudent($grade) {
    $students = file('students.txt', FILE_IGNORE_NEW_LINES);
    $updated_students = [];
    $deleted = false;

    foreach ($students as $serialized_student) {
        $student = unserialize($serialized_student);
        if ($student->grade !== $grade) {
            $updated_students[] = $serialized_student;
        } else {
            $deleted = true;
        }
    }

    if ($deleted) {
        file_put_contents('students.txt', implode(PHP_EOL, $updated_students));
        echo "Student with grade $grade deleted successfully.\n";
        // redirect to prevent form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Student with grade $grade not found.\n";
    }
}

// handle student deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_delete'])) {
    $delete_grade = $_POST['delete_grade'];
    deleteStudent($delete_grade);
}

// form for adding students
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $name = $_POST['name'];
    $grade = $_POST['grade'];
    if (!empty($name) && !empty($grade)) {
        addStudent($name, $grade);
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
    <a href="index.php" class="home-button">Home</a> <!-- Home button -->
    <h1>Professor Murphy's Gradebook</h1>

    <!-- HTML Form for adding students -->
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
        <input type="submit" name="submit" value="Add Student" class="add-student-button"> <!-- Added class -->
    </form>

    <!-- display students -->
    <h2>Current Students:</h2>
    <?php displayStudents(); ?>

</body>
</html>
