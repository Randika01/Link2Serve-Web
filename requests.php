<?php  
include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
   header('location:login.php');
}

if(isset($_POST['delete'])){
   $delete_id = $_POST['request_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_delete = $conn->prepare("SELECT * FROM `requests` WHERE id = ?");
   $verify_delete->execute([$delete_id]);

   if($verify_delete->rowCount() > 0){
      $delete_request = $conn->prepare("DELETE FROM `requests` WHERE id = ?");
      $delete_request->execute([$delete_id]);
      $success_msg[] = 'Request deleted successfully!';
   }else{
      $warning_msg[] = 'Request deleted already!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible"="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Requests</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="requests">

   <h1 class="heading">All Requests</h1>

   <div class="box-container">

   <?php
      $select_requests = $conn->prepare("SELECT * FROM `requests` WHERE receiver = ?");
      $select_requests->execute([$user_id]);
      if($select_requests->rowCount() > 0){
         while($fetch_request = $select_requests->fetch(PDO::FETCH_ASSOC)){

            $select_sender = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_sender->execute([$fetch_request['sender']]);
            $fetch_sender = $select_sender->fetch(PDO::FETCH_ASSOC);

            if($fetch_sender) {
                if(isset($fetch_request['property_id']) && !empty($fetch_request['property_id'])) {
                    $select_property = $conn->prepare("SELECT * FROM `property` WHERE id = ?");
                    $select_property->execute([$fetch_request['property_id']]);
                    $fetch_property = $select_property->fetch(PDO::FETCH_ASSOC);

                    if($fetch_property) {
                        $listing_name = $fetch_property['property_name'];
                        $view_url = "view_property.php?get_id=" . $fetch_property['id'];
                    } else {
                        $listing_name = 'Property not found';
                        $view_url = '#';
                    }
                }
   ?>
   <div class="box">
      <p>Name : <span><?= htmlspecialchars($fetch_sender['name']); ?></span></p>
      <p>Number : <a href="tel:<?= htmlspecialchars($fetch_sender['number']); ?>"><?= htmlspecialchars($fetch_sender['number']); ?></a></p>
      <p>Email : <a href="mailto:<?= htmlspecialchars($fetch_sender['email']); ?>"><?= htmlspecialchars($fetch_sender['email']); ?></a></p>
      <p>Request for : <span><?= htmlspecialchars($listing_name); ?></span></p>
      <form action="" method="POST">
         <input type="hidden" name="request_id" value="<?= htmlspecialchars($fetch_request['id']); ?>">
         <input type="submit" value="Delete Request" class="btn" onclick="return confirm('Remove this request?');" name="delete">
         <a href="<?= htmlspecialchars($view_url); ?>" class="btn">View Listing</a>
         <a href="pages/chat.php?request_id=<?= $fetch_request['id']; ?>" class="btn">Chat</a>
      </form>
   </div>
   <?php
                
            }
        }
    } else {
        echo '<p class="empty">You have no requests!</p>';
    }
   ?>

   </div>

</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<?php include 'components/message.php'; ?>

</body>
</html>
