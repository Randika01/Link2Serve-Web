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
   <title>View Course</title>

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

   <h1 class="heading">Course Details</h1>

   <?php
      $select_courses = $conn->prepare("SELECT * FROM `course` WHERE id = ? ORDER BY date DESC LIMIT 1");
      $select_courses->execute([$get_id]);
      if($select_courses->rowCount() > 0){
         while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){

         $course_id = $fetch_course['id'];

         $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
         $select_user->execute([$fetch_course['user_id']]);
         $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

         $select_saved = $conn->prepare("SELECT * FROM `saved` WHERE course_id = ? and user_id = ?");
         $select_saved->execute([$fetch_course['id'], $user_id]);

         // Function to format address into Google Maps URL
         function format_address_for_maps($address) {
            $address = str_replace(" ", "+", $address);
            return "https://www.google.com/maps/search/?api=1&query=" . $address;
         }
            $google_maps_url = format_address_for_maps($fetch_course['address']);


   ?>
   <div class="details">
     <div class="swiper images-container">
         <div class="swiper-wrapper">
            <img src="../uploaded_files/<?= $fetch_course['image_01']; ?>" alt="" class="swiper-slide">
            <?php if(!empty($fetch_course['image_02'])){ ?>
            <img src="../uploaded_files/<?= $fetch_course['image_02']; ?>" alt="" class="swiper-slide">
            <?php } ?>
         </div>
         <div class="swiper-pagination"></div>
     </div>
      <h3 class="name"><?= $fetch_course['course_name']; ?></h3>
      <p class="location"><i class="fas fa-map-marker-alt"></i><a href="<?= $google_maps_url ?>" target="_blank"><?= $fetch_course['address']; ?></a></p>
      <div class="info">
         <p><i class="fas fa-user"></i><span><?= $fetch_user['name']; ?></span></p>
         <p><i class="fas fa-phone"></i><a href="tel:<?= $fetch_user['number']; ?>"><?= $fetch_user['number']; ?></a></p>
         <p><i class="far fa-clock"></i><span><?= $fetch_course['duration']; ?></span></p>
         <p><i class="fas fa-calendar"></i><span><?= $fetch_course['date']; ?></span></p>
      </div>
      <h3 class="title">Course Details</h3>
      <div class="flex">
         <div class="box">
            <p><i>Institute:</i><span><?= $fetch_course['institute']; ?></span></p>
            <p><i>University:</i><span><?= $fetch_course['university']; ?></span></p>
            <p><i>Fees:</i><span><?= $fetch_course['fees']; ?></span></p>
         </div>
         <div class="box">
            <p><i>Closest Premises:</i><span><?= $fetch_course['premises']; ?></span></p>
            <p><i>Distance from University:</i><span><?= $fetch_course['distance']; ?> km</span></p>
         </div>
         <div class="box">
            <p><i>Pre-requisites:</i><span><?= $fetch_course['prerequisites']; ?></span></p>
            <p><i>Scheduling:</i><span><?= $fetch_course['scheduling']; ?></span></p>
            <p><i>Contact:</i><a href="mailto:<?= $fetch_course['contact_information']; ?>"><?= $fetch_course['contact_information']; ?></a></p>
         </div>
      </div>
      
      <div class="flex">
         
         <div class="box">
            <p><i>Certificate:</i><span><?= $fetch_course['certificate'] == 'yes' ? 'Will be Provided' : 'Will not be Provided'; ?></span></p>
         </div>
      </div>
            <form action="" method="post" class="flex-btn">
               <input type="hidden" name="course_id" value="<?= $course_id; ?>">
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
               <?php if($fetch_course['user_id'] != $user_id) { ?>
                  <input type="submit" value="Send Request" name="send" class="btn">
               <?php } ?>
            </form>

   </div>
   <?php
         }
      }else{
         echo '<p class="empty">Courses not found! <a href="course.php" style="margin-top:1.5rem;" class="btn">Add New</a></p>';
      }
   ?>

</section>

<!-- View job section ends -->

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
