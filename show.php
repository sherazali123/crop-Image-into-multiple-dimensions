<?php
include '_config.php';
$error = array();
 ?>
<!DOCTYPE html>
<html>
<head>
<?php include '_header.php'; ?>
</head>
<body>
<?php

  $filename = $_GET['file'];
  if (empty($filename)) {
      echo 'Not allowed to access.';
      die;
  }
  $dir = dirname(__FILE__);

  $rootPath = 'uploads/'.$filename.'/';
  $folders = preg_grep('/^([^.])/', scandir($rootPath));
  if (empty($folders)) {
      echo 'No file exists.';
      die;
  }
  rsort($folders);
  foreach ($folders as $folder) {
      $folderPath = $rootPath.$folder.'/';
      $files = preg_grep('/^([^.])/', scandir($folderPath));
      foreach ($files as $file) {
          ?>



<div class="container">
  <?php if (!empty($error)): ?>
    <?php foreach ($error as $e): ?>
    <div class="row">
      <div class="alert alert-danger"><?php echo $e;
          ?></div>
    </div>
    <?php endforeach;
          ?>
  <?php endif;
          ?>

  <div class="row text-center">


    <div class="panel panel-default">
  <div class="panel-heading">
    <?php
      if($folder === 'original'){
        ?>
        <span class="label label-info">ORIGINAL</span>
        <?php
      }
    ?>
    <span class="label label-default lab">File: <?php echo $file;
          ?></span>
    <span class="label label-primary lab">Dimensions(w x h): <?php echo $folder;
          ?></span>
    <span class="label label-success lab">View: <a href="<?php echo $baseUrl.$folderPath.$file;
          ?>" target="_blank">Click here to view</a></span>
  </div>
  <div class="panel-body">
      <img src="<?php echo $baseUrl.$folderPath.$file; ?>" />
  </div>
</div>

  </div>

</div>




<?php

      }
  }

 ?>
 <div class="row text-center">
 <a href="<?php echo $baseUrl.'index.php'; ?>" class="btn btn-primary text-center">Upload more</a></div>
	</body>
	</html>
