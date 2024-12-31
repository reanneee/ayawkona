<?php
include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // MySQLi query for deleting a user account
    $delete_order_query = "DELETE FROM `user` WHERE id = $delete_id";
    $delete_result = mysqli_query($conn, $delete_order_query);

    if ($delete_result) {
        // Redirect after deletion
        header('location:users_accounts.php');
    } else {
        echo "Error deleting user account: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Accounts</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Custom admin style link -->
    <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include 'admin_header.php' ?>

<section class="accounts">

   <h1 class="heading">User Accounts</h1>

   <div class="box-container">

   <?php
   $select_accounts_query = "SELECT * FROM `user`";

   $result = mysqli_query($conn, $select_accounts_query);

   if (mysqli_num_rows($result) > 0) {

       while ($fetch_accounts = mysqli_fetch_assoc($result)) {
   ?>
      <div class="box">
         <p> User Id : <span><?= $fetch_accounts['id']; ?></span> </p>
         <p> Username : <span><?= $fetch_accounts['name']; ?></span> </p>
         <p> Email : <span><?= $fetch_accounts['email']; ?></span> </p>
         <a href="users_accounts.php?delete=<?= $fetch_accounts['id']; ?>" onclick="return confirm('Delete this account?')" class="delete-btn">Delete</a>
      </div>
   <?php
       }
   } else {
       echo '<p class="empty">No accounts available!</p>';
   }
   ?>

   </div>

</section>

<script src="js/admin_script.js"></script>

</body>
</html>
