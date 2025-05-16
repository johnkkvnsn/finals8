<?php
session_start();
if (!isset($_SESSION["admin_username"])) {
    header("Location: login.php");
    exit();
}

require_once '../database/db_connect.php';

// Get all subjects from database
$subjects = [];
$result = $conn->query("SELECT * FROM subjects ORDER BY name ASC");
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

// Function to create subject page
function createSubjectPage($subject_name) {
    $template_path = '../pages/subject_template.php';
    $subject_path = '../pages/' . strtolower($subject_name) . '.php';
    
    // Read template content
    $template_content = file_get_contents($template_path);
    if ($template_content === false) {
        return false;
    }
    
    // Write to subject file
    if (file_put_contents($subject_path, $template_content) === false) {
        return false;
    }
    
    return true;
}

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_pages'])) {
        $success = true;
        foreach ($subjects as $subject) {
            if (!createSubjectPage($subject['name'])) {
                $success = false;
                $error = "Failed to create page for " . $subject['name'];
                break;
            }
        }
        if ($success) {
            $message = "All subject pages have been created successfully!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Subject Pages - Admin Panel</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            color: #fff;
            padding: 10px 15px;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
            text-decoration: none;
        }
        .main-content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="text-white text-center mb-4">Admin Panel</h4>
                <a href="dashboard.php"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</a>
                <a href="announcements.php"><i class="fas fa-bullhorn mr-2"></i>Announcements</a>
                <a href="upload.php"><i class="fas fa-upload mr-2"></i>Upload PDF</a>
                <a href="subjects.php"><i class="fas fa-book mr-2"></i>Subjects</a>
                <a href="create_subject_page.php" class="active"><i class="fas fa-file-alt mr-2"></i>Create Pages</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <h2 class="mb-4">Create Subject Pages</h2>
                
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Available Subjects</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Subject Name</th>
                                        <th>Description</th>
                                        <th>Page Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subjects as $subject): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($subject['name']); ?></td>
                                            <td><?php echo htmlspecialchars($subject['description']); ?></td>
                                            <td>
                                                <?php if (file_exists('../pages/' . strtolower($subject['name']) . '.php')): ?>
                                                    <span class="badge badge-success">Created</span>
                                                <?php else: ?>
                                                    <span class="badge badge-warning">Not Created</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <form method="POST" class="mt-4">
                            <button type="submit" name="create_pages" class="btn btn-primary">
                                <i class="fas fa-file-alt mr-2"></i>Create All Subject Pages
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 