<?php
// Start the session
session_start();

// Session Validation
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Role-based Restrictions
$user_level = $_SESSION['user_level'];

// Include Database Configuration
include_once 'database.php';

// Handle Product Deletion (Admins Only)
if (isset($_GET['delete']) && $user_level === 'Admin') {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("DELETE FROM tbl_products_a195661_pt2 WHERE FLD_PRODUCT_ID = :pid");
        $stmt->bindParam(':pid', $pid, PDO::PARAM_STR);
        $pid = $_GET['delete'];
        $stmt->execute();

        header("Location: products.php"); // Redirect after deletion
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch Product Data for Editing
$editrow = null;
if (isset($_GET['edit'])) {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("SELECT * FROM tbl_products_a195661_pt2 WHERE FLD_PRODUCT_ID = :pid");
        $stmt->bindParam(':pid', $pid, PDO::PARAM_STR);
        $pid = $_GET['edit'];
        $stmt->execute();

        $editrow = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cutejoy Cleaning Shop : Products</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include_once 'nav_bar.php'; ?>

    <div class="container-fluid">
        <?php if ($user_level === 'Admin') { ?>
            <div class="row">
                <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
                    <div class="page-header">
                        <h2><?php echo isset($editrow) ? 'Edit Product' : 'Create New Product'; ?></h2>
                    </div>
                    <form action="products_crud.php" method="post" class="form-horizontal" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="productid" class="col-sm-3 control-label">ID</label>
                            <div class="col-sm-9">
                                <input name="pid" type="text" class="form-control" id="productid" placeholder="Product ID" value="<?php echo isset($editrow) ? $editrow['FLD_PRODUCT_ID'] : ''; ?>" <?php echo isset($editrow) ? 'readonly' : ''; ?> required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="productname" class="col-sm-3 control-label">Name</label>
                            <div class="col-sm-9">
                                <input name="name" type="text" class="form-control" id="productname" placeholder="Product Name" value="<?php echo isset($editrow) ? $editrow['FLD_PRODUCT_NAME'] : ''; ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="productprice" class="col-sm-3 control-label">Price</label>
                            <div class="col-sm-9">
                                <input name="price" type="number" class="form-control" id="productprice" placeholder="Product Price" min="0.0" step="0.01" value="<?php echo isset($editrow) ? $editrow['FLD_PRICE'] : ''; ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="productbrand" class="col-sm-3 control-label">Brand</label>
                            <div class="col-sm-9">
                                <input name="brand" type="text" class="form-control" id="productbrand" placeholder="Product Brand" value="<?php echo isset($editrow) ? $editrow['FLD_BRAND'] : ''; ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="productcolor" class="col-sm-3 control-label">Color</label>
                            <div class="col-sm-9">
                                <input name="color" type="text" class="form-control" id="productcolor" placeholder="Product Color" value="<?php echo isset($editrow) ? $editrow['FLD_COLOUR'] : ''; ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="producttype" class="col-sm-3 control-label">Type</label>
                            <div class="col-sm-9">
                                <input name="type" type="text" class="form-control" id="producttype" placeholder="Product Type" value="<?php echo isset($editrow) ? $editrow['FLD_TYPE'] : ''; ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="productunit" class="col-sm-3 control-label">Unit Count</label>
                            <div class="col-sm-9">
                                <input name="unit" type="number" class="form-control" id="productunit" placeholder="Unit Count" value="<?php echo isset($editrow) ? $editrow['FLD_UNIT_COUNT'] : ''; ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="productdesc" class="col-sm-3 control-label">Description</label>
                            <div class="col-sm-9">
                                <textarea name="description" class="form-control" id="productdesc" placeholder="Description" required><?php echo isset($editrow) ? $editrow['FLD_DESCRIPTION'] : ''; ?></textarea>
                            </div>
                        </div>
                        <?php if (isset($editrow)) { ?>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Current Image</label>
                            <div class="col-sm-9">
                                <img src="uploads/<?php echo $editrow['FLD_PRODUCT_ID']; ?>.jpg" class="img-responsive" alt="Product Image">
                            </div>
                        </div>
                        <?php } ?>
                        <div class="form-group">
                            <label for="productimage" class="col-sm-3 control-label">New Picture</label>
                            <div class="col-sm-9">
                                <input name="image" type="file" class="form-control" id="productimage" accept=".jpg,.jpeg,.png">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button class="btn btn-default" type="submit" name="<?php echo isset($editrow) ? 'update' : 'create'; ?>">
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> <?php echo isset($editrow) ? 'Update' : 'Create'; ?>
                                </button>
                                <button class="btn btn-default" type="reset"><span class="glyphicon glyphicon-erase" aria-hidden="true"></span> Clear</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-xs-12">
                <h2>Products List</h2>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Product ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Brand</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $per_page = 10;
                        $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                        $offset = ($current_page - 1) * $per_page;

                        try {
                            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db);
                            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tbl_products_a195661_pt2");
                            $stmt->execute();
                            $total_records = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                            $total_pages = ceil($total_records / $per_page);

                            $stmt = $conn->prepare("SELECT * FROM tbl_products_a195661_pt2 LIMIT :offset, :per_page");
                            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                            $stmt->bindParam(':per_page', $per_page, PDO::PARAM_INT);
                            $stmt->execute();
                            $products = $stmt->fetchAll();

                            foreach ($products as $product) {
                                echo "<tr>";
                                echo "<td>{$product['FLD_PRODUCT_ID']}</td>";
                                echo "<td>{$product['FLD_PRODUCT_NAME']}</td>";
                                echo "<td>RM {$product['FLD_PRICE']}</td>";
                                echo "<td>{$product['FLD_BRAND']}</td>";
                                echo "<td>";
                                echo "<a href='products_details.php?pid={$product['FLD_PRODUCT_ID']}' class='btn btn-warning btn-xs'>Details</a> ";
                                if ($user_level === 'Admin') {
                                    echo "<a href='products.php?edit={$product['FLD_PRODUCT_ID']}' class='btn btn-success btn-xs'>Edit</a> ";
                                    echo "<a href='products.php?delete={$product['FLD_PRODUCT_ID']}' onclick='return confirm(\"Are you sure to delete?\");' class='btn btn-danger btn-xs'>Delete</a>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='5'>Error fetching products: {$e->getMessage()}</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <nav>
                    <ul class="pagination">
                        <li class="<?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                            <a href="?page=<?php echo max(1, $current_page - 1); ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                            <li class="<?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php } ?>
                        <li class="<?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a href="?page=<?php echo min($total_pages, $current_page + 1); ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
