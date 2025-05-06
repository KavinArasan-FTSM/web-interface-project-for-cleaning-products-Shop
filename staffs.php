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
if ($_SESSION['user_level'] !== 'Admin') { // Restrict access to Admin only
    echo "<script>alert('You have no permission to access this page. Redirecting to the homepage.'); window.location.href='index.php';</script>";
    exit();
}

include_once 'staffs_crud.php';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Staff Management System</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

  <?php include_once 'nav_bar.php'; ?>

<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
      <div class="page-header">
        <h2>Create New Staff</h2>
      </div>
      <form action="staffs.php" method="post" class="form-horizontal">
        <div class="form-group">
          <label for="staffid" class="col-sm-3 control-label">Staff ID</label>
          <div class="col-sm-9">
            <input name="sid" type="text" class="form-control" id="sid" placeholder="Staff ID" value="<?php if(isset($_GET['edit'])) echo $editrow['FLD_STAFF_ID']; ?>" required>
          </div>
        </div>
        <div class="form-group">
          <label for="staffname" class="col-sm-3 control-label">Staff Name</label>
          <div class="col-sm-9">
            <input name="name" type="text" class="form-control" id="staffname" placeholder="Staff Name" value="<?php if(isset($_GET['edit'])) echo $editrow['FLD_STAFF_NAME']; ?>" required>
          </div>
        </div>
        <div class="form-group">
          <label for="position" class="col-sm-3 control-label">Position</label>
          <div class="col-sm-9">
            <input name="position" type="text" class="form-control" id="position" placeholder="Position" value="<?php if(isset($_GET['edit'])) echo $editrow['FLD_POSITION']; ?>" required>
          </div>
        </div>
        <div class="form-group">
          <label for="salary" class="col-sm-3 control-label">Salary(RM)</label>
          <div class="col-sm-9">
            <input name="salary" type="number" class="form-control" id="salary" placeholder="Salary" value="<?php if(isset($_GET['edit'])) echo $editrow['FLD_SALARY']; ?>" required>
          </div>
        </div>
        <div class="form-group">
          <label for="username" class="col-sm-3 control-label">Username</label>
          <div class="col-sm-9">
            <input name="username" type="text" class="form-control" id="username" placeholder="Username" value="<?php if(isset($_GET['edit'])) echo $editrow['username']; ?>" required>
          </div>
        </div>
        <div class="form-group">
          <label for="password" class="col-sm-3 control-label">Password</label>
          <div class="col-sm-9">
            <input name="password" type="password" class="form-control" id="password" placeholder="Password" <?php if(!isset($_GET['edit'])) echo 'required'; ?>>
          </div>
        </div>
        <div class="form-group">
          <label for="userlevel" class="col-sm-3 control-label">User Level</label>
          <div class="col-sm-9">
            <select name="user_level" class="form-control" id="userlevel">
              <option value="Normal Staff" <?php if(isset($_GET['edit']) && $editrow['user_level'] == 'Normal Staff') echo 'selected'; ?>>Normal Staff</option>
              <option value="Admin" <?php if(isset($_GET['edit']) && $editrow['user_level'] == 'Admin') echo 'selected'; ?>>Admin</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-9">
            <?php if (isset($_GET['edit'])) { ?>
            <input type="hidden" name="oldsid" value="<?php echo $editrow['FLD_STAFF_ID']; ?>">
            <button class="btn btn-default" type="submit" name="update"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Update</button>
            <?php } else { ?>
            <button class="btn btn-default" type="submit" name="create"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Create</button>
            <?php } ?>
            <button class="btn btn-default" type="reset"><span class="glyphicon glyphicon-erase" aria-hidden="true"></span> Clear</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
      <div class="page-header">
        <h2>Staffs List</h2>
      </div>
      <table class="table table-striped table-bordered">
        <tr>
          <th>Staff ID</th>
          <th>Staff Name</th>
          <th>Position</th>
          <th>Salary(RM)</th>
          <th>Username</th>
          <th>User Level</th>
          <th></th>
        </tr>
        <?php
        $per_page = 5;
        if (isset($_GET["page"])) $page = $_GET["page"];
        else $page = 1;
        $start_from = ($page-1) * $per_page;
        try {
          $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db);
          $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          $stmt = $conn->prepare("SELECT * FROM tbl_staffs_a195661_pt2 LIMIT $start_from, $per_page");
          $stmt->execute();
          $result = $stmt->fetchAll();
        } catch(PDOException $e){
          echo "Error: " . $e->getMessage();
        }
        foreach($result as $readrow) {
        ?>
        <tr>
          <td><?php echo $readrow['FLD_STAFF_ID']; ?></td>
          <td><?php echo $readrow['FLD_STAFF_NAME']; ?></td>
          <td><?php echo $readrow['FLD_POSITION']; ?></td>
          <td><?php echo $readrow['FLD_SALARY']; ?></td>
          <td><?php echo $readrow['username']; ?></td>
          <td><?php echo $readrow['user_level']; ?></td>
          <td>
            <a href="staffs.php?edit=<?php echo $readrow['FLD_STAFF_ID']; ?>" class="btn btn-success btn-xs" role="button"> Edit </a>
            <a href="staffs.php?delete=<?php echo $readrow['FLD_STAFF_ID']; ?>" onclick="return confirm('Are you sure to delete?');" class="btn btn-danger btn-xs" role="button">Delete</a>
          </td>
        </tr>
        <?php } ?>
      </table>
    </div>
  </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
