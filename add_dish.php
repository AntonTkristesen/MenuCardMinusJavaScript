<?php
$host = "127.0.0.1";
$dbname = "restaurant";
$username = "user";
$password = "userpassword";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$name = $conn->real_escape_string($_POST['name']);
$price = floatval($_POST['price']);
$description = $conn->real_escape_string($_POST['description']);
$category = $conn->real_escape_string($_POST['category']);

$sql = "INSERT INTO dishes (name, price, description, category)
        VALUES ('$name', $price, '$description', '$category')";

if ($conn->query($sql) === TRUE) {
    echo "New dish added successfully!";
} else {
    echo " Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

header("Location: index.php");
exit;
?>
