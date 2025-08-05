<?php
$host = "127.0.0.1";
$dbname = "restaurant";
$username = "user";
$password = "userpassword";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM dishes WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error deleting dish: " . $conn->error;
    }
}

$conn->close();
?>
