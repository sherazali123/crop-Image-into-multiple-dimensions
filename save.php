<?php

/*
 * save.php
 * Accept post request from the upload.php to save requested cropped images
 */

// general config
include '_config.php';
// if the request is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dir = dirname(__FILE__); //
    if (!empty($_POST['images'])) {
        $images = $_POST['images']; // get images from client end
        foreach ($images as $key => $image) {
            foreach ($image as $k => $d) {
                // gather required paramters
                if ($d['name'] === 'imageSource') {
                    $src = $d['value'];
                }
                if ($d['name'] === 'imageDest') {
                    $imageDest = $d['value'];
                }
                // if ($d['name'] === 'folderName') {
                //     $folderName = $d['value'];
                // }
                if ($d['name'] === 'filename') {
                    $filename = $d['value'];
                }
                if ($d['name'] === '_x') {
                    $x = $d['value'];
                }
                if ($d['name'] === '_y') {
                    $y = $d['value'];
                }
                if ($d['name'] === '_w') {
                    $w = $d['value'];
                }
                if ($d['name'] === '_h') {
                    $h = $d['value'];
                }
                if ($d['name'] === 'originalWidth') {
                    $originalWidth = $d['value'];
                }
                if ($d['name'] === 'originalHeight') {
                    $originalHeight = $d['value'];
                }
                if ($d['name'] === 'croppedOriginalWidth') {
                    $croppedOriginalWidth = $d['value'];
                }
                if ($d['name'] === 'croppedOriginalHeight') {
                    $croppedOriginalHeight = $d['value'];
                }
            }

            // crop on server side using Imagick
            $frame = new \Imagick($dir.'/'.$src);
            $frame->cropImage($w, $h, $x, $y);
            // save to root/wxh directory
            $frame->writeImage($dir.'/'.$imageDest.$filename);

            // resize again if zoomed by user according to the requested parameters
            if ($w != $croppedOriginalWidth || $h != $croppedOriginalHeight) {
                $resizeImg = new \Imagick($dir.'/'.$imageDest.$filename);
                $resizeImg->resizeImage($croppedOriginalWidth, $croppedOriginalHeight, Imagick::FILTER_LANCZOS, 1);
                $resizeImg->writeImage($dir.'/'.$imageDest.$filename);
            }
        }
        echo json_encode(array('success' => true, 'file' => $filename, 'width' => $originalWidth, 'height' => $originalHeight));
        die;
    } else {
        echo json_encode(array('success' => false));
        die;
    }
} else {
    echo json_encode(array('success' => false));
    die;
}
