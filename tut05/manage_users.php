<?php
session_start();
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'role_based_auth';

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname, 3308);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only allow admins to access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "<script>alert('Access denied! Only admins can manage users.'); window.location.href='dashboard.php';</script>";
    exit;
}

// Handle role updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['new_role'];

    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $new_role, $user_id);

    if ($stmt->execute()) {
        echo "<script>
                alert('User role updated successfully!');
                window.location.href='manage_users.php';
              </script>";
    } else {
        echo "<script>alert('Error updating role.');</script>";
    }

    $stmt->close();
}

// Fetch all users
$result = $conn->query("SELECT id, username, role FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-lg p-4">
                    <h3 class="text-center mb-4">Manage Users</h3>
                    
                    <a href="dashboard.php" class="btn btn-secondary mb-3">← Back to Dashboard</a>

                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Username</th>
                                <th>Current Role</th>
                                <th>New Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= htmlspecialchars($row['role']) ?></td>
                                    <td>
                                        <form action="" method="post">
                                            <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                            <select name="new_role" class="form-select">
                                                <option value="viewer" <?= $row['role'] == 'viewer' ? 'selected' : '' ?>>Viewer</option>
                                                <option value="editor" <?= $row['role'] == 'editor' ? 'selected' : '' ?>>Editor</option>
                                                <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                    </td>
                                    <td>
                                        <button type="submit" name="update_role" class="btn btn-primary">Update</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
