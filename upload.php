<?php
/*
 * upload.php
 * fetch requested file and show multiple required times and integrate a relevent cropper on every image
 */

 // include basic project configuration
include '_config.php';
$_dty_error = array();
 ?>
<!DOCTYPE html>
<html>
<head>
<?php include '_header.php'; ?>
<?php include '_footer.php'; ?>
</head>
<body>

<?php

$_dty_unique_id = strtotime('now');
$_dty_baseUrl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI'].'?').'/';
$_dty_uploaded = false;
if (!empty($_FILES['fileToUpload']['name'])) {
    $_dty_filename = basename($_FILES['fileToUpload']['name']);
    $_dty_filenameWithoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $_dty_filename);
    $_dty_folderName = $_dty_unique_id.'_'.$_dty_filenameWithoutExt;
    $_dty_target_dir = $_dty_root_dir = '/';
    // find dimensions
    list($_dty_width, $_dty_height, $_dty_type, $_dty_attr) = getimagesize($_FILES['fileToUpload']['tmp_name']);

    // open / create directory for original uploaded file
    $_dty_target_dir = 'original/';
    if (!file_exists($_dty_target_dir)) {
        mkdir($_dty_target_dir, 0777, true);
    }
    // start creating image set to crop
    $_dty_imagesSet = array();
    // this is for the original one
    $_dty_imagesSet[] = array('width' => $_dty_width, 'height' => $_dty_height, 'original' => true);
    // fullpath to original file
    $_dty_target_file = $_dty_target_dir.$_dty_filename;
    $_dty_uploadOk = 1;
    // get pathinfo and image extenstion
    $_dty_imageFileType = pathinfo($_dty_target_file, PATHINFO_EXTENSION);
    $_dty_imageFileType = strtolower($_dty_imageFileType);
    // Check if image file is a actual image or fake image
    if (isset($_POST['submit'])) {
      // check if the image is valid
        $_dty_check = getimagesize($_FILES['fileToUpload']['tmp_name']);
        if ($_dty_check !== false) {
            $_dty_uploadOk = 1;
        } else {
            $_dty_error[] = 'File is not an image.';
            $_dty_uploadOk = 0;
        }
    }

    // Allow certain file formats
    if ($_dty_imageFileType != 'jpg' && $_dty_imageFileType != 'png' && $_dty_imageFileType != 'jpeg') {
        $_dty_error[] = 'Sorry, only JPG, JPEG, PNG files are allowed.';
        $_dty_uploadOk = 0;
    }
    // Check if $_dty_uploadOk is set to 0 by an error
    if ($_dty_uploadOk == 0) {
        $_dty_error[] = 'Sorry, your file was not uploaded.';
    // if everything is ok, try to upload file
    } else {
        // move the temporary uploaded file to targeted destination
        if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $_dty_target_file)) {
            $_dty_minus = 100;
            $_dty_maxWidth = 2000;
            $_dty_maxHeights = 2000;
            // start from 2000 if the width is > 2000px
            if ($_dty_width > $_dty_maxWidth) {
                $_dty_width = 2000;
            }
            // start from 2000 if the height is > 2000px
            if ($_dty_height > $_dty_maxHeights) {
                $_dty_height = 2000;
            }

            // width iterations to crop
            $_dty_iterateWidthXTimes = (int) $_dty_width / 100;

            // height iterations
            $_dty_iterateHeightXTimes = (int) $_dty_height / 100;

            // subtract extra numbers if width is not multiple of 100
            if ($_dty_width % 100 > 0) {
                $_dty_tempWidth = $_dty_width - ($_dty_width % 100);
            } else {
                $_dty_tempWidth = $_dty_width;
            }

            for ($i = 0; $i < $_dty_iterateWidthXTimes; ++$i) {
                // subtract extra numbers if height is not multiple of 100
              if ($_dty_height % 100 > 0) {
                  $_dty_tempHeight = $_dty_height - ($_dty_height % 100);
              } else {
                  $_dty_tempHeight = $_dty_height;
              }
                for ($j = 0; $j < $_dty_iterateHeightXTimes; ++$j) {
                    if ($_dty_tempWidth >= 100 && $_dty_tempHeight >= 100) {
                        $_dty_imagesSet[] = array('width' => $_dty_tempWidth, 'height' => $_dty_tempHeight, 'original' => false);
                        if (!file_exists($_dty_tempWidth.'x'.$_dty_tempHeight.'/')) {
                            mkdir($_dty_tempWidth.'x'.$_dty_tempHeight.'/', 0777, true);
                        }
                    }
                    $_dty_tempHeight = $_dty_tempHeight - $_dty_minus;
                }
                $_dty_tempWidth = $_dty_tempWidth - $_dty_minus;
            }

            // var_dump($_dty_imagesSet);die;
            $_dty_uploaded = true;
        } else {
            echo 'Sorry, there was an error uploading your file.';
        }
    }
}
    ?>
