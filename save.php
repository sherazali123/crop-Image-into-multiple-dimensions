<?php

/*
 * save.php
 * Accept post request from the upload.php to save requested cropped images
 */

// general config
include '_config.php';
// if the request is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_dty_dir = dirname(__FILE__); //
    if (!empty($_POST['images'])) {
        $_dty_images = $_POST['images']; // get images from client end
        foreach ($_dty_images as $key => $image) {
            foreach ($image as $k => $d) {
                // gather required paramters
                if ($d['name'] === 'imageSource') {
                    $_dty_src = $d['value'];
                }
                if ($d['name'] === 'imageDest') {
                    $_dty_imageDest = $d['value'];
                }
                // if ($d['name'] === 'folderName') {
                //     $_dty_folderName = $d['value'];
                // }
                if ($d['name'] === 'filename') {
                    $_dty_filename = $d['value'];
                }
                if ($d['name'] === '_x') {
                    $_dty_x = $d['value'];
                }
                if ($d['name'] === '_y') {
                    $_dty_y = $d['value'];
                }
                if ($d['name'] === '_w') {
                    $_dty_w = $d['value'];
                }
                if ($d['name'] === '_h') {
                    $_dty_h = $d['value'];
                }
                if ($d['name'] === 'originalWidth') {
                    $_dty_originalWidth = $d['value'];
                }
                if ($d['name'] === 'originalHeight') {
                    $_dty_originalHeight = $d['value'];
                }
                if ($d['name'] === 'croppedOriginalWidth') {
                    $_dty_croppedOriginalWidth = $d['value'];
                }
                if ($d['name'] === 'croppedOriginalHeight') {
                    $_dty_croppedOriginalHeight = $d['value'];
                }
            }

            // crop on server side using Imagick
            $_dty_frame = new \Imagick($_dty_dir.'/'.$_dty_src);
            $_dty_frame->cropImage($_dty_w, $_dty_h, $_dty_x, $_dty_y);
            // save to root/wxh directory
            $_dty_frame->writeImage($_dty_dir.'/'.$_dty_imageDest.$_dty_filename);

            // resize again if zoomed by user according to the requested parameters
            if ($_dty_w != $_dty_croppedOriginalWidth || $_dty_h != $_dty_croppedOriginalHeight) {

                // create new Imagik instance to resize the cropped image again because of zoomed
                $_dty_resizeImg = new \Imagick($_dty_dir.'/'.$_dty_imageDest.$_dty_filename);
                // resizing here
                $_dty_resizeImg->resizeImage($_dty_croppedOriginalWidth, $_dty_croppedOriginalHeight, Imagick::FILTER_LANCZOS, 1);
                // save to root/wxh directory
                $_dty_resizeImg->writeImage($_dty_dir.'/'.$_dty_imageDest.$_dty_filename);
            }
        }
        echo json_encode(array('_dty_success' => true, '_dty_file' => $_dty_filename, '_dty_width' => $_dty_originalWidth, '_dty_height' => $_dty_originalHeight));
        die;
    } else {
        echo json_encode(array('_dty_success' => false));
        die;
    }
} else {
    echo json_encode(array('_dty_success' => false));
    die;
}
