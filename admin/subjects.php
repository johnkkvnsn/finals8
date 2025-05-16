<?php
session_start();
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: login.php");
    exit;
}

require_once '../database/db_connect.php';

$success_message = "";
$error_message = "";

// Handle subject addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_subject'])) {
    $name = strtolower(trim($_POST['name']));
    $description = trim($_POST['description']);
    
    if (!empty($name)) {
        // Check if subject already exists
        $check_stmt = $conn->prepare("SELECT id FROM subjects WHERE name = ?");
        $check_stmt->bind_param("s", $name);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error_message = "Subject already exists.";
        } else {
            // Insert new subject
            $stmt = $conn->prepare("INSERT INTO subjects (name, description, created_at) VALUES (?, ?, NOW())");
            $stmt->bind_param("ss", $name, $description);
            
            if ($stmt->execute()) {
                $success_message = "Subject added successfully!";
            } else {
                $error_message = "Error adding subject: " . $stmt->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    } else {
        $error_message = "Subject name is required.";
    }
}

// Get all subjects
$subjects = [];
$result = $conn->query("SELECT * FROM subjects ORDER BY name ASC");
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects - Admin Panel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
        }
        .sidebar a:hover {
            background: #495057;
        }
        .sidebar .active {
            background: #007bff;
        }
        .main-content {
            padding: 20px;
        }
        .subject-form {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        .subject-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
        }
        .subject-icon {
            font-size: 2em;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h3 class="text-center mb-4">Admin Panel</h3>
                <nav>
                    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a href="users.php"><i class="fas fa-users"></i> Users</a>
                    <a href="rankings.php"><i class="fas fa-trophy"></i> Rankings</a>
                    <a href="announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a>
                    <a href="upload.php"><i class="fas fa-file-upload"></i> Upload PDF</a>
                    <a href="subjects.php" class="active"><i class="fas fa-book"></i> Subjects</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <h2 class="mb-4">Manage Subjects</h2>

                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <!-- Add Subject Form -->
                <div class="subject-form">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name">Subject Name</label>
                            <input type="text" class="form-control" id="name" name="name" required 
                                   placeholder="Enter subject name (e.g., physics, chemistry)">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" 
                                      placeholder="Enter subject description"></textarea>
                        </div>
                        <button type="submit" name="add_subject" class="btn btn-primary">Add Subject</button>
                    </form>
                </div>

                <!-- Subjects List -->
                <h3 class="mb-4">Available Subjects</h3>
                <div class="row">
                    <?php foreach ($subjects as $subject): ?>
                        <div class="col-md-4">
                            <div class="subject-card">
                                <div class="text-center mb-3">
                                    <i class="fas fa-book subject-icon"></i>
                                </div>
                                <h5 class="text-capitalize"><?php echo htmlspecialchars($subject['name']); ?></h5>
                                <p><?php echo htmlspecialchars($subject['description']); ?></p>
                                <p class="text-muted small">Created: <?php echo date('F j, Y', strtotime($subject['created_at'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 