<div class="container">
  <?php if (!empty($_dty_error)): ?>
    <?php foreach ($_dty_error as $e): ?>
    <div class="row">
      <div class="alert alert-danger"><?php echo $e; ?></div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
	<?php if ($_dty_uploaded) : ?>
    <div class="row text-center">

          <button id="_dty_saveAll2" class="btn btn-primary _dty_saveAll" type="button" >Save all</button>
      	   <?php foreach ($_dty_imagesSet as $image): ?>
        		<form style=" text-align: -webkit-center;" action="http://www.google.com" method="POST" class="_dty_customizeImagesForms"  id="image_<?php echo $image['width']; ?>_<?php echo $image['height']; ?>">
            	<p><span class="label label-succcess"><?php echo $image['width'].' x '.$image['height']; ?></span></p>
              <?php if (!$image['original']): ?> <div class="img-container">   <?php endif; ?>
                <img class="customImg" customWidth="<?php echo $image['width']; ?>" customHeight="<?php echo $image['height']; ?>" original="<?php echo $image['original']; ?>" src="<?php echo $_dty_target_file; ?>" />
                  <?php if (!$image['original']): ?>
                    <input type="hidden" value="<?php echo $_dty_width; ?>" name="originalWidth" />
                    <input type="hidden" value="<?php echo $_dty_height; ?>" name="originalHeight" />
                    <input type="hidden" value="<?php echo $_dty_target_file; ?>" name="imageSource" />
                    <input type="hidden" value="<?php echo $_dty_root_dir.$image['width'].'x'.$image['height'].'/'; ?>" name="imageDest" />
                    <input type="hidden" value="<?php echo $_dty_filename; ?>" name="filename" />
                    <input type="hidden" value="<?php echo $image['width']; ?>" name="croppedOriginalWidth" />
                    <input type="hidden" value="<?php echo  $image['height']; ?>" name="croppedOriginalHeight" />
                    <input type="hidden" value="0" id="crop_<?php echo $image['width']; ?>_<?php echo $image['height']; ?>_x" name="_x" />
                    <input type="hidden" value="0" id="crop_<?php echo $image['width']; ?>_<?php echo $image['height']; ?>_y" name="_y" />
                    <input type="hidden" value="0" id="crop_<?php echo $image['width']; ?>_<?php echo $image['height']; ?>_w" name="_w" />
                    <input type="hidden" value="0" id="crop_<?php echo $image['width']; ?>_<?php echo $image['height']; ?>_h" name="_h" />
                    <input type="hidden" value="0" id="crop_<?php echo $image['width']; ?>_<?php echo $image['height']; ?>_rotate" name="rotate" />
                    <input type="hidden" value="0" id="crop_<?php echo $image['width']; ?>_<?php echo $image['height']; ?>_scaleX" name="scaleX" />
                    <input type="hidden" value="0" id="crop_<?php echo $image['width']; ?>_<?php echo $image['height']; ?>_scaleY" name="scaleY" />
                <?php endif; ?>
              <?php if (!$image['original']): ?> </div>   <?php endif; ?>
                <!-- img-container end -->
        		</form>
      	 <?php endforeach; ?>
         <button id="_dty_saveAll" class="btn btn-primary _dty_saveAll" type="button" >Save all</button>

    </div>
  <?php else: ?>
    <?php include '_form.php'; ?>
  <?php endif; ?>
</div>
<!-- container end -->
	</body>
	</html>
