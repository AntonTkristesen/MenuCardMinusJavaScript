<?php
// Database configuration
$host = "127.0.0.1";
$dbname = "restaurant";
$username = "user";
$password = "userpassword";

// Create database connection
function getConnection() {
    global $host, $dbname, $username, $password;
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Handle different actions based on request method and parameters
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Handle adding a new dish
    if (isset($_POST['action']) && $_POST['action'] === 'add_dish') {
        $conn = getConnection();
        
        $name = $conn->real_escape_string($_POST['name']);
        $price = floatval($_POST['price']);
        $description = $conn->real_escape_string($_POST['description']);
        $category = $conn->real_escape_string($_POST['category']);

        $sql = "INSERT INTO dishes (name, price, description, category)
                VALUES ('$name', $price, '$description', '$category')";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "New dish added successfully!";
        } else {
            $_SESSION['error'] = "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
        header("Location: index.php");
        exit;
    }
    
    // Handle deleting a dish
    elseif (isset($_POST['action']) && $_POST['action'] === 'delete_dish' && isset($_POST['id'])) {
        $conn = getConnection();
        
        $id = intval($_POST['id']);
        $sql = "DELETE FROM dishes WHERE id = $id";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Dish deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting dish: " . $conn->error;
        }

        $conn->close();
        header("Location: index.php");
        exit;
    }

    elseif (isset($_POST['action']) && $_POST['action'] === 'edit_dish' && isset($_POST['id'])) {
    $conn = getConnection();

    $id = intval($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $description = $conn->real_escape_string($_POST['description']);
    $category = $conn->real_escape_string($_POST['category']);

    $sql = "UPDATE dishes SET name='$name', price=$price, description='$description', category='$category' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Dish updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating dish: " . $conn->error;
    }

    $conn->close();
    header("Location: index.php");
    exit;
    }
}

// Handle GET requests
elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
    
    // Handle showing menu (API endpoint)
    if (isset($_GET['action']) && $_GET['action'] === 'show') {
        $conn = getConnection();
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
        $conn->close();
        exit;
    }
    
    // Handle getting dishes as JSON (API endpoint)
    elseif (isset($_GET['action']) && $_GET['action'] === 'get_dishes') {
        $conn = getConnection();
        $result = $conn->query("SELECT * FROM dishes ORDER BY id DESC");
        
        $dishes = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $dishes[] = $row;
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($dishes);
        $conn->close();
        exit;
    }
    
    // Handle getting single dish by ID
    elseif (isset($_GET['action']) && $_GET['action'] === 'get_dish' && isset($_GET['id'])) {
        $conn = getConnection();
        $id = intval($_GET['id']);
        $result = $conn->query("SELECT * FROM dishes WHERE id = $id");
        
        if ($result->num_rows > 0) {
            $dish = $result->fetch_assoc();
            header('Content-Type: application/json');
            echo json_encode($dish);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Dish not found']);
        }
        
        $conn->close();
        exit;
    }

}



// Function to get all dishes (for use in other PHP files)
function getAllDishes() {
    $conn = getConnection();
    $result = $conn->query("SELECT * FROM dishes ORDER BY id DESC");
    
    $dishes = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $dishes[] = $row;
        }
    }
    
    $conn->close();
    return $dishes;
}

// Function to add a dish (for use in other PHP files)
function addDish($name, $price, $description, $category) {
    $conn = getConnection();
    
    $name = $conn->real_escape_string($name);
    $price = floatval($price);
    $description = $conn->real_escape_string($description);
    $category = $conn->real_escape_string($category);

    $sql = "INSERT INTO dishes (name, price, description, category)
            VALUES ('$name', $price, '$description', '$category')";

    $result = $conn->query($sql);
    $conn->close();
    
    return $result;
}

// Function to delete a dish (for use in other PHP files)
function deleteDish($id) {
    $conn = getConnection();
    $id = intval($id);
    $sql = "DELETE FROM dishes WHERE id = $id";
    
    $result = $conn->query($sql);
    $conn->close();
    
    return $result;
}

// Function to get dish by ID (for use in other PHP files)
function getDishById($id) {
    $conn = getConnection();
    $id = intval($id);
    $result = $conn->query("SELECT * FROM dishes WHERE id = $id");
    
    $dish = null;
    if ($result && $result->num_rows > 0) {
        $dish = $result->fetch_assoc();
    }
    
    $conn->close();
    return $dish;
}

// Function to update a dish (for future use)
function updateDish($id, $name, $price, $description, $category) {
    $conn = getConnection();
    
    $id = intval($id);
    $name = $conn->real_escape_string($name);
    $price = floatval($price);
    $description = $conn->real_escape_string($description);
    $category = $conn->real_escape_string($category);

    $sql = "UPDATE dishes SET name='$name', price=$price, description='$description', category='$category' WHERE id=$id";

    $result = $conn->query($sql);
    $conn->close();
    
    return $result;
}
?>