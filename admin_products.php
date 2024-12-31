<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
   exit(); 
}

include 'add_product.php';

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];

   $delete_product_image_query = "SELECT image FROM `products` WHERE id = $delete_id";
   $result = mysqli_query($conn, $delete_product_image_query);
   $fetch_delete_image = mysqli_fetch_assoc($result);

   if ($fetch_delete_image && file_exists('uploaded_img/' . $fetch_delete_image['image'])) {
       unlink('uploaded_img/' . $fetch_delete_image['image']);
   }

   $delete_product_query = "DELETE FROM `products` WHERE id = $delete_id";
   mysqli_query($conn, $delete_product_query);

   $delete_cart_query = "DELETE FROM `cart` WHERE pid = $delete_id";
   mysqli_query($conn, $delete_cart_query);

   header('location:admin_products.php');
   exit(); 
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Products</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Google Fonts for a modern look -->
   <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

   <!-- Custom Admin Style -->
   <link rel="stylesheet" href="css/admin_style.css">

   <style>
      * {
         margin: 0;
         padding: 0;
         box-sizing: border-box;
      }

      body {
         font-family: 'Roboto', sans-serif;
         background-color: #f9fafc;
         color: #333;
         padding: 20px;
      }

      main {
         margin-top: 80px;
         margin-left: 250px;
         padding: 20px;
         width: calc(100% - 250px);
         background-color: #fff;
         box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
         border-radius: 8px;
      }

      .heading {
         font-size: 2.2rem;
         color: #2c3e50;
         text-align: center;
         margin-bottom: 30px;
      }

      .show-products {
         margin: 0 auto;
         width: 100%;
         max-width: 1200px;
      }

      .show-products table {
         width: 100%;
         border-collapse: collapse;
         border-radius: 8px;
         overflow: hidden;
         background-color: #fff;
         box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
      }

      .show-products table th,
      .show-products table td {
         padding: 16px;
         text-align: center;
         font-size: 1.1rem;
         border-bottom: 1px solid #eaeaea;
      }

      .show-products table th {
         background-color: #2980b9;
         color: white;
         font-weight: 500;
      }

      .show-products table tr:nth-child(even) {
         background-color: #f9f9f9;
      }

      .show-products table tr:hover {
         background-color: #f1f1f1;
      }

      .show-products table td img {
         width: 80px;
         height: 80px;
         object-fit: cover;
         border-radius: 5px;
      }

      /* Container for action buttons to display them in a row */
      .action-btn-container {
         display: flex;
         justify-content: center;
         /* Align buttons horizontally */
         gap: 10px;
         /* Space between the buttons */
      }

      /* General style for action buttons */
      .action-btn {
         font-size: 20px;
         color: #fff;
         background-color: #e74c3c;
         /* Same background color for both buttons */
         padding: 10px;
         /* Adjust padding to suit your design */
         border-radius: 50%;
         /* Round shape */
         transition: background-color 0.3s ease;
         cursor: pointer;
         width: 50px;
         
         height: 50px;
        
         display: flex;
         justify-content: center;
         align-items: center;
      }

      /* Edit button specific */
      .edit-btn {
         background-color: #3498db;
         /* Edit button background color (blue) */
      }

      .empty {
         text-align: center;
         font-size: 1.2rem;
         color: #777;
      }

      /* Responsive Design */
      @media (max-width: 768px) {
         main {
            margin-left: 0;
            width: 100%;
         }

         .show-products table th,
         .show-products table td {
            font-size: 0.9rem;
         }

         .show-products table td img {
            width: 60px;
            height: 60px;
         }

         .action-btn {
            font-size: 18px;
         }
      }
   </style>
</head>

<body>

   <main>
      <?php include 'admin_header.php' ?>

      <section class="show-products">
         <h1 class="heading">Products Added</h1>

         <!-- Search Form -->
         <input type="text" id="searchInput" placeholder="&#xf002; Search by product name or description..." style="padding: 10px; width: 300px; border: 1px solid #ccc; border-radius: 5px; margin-bottom: 20px;">

         <table id="productsTable">
            <thead>
               <tr>
                  <th>Image</th>
                  <th>Product Name</th>
                  <th>Price</th>
                  <th>Quantity</th>
                  <th>Description</th>
                  <th>Actions</th>
               </tr>
            </thead>
            <tbody>
               <?php
               // Default query for all products
               $select_products_query = "SELECT * FROM `products`";
               $result_products = mysqli_query($conn, $select_products_query);

               if (mysqli_num_rows($result_products) > 0) {
                  while ($fetch_products = mysqli_fetch_assoc($result_products)) {
               ?>
                     <tr class="productRow">
                        <td><img src="uploaded_img/<?= htmlspecialchars($fetch_products['image']); ?>" alt="Product Image"></td>
                        <td><?= htmlspecialchars($fetch_products['name']); ?></td>
                        <td>₱<?= number_format($fetch_products['price'], 2); ?></td>
                        <td><?= $fetch_products['quantity']; ?></td>
                        <td><?= htmlspecialchars($fetch_products['description']); ?></td>
                        <td class="action-btn-container">
                           <a href="admin_product_update.php?update=<?= $fetch_products['id']; ?>" class="action-btn edit-btn" title="Edit">
                              <i class="fa fa-edit"></i>
                           </a>
                           <a href="admin_products.php?delete=<?= $fetch_products['id']; ?>" class="action-btn" title="Delete" onclick="return confirm('Are you sure you want to delete this product?');">
                              <i class="fa fa-trash"></i>
                           </a>
                        </td>
                     </tr>
               <?php
                  }
               } else {
                  echo '<tr><td colspan="6" class="empty">No products found!</td></tr>';
               }
               ?>
            </tbody>
         </table>
      </section>

      <script>
         document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const productRows = document.querySelectorAll('.productRow');

            productRows.forEach(function(row) {
               const productName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
               const productDescription = row.querySelector('td:nth-child(5)').textContent.toLowerCase();

               if (productName.includes(searchTerm) || productDescription.includes(searchTerm)) {
                  row.style.display = '';
               } else {
                  row.style.display = 'none';
               }
            });
         });
      </script>
     

      <script src="js/admin_script.js"></script>
   </main>

</body>

</html>
