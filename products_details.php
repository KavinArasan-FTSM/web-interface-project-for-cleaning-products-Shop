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
$user_level = $_SESSION['user_level']; // Normal staff and admin are allowed

// Ensure a valid product ID is provided
if (!isset($_GET['pid']) || empty($_GET['pid'])) {
    echo "<h1>Invalid Product ID</h1>";
    exit();
}

$productID = $_GET['pid'];

try {
    // Establish a new PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch product details
    $stmt = $conn->prepare("SELECT * FROM tbl_products_a195661_pt2 WHERE FLD_PRODUCT_ID = :pid");
    $stmt->bindParam(':pid', $productID, PDO::PARAM_STR);
    $stmt->execute();
    $readrow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$readrow) {
        echo "<h1>Product Not Found</h1>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cutejoy Cleaning Shop : Product Details</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include_once 'nav_bar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12 col-sm-5 col-sm-offset-1 col-md-4 col-md-offset-2 well well-sm text-center">
            <?php 
            // Dynamically construct the image path using FLD_PRODUCT_ID
            $imagePath = "uploads/" . $readrow['FLD_PRODUCT_ID'] . ".jpg";

            // Check if the image file exists in the pictures folder
            if (file_exists($imagePath)) {
                echo "<img src='$imagePath' class='img-responsive' alt='Product Image'>";
            } else {
                echo "<p>No image available</p>";
            }
            ?>
        </div>
        <div class="col-xs-12 col-sm-5 col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Product Details</strong></div>
                <div class="panel-body">
                    Below are specifications of the product.
                </div>
                <table class="table">
                    <tr>
                        <td class="col-xs-4 col-sm-4 col-md-4"><strong>Product ID</strong></td>
                        <td><?php echo $readrow['FLD_PRODUCT_ID']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Name</strong></td>
                        <td><?php echo $readrow['FLD_PRODUCT_NAME']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Price</strong></td>
                        <td>RM <?php echo $readrow['FLD_PRICE']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Brand</strong></td>
                        <td><?php echo $readrow['FLD_BRAND']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Color</strong></td>
                        <td><?php echo $readrow['FLD_COLOUR']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Type</strong></td>
                        <td><?php echo $readrow['FLD_TYPE']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Unit Count</strong></td>
                        <td><?php echo $readrow['FLD_UNIT_COUNT']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Description</strong></td>
                        <td><?php echo $readrow['FLD_DESCRIPTION']; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
