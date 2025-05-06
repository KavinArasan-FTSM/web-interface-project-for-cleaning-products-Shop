<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it hasn't started already
}
// Include database connection
include_once 'database.php';

// Ensure user is authenticated
if (!isset($_SESSION['user_level'])) {
    header("Location: login.php");
    exit("Access denied. Please log in.");
}

// Check user role
$isAdmin = ($_SESSION['user_level'] === 'Admin');

// Establish database connection
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db); // Updated variable names
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: Could not connect to the database. " . $e->getMessage());
}

// Create
if (isset($_POST['addproduct'])) {
    if ($isAdmin || $_SESSION['user_level'] === 'Normal Staff') { // Allow for both Admin and Normal Staff
        try {
            $stmt = $conn->prepare("INSERT INTO tbl_orders_details_a195661(fld_order_detail_num, fld_order_num, fld_product_num, fld_order_detail_quantity) 
            VALUES(:did, :oid, :pid, :quantity)");

            $stmt->bindParam(':did', $did, PDO::PARAM_STR);
            $stmt->bindParam(':oid', $oid, PDO::PARAM_STR);
            $stmt->bindParam(':pid', $pid, PDO::PARAM_STR);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);

            $did = uniqid('D', true);
            $oid = $_POST['oid'];
            $pid = $_POST['pid'];
            $quantity = $_POST['quantity'];

            $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        $_GET['oid'] = $oid;
    } else {
        echo "Access denied. You do not have permission to perform this action.";
        exit;
    }
}

// Delete
if (isset($_GET['delete'])) {
    if ($isAdmin) { // Restrict delete functionality to Admin
        try {
            $stmt = $conn->prepare("DELETE FROM tbl_orders_details_a195661 WHERE fld_order_detail_num = :did");

            $stmt->bindParam(':did', $did, PDO::PARAM_STR);

            $did = $_GET['delete'];

            $stmt->execute();

            header("Location: orders_details.php?oid=" . $_GET['oid']);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Access denied. You do not have permission to perform this action.";
        exit;
    }
}

// Close connection
$conn = null;
?>
