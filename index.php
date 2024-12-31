<?php

include 'config.php';

session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
};

if (isset($_POST['register'])) {

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);
   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   // Check if user already exists using mysqli_query
   $select_user = mysqli_prepare($conn, "SELECT * FROM `user` WHERE name = ? AND email = ?");
   mysqli_stmt_bind_param($select_user, "ss", $name, $email);
   mysqli_stmt_execute($select_user);
   $result = mysqli_stmt_get_result($select_user);

   if (mysqli_num_rows($result) > 0) {
      $message[] = 'username or email already exists!';
   } else {
      if ($pass != $cpass) {
         $message[] = 'confirm password not matched!';
      } else {
         $insert_user = mysqli_prepare($conn, "INSERT INTO `user`(name, email, password) VALUES(?,?,?)");
         mysqli_stmt_bind_param($insert_user, "sss", $name, $email, $cpass);
         mysqli_stmt_execute($insert_user);
         $message[] = 'registered successfully, login now please!';
      }
   }
}

if (isset($_POST['update_qty'])) {
   $cart_id = $_POST['cart_id'];
   $qty = $_POST['qty'];
   $qty = filter_var($qty, FILTER_SANITIZE_STRING);
   $update_qty = mysqli_prepare($conn, "UPDATE `cart` SET quantity = ? WHERE id = ?");
   mysqli_stmt_bind_param($update_qty, "ii", $qty, $cart_id);
   mysqli_stmt_execute($update_qty);
   $message[] = 'cart quantity updated!';
}

if (isset($_GET['delete_cart_item'])) {
   $delete_cart_id = $_GET['delete_cart_item'];
   $delete_cart_item = mysqli_prepare($conn, "DELETE FROM `cart` WHERE id = ?");
   mysqli_stmt_bind_param($delete_cart_item, "i", $delete_cart_id);
   mysqli_stmt_execute($delete_cart_item);
   header('location:index.php');
}

if (isset($_GET['logout'])) {
   session_unset();
   session_destroy();
   header('location:index.php');
}

if (isset($_POST['add_to_cart'])) {

   if ($user_id == '') {
      $message[] = 'please login first!';
   } else {

      $pid = $_POST['pid'];
      $name = $_POST['name'];
      $price = $_POST['price'];
      $image = $_POST['image'];
      $qty = $_POST['qty'];
      $qty = filter_var($qty, FILTER_SANITIZE_STRING);

      // Check if item already added to cart using mysqli_query
      $select_cart = mysqli_prepare($conn, "SELECT * FROM `cart` WHERE user_id = ? AND name = ?");
      mysqli_stmt_bind_param($select_cart, "is", $user_id, $name);
      mysqli_stmt_execute($select_cart);
      $result_cart = mysqli_stmt_get_result($select_cart);

      if (mysqli_num_rows($result_cart) > 0) {
         $message[] = 'already added to cart';
      } else {
         $insert_cart = mysqli_prepare($conn, "INSERT INTO `cart`(user_id, pid, name, price, quantity, image) VALUES(?,?,?,?,?,?)");
         mysqli_stmt_bind_param($insert_cart, "iisdss", $user_id, $pid, $name, $price, $qty, $image);
         mysqli_stmt_execute($insert_cart);
         $message[] = 'added to cart!';
      }
   }
}

