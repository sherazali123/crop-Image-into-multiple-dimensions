<?php

// get file name from the url
$_dty_filename = $_GET['file'];

// get original width and height to show all cropped images: width and height will help us to find possible wxh folders on root
// original width
$_dty_width = $_GET['w'];

// original height
$_dty_height = $_GET['h'];

// filename, width and height in the url are required
if (empty($_dty_filename) || empty($_dty_width) || empty($_dty_height)) {
    echo 'Not allowed to access.';
    die;
}
// width and height must be numbers
if (!is_numeric($_dty_width) || !is_numeric($_dty_height)) {
    echo 'Invalid input';
    die;
}

// root directory real path as Imagak, a php extension to crop images, use real path to access and save file on server
$dir = dirname(__FILE__);

// Intialize an empty array to maintaim  dimensions (width, height, is Original flag) for all possible images cropped by user
$_dty_imagesSet = array();

// Original image dimensions (width, height, is Original flag)
$_dty_imagesSet[] = array('width' => $_dty_width, 'height' => $_dty_height, 'original' => true , 'link' => 'original/'.$_dty_filename);
if(file_exists($dir.'/'.'original/'.$_dty_filename)){
  unlink($dir.'/'.'original/'.$_dty_filename);
}
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
            $_dty_imagesSet[] = array('width' => $_dty_tempWidth, 'height' => $_dty_tempHeight, 'original' => false, 'link' => $_dty_tempWidth.'x'.$_dty_tempHeight.'/'.$_dty_filename);
            if(file_exists($dir.'/'.$_dty_tempWidth.'x'.$_dty_tempHeight.'/'.$_dty_filename)){
              unlink($dir.'/'.$_dty_tempWidth.'x'.$_dty_tempHeight.'/'.$_dty_filename);
            }

        }

        // subtracting 100 form height one by one until it gets to 100
        $_dty_tempHeight = $_dty_tempHeight - $_dty_minus;
    }

    // subtracting 100 form width one by one until it gets to 100
    $_dty_tempWidth = $_dty_tempWidth - $_dty_minus;
}
header("Location: list.php?deleted=ok#".$_dty_filename);
die();
