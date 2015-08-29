<?php
include '_config.php';
$error = array();
 ?>
<!DOCTYPE html>
<html>
<head>
<?php include '_header.php'; ?>
</head>
<body class="show">
  <div class="row text-center">
  <a href="<?php echo $baseUrl.'index.php'; ?>" class="btn btn-primary text-center">Upload more</a></div>
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
          </div>
<?php

  $filename = $_GET['file'];
  $width = $_GET['w'];
  $height = $_GET['h'];
  if (empty($filename) || empty($width) || empty($height)) {
      echo 'Not allowed to access.';
      die;
  }
  if (!is_numeric($width) || !is_numeric($height)) {
      echo 'Invalid input';
      die;
  }
  $dir = dirname(__FILE__);

  $imagesSet = array();
  $imagesSet[] = array('width' => $width, 'height' => $height, 'original' => true , 'link' => $baseUrl.'original/'.$filename);

  $minus = 100;
  $maxWidth = 2000;
  $maxHeights = 2000;
  if ($width > $maxWidth) {
      $width = 2000;
  }
  if ($height > $maxHeights) {
      $height = 2000;
  }
  $iterateWidthXTimes = (int) $width / 100;

  $iterateHeightXTimes = (int) $height / 100;
  if ($width % 100 > 0) {
      $tempWidth = $width - ($width % 100);
  } else {
      $tempWidth = $width;
  }
  for ($i = 0; $i < $iterateWidthXTimes; ++$i) {
      if ($height % 100 > 0) {
          $tempHeight = $height - ($height % 100);
      } else {
          $tempHeight = $height;
      }
      for ($j = 0; $j < $iterateHeightXTimes; ++$j) {
          if ($tempWidth >= 100 && $tempHeight >= 100) {
              $imagesSet[] = array('width' => $tempWidth, 'height' => $tempHeight, 'original' => false, 'link' => $baseUrl.$tempWidth.'x'.$tempHeight.'/'.$filename);
              if (!file_exists($tempWidth.'x'.$tempHeight.'/')) {
                  mkdir($tempWidth.'x'.$tempHeight.'/', 0777, true);
              }
          }
          $tempHeight = $tempHeight - $minus;
      }
      $tempWidth = $tempWidth - $minus;
  }

      foreach ($imagesSet as $file) {
          ?>



<div class="container">


  <div class="row text-center">


    <div class="panel panel-default">
  <div class="panel-heading">
    <?php
      if ($file['original'] === true) {
          ?>
        <span class="label label-info">ORIGINAL</span>
        <?php

      }
          ?>
    <span class="label label-default lab">File: <?php echo $filename;   ?></span>
    <span class="label label-primary lab">Dimensions(w x h): <?php echo $file['width'].' x '.$file['height'];   ?></span>
    <span class="label label-success lab">View: <a href="<?php echo $file['link']; ?>" target="_blank">Click here to view</a></span>

  </div>
  <div class="panel-body">
        <img src="<?php echo $file['link'];?>" />
  </div>
</div>

  </div>

</div>




<?php

      }

 ?>
 <div class="row text-center">
 <a href="<?php echo $baseUrl.'index.php'; ?>" class="btn btn-primary text-center">Upload more</a></div>
	</body>
	</html>