if (isset($_POST['order'])) {

   if ($user_id == '') {
      $message[] = 'please login first!';
   } else {
      $name = $_POST['name'];
      $name = filter_var($name, FILTER_SANITIZE_STRING);
      $number = $_POST['number'];
      $number = filter_var($number, FILTER_SANITIZE_STRING);
      $address = 'flat no.' . $_POST['flat'] . ', ' . $_POST['street'] . ' - ' . $_POST['pin_code'];
      $address = filter_var($address, FILTER_SANITIZE_STRING);
      $method = $_POST['method'];
      $method = filter_var($method, FILTER_SANITIZE_STRING);
      $total_price = $_POST['total_price'];
      $total_products = $_POST['total_products'];

      // Check if cart is not empty using mysqli_query
      $select_cart = mysqli_prepare($conn, "SELECT * FROM `cart` WHERE user_id = ?");
      mysqli_stmt_bind_param($select_cart, "i", $user_id);
      mysqli_stmt_execute($select_cart);
      $result_cart = mysqli_stmt_get_result($select_cart);

      if (mysqli_num_rows($result_cart) > 0) {
         // Insert order
         $insert_order = mysqli_prepare($conn, "INSERT INTO `orders`(user_id, name, number, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?)");
         mysqli_stmt_bind_param($insert_order, "isssssd", $user_id, $name, $number, $method, $address, $total_products, $total_price);
         mysqli_stmt_execute($insert_order);

         // Delete cart after order placed
         $delete_cart = mysqli_prepare($conn, "DELETE FROM `cart` WHERE user_id = ?");
         mysqli_stmt_bind_param($delete_cart, "i", $user_id);
         mysqli_stmt_execute($delete_cart);

         $message[] = 'order placed successfully!';
      } else {
         $message[] = 'your cart empty!';
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
   <title>Paquito's Pizza</title>
   <link rel="icon" type="image/png" href="images/pizzalogo32x32.png">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
</head>

<body>

   <?php
   if (isset($message)) {
      foreach ($message as $message) {
         echo '
         <div class="message">
            <span>' . $message . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
      }
   }
   ?>

   <!-- header section starts  -->
   <header class="header home-active" id="mainHeader">
      <section class="flex">
         <a class="navbar-brand" href=""><img src="images/paquitologo.png" alt="logo" class="img-responsive"></a>
         <nav class="navbar">
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#menu">Menu</a>
            <a href="#order">Order</a>
            <a href="#faq">FAQ</a>
         </nav>
         <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="user-btn" class="fas fa-user"></div>
            <div id="order-btn" class="fas fa-box"></div>
            <?php
            // Using mysqli query to count cart items for the user
            if ($user_id) {
               $count_cart_query = "SELECT * FROM `cart` WHERE user_id = ?";
               $stmt_count_cart = mysqli_prepare($conn, $count_cart_query);
               mysqli_stmt_bind_param($stmt_count_cart, "i", $user_id); // "i" means integer type
               mysqli_stmt_execute($stmt_count_cart);
               $result_count_cart = mysqli_stmt_get_result($stmt_count_cart);
               $total_cart_items = mysqli_num_rows($result_count_cart);
            } else {
               $total_cart_items = 0; // If no user_id, cart items should be 0
            }
            ?>
            <div id="cart-btn" class="fas fa-shopping-cart"><span>(<?= $total_cart_items; ?>)</span></div>
         </div>
      </section>
   </header>
   <!-- header section ends -->

   <div class="user-account">
   <section>
      <div id="close-account"><span>Close</span></div>
      <div class="user">
         <?php
            $select_user = mysqli_prepare($conn, "SELECT * FROM `user` WHERE id = ?");
            mysqli_stmt_bind_param($select_user, "i", $user_id);
            mysqli_stmt_execute($select_user);
            $result_user = mysqli_stmt_get_result($select_user);
            if(mysqli_num_rows($result_user) > 0){
               while($fetch_user = mysqli_fetch_assoc($result_user)){
                  echo '<p>Welcome ! <span>'.$fetch_user['name'].'</span></p>';
                  echo '<a href="index.php?logout" class="btn">logout</a>';
               }
            }else{
               echo '<p><span>You are not logged in now!</span></p>';
            }
         ?>
      </div>
      <div class="display-orders">
      <?php
            if ($user_id) {
               // Using mysqli query for cart selection with prepared statement
               $select_cart_query = "SELECT * FROM `cart` WHERE user_id = ?";
               $stmt_cart = mysqli_prepare($conn, $select_cart_query);
               mysqli_stmt_bind_param($stmt_cart, "i", $user_id); // "i" means integer type
               mysqli_stmt_execute($stmt_cart);
               $result_cart = mysqli_stmt_get_result($stmt_cart);

               if (mysqli_num_rows($result_cart) > 0) {
                  while ($fetch_cart = mysqli_fetch_assoc($result_cart)) {
                     echo '<p>' . htmlspecialchars($fetch_cart['name']) . ' <span>(' . htmlspecialchars($fetch_cart['price']) . ' x ' . htmlspecialchars($fetch_cart['quantity']) . ')</span></p>';
                  }
               } else {
                  echo '<p><span>Your cart is empty!</span></p>';
               }
            }
            ?>
      </div>
      <div class="flex">
         <form action="user_login.php" method="post" id="register">
            <h3>Login now</h3>
            <input type="email" name="email" required class="box" placeholder="Enter your email" maxlength="50">
            <input type="password" name="pass" required class="box" placeholder="Enter your password" maxlength="20">
            <input type="submit" value="Login" name="login" class="btn">
         </form>
         <form action="user_register.php" method="post" id="register">
            <h3>Register now</h3>
            <input type="text" name="name" required class="box" placeholder="Enter your name" maxlength="50">
            <input type="email" name="email" required class="box" placeholder="Enter your email" maxlength="50">
            <input type="password" name="pass" required class="box" placeholder="Enter your password" maxlength="20">
            <input type="password" name="cpass" required class="box" placeholder="Confirm password" maxlength="20">
            <input type="submit" value="Register" name="register" class="btn">
         </form>
      </div>
   </section>
   </div>

   <div class="my-orders">
      <section>
         <div id="close-orders"><span>Close</span></div>
         <h3 class="title"> My Orders </h3>
         <?php
         // Using mysqli query for orders selection
         $select_orders_query = "SELECT * FROM `orders` WHERE user_id = '$user_id'";
         $result_orders = mysqli_query($conn, $select_orders_query);

         if (mysqli_num_rows($result_orders) > 0) {
            while ($fetch_orders = mysqli_fetch_assoc($result_orders)) {
         ?>

               <div class="box">
                  <p> Placed on : <span><?= $fetch_orders['placed_on']; ?></span> </p>
                  <p> Name : <span><?= $fetch_orders['name']; ?></span> </p>
                  <p> Number : <span><?= $fetch_orders['number']; ?></span> </p>
                  <p> Address : <span><?= $fetch_orders['address']; ?></span> </p>
                  <p> Payment method : <span><?= $fetch_orders['method']; ?></span> </p>
                  <p> Total_orders : <span><?= $fetch_orders['total_products']; ?></span> </p>
                  <p> Total price : <span>₱<?= $fetch_orders['total_price']; ?></span> </p>
                  <p> Payment status : <span style="color:<?php if ($fetch_orders['payment_status'] == 'pending') {
                                                               echo 'red';
                                                            } else {
                                                               echo 'green';
                                                            }; ?>"><?= $fetch_orders['payment_status']; ?></span> </p>
               </div>
         <?php
            }
         } else {
            echo '<p class="empty">nothing ordered yet!</p>';
         }
         ?>
      </section>
   </div>

   <div class="shopping-cart">
      <section>
         <div id="close-cart"><span>Close</span></div>
         <?php
         $grand_total = 0;

         // MySQLi query to select cart items
         $select_cart_query = "SELECT * FROM `cart` WHERE user_id = '$user_id'";
         $result_cart = mysqli_query($conn, $select_cart_query);

         if (mysqli_num_rows($result_cart) > 0) {
            while ($fetch_cart = mysqli_fetch_assoc($result_cart)) {
               $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']);
               $grand_total += $sub_total;
         ?>

               <div class="box">
                  <a href="index.php?delete_cart_item=<?= $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('delete this cart item?');"></a>
                  <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
                  <div class="content">
                     <p> <?= $fetch_cart['name']; ?> <br><span>(<?= $fetch_cart['price']; ?> x <?= $fetch_cart['quantity']; ?>)</span></p>
                     <form action="" method="post">
                        <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
                        <input type="number" name="qty" class="qty" min="1" max="99" value="<?= $fetch_cart['quantity']; ?>" onkeypress="if(this.value.length == 2) return false;">
                        <button type="submit" class="fas fa-edit" name="update_qty"></button>
                     </form>
                  </div>
               </div>
         <?php
            }
         } else {
            echo '<p class="empty"><span>your cart is empty!</span></p>';
         }
         ?>
         <div class="cart-total"> Grand total : <span>₱<?= $grand_total; ?></span></div>
         <a href="#order" class="btn">Order Now</a>
      </section>
   </div>

   <div class="home-bg">
      <section class="home" id="home">
         <br><br><br>
         <div class="slide-container">
            <div class="slide active">
               <div class="image">
                  <img src="images/home-img-1.png" alt="">
               </div>
               <div class="content">
                  <h5>YES WE HAVE THE </h5>
                  <h3>BEST HOMEMADE PIZZA</h3>
               </div>
            </div>

            <div class="slide">
               <div class="image">
                  <img src="images/Hawaiian1.png" alt="">
               </div>
               <div class="content">
                  <h3>Hawaiian</h3>
               </div>
            </div>

            <div class="slide">
               <div class="image">
                  <img src="images/Triple Cheese.png" alt="">
               </div>
               <div class="content">
                  <h3>Triple Cheese</h3>
               </div>
            </div>
         </div>
      </section>
   </div>

   <!-- about section starts  -->

   <section class="about" id="about">
      <br><br>
      <h1 class="heading">About Us</h1>

      <div class="box-container">

         <div class="box">
            <img src="images/order to bake.png" alt="">
            <h3>Order to Bake</h3>
            <p style="margin-bottom: 15px;">"Order to Make" service, simply select your favorite pizza from our tempting menu, and we'll start crafting it immediately. No waiting required! Preparing your pizza right away, ensuring it's made to perfection with fresh ingredients and expert techniques</p>
            <a href="#menu" class="btn">Our menu</a>
         </div>

         <div class="box">
            <img src="images/dine-delivery.png" alt="">
            <h3>Serve Dine-In or for Delivery</h3>
            <p>Prefer to dine in? Visit our cozy restaurant ambiance, where you can savor your pizza hot out of the oven. If you're staying in, no worries! We offer convenient delivery options straight to your doorstep. </p>
            <a href="#menu" class="btn">Our menu</a>
         </div>

         <div class="box">
            <img src="images/Share with friends.png" alt="">
            <h3>Share with Friends</h3>
            <p>Pizza is best enjoyed with good company! Invite your friends over for a pizza party and share the joy of Paquito's delicious creations. With a variety of flavors to choose from. Let the good times and great pizza roll!</p>
            <a href="#menu" class="btn">Our menu</a>
         </div>

      </div>

   </section>
   <!-- about section ends -->

   <!-- Menu section -->
   <section id="menu" class="menu">
      <br><br>
      <h1 class="heading">Our Menu</h1>

      <?php
      // MySQLi query to retrieve all products
      $select_products_query = "SELECT * FROM `products`";
      $result_products = mysqli_query($conn, $select_products_query);

      // Check if any products exist
      if (mysqli_num_rows($result_products) > 0) {
         echo '<div class="box-container">';
         // Loop through each product and display it
         while ($fetch_products = mysqli_fetch_assoc($result_products)) {
      ?>

            <div class="box">
               <div class="price">₱<?= $fetch_products['price'] ?></div>
               <img src="uploaded_img/<?= $fetch_products['image'] ?>" alt="">
               <div class="name"><?= $fetch_products['name'] ?></div>

               <form action="" method="post">
                  <input type="hidden" name="pid" value="<?= $fetch_products['id'] ?>">
                  <input type="hidden" name="name" value="<?= $fetch_products['name'] ?>">
                  <input type="hidden" name="price" value="<?= $fetch_products['price'] ?>">
                  <input type="hidden" name="image" value="<?= $fetch_products['image'] ?>">
                  <input type="number" name="qty" class="qty" min="1" max="99" onkeypress="if(this.value.length == 2) return false;" value="1">
                  <input type="submit" class="btn" name="add_to_cart" value="Add to Cart">
               </form>
            </div>
      <?php
         }
         echo '</div>';
      } else {
         echo '<p class="empty">No products available at the moment!</p>';
      }
      ?>
   </section>


   <!-- filter menu ends -->

   <!-- order section starts  -->

   <section class="order" id="order">
      <br><br>
      <h1 class="heading">order now</h1>

      <form action="" method="post">

         <div class="display-orders">

            <?php
            $grand_total = 0;
            $cart_item = [];


            $select_cart_query = "SELECT * FROM `cart` WHERE user_id = ?";
            $stmt_cart = mysqli_prepare($conn, $select_cart_query);
            mysqli_stmt_bind_param($stmt_cart, 'i', $user_id);
            mysqli_stmt_execute($stmt_cart);
            $result_cart = mysqli_stmt_get_result($stmt_cart);

            if (mysqli_num_rows($result_cart) > 0) {
               while ($fetch_cart = mysqli_fetch_assoc($result_cart)) {

                  $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']);
                  $grand_total += $sub_total;


                  $cart_item[] = $fetch_cart['name'] . ' ( ' . $fetch_cart['price'] . ' x ' . $fetch_cart['quantity'] . ' ) - ';
               }

               $total_products = implode('', $cart_item);


               mysqli_data_seek($result_cart, 0);
               while ($fetch_cart = mysqli_fetch_assoc($result_cart)) {
                  echo '<p>' . $fetch_cart['name'] . ' <span>(' . $fetch_cart['price'] . ' x ' . $fetch_cart['quantity'] . ')</span></p>';
               }
            } else {
               echo '<p class="empty"><span>your cart is empty!</span></p>';
            }
            ?>


         </div>

         <div class="grand-total"> Grand Total : <span>₱<?= $grand_total; ?></span></div>

         <input type="hidden" name="total_products" value="<?= $total_products; ?>">
         <input type="hidden" name="total_price" value="<?= $grand_total; ?>">

         <div class="flex">
            <div class="inputBox">
               <span>Name :</span>
               <input type="text" name="name" class="box" required placeholder="Enter your name" maxlength="20">
            </div>
            <div class="inputBox">
               <span>Phone number :</span>
               <input type="number" name="number" class="box" required placeholder="Enter your number" min="0" max="9999999999" onkeypress="if(this.value.length == 10) return false;">
            </div>
            <div class="inputBox">
               <span>Payment Method</span>
               <select name="method" class="box">
                  <option value="Cash on delivery">Cash on delivery</option>
                  <option value="Credit card">Credit card</option>
                  <option value="gcash">Gcash</option>
                  <option value="Paypal">Paypal</option>
               </select>
            </div>
            <div class="inputBox">
               <span>Address line 01 :</span>
               <input type="text" name="flat" class="box" required placeholder="E.g. flat no." maxlength="50">
            </div>
            <div class="inputBox">
               <span>Address line 02 :</span>
               <input type="text" name="street" class="box" required placeholder="E.g. street name." maxlength="50">
            </div>
            <div class="inputBox">
               <span>Pin Code :</span>
               <input type="number" name="pin_code" class="box" required placeholder="E.g. 123456" min="0" max="999999" onkeypress="if(this.value.length == 6) return false;">
            </div>
         </div>

         <input type="submit" value="order now" class="btn" name="order">

      </form>

   </section>

   <!-- order section ends -->

   <!-- faq section starts  -->

   <section class="faq" id="faq">
      <br><br>
      <h1 class="heading">FAQ</h1>

      <div class="accordion-container">

         <div class="accordion active">
            <div class="accordion-heading">
               <span>What sets Paquito's Pizza apart from other pizza places?</span>
               <i class="fas fa-angle-down"></i>
            </div>
            <p class="accrodion-content">
               At Paquito's Pizza, we pride ourselves on our "Order to Bake" service. This means that once you place your order, we start crafting your pizza immediately, ensuring it's made to perfection with fresh ingredients and expert techniques. No waiting required!
            </p>
         </div>

         <div class="accordion">
            <div class="accordion-heading">
               <span>Can I enjoy Paquito's Pizza in the comfort of my own home?</span>
               <i class="fas fa-angle-down"></i>
            </div>
            <p class="accrodion-content">
               Absolutely! We offer convenient delivery options straight to your doorstep. So whether you're craving our delicious pizzas but prefer to dine in or want to enjoy them from the comfort of your own home, we've got you covered.
            </p>
         </div>

         <div class="accordion">
            <div class="accordion-heading">
               <span>Are there options for dining in at Paquito's Pizza?</span>
               <i class="fas fa-angle-down"></i>
            </div>
            <p class="accrodion-content">
               Yes, indeed! If you prefer to dine in, we invite you to visit our cozy restaurant ambiance. You can savor your pizza hot out of the oven while enjoying the friendly atmosphere of our establishment.
            </p>
         </div>

         <div class="accordion">
            <div class="accordion-heading">
               <span> Does Paquito's Pizza offer a variety of flavors to choose from?</span>
               <i class="fas fa-angle-down"></i>
            </div>
            <p class="accrodion-content">
               Definitely! We understand that pizza preferences vary, which is why we offer a wide variety of flavors on our menu. Whether you're a fan of classic pepperoni or prefer something more adventurous like our specialty gourmet pizzas, we have something to satisfy every craving.
            </p>
         </div>


         <div class="accordion">
            <div class="accordion-heading">
               <span>Can I customize my pizza with specific toppings or ingredients?</span>
               <i class="fas fa-angle-down"></i>
            </div>
            <p class="accrodion-content">
               Absolutely! We want you to enjoy your pizza just the way you like it. You can customize your pizza with a variety of toppings, sauces, and crust options from our menu. Whether you prefer classic combinations or want to get creative with your toppings, our customizable options ensure your pizza is tailored to your preferences. Just contact us for your preferred customization, and we'll be happy to accommodate your requests!
            </p>
         </div>

      </div>

   </section>

   <!-- faq section ends -->

   <!-- footer section starts  -->

   <section class="footer">

      <div class="box-container">

         <div class="box">
            <i class="fas fa-phone"></i>
            <h3>Phone number</h3>
            <p style="margin-bottom: 32px;color: #008C3B;">+63-961-783-2752</p>

         </div>

         <div class="box">
            <i class="fas fa-map-marker-alt"></i>
            <h3>Our Address</h3>
            <p><a href="https://maps.app.goo.gl/8KxHL53jjt171Ds69" style="color: #008C3B;">Poblacion West, Santa Maria, Pangasinan</a></p>
         </div>

         <div class="box">
            <i class="fas fa-clock"></i>
            <h3>Opening hours</h3>
            <p style="color: #008C3B;">08:00am to 05:30pm</p>
            <p style="color: #008C3B;">Mon - Sat</p>
         </div>

         <div class="box">
            <i class="fas fa-envelope"></i>
            <h3>Social Medias</h3>
            <div class="socials">
               <p><i class="social fab fa-facebook"></i><a href="https://www.facebook.com/paquitospizza?mibextid=ZbWKwL" style="color: #008C3B;">Paquitos Pizza</a></p>
               <p><i class="social fas fa-envelope"></i><a href="paquitos.pizza@gmail.com" style="color: #008C3B;">paquitos.pizza@gmail.com</a> </p>
            </div>

         </div>


      </div>

      <div class="credit">
         &copy; copyright @ <?= date('Y'); ?> <span> Paquito's Pizza. </span> | All rights reserved!
      </div>

   </section>

   <!-- footer section ends -->



   <!-- custom js file link  -->
   <script src="js/script.js"></script>


</body>

</html>