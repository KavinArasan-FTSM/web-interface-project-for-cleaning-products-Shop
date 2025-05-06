<?php
session_start(); // Start the session

// Include database connection and orders CRUD
include_once 'database.php';
include_once 'orders_crud.php';

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
  <title>Cutejoy Item Orders : Orders</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
   <?php include_once 'nav_bar.php'; ?>

<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
      <div class="page-header">
        <h2>Create New Order</h2>
      </div>

      <form action="orders.php" method="post" class="form-horizontal">
        <div class="form-group">
          <label class="control-label col-md-3" for="oid">Order ID</label>
          <div class="col-md-9">
            <input name="oid" type="text" class="form-control" id="oid" readonly value="<?php echo uniqid('O', true); ?>" placeholder="Order ID" required>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-md-3" for="orderdate">Order Date</label>
          <div class="col-md-9">
            <input name="orderdate" type="text" class="form-control" id="orderdate" readonly value="<?php echo date('Y-m-d H:i:s'); ?>" placeholder="Order Date" required>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-md-3" for="sid">Staff</label>
          <div class="col-md-9">
            <select name="sid" class="form-control" id="sid" required>
              <?php
              try {
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $stmt = $conn->prepare("SELECT * FROM tbl_staffs_a195661_pt2");
                $stmt->execute();
                $result = $stmt->fetchAll();
              } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
              }
              foreach ($result as $staffrow) {
                echo "<option value='{$staffrow['FLD_STAFF_ID']}'>{$staffrow['FLD_STAFF_NAME']}</option>";
              }
              ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-md-3" for="cid">Customer</label>
          <div class="col-md-9">
            <select name="cid" class="form-control" id="cid" required>
              <?php
              try {
                $stmt = $conn->prepare("SELECT * FROM tbl_customers_a195661_pt2");
                $stmt->execute();
                $result = $stmt->fetchAll();
              } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
              }
              foreach ($result as $custrow) {
                echo "<option value='{$custrow['FLD_CUS_ID']}'>{$custrow['FLD_CUS_NAME']}</option>";
              }
              ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-9 col-md-offset-3">
            <button class="btn btn-default" type="submit" name="create"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create</button>
            <button class="btn btn-default" type="reset"><span class="glyphicon glyphicon-erase" aria-hidden="true"></span> Clear</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <hr>

  <div class="row">
    <div class="col-xs-12">
      <h3>Orders List</h3>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Order Date</th>
            <th>Staff</th>
            <th>Customer</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $records_per_page = 5;
          $page = isset($_GET['page']) ? $_GET['page'] : 1;
          $start_from = ($page - 1) * $records_per_page;

          try {
            $stmt = $conn->prepare("SELECT * FROM tbl_orders_a195661 
              JOIN tbl_staffs_a195661_pt2 ON tbl_orders_a195661.FLD_STAFF_NUM = tbl_staffs_a195661_pt2.FLD_STAFF_ID 
              JOIN tbl_customers_a195661_pt2 ON tbl_orders_a195661.FLD_CUSTOMER_NUM = tbl_customers_a195661_pt2.FLD_CUS_ID 
              LIMIT $start_from, $records_per_page");
            $stmt->execute();
            $result = $stmt->fetchAll();
          } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
          }

          foreach ($result as $orderrow) {
          ?>
          <tr>
            <td><?php echo $orderrow['fld_order_num']; ?></td>
            <td><?php echo $orderrow['fld_order_date']; ?></td>
            <td><?php echo $orderrow['FLD_STAFF_NAME']; ?></td>
            <td><?php echo $orderrow['FLD_CUS_NAME']; ?></td>
            <td>
              <a href="orders_details.php?oid=<?php echo $orderrow['fld_order_num']; ?>" class="btn btn-warning btn-xs" role="button">Details</a>
              <?php if ($isAdmin) { ?>
              <a href="orders.php?edit=<?php echo $orderrow['fld_order_num']; ?>" class="btn btn-success btn-xs" role="button"> Edit </a>
              <a href="orders.php?delete=<?php echo $orderrow['fld_order_num']; ?>" onclick="return confirm('Are you sure to delete?');" class="btn btn-danger btn-xs" role="button">Delete</a>
              <?php } ?>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>

      <nav aria-label="Page navigation">
        <ul class="pagination">
          <?php
          try {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_orders_a195661");
            $stmt->execute();
            $total_records = $stmt->fetchColumn();
            $total_pages = ceil($total_records / $records_per_page);

            if ($page > 1) {
              echo "<li><a href='orders.php?page=" . ($page - 1) . "'>&laquo; Previous</a></li>";
            }

            for ($i = 1; $i <= $total_pages; $i++) {
              $active = ($i == $page) ? 'class="active"' : '';
              echo "<li $active><a href='orders.php?page=$i'>$i</a></li>";
            }

            if ($page < $total_pages) {
              echo "<li><a href='orders.php?page=" . ($page + 1) . "'>Next &raquo;</a></li>";
            }
          } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
          }
          ?>
        </ul>
      </nav>
    </div>
  </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
