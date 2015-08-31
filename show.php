<?php
include '_config.php';
$_dty_error = array();
 ?>
<!DOCTYPE html>
<html>
<head>
<?php include '_header.php'; ?>
</head>
<body class="show">
  <div class="row text-center">
  <a href="<?php echo $_dty_baseUrl.'index.php'; ?>" class="btn btn-primary text-center">Upload more</a></div>
<div class="container">
  <?php if (!empty($_dty_error)): ?>
    <?php foreach ($_dty_error as $e): ?>
    <div class="row">
      <div class="alert alert-danger">
        <?php echo $e; ?>
      </div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>
<?php
  // get file name from the url
  $_dty_filename = $_GET['file'];
  // get width and height to show required images
  $_dty_width = $_GET['w'];
  $_dty_height = $_GET['h'];

  // width and height in the url are required
  if (empty($_dty_filename) || empty($_dty_width) || empty($_dty_height)) {
      echo 'Not allowed to access.';
      die;
  }
  // must be numbers
  if (!is_numeric($_dty_width) || !is_numeric($_dty_height)) {
      echo 'Invalid input';
      die;
  }
  $dir = dirname(__FILE__);

  // start creating array for cropped images to display
  $_dty_imagesSet = array();
  // here is the original
  $_dty_imagesSet[] = array('width' => $_dty_width, 'height' => $_dty_height, 'original' => true , 'link' => $_dty_baseUrl.'original/'.$_dty_filename);

  // show by multiple of 100
  $_dty_minus = 100;

  // max width and height
  $_dty_maxWidth = 2000;
  $_dty_maxHeights = 2000;
  if ($_dty_width > $_dty_maxWidth) {
      $_dty_width = 2000;
  }
  if ($_dty_height > $_dty_maxHeights) {
      $_dty_height = 2000;
  }
  // how many images according to width
  $_dty_iterateWidthXTimes = (int) $_dty_width / 100;
  // how many images according to height
  $_dty_iterateHeightXTimes = (int) $_dty_height / 100;

  // if not multiple of 100
  if ($_dty_width % 100 > 0) {
      $_dty_tempWidth = $_dty_width - ($_dty_width % 100);
  } else {
      $_dty_tempWidth = $_dty_width;
  }
  for ($i = 0; $i < $_dty_iterateWidthXTimes; ++$i) {
      // if not multiple of 100
      if ($_dty_height % 100 > 0) {
          $_dty_tempHeight = $_dty_height - ($_dty_height % 100);
      } else {
          $_dty_tempHeight = $_dty_height;
      }
      for ($j = 0; $j < $_dty_iterateHeightXTimes; ++$j) {
          if ($_dty_tempWidth >= 100 && $_dty_tempHeight >= 100) {
              $_dty_imagesSet[] = array('width' => $_dty_tempWidth, 'height' => $_dty_tempHeight, 'original' => false, 'link' => $_dty_baseUrl.$_dty_tempWidth.'x'.$_dty_tempHeight.'/'.$_dty_filename);
              // create requierd w/h dir if doesnt exists
              if (!file_exists($_dty_tempWidth.'x'.$_dty_tempHeight.'/')) {
                  mkdir($_dty_tempWidth.'x'.$_dty_tempHeight.'/', 0777, true);
              }
          }
          $_dty_tempHeight = $_dty_tempHeight - $_dty_minus;
      }
      $_dty_tempWidth = $_dty_tempWidth - $_dty_minus;
  }

      foreach ($_dty_imagesSet as $file) {
          ?>
<div class="container">
  <div class="row text-center">
      <div class="panel panel-default">
        <div class="panel-heading">
          <?php if ($file['original'] === true) { ?>
              <span class="label label-info">ORIGINAL</span>
          <?php } ?>
              <span class="label label-default lab">File: <?php echo $_dty_filename;   ?></span>
              <span class="label label-primary lab">Dimensions(w x h): <?php echo $file['width'].' x '.$file['height'];   ?></span>
              <span class="label label-success lab">View: <a href="<?php echo $file['link']; ?>" target="_blank">Click here to view</a></span>
        </div>
        <div class="panel-body">
              <img src="<?php echo $file['link'];?>" />
        </div>
    </div>
  </div>
</div>
<?php } ?>
 <div class="row text-center">
 <a href="<?php echo $_dty_baseUrl.'index.php'; ?>" class="btn btn-primary text-center">Upload more</a></div>
</body>
</html>
