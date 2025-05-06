<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it hasn't started already
}
include_once 'database.php';

// Session Validation
if (!isset($_SESSION['user_level'])) {
    header("Location: login.php");
    exit("Access denied. Please log in.");
}

// Role-based Restriction: Only Admins can Create, Update, and Delete
$isAdmin = $_SESSION['user_level'] === 'Admin';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Create
if (isset($_POST['create'])) {
    if ($isAdmin) { // Allow only Admins
        try {
            $stmt = $conn->prepare("INSERT INTO tbl_customers_a195661_pt2 (FLD_CUS_ID, FLD_CUS_NAME, FLD_ADDRESS, FLD_TEL_NO) 
                VALUES (:cid, :name, :address, :phone)");
            $stmt->bindParam(':cid', $_POST['cid'], PDO::PARAM_STR);
            $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
            $stmt->bindParam(':address', $_POST['address'], PDO::PARAM_STR);
            $stmt->bindParam(':phone', $_POST['phone'], PDO::PARAM_STR);

            $stmt->execute();
            header("Location: customers.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Access denied. Only Admins can create customers.";
    }
}

// Update
if (isset($_POST['update'])) {
    if ($isAdmin) { // Allow only Admins
        try {
            $stmt = $conn->prepare("UPDATE tbl_customers_a195661_pt2 
                SET FLD_CUS_ID = :cid, FLD_CUS_NAME = :name, FLD_ADDRESS = :address, FLD_TEL_NO = :phone 
                WHERE FLD_CUS_ID = :oldcid");
            $stmt->bindParam(':cid', $_POST['cid'], PDO::PARAM_STR);
            $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
            $stmt->bindParam(':address', $_POST['address'], PDO::PARAM_STR);
            $stmt->bindParam(':phone', $_POST['phone'], PDO::PARAM_STR);
            $stmt->bindParam(':oldcid', $_POST['oldcid'], PDO::PARAM_STR);

            $stmt->execute();
            header("Location: customers.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Access denied. Only Admins can update customers.";
    }
}

// Delete
if (isset($_GET['delete'])) {
    if ($isAdmin) { // Allow only Admins
        try {
            $stmt = $conn->prepare("DELETE FROM tbl_customers_a195661_pt2 WHERE FLD_CUS_ID = :cid");
            $stmt->bindParam(':cid', $_GET['delete'], PDO::PARAM_STR);

            $stmt->execute();
            header("Location: customers.php");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Access denied. Only Admins can delete customers.";
    }
}

// Edit
if (isset($_GET['edit'])) {
    if ($isAdmin) { // Allow only Admins
        try {
            $stmt = $conn->prepare("SELECT * FROM tbl_customers_a195661_pt2 WHERE FLD_CUS_ID = :cid");
            $stmt->bindParam(':cid', $_GET['edit'], PDO::PARAM_STR);

            $stmt->execute();
            $editrow = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Access denied. Only Admins can edit customers.";
    }
}

// Close the database connection
$conn = null;
?>
