<?php

/*
 * save.php
 * Accept post request from the upload.php to save requested cropped images
 */

// general config +  global variables
include '_config.php';

// if the request for file upload is POST from the client side
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // root directory real path as Imagak, a php extension to crop images, use real path to access and save file on server
    $_dty_dir = dirname(__FILE__);

    // making sure user has cropped images on client side
    if (!empty($_POST['images'])) {

        // saving posted cropped images ARRAY into _dty_images
        $_dty_images = $_POST['images'];

        // loop cropped images array one by one and save on server according to their width and height
        foreach ($_dty_images as $key => $image) {

            // as array was serialized while sending from client side, so looping array element to access cropped properties like width, height, x and y points of image when cropped
            foreach ($image as $k => $d) {
                // original file path that will be required to crop images
                if ($d['name'] === 'imageSource') {
                    $_dty_src = $d['value'];
                }
                // destination path of image: where cropped image will be saved
                if ($d['name'] === 'imageDest') {
                    $_dty_imageDest = $d['value'];
                }
                // uploaded file name with extension
                if ($d['name'] === 'filename') {
                    $_dty_filename = $d['value'];
                }
                // Pixels from the left when cropped on client side
                if ($d['name'] === '_x') {
                    $_dty_x = $d['value'];
                }
                // Pixels from the top when cropped on client side
                if ($d['name'] === '_y') {
                    $_dty_y = $d['value'];
                }
                // Dynamic width of the cropped image: It's dynamic because of the zoom effect
                if ($d['name'] === '_w') {
                    $_dty_w = $d['value'];
                }
                // Dynamic height of the cropped image: It's dynamic because of the zoom effect
                if ($d['name'] === '_h') {
                    $_dty_h = $d['value'];
                }
                // Fixed width of the cropped image
                if ($d['name'] === 'croppedOriginalWidth') {
                    $_dty_croppedOriginalWidth = $d['value'];
                }
                // Fixed height of the cropped image
                if ($d['name'] === 'croppedOriginalHeight') {
                    $_dty_croppedOriginalHeight = $d['value'];
                }
                // Original width of image that was uploaded by user
                if ($d['name'] === 'originalWidth') {
                    $_dty_originalWidth = $d['value'];
                }
                // Original height of image that was uploaded by user
                if ($d['name'] === 'originalHeight') {
                    $_dty_originalHeight = $d['value'];
                }
            }

            // Using PHP extension Imagick to crop images

            // creating Imagik instance for orginal upload file in orignal folder that would be cropped and save into the destination.
            // Note:- Original will be the same at the same location
            $_dty_frame = new \Imagick($_dty_dir.'/'.$_dty_src);

            // crop  orignal file as image for cropped width, height, distance from left, distance from top
            $_dty_frame->cropImage($_dty_w, $_dty_h, $_dty_x, $_dty_y);

            // save to root/wxh directory
            $_dty_frame->writeImage($_dty_dir.'/'.$_dty_imageDest.$_dty_filename);

            // resize again if zoomed by user according to the requested dynamic and fixed width
            // if dynamic width or dynamic height is not equal to fixed width or fixed height. That shows the image was zoomed when cropped by user
            if ($_dty_w != $_dty_croppedOriginalWidth || $_dty_h != $_dty_croppedOriginalHeight) {

                // create new Imagik instance to resize the cropped image again because of zoomed
                $_dty_resizeImg = new \Imagick($_dty_dir.'/'.$_dty_imageDest.$_dty_filename);

                // resizing the image to get into the fixed width and height
                $_dty_resizeImg->resizeImage($_dty_croppedOriginalWidth, $_dty_croppedOriginalHeight, Imagick::FILTER_LANCZOS, 1);

                // save to root/wxh directory
                $_dty_resizeImg->writeImage($_dty_dir.'/'.$_dty_imageDest.$_dty_filename);
            }
        }
        // return success response as json width uploaded filename, it's orignal width and original height
        echo json_encode(array('_dty_success' => true, '_dty_file' => $_dty_filename, '_dty_width' => $_dty_originalWidth, '_dty_height' => $_dty_originalHeight));
        die;
    } else {
        // return failure response as json no image was cropped
        echo json_encode(array('_dty_success' => false));
        die;
    }
} else {
  // return failure response as json if request type is not POST
    echo json_encode(array('_dty_success' => false));
    die;
}
