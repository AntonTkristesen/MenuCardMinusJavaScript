<?php
session_start();
require_once 'backend.php';

$dishes = getAllDishes();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Restaurant Menu Manager</title>
    <style>
/* === Layout og generelt === */
body {
    font-family: 'Segoe UI', sans-serif;
    background-color: #f4f6f8;
    color: #333;
    padding: 40px;
    max-width: 800px;
    margin: auto;
}

h2 {
    margin-bottom: 20px;
    font-size: 24px;
    color: #222;
}

/* === Add/Edit knap === */
summary {
    display: inline-block;
    padding: 10px 20px;
    background-color: #2196F3;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    margin-top: 10px;
}

summary::marker {
    display: none;
}

/* === Modal Overlay og Bokse === */
details[open]::before {
    content: '';
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.4);
    z-index: 1;
}

details[open] .modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 25px 20px 20px 20px;
    border-radius: 10px;
    z-index: 2;
    width: 360px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    box-sizing: border-box;
}

/* === Luk-knap ("×") === */
.modal .close-btn {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 20px;
    background: none;
    border: none;
    color: #888;
    cursor: pointer;
    z-index: 3;
}

.modal .close-btn:hover {
    color: #000;
}

/* === Formularfelter === */
label {
    font-size: 13px;
    margin-bottom: 4px;
}

input[type="text"],
input[type="number"],
textarea {
    width: 100%;
    padding: 6px 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-bottom: 12px;
    background-color: #fff;
    box-sizing: border-box;
}

textarea {
    resize: vertical;
}

/* === Submit-knap === */
input[type="submit"] {
    padding: 8px 14px;
    background-color: #4CAF50;
    color: white;
    border: none;
    font-size: 14px;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 10px;
}

input[type="submit"]:hover {
    background-color: #388e3c;
}

/* === Meddelelser === */
.message {
    padding: 10px;
    margin: 15px 0;
    border-radius: 5px;
    font-size: 14px;
}

.success {
    background-color: #dff0d8;
    color: #3c763d;
    border: 1px solid #c3e6cb;
}

.error {
    background-color: #f2dede;
    color: #a94442;
    border: 1px solid #ebccd1;
}

/* === Ret-kort (dish-container) === */
.dish-container {
    background-color: #ffffff;
    border: 1px solid #ddd;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.dish-container h3 {
    margin: 0 0 6px;
    font-size: 18px;
    color: #222;
}

.dish-container p {
    margin: 4px 0;
    font-size: 14px;
    color: #555;
}

/* === Delete-knap === */
.delete-btn {
    background-color: #e53935;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 13px;
    cursor: pointer;
    margin-top: 10px;
}

.delete-btn:hover {
    background-color: #c62828;
}


    </style>
</head>
<body>

    <?php
    // Display messages
    if (isset($_SESSION['message'])) {
        echo "<div class='message success'>" . $_SESSION['message'] . "</div>";
        unset($_SESSION['message']);
    }
    if (isset($_SESSION['error'])) {
        echo "<div class='message error'>" . $_SESSION['error'] . "</div>";
        unset($_SESSION['error']);
    }
    ?>

    <details>
        <summary>Add New Dish</summary>
        <div class="modal">
            <form action="backend.php" method="post">
                <div class="close-btn">
                    <input type="submit" formaction="#" value="×" formnovalidate style="background:none;border:none;font-size:20px;cursor:pointer;">
                </div>

                <h2>Add a New Dish</h2>

                <input type="hidden" name="action" value="add_dish">

                <label for="name">Dish Name:</label>
                <input type="text" id="name" name="name" required>

               <label for="price">Price ($):</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>


                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4"></textarea>

                <label for="category">Category:</label>
                <input type="text" id="category" name="category">

                <input type="submit" value="Add Dish">
                <div class="close-btn">
                    <button type="button" onclick="this.closest('details').removeAttribute('open')">×</button>
                </div>
            </form>
        </div>
    </details>

    <h2>Menu</h2>
    <div>
        <?php if (!empty($dishes)): ?>
           <?php foreach ($dishes as $dish): ?>
    <div class="dish-container">
        <h3><?php echo htmlspecialchars($dish['name']); ?></h3>
        <p><strong>Price:</strong> $<?php echo number_format($dish['price'], 2); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($dish['description']); ?></p>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($dish['category']); ?></p>

        <!-- Edit button opens modal -->
        <details>
            <summary>Edit</summary>
            <div class="modal">
                <form action="backend.php" method="post">
                    <div class="close-btn">
                        <input type="submit" formaction="#" value="×" formnovalidate style="background:none;border:none;font-size:20px;cursor:pointer;">
                    </div>

                    <h2>Edit Dish</h2>
                    <input type="hidden" name="action" value="edit_dish">
                    <input type="hidden" name="id" value="<?php echo $dish['id']; ?>">

                    <label for="name_<?php echo $dish['id']; ?>">Dish Name:</label>
                    <input type="text" id="name_<?php echo $dish['id']; ?>" name="name" value="<?php echo htmlspecialchars($dish['name']); ?>" required>

                    <label for="price_<?php echo $dish['id']; ?>">Price ($):</label>
                    <input type="number" id="price_<?php echo $dish['id']; ?>" name="price"
                        step="0.01" min="0" value="<?php echo htmlspecialchars($dish['price']); ?>" required>


                    <label for="description_<?php echo $dish['id']; ?>">Description:</label>
                    <textarea id="description_<?php echo $dish['id']; ?>" name="description" rows="4"><?php echo htmlspecialchars($dish['description']); ?></textarea>

                    <label for="category_<?php echo $dish['id']; ?>">Category:</label>
                    <input type="text" id="category_<?php echo $dish['id']; ?>" name="category" value="<?php echo htmlspecialchars($dish['category']); ?>">

                    <input type="submit" value="Save Changes">
                </form>
            </div>
        </details>

        <!-- Delete form -->
        <form action="backend.php" method="post" onsubmit="return confirm('Are you sure you want to delete this dish?');">
            <input type="hidden" name="action" value="delete_dish">
            <input type="hidden" name="id" value="<?php echo $dish['id']; ?>">
            <input type="submit" value="Delete" class="delete-btn">
        </form>
    </div>
<?php endforeach; ?>
        <?php else: ?>
            <p>No dishes found.</p>
        <?php endif; ?>
    </div>

</body>
</html>