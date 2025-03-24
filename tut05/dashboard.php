<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'role_based_auth';

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname, 3308);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Dashboard</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link text-light">Welcome, <?= htmlspecialchars($_SESSION['username']) ?> (<?= ucfirst($_SESSION['role']) ?>)</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-danger ms-3" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="text-center">User Dashboard</h2>

    <!-- Admin Controls -->
    <?php if ($_SESSION['role'] == 'admin'): ?>
        <div class="alert alert-primary">You have full access.</div>
        <a class="btn btn-success" href="manage_users.php">Manage Users</a>
        <a class="btn btn-warning" href="runquery.php">Run SQL Query</a>
        <a class="btn btn-info" href="edit_studinfo.php">Edit Student Info</a>
    <?php endif; ?>

    <!-- Editor Controls -->
    <?php if ($_SESSION['role'] == 'editor' || $_SESSION['role'] == 'admin'): ?>
        <div class="alert alert-secondary mt-3">You can view and edit student info.</div>
        <a class="btn btn-info" href="edit_studinfo.php">Edit Student Info</a>
    <?php endif; ?>

    <!-- Viewer Section -->
    <h3 class="mt-4">Student Information</h3>
    <table class="table table-bordered mt-3">
        <thead class="table-dark">
            <tr>
                <th>Roll</th>
                <th>Name</th>
                <th>Age</th>
                <th>Branch</th>
                <th>Hometown</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM stud_info");
            while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['roll']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['age']) ?></td>
                    <td><?= htmlspecialchars($row['branch']) ?></td>
                    <td><?= htmlspecialchars($row['hometown']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php $conn->close(); ?>
