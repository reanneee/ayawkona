<?php
include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit(); // Stop the script after redirection
}

if (isset($_POST['update'])) {
   // Sanitize name input
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);

   // Update admin name
   $update_profile_name_query = "UPDATE `admin` SET name = '$name' WHERE id = $admin_id";
   mysqli_query($conn, $update_profile_name_query);

   // Password update logic
   $prev_pass = $_POST['prev_pass'];
   $old_pass = sha1($_POST['old_pass']);
   $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);
   $new_pass = sha1($_POST['new_pass']);
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
   $confirm_pass = sha1($_POST['confirm_pass']);
   $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);
   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';

   // Checking and updating the password
   if ($old_pass != $empty_pass) {
      // Check if old password matches the previous one
      if ($old_pass != $prev_pass) {
         $message[] = 'Old password not matched!';
      } elseif ($new_pass != $confirm_pass) {
         // Check if new and confirm passwords match
         $message[] = 'Confirm password not matched!';
      } else {
         // If a new password is provided, update it
         if ($new_pass != $empty_pass) {
            $update_admin_pass_query = "UPDATE `admin` SET password = '$confirm_pass' WHERE id = $admin_id";
            mysqli_query($conn, $update_admin_pass_query);
            $message[] = 'Password updated successfully!';
         } else {
            $message[] = 'Please enter a new password!';
         }
      }
   } else {
      $message[] = 'Please enter old password!';
   }
}
?>
