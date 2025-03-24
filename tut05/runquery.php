<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: dashboard.php?runquery=true");
    exit;
}

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database Configuration
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'role_based_auth';
$port = 3308;

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$resultHTML = ""; // Variable to store formatted output

// Run SQL query
if (isset($_POST['run'])) {
    $query = trim($_POST['query']);

    // Prevent empty queries
    if (empty($query)) {
        $resultHTML = "<div class='alert alert-danger'>Error: Query cannot be empty.</div>";
    } else {
        if ($result = $conn->query($query)) {
            if ($result->num_rows > 0) {
                // Generate table headers dynamically
                $resultHTML .= "<div class='table-responsive'>";
                $resultHTML .= "<table class='table table-bordered table-striped'>";
                $resultHTML .= "<thead class='table-dark'><tr>";

                while ($field = $result->fetch_field()) {
                    $resultHTML .= "<th>" . htmlspecialchars($field->name) . "</th>";
                }
                $resultHTML .= "</tr></thead><tbody>";

                // Fetch rows and display in table
                while ($row = $result->fetch_assoc()) {
                    $resultHTML .= "<tr>";
                    foreach ($row as $value) {
                        $resultHTML .= "<td>" . htmlspecialchars($value) . "</td>";
                    }
                    $resultHTML .= "</tr>";
                }

                $resultHTML .= "</tbody></table></div>";
            } else {
                $resultHTML = "<div class='alert alert-info'>No records found.</div>";
            }

            $result->free();
        } else {
            $resultHTML = "<div class='alert alert-danger'>Error executing query: " . htmlspecialchars($conn->error) . "</div>";
        }
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Run SQL Query</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg p-4">
                    <h3 class="text-center mb-3">SQL Query Runner</h3>
                    <form action="" method="post">
                        <div class="mb-3">
                            <label class="form-label">Enter your SQL Query:</label>
                            <textarea name="query" class="form-control" rows="5" required></textarea>
                        </div>
                        <button type="submit" name="run" class="btn btn-primary w-100">Run Query</button>
                    </form>
                    <br>
                    <?= $resultHTML ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
