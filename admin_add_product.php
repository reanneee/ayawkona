<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

$message = []; // Initialize message as an empty array

if(isset($_POST['add_product'])){
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);

   $price = $_POST['price'];
   $price = filter_var($price, FILTER_SANITIZE_STRING);

   $quantity = $_POST['quantity'];
   $quantity = filter_var($quantity, FILTER_SANITIZE_NUMBER_INT);

   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/'.$image;

   // Get current date and time
   $created_at = date('Y-m-d H:i:s');

   // Use mysqli_query instead of prepared statement
   $select_product_query = "SELECT * FROM `products` WHERE name = '$name'";
   $select_product_result = mysqli_query($conn, $select_product_query);

   if(mysqli_num_rows($select_product_result) > 0){  // Check if the product already exists
      $message[] = 'Product name already exists!';
   } else {
      if($image_size > 2000000){
         $message[] = 'Image size is too large!';
      } else {
         // Insert product with created_at field
         $insert_product_query = "INSERT INTO `products`(name, price, quantity, description, image, date) 
                                  VALUES('$name', '$price', '$quantity', '$description', '$image', '$created_at')";
         $insert_product_result = mysqli_query($conn, $insert_product_query);

         if($insert_product_result){
            move_uploaded_file($image_tmp_name, $image_folder);
            $message[] = 'New product added!';
         } else {
            $message[] = 'Failed to add product!';
         }
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Add Product</title>

   <!-- font awesome cdn link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom admin style link -->
   <link rel="stylesheet" href="css/admin_style.css">
   <style>
body {
   font-family: 'Roboto', sans-serif;
   background-color: #f4f4f4;
   margin: 0;
   padding: 0;
   display: flex;
   justify-content: center;
   align-items: center;
   height: 100vh; /* Make the body take up the full viewport height */
   overflow: hidden; /* Prevent scrolling */
}

main {
   margin-left: 250px; /* Space for the sidebar */
   margin-right: 0px;
   padding: 20px;
   width: calc(100% - 250px); /* Ensure content doesn't overflow */
   background-color: #f9f9f9;
   min-height: 100vh;
   overflow: hidden; /* Prevent scrolling within main */
   box-sizing: border-box;
}

.heading {
   text-align: center;
   font-size: 2.5rem;
   margin-bottom: 20px;
   color: #2c3e50;
}

.add-products {
   margin-top: 70px;
   width: 100%;
   padding: 30px;
   background-color: #fff;
   border-radius: 10px;
   box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
   display: flex;
   flex-direction: column;
   align-items: center;
   box-sizing: border-box;
}

.add-products input, .add-products textarea {
   width: 100%;
   padding: 12px;
   margin: 10px 0;
   border-radius: 5px;
   border: 1px solid #ddd;
   font-size: 1rem;
   transition: 0.3s ease;
   box-sizing: border-box;
}

.add-products input:focus, .add-products textarea:focus {
   border-color: #2980b9;
   box-shadow: 0 0 5px rgba(41, 128, 185, 0.5);
}

.add-products input[type="submit"] {
   background-color: #2980b9;
   color: #fff;
   font-size: 1.1rem;
   cursor: pointer;
   border: none;
   transition: background-color 0.3s ease;
}

.add-products input[type="submit"]:hover {
   background-color: #3498db;
}

.message {
   text-align: center;
margin-top: 40px;
   font-size: 1.2rem;
   color: #e74c3c;
}

.box {
   background-color: #f9f9f9;
   border: 1px solid #ddd;
   border-radius: 8px;
   box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
   .heading {
      font-size: 2rem;
   }

   .add-products {
      padding: 20px;
   }
}

   </style>
</head>
<body>

<main>
   <?php include 'admin_header.php' ?>

   <section class="add-products">
      <h1 class="heading">Add New Product</h1>

      <form action="" method="post" enctype="multipart/form-data">
         <input type="text" class="box" required maxlength="100" placeholder="Enter product name" name="name">
         <input type="number" min="0" class="box" required max="9999999999" placeholder="Enter product price" onkeypress="if(this.value.length == 10) return false;" name="price">
         <input type="number" min="0" class="box" required placeholder="Enter product quantity" name="quantity">
         <textarea class="box" required maxlength="500" placeholder="Enter product description" name="description"></textarea>
         <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
         <input type="submit" value="Add Product" class="btn" name="add_product">
      </form>

      <!-- Display Messages -->
      <?php if(!empty($message)): ?>
   <div class="message">
      <?php 
         if(is_array($message)){
            foreach($message as $msg): 
                echo "<p>$msg</p>";
            endforeach;
         }
      ?>
   </div>
<?php endif; ?>
   </section>
</main>

</body>
</html>
