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
if(isset($_POST['delete'])){

   $delete_id = $_POST['job_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_delete = $conn->prepare("SELECT * FROM `job` WHERE id = ?");
   $verify_delete->execute([$delete_id]);

   if($verify_delete->rowCount() > 0){
      $select_images = $conn->prepare("SELECT * FROM `job` WHERE id = ?");
      $select_images->execute([$delete_id]);
      while($fetch_images = $select_images->fetch(PDO::FETCH_ASSOC)){
         $image_01 = $fetch_images['image_01'];
        
         unlink('uploaded_files/'.$image_01);
        
      }
    //   $delete_saved = $conn->prepare("DELETE FROM `saved` WHERE job_id = ?");
    //   $delete_saved->execute([$delete_id]);
    //   $delete_requests = $conn->prepare("DELETE FROM `requests` WHERE job_id = ?");
    //   $delete_requests->execute([$delete_id]);
      $delete_listing = $conn->prepare("DELETE FROM `job` WHERE id = ?");
      $delete_listing->execute([$delete_id]);
      $success_msg[] = 'listing deleted successfully!';
   }else{
      $warning_msg[] = 'listing deleted already!';
   }

}
include '../components/save_send.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>View Job</title>

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

   <h1 class="heading">Job Details</h1>

   <?php
      $select_jobs = $conn->prepare("SELECT * FROM `job` WHERE id = ? ORDER BY date DESC LIMIT 1");
      $select_jobs->execute([$get_id]);
      if($select_jobs->rowCount() > 0){
         while($fetch_job = $select_jobs->fetch(PDO::FETCH_ASSOC)){

         $job_id = $fetch_job['id'];

         $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
         $select_user->execute([$fetch_job['user_id']]);
         $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

         $select_saved = $conn->prepare("SELECT * FROM `saved` WHERE job_id = ? and user_id = ?");
         $select_saved->execute([$fetch_job['id'], $user_id]);

         // Function to format address into Google Maps URL
         function format_address_for_maps($address) {
            $address = str_replace(" ", "+", $address);
            return "https://www.google.com/maps/search/?api=1&query=" . $address;
         }
            $google_maps_url = format_address_for_maps($fetch_job['address']);


   ?>
   <div class="details">
     <div class="swiper images-container">
         <div class="swiper-wrapper">
            <img src="../uploaded_files/<?= $fetch_job['image_01']; ?>" alt="" class="swiper-slide">
            
         </div>
         <div class="swiper-pagination"></div>
     </div>
      <h3 class="name"><?= $fetch_job['title']; ?></h3>
      <p class="location"><i class="fas fa-map-marker-alt"></i><a href="<?= $google_maps_url ?>" target="_blank"><?= $fetch_job['address']; ?></a></p>
      <div class="info">
         <p><i class="fas fa-user"></i><span><?= $fetch_user['name']; ?></span></p>
         <p><i class="fas fa-phone"></i><a href="tel:<?= $fetch_user['number']; ?>"><?= $fetch_user['number']; ?></a></p>
         <p><i class="far fa-clock"></i><span><?= $fetch_job['time']; ?></span></p>
         <p><i class="fas fa-calendar"></i><span><?= $fetch_job['date']; ?></span></p>
      </div>
      <h3 class="title">Job Details</h3>
      <div class="flex">
         <div class="box">
            <p><i>Qualification:</i><span><?= $fetch_job['qualification']; ?></span></p>
            <p><i>Skill:</i><span><?= $fetch_job['skill']; ?></span></p>
            <p><i>University:</i><span><?= $fetch_job['university']; ?></span></p>
         </div>
         <div class="box">
            <p><i>Closest Premises:</i><span><?= $fetch_job['premises']; ?></span></p>
            <p><i>Distance from University:</i><span><?= $fetch_job['distance']; ?> km</span></p>
         </div>
         <div class="box">
            <p><i>Vacancy:</i><span><?= $fetch_job['vacancy']; ?></span></p>
            <p><i>Age:</i><span><?= $fetch_job['age']; ?></span></p>
            <p><i>Contact:</i><a href="mailto:<?= $fetch_job['contact']; ?>"><?= $fetch_job['contact']; ?></a></p>
         </div>
      </div>
      <h3 class="title">Salary and Internship</h3>
      <div class="flex">
         <div class="box">
            <p><i>Salary:</i><span><?= !empty($fetch_job['salary']) ? $fetch_job['salary'] : 'Not specified'; ?></span></p>
         </div>
         <div class="box">
            <p><i>Internship:</i><span><?= $fetch_job['internship'] == 'yes' ? 'Available' : 'Not Available'; ?></span></p>
         </div>
      </div>
      <form action="" method="post" class="flex-btn">
   <input type="hidden" name="job_id" value="<?= $job_id; ?>">

   <?php if($fetch_job['user_id'] != $user_id) { ?>
      <!-- Show Save button for other users' listings -->
      <?php if($select_saved->rowCount() > 0){ ?>
         <button type="submit" name="save" class="save"><i class="fas fa-heart"></i><span>Saved</span></button>
      <?php } else { ?>
         <button type="submit" name="save" class="save"><i class="far fa-heart"></i><span>Save</span></button>
      <?php } ?>
      
      <!-- Show Send Request button for other users' listings -->
      <input type="submit" value="Send Request" name="send" class="btn">
      <?php } else { ?>
      <a href="update_job.php?job_id=<?= $job_id; ?>" class="btn">Update</a>
      <button type="submit" name="delete" class="btn" onclick="return confirm('Are you sure you want to delete this job?');">Delete</button>
   <?php } ?>
</form>


   </div>
   <?php
         }
      }else{
         echo '<p class="empty">Job not found! <a href="job.php" style="margin-top:1.5rem;" class="btn">Add New</a></p>';
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
