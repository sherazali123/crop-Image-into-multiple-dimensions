<?php
include('_config.php');
$error = array();
 ?>
<!DOCTYPE html>
<html>
<head>
<?php include('_header.php'); ?>
</head>
<body>

<?php
// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(-1);
$unique_id = strtotime("now");
$baseUrl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI'].'?').'/';
$uploaded = false;
if (!empty($_FILES['fileToUpload']['name'])) {
    $filename = basename($_FILES['fileToUpload']['name']);
    $filenameWithoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);
    $folderName = $unique_id.'_'.$filenameWithoutExt;
    $target_dir = $root_dir = '/';
    // $target_dir = $root_dir = 'uploads/'.$folderName.'/';
    // if (!file_exists($target_dir)) {
    //     mkdir($target_dir, 0777, true);
    // }
// find dimensions
  list($width, $height, $type, $attr) = getimagesize($_FILES['fileToUpload']['tmp_name']);

    $target_dir = 'original/';
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
        $imagesSet = array();
        $imagesSet[] = array('width' => $width, 'height' => $height, 'original' => true);

    $target_file = $target_dir.$filename;
    $uploadOk = 1;
    $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
    $imageFileType = strtolower($imageFileType);
// Check if image file is a actual image or fake image
if (isset($_POST['submit'])) {
    $check = getimagesize($_FILES['fileToUpload']['tmp_name']);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $error[] =  'File is not an image.';
        $uploadOk = 0;
    }
}
// Check if file already exists
// if (file_exists($target_file)) {
//     $error[] = 'Sorry, file already exists.';
//     $uploadOk = 0;
// }

// Allow certain file formats
if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg') {
    $error[] = 'Sorry, only JPG, JPEG, PNG files are allowed.';
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    $error[] =  'Sorry, your file was not uploaded.';
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target_file)) {
        $minus = 100;
        $maxWidth = 2000;
        $maxHeights = 2000;
        if($width > $maxWidth)
        {
          $width = 2000;
        }
        if($height > $maxHeights)
        {
          $height = 2000;
        }
        $iterateWidthXTimes = (int) $width / 100;

        $iterateHeightXTimes = (int) $height / 100;
        if($width%100 > 0){
          $tempWidth = $width - ($width%100);
        } else {
          $tempWidth = $width;
        }
        for ($i=0; $i < $iterateWidthXTimes; $i++) {
          if($height%100 > 0){
            $tempHeight = $height - ($height%100);
          } else {
            $tempHeight = $height;
          }
          for ($j = 0; $j < $iterateHeightXTimes; ++$j) {

              if ($tempWidth >= 100 && $tempHeight >= 100) {
                  $imagesSet[] = array('width' => $tempWidth, 'height' => $tempHeight, 'original' => false);
                  if(!file_exists($tempWidth.'x'.$tempHeight.'/')){
                    mkdir($tempWidth.'x'.$tempHeight.'/', 0777, true);
                  }
              }
              $tempHeight = $tempHeight - $minus;
          }
          $tempWidth = $tempWidth - $minus;
        }

        // var_dump($imagesSet);die;
        $uploaded = true;
    } else {
        echo 'Sorry, there was an error uploading your file.';
    }
}
}
    ?>
<div class="container">
  <?php if (!empty($error)): ?>
    <?php foreach ($error as $e): ?>
    <div class="row">
      <div class="alert alert-danger"><?php echo $e; ?></div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
	<?php if ($uploaded) : ?>
    <div class="row text-center">
	<?php foreach ($imagesSet as $image): ?>
		<form style=" text-align: -webkit-center;" action="http://www.google.com" method="POST" class="customizeImagesForms"  id="image_<?php echo $image['width']; ?>_<?php echo $image['height']; ?>">
    	<p><span class="label label-succcess"><?php echo $image['width']. ' x '.$image['height']; ?></span></p>
      <img class="customImg" customWidth="<?php echo $image['width']; ?>" customHeight="<?php echo $image['height']; ?>" original="<?php echo $image['original']; ?>" src="<?php echo $target_file; ?>" />
      <?php if (!$image['original']): ?>
        <input type="hidden" value="<?php echo $width; ?>" name="originalWidth" />
        <input type="hidden" value="<?php echo $height; ?>" name="originalHeight" />
        <input type="hidden" value="<?php echo $target_file; ?>" name="imageSource" />
        <input type="hidden" value="<?php echo $root_dir.$image['width'].'x'.$image['height'].'/'; ?>" name="imageDest" />
        <input type="hidden" value="<?php echo $filename; ?>" name="filename" />
        <input type="hidden" value="0" id="crop_<?php echo $image['width']; ?>_<?php echo $image['height']; ?>_x" name="_x" />
        <input type="hidden" value="0" id="crop_<?php echo $image['width']; ?>_<?php echo $image['height']; ?>_y" name="_y" />
        <input type="hidden" value="0" id="crop_<?php echo $image['width']; ?>_<?php echo $image['height']; ?>_w" name="_w" />
        <input type="hidden" value="0" id="crop_<?php echo $image['width']; ?>_<?php echo $image['height']; ?>_h" name="_h" />
        <input type="hidden" value="0" id="crop_<?php echo $image['width']; ?>_<?php echo $image['height']; ?>_rotate" name="rotate" />
        <input type="hidden" value="0" id="crop_<?php echo $image['width']; ?>_<?php echo $image['height']; ?>_scaleX" name="scaleX" />
        <input type="hidden" value="0" id="crop_<?php echo $image['width']; ?>_<?php echo $image['height']; ?>_scaleY" name="scaleY" />
    <?php endif; ?>
		</form>
	 <?php endforeach; ?>
   <button id="saveAll" class="btn btn-primary" type="button" >Save all</button>
    </div>
  <?php else: ?>
    <?php include '_form.php'; ?>
  <?php endif; ?>
</div>
<?php include('_footer.php'); ?>
	</body>
	</html>
