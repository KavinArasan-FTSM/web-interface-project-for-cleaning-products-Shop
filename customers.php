<?php
session_start();
include_once 'database.php';
include_once 'customers_crud.php';

// Session Validation
if (!isset($_SESSION['user_level'])) {
    header("Location: login.php");
    exit("Access denied. Please log in.");
}

// Role-based Restrictions
$isAdmin = ($_SESSION['user_level'] === 'Admin');
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Customer Management System</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <?php include_once 'nav_bar.php'; ?>

<div class="container-fluid">
  <div class="row">
    <?php if ($isAdmin) { ?>
    <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
      <div class="page-header">
        <h2>Create New Customer</h2>
      </div>
      <form action="customers_crud.php" method="post" class="form-horizontal">
        <div class="form-group">
          <label for="customerid" class="col-sm-3 control-label">Customer ID</label>
          <div class="col-sm-9">
            <input name="cid" type="text" class="form-control" id="cid" placeholder="Customer ID" value="<?php if(isset($_GET['edit'])) echo $editrow['FLD_CUS_ID']; ?>" required>
          </div>
        </div>
        <div class="form-group">
          <label for="cusname" class="col-sm-3 control-label">Customer Name</label>
          <div class="col-sm-9">
            <input name="name" type="text" class="form-control" id="cusname" placeholder="Customer Name" value="<?php if(isset($_GET['edit'])) echo $editrow['FLD_CUS_NAME']; ?>" required>
          </div>
        </div>
        <div class="form-group">
          <label for="address" class="col-sm-3 control-label">Address</label>
          <div class="col-sm-9">
            <textarea name="address" class="form-control" id="address" placeholder="Address" required><?php if(isset($_GET['edit'])) echo $editrow['FLD_ADDRESS']; ?></textarea>
          </div>
        </div>
        <div class="form-group">
          <label for="phone" class="col-sm-3 control-label">Phone Number</label>
          <div class="col-sm-9">
            <input name="phone" type="text" class="form-control" id="phone" placeholder="Phone Number" value="<?php if(isset($_GET['edit'])) echo $editrow['FLD_TEL_NO']; ?>" required>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-9">
            <?php if (isset($_GET['edit'])) { ?>
            <input type="hidden" name="oldcid" value="<?php echo $editrow['FLD_CUS_ID']; ?>">
            <button class="btn btn-default" type="submit" name="update"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Update</button>
            <?php } else { ?>
            <button class="btn btn-default" type="submit" name="create"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create</button>
            <?php } ?>
            <button class="btn btn-default" type="reset"><span class="glyphicon glyphicon-erase" aria-hidden="true"></span> Clear</button>
          </div>
        </div>
      </form>
    </div>
    <?php } ?>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
      <div class="page-header">
        <h2>Customers List</h2>
      </div>
      <table class="table table-striped table-bordered">
        <tr>
          <th>Customer ID</th>
          <th>Customer Name</th>
          <th>Address</th>
          <th>Phone Number</th>
          <?php if ($isAdmin) { ?><th>Actions</th><?php } ?>
        </tr>
        <?php
        // Pagination
        $per_page = 5;
        if (isset($_GET["page"]))
          $page = $_GET["page"];
        else
          $page = 1;
        $start_from = ($page - 1) * $per_page;

        // Fetch customers
        try {
          $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db);
          $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $stmt = $conn->prepare("SELECT * FROM tbl_customers_a195661_pt2 LIMIT $start_from, $per_page");
          $stmt->execute();
          $result = $stmt->fetchAll();

          foreach ($result as $readrow) {
            echo "<tr>";
            echo "<td>{$readrow['FLD_CUS_ID']}</td>";
            echo "<td>{$readrow['FLD_CUS_NAME']}</td>";
            echo "<td>{$readrow['FLD_ADDRESS']}</td>";
            echo "<td>{$readrow['FLD_TEL_NO']}</td>";
            if ($isAdmin) {
              echo "<td>
                <a href='customers.php?edit={$readrow['FLD_CUS_ID']}' class='btn btn-success btn-xs'>Edit</a>
                <a href='customers_crud.php?delete={$readrow['FLD_CUS_ID']}' onclick='return confirm(\"Are you sure to delete?\");' class='btn btn-danger btn-xs'>Delete</a>
              </td>";
            }
            echo "</tr>";
          }
        } catch (PDOException $e) {
          echo "<tr><td colspan='5'>Error: {$e->getMessage()}</td></tr>";
        }
        ?>
      </table>
    </div>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

</body>
</html>
