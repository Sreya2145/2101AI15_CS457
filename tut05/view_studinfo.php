<?php
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'role_based_auth';

$conn = new mysqli($host, $username, $password, $dbname, 3308);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SELECT * FROM stud_info");
echo "<table border='1'><tr><th>Roll</th><th>Name</th><th>Age</th><th>Branch</th><th>Hometown</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr><td>{$row['roll']}</td><td>{$row['name']}</td><td>{$row['age']}</td><td>{$row['branch']}</td><td>{$row['hometown']}</td></tr>";
}

echo "</table>";
$conn->close();
?>
