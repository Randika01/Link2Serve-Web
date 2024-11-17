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
   <title>View Courier</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="../css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<!-- View job section starts  -->

<section class="view-property">

   <h1 class="heading">Courier Details</h1>

   <?php
      $select_couriers = $conn->prepare("SELECT * FROM `courier` WHERE id = ? ORDER BY date DESC LIMIT 1");
      $select_couriers->execute([$get_id]);
      if($select_couriers->rowCount() > 0){
         while($fetch_courier = $select_couriers->fetch(PDO::FETCH_ASSOC)){

         $courier_id = $fetch_courier['id'];

         $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
         $select_user->execute([$fetch_courier['user_id']]);
         $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

         $select_saved = $conn->prepare("SELECT * FROM `saved` WHERE courier_id = ? and user_id = ?");
         $select_saved->execute([$fetch_courier['id'], $user_id]);

         // Function to format address into Google Maps URL
         function format_address_for_maps($address) {
            $address = str_replace(" ", "+", $address);
            return "https://www.google.com/maps/search/?api=1&query=" . $address;
         }
            $google_maps_url = format_address_for_maps($fetch_courier['courier_address']);


   ?>
   <div class="details">
     <div class="swiper images-container">
         <div class="swiper-wrapper">
            <img src="../uploaded_files/<?= $fetch_courier['image_01']; ?>" alt="" class="swiper-slide">
            <?php if(!empty($fetch_courier['image_02'])){ ?>
            <img src="../uploaded_files/<?= $fetch_courier['image_02']; ?>" alt="" class="swiper-slide">
            <?php } ?>
         </div>
         <div class="swiper-pagination"></div>
     </div>
      <h3 class="name"><?= $fetch_courier['courier_name']; ?></h3>
      <p class="location"><i class="fas fa-map-marker-alt"></i><a href="<?= $google_maps_url ?>" target="_blank"><?= $fetch_courier['courier_address']; ?></a></p>
      <div class="info">
         <p><i class="fas fa-user"></i><span><?= $fetch_user['name']; ?></span></p>
         <p><i class="fas fa-phone"></i><a href="tel:<?= $fetch_user['number']; ?>"><?= $fetch_user['number']; ?></a></p>
         <p><i class="far fa-clock"></i><span><?= $fetch_courier['date']; ?></span></p>
         <p><i class="fa-regular fa-building"></i><span><?= $fetch_courier['university']; ?></span></p>
      </div>
      <h3 class="title">Courier Details</h3>
      <div class="flex">
         <div class="box">
            <p><i>Courier service:</i><span><?= $fetch_courier['courier_name']; ?></span></p>
            <p><i>University:</i><span><?= $fetch_courier['university']; ?></span></p>
            <p><i>Closest Premises:</i><span><?= $fetch_courier['premises']; ?></span></p>
            
         </div>
         <div class="box">
            <p><i>Tel. No:</i><span><?= $fetch_courier['phone_number']; ?></span></p>
            <p><i>Contact:</i><a href="mailto:<?= $fetch_courier['email_address']; ?>"><?= $fetch_courier['email_address']; ?></a></p>
         </div><br>

         <div class="box">
            <p><i>Description:</i><span><?= $fetch_courier['description']; ?></span></p>
         </div>
         
      </div>
      
      <div class="flex">
         
         <div class="box">
            <h1>Transport by:</h1>
            <p><i>Bike:</i><span><?= $fetch_courier['bike'] == 'yes' ? 'Available' : 'Not Available'; ?></span></p>
            <p><i>Bus:</i><span><?= $fetch_courier['bus'] == 'yes' ? 'Available' : 'Not Available'; ?></span></p>
            <p><i>Car:</i><span><?= $fetch_courier['car'] == 'yes' ? 'Available' : 'Not Available'; ?></span></p>
            <p><i>Three Wheel:</i><span><?= $fetch_courier['three_weel'] == 'yes' ? 'Available' : 'Not Available'; ?></span></p>
         </div>
         <div class="box">
            <h1>Work time:</h1>
            <p><i>Day:</i><span><?= $fetch_courier['day'] == 'yes' ? 'Available' : 'Not Available'; ?></span></p>
            <p><i>Night:</i><span><?= $fetch_courier['night'] == 'yes' ? 'Available' : 'Not Available'; ?></span></p>
            <p><i>Anytime:</i><span><?= $fetch_courier['anytime'] == 'yes' ? 'Available' : 'Not Available'; ?></span></p>
         </div>
      </div>
            <form action="" method="post" class="flex-btn">
               <input type="hidden" name="courier_id" value="<?= $courier_id; ?>">
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
               <?php if($fetch_courier['user_id'] != $user_id) { ?>
                  <input type="submit" value="Send Request" name="send" class="btn">
               <?php } ?>
            </form>

   </div>
   <?php
         }
      }else{
         echo '<p class="empty">Couriers not found! <a href="courier.php" style="margin-top:1.5rem;" class="btn">Add New</a></p>';
      }
   ?>

</section>

<!-- View courier section ends -->

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<?php include '../components/footer.php'; ?>

<!-- Custom JS file link -->
<script src="../js/script.js"></script>

<?php include '../components/message.php'; ?>

<script>

var swiper = new Swiper(".images-container", {
   effect: "coverflow",
   grabCursor: true,
   centeredSlides: true,
   slidesPerView: "auto",
   loop:true,
   coverflowEffect: {
      rotate: 0,
      stretch: 0,
      depth: 200,
      modifier: 3,
      slideShadows: true,
   },
   pagination: {
      el: ".swiper-pagination",
   },
});

</script>

</body>
</html>
