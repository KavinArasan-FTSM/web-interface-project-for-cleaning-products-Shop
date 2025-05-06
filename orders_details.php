<?php
session_start(); // Start the session

// Include necessary files
include_once 'orders_details_crud.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_level'])) {
    header("Location: login.php");
    exit("Access denied. Please log in.");
}

// Check user role
$isAdmin = $_SESSION['user_level'] === 'Admin';

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">                       
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Bike Ordering System : Orders Details</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include_once 'nav_bar.php'; ?>

<?php
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db); // Use correct variables
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $oid = isset($_GET['oid']) ? $_GET['oid'] : ''; // Ensure oid is retrieved properly
    $stmt = $conn->prepare("SELECT * FROM tbl_orders_a195661 
                            JOIN tbl_staffs_a195661_pt2 ON tbl_orders_a195661.fld_staff_num = tbl_staffs_a195661_pt2.FLD_STAFF_ID 
                            JOIN tbl_customers_a195661_pt2 ON tbl_orders_a195661.fld_customer_num = tbl_customers_a195661_pt2.FLD_CUS_ID 
                            WHERE fld_order_num = :oid");
    $stmt->bindParam(':oid', $oid, PDO::PARAM_STR);
    $stmt->execute();
    $readrow = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
$conn = null;
?>

<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
      <div class="panel panel-default">
        <div class="panel-heading"><strong>Order Details</strong></div>
        <div class="panel-body">
          Below are details of the order.
        </div>
        <table class="table">
          <tr>
            <td class="col-xs-4 col-sm-4 col-md-4"><strong>Order ID</strong></td>
            <td><?php echo $readrow['fld_order_num'] ?></td>
          </tr>
          <tr>
            <td><strong>Order Date</strong></td>
            <td><?php echo $readrow['fld_order_date'] ?></td>
          </tr>
          <tr>
            <td><strong>Staff</strong></td>
            <td><?php echo $readrow['FLD_STAFF_NAME']; ?></td>
          </tr>
          <tr>
            <td><strong>Customer</strong></td>
            <td><?php echo $readrow['FLD_CUS_NAME']; ?></td>
          </tr>
        </table>
      </div>
    </div>
  </div>

  <?php if ($isAdmin || $_SESSION['user_level'] === 'Normal Staff') { ?>
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
      <div class="page-header">
        <h2>Add a Product</h2>
      </div>
      <form action="orders_details.php" method="post" class="form-horizontal" name="frmorder" id="forder">
        <div class="form-group">
          <label for="prd" class="col-sm-3 control-label">Product</label>
          <div class="col-sm-9">
            <select name="pid" class="form-control" id="prd">
              <option value="">Please select</option>
              <?php
              try {
                  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db);
                  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                  $stmt = $conn->prepare("SELECT * FROM tbl_products_a195661_pt2");
                  $stmt->execute();
                  $result = $stmt->fetchAll();
              } catch (PDOException $e) {
                  echo "Error: " . $e->getMessage();
              }
              foreach ($result as $productrow) {
                  ?>
                  <option value="<?php echo $productrow['FLD_PRODUCT_ID']; ?>"><?php echo $productrow['FLD_BRAND'] . " " . $productrow['FLD_PRODUCT_NAME']; ?></option>
                  <?php
              }
              $conn = null;
              ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label for="qty" class="col-sm-3 control-label">Quantity</label>
          <div class="col-sm-9">
            <input name="quantity" type="number" class="form-control" id="qty" min="1">
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-9">
            <input name="oid" type="hidden" value="<?php echo $readrow['fld_order_num'] ?>">
            <button class="btn btn-default" type="submit" name="addproduct"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add Product</button>
            <button class="btn btn-default" type="reset"><span class="glyphicon glyphicon-erase" aria-hidden="true"></span> Clear</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php } ?>

  <div class="row">
    <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
      <div class="page-header">
        <h2>Products in This Order</h2>
      </div>
      <table class="table table-striped table-bordered">
        <tr>
          <th>Order Detail ID</th>
          <th>Product</th>
          <th>Quantity</th>
          <th></th>
        </tr>
        <?php
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare("SELECT * FROM tbl_orders_details_a195661 
                                    JOIN tbl_products_a195661_pt2 
                                    ON tbl_orders_details_a195661.fld_product_num = tbl_products_a195661_pt2.FLD_PRODUCT_ID 
                                    WHERE fld_order_num = :oid");
            $stmt->bindParam(':oid', $oid, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        foreach ($result as $detailrow) {
            ?>
            <tr>
              <td><?php echo $detailrow['fld_order_detail_num']; ?></td>
              <td><?php echo $detailrow['FLD_PRODUCT_NAME']; ?></td>
              <td><?php echo $detailrow['fld_order_detail_quantity']; ?></td>
              <td>
                <?php if ($isAdmin) { ?>
                  <a href="orders_details.php?delete=<?php echo $detailrow['fld_order_detail_num']; ?>&oid=<?php echo $_GET['oid']; ?>" onclick="return confirm('Are you sure to delete?');" class="btn btn-danger btn-xs" role="button">Delete</a>
                <?php } ?>
              </td>
            </tr>
            <?php
        }
        ?>
      </table>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
      <a href="invoice.php?oid=<?php echo $_GET['oid']; ?>" target="_blank" role="button" class="btn btn-primary btn-lg btn-block">Generate Invoice</a>
    </div>
  </div>
  <br>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
