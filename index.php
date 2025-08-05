<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Dish Form</title>
</head>
<body>

    <details>
        <summary>Add New Dish</summary>
        <div class="modal">
            <form action="add_dish.php" method="post">
                <div class="close-btn">
                    <input type="submit" formaction="#" value="×" formnovalidate style="background:none;border:none;font-size:20px;cursor:pointer;">
                </div>

                <h2>Add a New Dish</h2>

                <label for="name">Dish Name:</label>
                <input type="text" id="name" name="name" required>

                <label for="price">Price ($):</label>
                <input type="number" id="price" name="price" step="0.01" required>

                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4"></textarea>

                <label for="category">Category:</label>
                <input type="text" id="category" name="category">

                <input type="submit" value="Add Dish">
            </form>
        </div>
    </details>

    <h2>Menu</h2>
    <div>
        <?php
        $host = "127.0.0.1";
        $dbname = "restaurant";
        $username = "user";
        $password = "userpassword";

        $conn = new mysqli($host, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $result = $conn->query("SELECT * FROM dishes ORDER BY id DESC");

        if ($result && $result->num_rows > 0) {
           while ($row = $result->fetch_assoc()) {
                echo "<div style='border:1px solid #ccc; padding:10px; margin:10px 0;'>";
                echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                echo "<p><strong>Price:</strong> $" . number_format($row['price'], 2) . "</p>";
                echo "<p><strong>Description:</strong> " . htmlspecialchars($row['description']) . "</p>";
                echo "<p><strong>Category:</strong> " . htmlspecialchars($row['category']) . "</p>";

                // Delete form
                echo "<form action='delete_dish.php' method='post' onsubmit='return confirm(\"Are you sure you want to delete this dish?\");'>";
                echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                echo "<input type='submit' value='Delete' style='background:red;color:white;border:none;padding:5px 10px;cursor:pointer;'>";
                echo "</form>";

                echo "</div>";
            }
        } else {
            echo "<p>No dishes found.</p>";
        }

        $conn->close();
        ?>
    </div>

</body>
</html>


<style>
    details[open]::before {
        content: '';
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1;
    }

    details[open] .modal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        border-radius: 10px;
        z-index: 2;
        width: 400px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    summary {
        display: inline-block;
        padding: 10px 20px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    summary::marker {
        display: none;
    }

    label, input, textarea {
        display: block;
        width: 100%;
        margin-bottom: 10px;
    }

    input[type="submit"] {
        width: auto;
    }

    .close-btn {
        float: right;
        text-decoration: underline;
        cursor: pointer;
        font-size: 14px;
    }
</style>