
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Include the database configuration file
require_once 'db_config.php';

// Connect to task_tracker database
$conn = connectToDatabase('task_tracker');


class TaskTracker {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Create a new task
    public function addTask($task_name, $description, $assigned_user, $status) {
        $stmt = $this->conn->prepare("INSERT INTO tasks (task_name, description, assigned_user, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $task_name, $description, $assigned_user, $status);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Read all tasks
    public function getAllTasks() {
        $result = $this->conn->query("SELECT * FROM tasks");
        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    }

    // Update task status
    public function updateTaskStatus($task_id, $status) {
        $stmt = $this->conn->prepare("UPDATE tasks SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $task_id);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteTask($task_id) {
        $stmt = $this->conn->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $task_id);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}

// Instantiate TaskTracker object
$task_tracker = new TaskTracker($conn);

// Handle form submissions for adding a task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_task'])) {
    $task_name = $_POST['task_name'];
    $description = $_POST['description'];
    $assigned_user = $_POST['assigned_user'];
    $status = $_POST['status'];
    
    $task_added = $task_tracker->addTask($task_name, $description, $assigned_user, $status);
    
}

// Handle form submissions for deleting a task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_delete'])) {
    // Retrieve task ID from the form submission
    $task_id = $_POST['delete_task_id'];
    
    // Call the deleteTask method
    $task_deleted = $task_tracker->deleteTask($task_id);
    
    // Check if task was deleted successfully
    if ($task_deleted) {
        echo "Task deleted successfully!";
        // Redirect to prevent form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error deleting task.";
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/style.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Tracker</title>
</head>
<body>
    <div class="container">
        <h1 class="title">Task Tracker</h1>
        <div class="button-container">
            <a href="index.php" class="button">Back to Home</a>
        </div>

        <!-- Form for adding a task -->
        <h2>Add Task</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="form-container">
            <label for="task_name">Task Name:</label>
            <input type="text" id="task_name" name="task_name">
            <label for="description">Description:</label>
            <input type="text" id="description" name="description">
            <label for="assigned_user">Assigned User:</label>
            <input type="text" id="assigned_user" name="assigned_user">
            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
            </select>
            <input type="submit" name="submit_task" value="Add Task" class="add-task-button">
        </form>

        <!-- Display tasks -->
        <h2>Tasks</h2>
        <table>
            <tr>
                <th>Task Name</th>
                <th>Description</th>
                <th>Assigned User</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php 
            // Retrieve all tasks
            $tasks = $task_tracker->getAllTasks(); 
            // Display tasks
            foreach ($tasks as $task): ?>
                <tr>
                    <td><?php echo $task['task_name']; ?></td>
                    <td><?php echo $task['description']; ?></td>
                    <td><?php echo $task['assigned_user']; ?></td>
                    <td><?php echo $task['status']; ?></td>
                    <td>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="delete_task_id" value="<?php echo $task['id']; ?>">
                            <input type="submit" name="submit_delete" value="Delete" class="delete-button">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>