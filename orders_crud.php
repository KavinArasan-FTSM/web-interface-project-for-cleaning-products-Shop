<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if not already started
}

// Include the database connection
include_once 'database.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_level'])) {
    header("Location: login.php");
    exit("Access denied. Please log in.");
}

// Check user role
$isAdmin = $_SESSION['user_level'] === 'Admin';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: Could not connect to the database. " . $e->getMessage());
}

// Create
if (isset($_POST['create'])) {
    try {
        $stmt = $conn->prepare("INSERT INTO tbl_orders_a195661 (fld_order_num, fld_order_date, fld_staff_num, fld_customer_num) 
                                VALUES (:oid, :orderdate, :sid, :cid)");
       
        $stmt->bindParam(':oid', $oid, PDO::PARAM_STR);
        $stmt->bindParam(':orderdate', $orderdate, PDO::PARAM_STR);
        $stmt->bindParam(':sid', $sid, PDO::PARAM_STR);
        $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
       
        $oid = $_POST['oid'];
        $orderdate = $_POST['orderdate'];
        $sid = $_POST['sid'];
        $cid = $_POST['cid'];
       
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Update
if (isset($_POST['update'])) {
    if ($isAdmin) {
        try {
            $stmt = $conn->prepare("UPDATE tbl_orders_a195661 SET fld_staff_num = :sid, fld_customer_num = :cid 
                                    WHERE fld_order_num = :oid");
           
            $stmt->bindParam(':oid', $oid, PDO::PARAM_STR);
            $stmt->bindParam(':sid', $sid, PDO::PARAM_STR);
            $stmt->bindParam(':cid', $cid, PDO::PARAM_STR);
               
            $oid = $_POST['oid'];
            $sid = $_POST['sid'];
            $cid = $_POST['cid'];
             
            $stmt->execute();
            header("Location: orders.php");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        exit("Access denied. Only admins can update orders.");
    }
}

// Delete
if (isset($_GET['delete'])) {
    if ($isAdmin) {
        try {
            $stmt = $conn->prepare("DELETE FROM tbl_orders_a195661 WHERE fld_order_num = :oid");
           
            $stmt->bindParam(':oid', $oid, PDO::PARAM_STR);
            $oid = $_GET['delete'];
             
            $stmt->execute();
            header("Location: orders.php");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        exit("Access denied. Only admins can delete orders.");
    }
}

// Edit
if (isset($_GET['edit'])) {
    try {
        $stmt = $conn->prepare("SELECT * FROM tbl_orders_a195661 WHERE fld_order_num = :oid");
        $stmt->bindParam(':oid', $oid, PDO::PARAM_STR);
        $oid = $_GET['edit'];
        $stmt->execute();
        $editrow = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

$conn = null;
?>
