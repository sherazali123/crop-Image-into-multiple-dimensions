<?php
/*
 * upload.php
 * fetch requested file and show multiple required times and integrate a relevent cropper on every image
 */

 // general config +  global variables
include '_config.php';

// error array: push error into this if found any while excecution
$_dty_error = array();
 ?>
<!DOCTYPE html>
<html>
<head>
  <!-- Header: Including all the css content here -->
<?php include '_header.php'; ?>
<!--  Including all the JS content here -->
<?php include '_footer.php'; ?>
</head>
<body>

<?php

// Full Site Url for absolute/realpath for images
$_dty_baseUrl = 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI'].'?').'/';

// bool: Flag to check if file is uploaded correctly
$_dty_uploaded = false;

// making sure if user has selected file form browser and it's not empty request
if (!empty($_FILES['fileToUpload']['name'])) {

    // fetching filename from uploaded file
    $_dty_filename = basename($_FILES['fileToUpload']['name']);

    // remove extenstion from filename and store into _dty_filenameWithoutExt
    $_dty_filenameWithoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $_dty_filename);

    // root folder of project
    $_dty_target_dir = $_dty_root_dir = '/';

    // find original image dimensions e.g. image width, image height, image type and others attributes from uploaded file
    list($_dty_width, $_dty_height, $_dty_type, $_dty_attr) = getimagesize($_FILES['fileToUpload']['tmp_name']);

    // path for the original image
    $_dty_target_dir = 'original/';

    // if original folder doesn't exists
    if (!file_exists($_dty_target_dir)) {

        // create folder "original" in root path of project and give 777 permissions
        mkdir($_dty_target_dir, 0777, true);
    }

    // Intialize an empty array to maintaim  dimensions (width, height, is Original flag) for all possible images to be cropped by user
    $_dty_imagesSet = array();

    // Original image dimensions (width, height, is Original flag)
    $_dty_imagesSet[] = array('width' => $_dty_width, 'height' => $_dty_height, 'original' => true);

    // absolute or realpath for the original uploaded file
    $_dty_target_file = $_dty_target_dir.$_dty_filename;

    // flag to maintain if the uploaded file is valid image
    $_dty_uploadOk = 1;

    // get pathinfo and image extenstion
    $_dty_imageFileType = pathinfo($_dty_target_file, PATHINFO_EXTENSION);

    // convert image extension into lowercase characters to reduce IF checks to validate extension
    $_dty_imageFileType = strtolower($_dty_imageFileType);

    // Check if the server is receiving expected request and submitted cottectly.
    if (isset($_POST['submit'])) {

        // check if the image is valid
        $_dty_check = getimagesize($_FILES['fileToUpload']['tmp_name']);
        if ($_dty_check !== false) {

            // image is fine
            $_dty_uploadOk = 1;
        } else {
            // some error thrown by php. we will consider the image is not valid and show it to the user
            $_dty_error[] = 'File is not an image.';
            // image is not correct
            $_dty_uploadOk = 0;
        }
    }

    // Allowed image extensions
    if ($_dty_imageFileType != 'jpg' && $_dty_imageFileType != 'png' && $_dty_imageFileType != 'jpeg') {

        // image is not valid and current functionality will not handle images/files other than of jpg/png/jpeg
        $_dty_error[] = 'Sorry, only JPG, JPEG, PNG files are allowed.';

        // image is not correct
        $_dty_uploadOk = 0;
    }

    // Finally checking if the image is correct or not
    if ($_dty_uploadOk == 0) {

        // image was not correct that's why couldn't be uplsoaded
        $_dty_error[] = 'Sorry, your file was not uploaded.';
    } else {

        // default behaviour is: server store uploaded file in the temporary folder then we move that file into our required destination
        // moving upload image in the original folder
        if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $_dty_target_file)) {

          // root directory real path as Imagak, a php extension to crop images, use real path to access and save file on server
          $_dty_dir = dirname(__FILE__);

          // creating Imagik instance for orginal upload file in orignal folder that would be cropped and save into the destination.
          // Note:- Original will be the same at the same location
          $_dty_frame = new \Imagick($_dty_dir.'/'.$_dty_target_file);
          // var_dump($_dty_dir.'/'.$_dty_target_file);die;
          $_dty_frame->setImageCompression(Imagick::COMPRESSION_JPEG);
          $_dty_frame->setImageCompressionQuality(80);

          // save to root/wxh directory
          $_dty_frame->writeImage($_dty_dir.'/'.$_dty_target_file);

            // mainting a variable _dty_minus with mentioned value. It will populate the cropping form with dimensions (width and height multiple of this variable )
            $_dty_minus = 100;

            // maximum width that can be cropped
            $_dty_maxWidth = 2000;

            // maximum height that can be cropped
            $_dty_maxHeights = 2000;

            // start from 2000px if the width is greater than 2000px
            if ($_dty_width > $_dty_maxWidth) {
                $_dty_width = 2000;
            }

            // start from 2000px if the height is greter than 2000px
            if ($_dty_height > $_dty_maxHeights) {
                $_dty_height = 2000;
            }

            // width iterations to crop
            $_dty_iterateWidthXTimes = (int) $_dty_width / 100;

            // height iterations to crop
            $_dty_iterateHeightXTimes = (int) $_dty_height / 100;

            // if height is not multiple of 100 then minux the extra numbers to make it multiple 0f 100
            if ($_dty_width % 100 > 0) {

                // subtract extra numbers if width is not multiple of 100
                $_dty_tempWidth = $_dty_width - ($_dty_width % 100);
            } else {

                // no need to minus as already multiple of 100s
                $_dty_tempWidth = $_dty_width;
            }

            for ($i = 0; $i < $_dty_iterateWidthXTimes; ++$i) {
              // if height is not multiple of 100 then minux the extra numbers to make it multiple 0f 100
              if ($_dty_height % 100 > 0) {

                  // subtract extra numbers if height is not multiple of 100
                  $_dty_tempHeight = $_dty_height - ($_dty_height % 100);
              } else {

                  // no need to minus as already multiple of 100s
                  $_dty_tempHeight = $_dty_height;
              }
                for ($j = 0; $j < $_dty_iterateHeightXTimes; ++$j) {

                    // show only images width width and height greater than 100
                    if ($_dty_tempWidth >= 100 && $_dty_tempHeight >= 100) {

                        // all possible widths and heights that would be cropped
                        $_dty_imagesSet[] = array('width' => $_dty_tempWidth, 'height' => $_dty_tempHeight, 'original' => false);

                        // if directory doesn't exist for width and height (wxh) on root of the project
                        if (!file_exists($_dty_tempWidth.'x'.$_dty_tempHeight.'/')) {

                            // create folder with name wxh and give full permissions of 777
                            mkdir($_dty_tempWidth.'x'.$_dty_tempHeight.'/', 0777, true);
                        }
                    }

                    // subtracting 100 form height one by one until it gets to 100
                    $_dty_tempHeight = $_dty_tempHeight - $_dty_minus;
                }

                // subtracting 100 form width one by one until it gets to 100
                $_dty_tempWidth = $_dty_tempWidth - $_dty_minus;
            }

            // Finally store the confirmation that image is uploaded in original folder and created all possible dimensions(widths, heights) that would be cropped
            $_dty_uploaded = true;
        } else {

            // file was not moved to destination correct. Can be any reason. Prmissions may be
            echo 'Sorry, there was an error uploading your file.';
        }
    }
}
    ?>
