<?php
  session_start();
  $user_id = $_SESSION['user_id'];
  $username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  <?php
  echo "Xin chao $user_id.$username";
  ?>
</body>
</html>