<?php
include 'config.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/admin_style.css">


    <style>
        main {
            margin-top: 80px;
            margin-left: 250px;
            padding: 20px;
            background-color: #fff;
            min-height: 100vh;
        }

        .row-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            padding: 20px;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

    

        .form-container {
            margin-top: 0px;
            width: 38%;
        }

        .table-container {
            width: 100%;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-container input[type="text"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-container input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 80%;
        }

        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }

        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            text-align: center;
        }

        .message.success {
            background-color: #4CAF50;
            color: white;
        }

        .message.error {
            background-color: #f44336;
            color: white;
        }

        .table-container {
        width: 100%;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .table-title {
        text-align: center;
        font-size: 24px;
        color: #333;
        margin-bottom: 15px;
    }

        table {
            width: 100%;
            border-collapse: collapse;

        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }



        a:hover {
            text-decoration: underline;
        }

       .action-icons {
            font-size: 18px;
        }
        .action-icons a {
            color: #007BFF;
            padding: 5px;
            margin-right: 10px;
        }
        .action-icons a:hover {
            color: #333;
        }

        .form-container {
            min-height: 35vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>

  


</head>

<body>
    <?php include 'admin_header.php'; ?>
    <main>

        <?php
        include 'config.php';

        // Insert new size
        if (isset($_POST['submit'])) {
            $sizename = mysqli_real_escape_string($conn, $_POST['sizename']);
            $sizeprice = mysqli_real_escape_string($conn, $_POST['sizeprice']);

            // Insert query
            $sql = "INSERT INTO size (sizename, sizeprice) VALUES ('$sizename', '$sizeprice')";

            if (mysqli_query($conn, $sql)) {
                echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        New size inserted successfully
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
            } else {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                Error: " . mysqli_error($conn) . "
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
            }
        }

        // Delete size
        if (isset($_GET['delete'])) {
            $sizeID = intval($_GET['delete']);  // Ensure it's an integer

            // Delete query
            $sql = "DELETE FROM size WHERE sizeID = $sizeID";

            if (mysqli_query($conn, $sql)) {
                echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                Size deleted successfully
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";

            } else {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                Error: " . mysqli_error($conn) . "
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
            }
        }

        // Select query to fetch size data
        $sql = "SELECT * FROM size";
        $result = mysqli_query($conn, $sql);

        // Debugging line to check if query was successful
        if (!$result) {
            die("Query failed: " . mysqli_error($conn));
        }

        ?>


        <div class="row-container">

            <div class="form-container">

                <form method="POST">
                    <h2>Insert Size</h2>
                    <input type="text" name="sizename" placeholder="Enter Size Name" required><br>
                    <input type="text" name="sizeprice" placeholder="Enter Size Price" required><br>
                    <input type="submit" name="submit" value="Insert Size">
                </form>
            </div>

            <!-- Second Column: Display Sizes -->
            <div class="table-container">
            <h2 class="table-title">Available Sizes</h2>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    echo "<table>
        <thead>
        <tr>
            <th>Size ID</th>
            <th>Size Name</th>
            <th>Size Price</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>";
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                        <td>" . $row["sizeID"] . "</td>
                        <td>" . $row["sizename"] . "</td>
                        <td>" . $row["sizeprice"] . "</td>
                        <td class='action-icons'>
                            <a href='admin_edit_size.php?edit=" . $row['sizeID'] . "'><i class='fas fa-edit'></i></a>
                             <a href='?delete=" . $row['sizeID'] . "' onclick='return confirm(\"Are you sure you want to delete this size?\");'><i class='fas fa-trash-alt'></i></a>
                            
                        </a>
                        </td>
                      </tr>";
            }
                    
                    echo "</tbody></table>";
                } else {
                    echo "<p>No sizes available.</p>";
                }
                ?>
            </div>

        </div>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>