<!-- Container -->
<div class="container">
  <!-- Print errors if found any -->
  <?php if (!empty($_dty_error)): ?>
    <?php foreach ($_dty_error as $e): ?>
    <div class="row">
      <div class="alert alert-danger"><?php echo $e; ?></div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
  <!-- If uploaded correctly -->
	<?php if ($_dty_uploaded) : ?>
    <div class="row text-center">
          <!-- Button to save cropped images -->
          <button id="_dty_saveAll2" class="btn btn-primary _dty_saveAll" type="button" >Save all</button>
      	   <!-- Images to cropped + Original Image-->
           <?php foreach ($_dty_imagesSet as $image): ?>
        		<form style=" text-align: -webkit-center;" action="http://www.google.com" method="POST" class="_dty_customizeImagesForms"  id="image_<?php echo $image['width']; ?>_<?php echo $image['height']; ?>">
            	<p><span class="label label-succcess"><?php echo $image['width'].' x '.$image['height']; ?></span></p>
              <?php if (!$image['original']): ?> <div class="img-container">   <?php endif; ?>
                <img class="customImg" id="croppedImage_<?php echo $image['width']; ?>_<?php echo $image['height']; ?>" customWidth="<?php echo $image['width']; ?>" customHeight="<?php echo $image['height']; ?>" original="<?php echo $image['original']; ?>" src="<?php echo $_dty_target_file; ?>" />
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
    <!-- Upload again if not uploaded -->
    <?php include '_form.php'; ?>
  <?php endif; ?>
</div>
<!-- container end -->
	</body>
	</html>
