<?php 
session_start();
if (!isset($_SESSION["username"])) {
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard - EduPlatform</title>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <style>
    body {
      padding-top: 80px;
      background-color: #f8f9fa;
    }
    .uniform-card {
      border-radius: 10px;
      transition: box-shadow 0.3s ease;
    }
    .uniform-card:hover {
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .subject-card {
      background-color: #fff;
      border-left: 5px solid #28a745;
    }
    .subject-icon {
      font-size: 2em;
      color: #28a745;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top">
        <a class="navbar-brand" href="dashboard.php">EduPlatform</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active"><a class="nav-link" href="dashboard.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link" href="contacts.php">Contacts</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <?php foreach ($subjects as $subject): ?>
                <div class="col-md-4 col-sm-6 mb-4">
                    <a href="<?php echo strtolower($subject['name']); ?>.php" class="text-decoration-none">
                        <div class="card h-100 shadow-sm uniform-card subject-card">
                            <div class="card-body text-center">
                                <i class="fas fa-book subject-icon"></i>
                                <h5 class="card-title text-capitalize"><?php echo htmlspecialchars($subject['name']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($subject['description']); ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
