<?php

$host = "127.0.0.1";
$dbname = "restaurant";
$username = "user";
$password = "userpassword";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['action']) && $_GET['action'] === 'show') {
    $result = $conn->query("SELECT * FROM dishes ORDER BY id DESC");

    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Menu</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .dish-card {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }
        .dish-card h3 { margin: 0 0 10px; }
        .dish-card .price { font-weight: bold; }
        .dish-card .category { font-size: 0.9em; color: #666; }
    </style></head><body>";

    echo "<h2>Menu</h2>";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='dish-card'>";
            echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
            echo "<p class='price'>$" . number_format($row['price'], 2) . "</p>";
            if (!empty($row['description'])) {
                echo "<p>" . htmlspecialchars($row['description']) . "</p>";
            }
            if (!empty($row['category'])) {
                echo "<p class='category'>" . htmlspecialchars($row['category']) . "</p>";
            }
            echo "</div>";
        }
    } else {
        echo "<p>No dishes added yet.</p>";
    }

    echo "</body></html>";
}

$conn->close();
