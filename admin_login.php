<?php
// Include the database configuration file
include 'config.php';

session_start();

if (isset($_POST['login'])) {

    // Get and sanitize input data
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $pass = sha1($_POST['pass']); // Hashing the password using SHA1 (not recommended for modern apps, consider using password_hash())
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);

    // Prepare the SQL query to fetch the admin record
    $select_admin_query = "SELECT * FROM `admin` WHERE name = '$name' AND password = '$pass'";

    // Execute the query
    $result = mysqli_query($conn, $select_admin_query);

    // Check if any record is found
    if (mysqli_num_rows($result) > 0) {
        // Fetch the result
        $row = mysqli_fetch_assoc($result);

        // Set session variable to log the user in
        $_SESSION['admin_id'] = $row['id'];
        header('location: admin_page.php');
        exit(); // Always call exit after a redirect
    } else {
        // If no match is found, show error message
        $message[] = 'Incorrect username or password!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Login</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom Admin Style link -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>

<?php
// Display message if set
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="message">
            <span>' . $msg . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>';
    }
}
?>

<section class="form-container">
   <form action="" method="post">
      <h3>Login Now</h3>
      <p>Default username = <span>admin</span> & password = <span>111</span></p>
      <input type="text" name="name" required placeholder="Enter your username" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="Enter your password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Login Now" class="btn" name="login">
   </form>
</section>

</body>
</html>
