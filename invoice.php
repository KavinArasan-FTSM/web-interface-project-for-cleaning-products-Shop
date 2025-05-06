<?php
session_start(); // Start the session

// Include the database connection
include_once 'database.php';

// Ensure user is authenticated
if (!isset($_SESSION['user_level'])) {
    header("Location: login.php");
    exit("Access denied. Please log in.");
}

// Restrict to read-only access for all authenticated users
if ($_SESSION['user_level'] !== 'Normal Staff' && $_SESSION['user_level'] !== 'Admin') {
    exit("Access denied. You do not have permission to access this page.");
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT * FROM tbl_orders_a195661, tbl_staffs_a195661_pt2, tbl_customers_a195661_pt2 
                            WHERE tbl_orders_a195661.fld_staff_num = tbl_staffs_a195661_pt2.FLD_STAFF_ID 
                            AND tbl_orders_a195661.fld_customer_num = tbl_customers_a195661_pt2.FLD_CUS_ID 
                            AND tbl_orders_a195661.fld_order_num = :oid");
    $stmt->bindParam(':oid', $oid, PDO::PARAM_STR);
    $oid = $_GET['oid'];
    $stmt->execute();
    $readrow = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
$conn = null;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="row">
    <div class="col-xs-6 text-center">
        <br>
        <img src="logo.png" width="60%" height="60%">
    </div>
    <div class="col-xs-6 text-right">
        <h1>INVOICE</h1>
        <h5>Order: <?php echo $readrow['fld_order_num'] ?></h5>
        <h5>Date: <?php echo $readrow['fld_order_date'] ?></h5>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-xs-5">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>From: Cutejoy Cleaning Shop Sdn. Bhd.</h4>
            </div>
            <div class="panel-body">
                <p>
                    No.1 Jalan Mahawangsa <br>
                    Seri Kota <br>
                    09877 <br>
                    Kedah <br>
                </p>
            </div>
        </div>
    </div>
    <div class="col-xs-5 col-xs-offset-2 text-right">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>To: <?php echo $readrow['FLD_CUS_NAME']; ?></h4>
            </div>
            <div class="panel-body">
                <p>
                    Address 1 <br>
                    Address 2 <br>
                    Postcode City <br>
                    State <br>
                </p>
            </div>
        </div>
    </div>
</div>

<table class="table table-bordered">
    <tr>
        <th>No</th>
        <th>Product</th>
        <th class="text-right">Quantity</th>
        <th class="text-right">Price(RM)/Unit</th>
        <th class="text-right">Total(RM)</th>
    </tr>
    <?php
    $grandtotal = 0;
    $counter = 1;
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT * FROM tbl_orders_details_a195661, tbl_products_a195661_pt2 
                                WHERE tbl_orders_details_a195661.fld_product_num = tbl_products_a195661_pt2.FLD_PRODUCT_ID 
                                AND fld_order_num = :oid");
        $stmt->bindParam(':oid', $oid, PDO::PARAM_STR);
        $oid = $_GET['oid'];
        $stmt->execute();
        $result = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
    foreach ($result as $detailrow) {
        ?>
        <tr>
            <td><?php echo $counter; ?></td>
            <td><?php echo $detailrow['FLD_PRODUCT_NAME']; ?></td>
            <td class="text-right"><?php echo $detailrow['fld_order_detail_quantity']; ?></td>
            <td class="text-right"><?php echo $detailrow['FLD_PRICE']; ?></td>
            <td class="text-right"><?php echo $detailrow['FLD_PRICE'] * $detailrow['fld_order_detail_quantity']; ?></td>
        </tr>
        <?php
        $grandtotal += $detailrow['FLD_PRICE'] * $detailrow['fld_order_detail_quantity'];
        $counter++;
    }
    ?>
    <tr>
        <td colspan="4" class="text-right">Grand Total</td>
        <td class="text-right"><?php echo $grandtotal; ?></td>
    </tr>
</table>

<div class="row">
    <div class="col-xs-5">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Bank Details</h4>
            </div>
            <div class="panel-body">
                <p>Your Name</p>
                <p>Bank Name</p>
                <p>SWIFT: </p>
                <p>Account Number: </p>
                <p>IBAN: </p>
            </div>
        </div>
    </div>
    <div class="col-xs-7">
        <div class="span7">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>Staff Details</h4>
                </div>
                <div class="panel-body">
                    <p> Staff Name: <?php echo $readrow['FLD_STAFF_NAME']; ?> </p>
                    <p>Computer-generated invoice. No signature is required.</p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
