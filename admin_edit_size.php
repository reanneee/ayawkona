<?php
include 'config.php';

// Fetch size data if edit is triggered
if (isset($_GET['edit'])) {
    $sizeID = $_GET['edit'];

    // Select query to fetch size data
    $sql = "SELECT * FROM size WHERE sizeID = $sizeID";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $sizename = $row['sizename'];
        $sizeprice = $row['sizeprice'];
    } else {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Size not found
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}

// Update size if form is submitted
if (isset($_POST['update'])) {
    $sizeID = $_POST['sizeID'];
    $sizename = $_POST['sizename'];
    $sizeprice = $_POST['sizeprice'];

    // Update query
    $sql = "UPDATE size SET sizename = '$sizename', sizeprice = '$sizeprice' WHERE sizeID = $sizeID";

    if ($conn->query($sql) === TRUE) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                Size updated successfully
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    } else {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Error: ' . $sql . '<br>' . $conn->error . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Size</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
    <!-- Form to edit size -->
    <div class="card">
        <div class="card-header">
            <h2>Edit Size</h2>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="sizeID" value="<?php echo $sizeID; ?>">
                <div class="mb-3">
                    <input type="text" class="form-control" name="sizename" placeholder="Enter Size Name" required value="<?php echo $sizename; ?>">
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control" name="sizeprice" placeholder="Enter Size Price" required value="<?php echo $sizeprice; ?>">
                </div>
                <button type="submit" class="btn btn-primary" name="update">Update Size</button>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>