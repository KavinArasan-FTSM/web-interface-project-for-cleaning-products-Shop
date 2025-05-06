<?php
// Start the session
session_start();

// Include the database connection
include_once 'database.php';

// Session Validation
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Role-Based Access Control
$user_level = $_SESSION['user_level'];
$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create a New Product
if (isset($_POST['create'])) {
    if ($user_level !== 'Admin') { // Only Admins can create
        header("Location: products.php"); // Redirect if unauthorized
        exit();
    }

    try {
        // Insert query
        $stmt = $conn->prepare("INSERT INTO tbl_products_a195661_pt2(FLD_PRODUCT_ID, FLD_PRODUCT_NAME, FLD_PRICE, FLD_BRAND, FLD_COLOUR, FLD_TYPE, FLD_UNIT_COUNT, FLD_DESCRIPTION) 
                                VALUES(:pid, :name, :price, :brand, :color, :type, :unit, :description)");
        
        $stmt->bindParam(':pid', $_POST['pid'], PDO::PARAM_STR);
        $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(':price', $_POST['price'], PDO::PARAM_STR);
        $stmt->bindParam(':brand', $_POST['brand'], PDO::PARAM_STR);
        $stmt->bindParam(':color', $_POST['color'], PDO::PARAM_STR);
        $stmt->bindParam(':type', $_POST['type'], PDO::PARAM_STR);
        $stmt->bindParam(':unit', $_POST['unit'], PDO::PARAM_INT);
        $stmt->bindParam(':description', $_POST['description'], PDO::PARAM_STR);

        $stmt->execute();

        // File Upload Logic
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            $uploadFile = $uploadDir . $_POST['pid'] . '.jpg';
            $imageInfo = getimagesize($_FILES['image']['tmp_name']);

            if (!$imageInfo) {
                echo "<script>alert('The uploaded file is not a valid image.'); window.history.back();</script>";
                exit();
            }

            if ($imageInfo[0] > 300 || $imageInfo[1] > 400) {
                echo "<script>alert('Image dimensions exceed the allowed limit of 300x400 pixels.'); window.history.back();</script>";
                exit();
            }

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                echo "<script>alert('Failed to upload image.'); window.history.back();</script>";
                exit();
            }
        }

        header("Location: products.php?status=created");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Update Existing Product
if (isset($_POST['update'])) {
    if ($user_level !== 'Admin') { // Only Admins can update
        header("Location: products.php"); // Redirect if unauthorized
        exit();
    }

    try {
        // Update query
        $stmt = $conn->prepare("UPDATE tbl_products_a195661_pt2 
                                SET FLD_PRODUCT_NAME = :name, FLD_PRICE = :price, FLD_BRAND = :brand, FLD_COLOUR = :color, FLD_TYPE = :type, FLD_UNIT_COUNT = :unit, FLD_DESCRIPTION = :description 
                                WHERE FLD_PRODUCT_ID = :pid");

        $stmt->bindParam(':pid', $_POST['pid'], PDO::PARAM_STR);
        $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(':price', $_POST['price'], PDO::PARAM_STR);
        $stmt->bindParam(':brand', $_POST['brand'], PDO::PARAM_STR);
        $stmt->bindParam(':color', $_POST['color'], PDO::PARAM_STR);
        $stmt->bindParam(':type', $_POST['type'], PDO::PARAM_STR);
        $stmt->bindParam(':unit', $_POST['unit'], PDO::PARAM_INT);
        $stmt->bindParam(':description', $_POST['description'], PDO::PARAM_STR);

        $stmt->execute();

        // File Upload Logic
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            $uploadFile = $uploadDir . $_POST['pid'] . '.jpg';
            $imageInfo = getimagesize($_FILES['image']['tmp_name']);

            if (!$imageInfo) {
                echo "<script>alert('The uploaded file is not a valid image.'); window.history.back();</script>";
                exit();
            }

            if ($imageInfo[0] > 300 || $imageInfo[1] > 400) {
                echo "<script>alert('Image dimensions exceed the allowed limit of 300x400 pixels.'); window.history.back();</script>";
                exit();
            }

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                echo "<script>alert('Failed to upload image.'); window.history.back();</script>";
                exit();
            }
        }

        header("Location: products.php?status=updated");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Delete Product
if (isset($_GET['delete'])) {
    if ($user_level !== 'Admin') { // Only Admins can delete
        header("Location: products.php"); // Redirect if unauthorized
        exit();
    }

    try {
        // Delete query
        $stmt = $conn->prepare("DELETE FROM tbl_products_a195661_pt2 WHERE FLD_PRODUCT_ID = :pid");
        $stmt->bindParam(':pid', $_GET['delete'], PDO::PARAM_STR);
        $stmt->execute();

        $imagePath = 'uploads/' . $_GET['delete'] . '.jpg';
        if (file_exists($imagePath)) {
            unlink($imagePath); // Delete image file if it exists
        }

        header("Location: products.php?status=deleted");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Edit Product
if (isset($_GET['edit'])) {
    try {
        // Fetch product for editing
        $stmt = $conn->prepare("SELECT * FROM tbl_products_a195661_pt2 WHERE FLD_PRODUCT_ID = :pid");
        $stmt->bindParam(':pid', $_GET['edit'], PDO::PARAM_STR);
        $stmt->execute();

        $editrow = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

$conn = null;
?>
