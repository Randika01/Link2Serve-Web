<?php

include '../components/connect.php';

if(isset($_COOKIE['admin_id'])){
   $admin_id = $_COOKIE['admin_id'];
}else{
   $admin_id = '';
   header('location:login.php');
}

if(isset($_POST['delete'])){
   $delete_id = $_POST['delete_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Delete property functionality
   $verify_delete = $conn->prepare("SELECT * FROM `property` WHERE id = ?");
   $verify_delete->execute([$delete_id]);

   if($verify_delete->rowCount() > 0){
      $select_images = $conn->prepare("SELECT * FROM `property` WHERE id = ?");
      $select_images->execute([$delete_id]);
      while($fetch_images = $select_images->fetch(PDO::FETCH_ASSOC)){
         $image_01 = $fetch_images['image_01'];
         $image_02 = $fetch_images['image_02'];
         $image_03 = $fetch_images['image_03'];
         $image_04 = $fetch_images['image_04'];
         $image_05 = $fetch_images['image_05'];
         unlink('../uploaded_files/'.$image_01);
         if(!empty($image_02)){
            unlink('../uploaded_files/'.$image_02);
         }
         if(!empty($image_03)){
            unlink('../uploaded_files/'.$image_03);
         }
         if(!empty($image_04)){
            unlink('../uploaded_files/'.$image_04);
         }
         if(!empty($image_05)){
            unlink('../uploaded_files/'.$image_05);
         }
      }
      $delete_listings = $conn->prepare("DELETE FROM `property` WHERE id = ?");
      $delete_listings->execute([$delete_id]);

      $success_msg[] = 'Listing deleted!';
   }else{
      $warning_msg[] = 'Listing deleted already!';
   }

   // Delete job functionality
   $verify_delete_job = $conn->prepare("SELECT * FROM `job` WHERE id = ?");
   $verify_delete_job->execute([$delete_id]);

   if($verify_delete->rowCount() > 0){
      $select_images = $conn->prepare("SELECT * FROM `job` WHERE id = ?");
      $select_images->execute([$delete_id]);

      while($fetch_images = $select_images->fetch(PDO::FETCH_ASSOC)){
         $image_01 = $fetch_images['image_01'];
         unlink('../uploaded_files/'.$image_01);
         
      }

      $delete_listings = $conn->prepare("DELETE FROM `job` WHERE id = ?");
      $delete_listings->execute([$delete_id]);

      $success_msg[] = 'Job deleted!';
   }else{
      $warning_msg[] = 'Job deleted already!';
   }

   // Delete course functionality
   $verify_delete_course = $conn->prepare("SELECT * FROM `course` WHERE id = ?");
   $verify_delete_course->execute([$delete_id]);

   if($verify_delete->rowCount() > 0){
      $select_images = $conn->prepare("SELECT * FROM `course` WHERE id = ?");
      $select_images->execute([$delete_id]);

      while($fetch_images = $select_images->fetch(PDO::FETCH_ASSOC)){
         $image_01 = $fetch_images2['image_01'];
         unlink('../uploaded_files/'.$image_01);
         if(!empty($image_02)){
            unlink('../uploaded_files/'.$image_02);
         }
      }
      $delete_listings = $conn->prepare("DELETE FROM `course` WHERE id = ?");
      $delete_listings->execute([$delete_id]);

      $success_msg[] = 'Course deleted!';
   }else{
      $warning_msg[] = 'Course deleted already!';
   }

   // Delete bike functionality
   $verify_delete_bike = $conn->prepare("SELECT * FROM `bike` WHERE id = ?");
   $verify_delete_bike->execute([$delete_id]);

   if($verify_delete->rowCount() > 0){
      $select_images = $conn->prepare("SELECT * FROM `bike` WHERE id = ?");
      $select_images->execute([$delete_id]);

      while($fetch_images = $select_images->fetch(PDO::FETCH_ASSOC)){
         $image_01 = $fetch_images['image_01'];
         unlink('../uploaded_files/'.$image_01);
         
      }

      $delete_listings = $conn->prepare("DELETE FROM `bike` WHERE id = ?");
      $delete_listings->execute([$delete_id]);

      $success_msg[] = 'Bike deleted!';
   }else{
      $warning_msg[] = 'Bike deleted already!';
   }

   // Delete courier functionality
   $verify_delete_courier = $conn->prepare("SELECT * FROM `courier` WHERE id = ?");
   $verify_delete_courier->execute([$delete_id]);

   if($verify_delete->rowCount() > 0){
      $select_images = $conn->prepare("SELECT * FROM `courier` WHERE id = ?");
      $select_images->execute([$delete_id]);

      while($fetch_images = $select_images->fetch(PDO::FETCH_ASSOC)){
         $image_01 = $fetch_images['image_01'];
         unlink('../uploaded_files/'.$image_01);
         
      }

      $delete_listings = $conn->prepare("DELETE FROM `courier` WHERE id = ?");
      $delete_listings->execute([$delete_id]);

      $success_msg[] = 'Courier deleted!';
   }else{
      $warning_msg[] = 'Courier deleted already!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Listings</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include '../components/admin_header.php'; ?>
<!-- header section ends -->

<section class="listings">

   <h1 class="heading">All listings</h1>

   <form action="" method="POST" class="search-form">
      <input type="text" name="search_box" placeholder="search listings..." maxlength="100" required>
      <button type="submit" class="fas fa-search" name="search_btn"></button>
   </form>

   <h2 class="heading">Boarding Places</h2>
   <div class="box-container">

      <!-- Properties Listing -->

      <?php
      if(isset($_POST['search_box']) || isset($_POST['search_btn'])){
         $search_box = $_POST['search_box'];
         $search_box = filter_var($search_box, FILTER_SANITIZE_STRING);
         
         // Search properties
         $select_properties = $conn->prepare("SELECT * FROM `property` WHERE property_name LIKE '%{$search_box}%' OR address LIKE '%{$search_box}%' ORDER BY date DESC");
         $select_properties->execute();
      }else{
         $select_properties = $conn->prepare("SELECT * FROM `property` ORDER BY date DESC");
         $select_properties->execute();
      } 
      
      if($select_properties->rowCount() > 0){
         while($fetch_listing = $select_properties->fetch(PDO::FETCH_ASSOC)){

            $listing_id = $fetch_listing['id'];

            $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_user->execute([$fetch_listing['user_id']]);
            $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

            $total_images = 1 + (!empty($fetch_listing['image_02'])) + (!empty($fetch_listing['image_03'])) + (!empty($fetch_listing['image_04'])) + (!empty($fetch_listing['image_05']));
      ?>
  
         <div class="box">
            <div class="thumb">
               <p><i class="far fa-image"></i><span><?= $total_images; ?></span></p>
               <img src="../uploaded_files/<?= $fetch_listing['image_01']; ?>" alt="">
            </div>
            <p class="price">LKR<?= $fetch_listing['price']; ?></p>
            <h3 class="name"><?= $fetch_listing['property_name']; ?></h3>
            <p class="location"><i class="fas fa-map-marker-alt"></i><?= $fetch_listing['address']; ?></p>
            <p class="location"><i class="fa-regular fa-building"></i><span><?= $fetch_listing['university']; ?></span></p>
            <form action="" method="POST">
               <input type="hidden" name="delete_id" value="<?= $listing_id; ?>">
               <a href="view_property.php?get_id=<?= $listing_id; ?>" class="btn">view property</a>
               <input type="submit" value="delete listing" onclick="return confirm('Delete this listing?');" name="delete" class="delete-btn">
            </form>
         </div>
      <?php
         }
      }elseif(isset($_POST['search_box']) || isset($_POST['search_btn'])){
         echo '<p class="empty">No Boarding results found!</p>';
      }else{
         echo '<p class="empty">No Boardings posted yet!</p>';
      }
      ?>

   </div>

   



</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

<?php include '../components/message.php'; ?>

</body>
</html>
