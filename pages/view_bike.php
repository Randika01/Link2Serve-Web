<?php

include '../components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   header('location:filtersearch.php');
}

include '../components/save_send.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>View Bike</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="../css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<!-- View Bike section starts  -->

<section class="view-property">

   <h1 class="heading">Bike Details</h1>

   <?php
      $select_bikes = $conn->prepare("SELECT * FROM `bike` WHERE id = ? ORDER BY date DESC LIMIT 1");
      $select_bikes->execute([$get_id]);
      if($select_bikes->rowCount() > 0){
         while($fetch_bike = $select_bikes->fetch(PDO::FETCH_ASSOC)){

         $bike_id = $fetch_bike['id'];

         $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
         $select_user->execute([$fetch_bike['user_id']]);
         $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

         $select_saved = $conn->prepare("SELECT * FROM `saved` WHERE bike_id = ? and user_id = ?");
         $select_saved->execute([$fetch_bike['id'], $user_id]);

         // Function to format address into Google Maps URL
         function format_address_for_maps($address) {
            $address = str_replace(" ", "+", $address);
            return "https://www.google.com/maps/search/?api=1&query=" . $address;
         }
            $google_maps_url = format_address_for_maps($fetch_bike['address']);


   ?>
   <div class="details">
     <div class="swiper images-container">
         <div class="swiper-wrapper">
            <img src="../uploaded_files/<?= $fetch_bike['image_01']; ?>" alt="" class="swiper-slide">
            
         </div>
         <div class="swiper-pagination"></div>
     </div>
      <h3 class="name"><?= $fetch_bike['bike_model']; ?></h3>
      <p class="location"><i class="fas fa-map-marker-alt"></i><a href="<?= $google_maps_url ?>" target="_blank"><?= $fetch_bike['address']; ?></a></p>
      <div class="info">
         <p><i class="fas fa-user"></i><span><?= $fetch_user['name']; ?></span></p>
         <p><i class="fas fa-phone"></i><a href="tel:<?= $fetch_user['number']; ?>"><?= $fetch_user['number']; ?></a></p>
         <p><i class="far fa-clock"></i><span><?= $fetch_bike['year']; ?></span></p>
         <p><i class="fas fa-calendar"></i><span><?= $fetch_bike['date']; ?></span></p>
      </div>
      <h3 class="title">Bike Details</h3>
      <div class="flex">
         <div class="box">
            <p><i>Bike model:</i><span><?= $fetch_bike['bike_model']; ?></span></p>
            <p><i>Bike number:</i><span><?= $fetch_bike['bike_number']; ?></span></p>
            <p><i>University:</i><span><?= $fetch_bike['university']; ?></span></p>
         </div>
         <div class="box">
            <p><i>Premises:</i><span><?= $fetch_bike['premises']; ?></span></p>
            <p><i>Distance:</i><span><?= $fetch_bike['distance']; ?> km</span></p>
         </div>
         <div class="box">
            <p><i>Year:</i><span><?= $fetch_bike['year']; ?></span></p>
            <p><i>Address:</i><span><?= $fetch_bike['address']; ?></span></p>
            <p><i>Location:</i><a href="mailto:<?= $fetch_bike['location']; ?>"><?= $fetch_bike['location']; ?></a></p>
         </div>
         <div class="box">
            <p><i>Date:</i><span><?= $fetch_bike['date']; ?></span></p>
            <p><i>Type:</i><span><?= $fetch_bike['type']; ?></span></p>
         </div>
      </div>
      
      <div class="flex">
         <div class="box">
            <p><i>Email:</i><span><?= !empty($fetch_bike['email']) ? $fetch_bike['email'] : 'Not specified'; ?></span></p>
         </div>
         <div class="box">
         <p><i>Price: </i>LKR <span><?= $fetch_bike['price']; ?></span></p>
         </div>
      </div>
            <form action="" method="post" class="flex-btn">
               <input type="hidden" name="bike_id" value="<?= $bike_id; ?>">
               <?php
                  if($select_saved->rowCount() > 0){
               ?>
               <button type="submit" name="save" class="save"><i class="fas fa-heart"></i><span>Saved</span></button>
               <?php
                  }else{ 
               ?>
               <button type="submit" name="save" class="save"><i class="far fa-heart"></i><span>Save</span></button>
               <?php
                  }
               ?>
               <?php if($fetch_bike['user_id'] != $user_id) { ?>
                  <input type="submit" value="Send Request" name="send" class="btn">
               <?php } ?>
            </form>

   </div>
   <?php
         }
      }else{
         echo '<p class="empty">Bike not found! <a href="bike.php" style="margin-top:1.5rem;" class="btn">Add New</a></p>';
      }
   ?>

</section>

<!-- View bike section ends -->

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<?php include '../components/footer.php'; ?>

<!-- Custom JS file link -->
<script src="../js/script.js"></script>

<?php include '../components/message.php'; ?>



</body>
</html>
